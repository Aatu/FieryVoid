<?php
/**
 * HexZone - shared hex geometry: lines between hexes, and the zone a set of hexes encloses.
 *
 * WHY THIS FILE EXISTS
 * Two subtle, hard-won pieces of geometry were sitting on the wrong classes. The hex-line
 * tracer was a static on SpatialCutter (specialWeapons.php); the zone tests - convex hull,
 * point-in-polygon, the per-angle touch tolerance - were private methods on GraviticMine
 * (gravitic.php). The Walkers of Sigma-957 Energy Draining Field and Energy Draining Net
 * need both, and neither is in any way about spatial cutters or mines.
 *
 * EVERY BODY BELOW IS A VERBATIM MOVE. Zero logic edits: only the method names, the
 * static/visibility keywords and the $this-> to self:: calls changed. That is deliberate.
 * This geometry cost real investigation time to get right - the per-angle tolerance runs
 * 0.866 to 1.000 rather than the constant sqrt(3)/2 it started as, and the 1e-9 epsilon is
 * load-bearing on its own (game 7027 T1 sheared on a +1.1e-15 margin). Do not "tidy" it.
 *
 * The comments and local variable names inside containsUnit() still speak of "mines",
 * because that is where the code came from and rewording them would have meant editing the
 * very lines this move promises not to touch. Read "mine" as "any hex position defining the
 * zone" - a Gravitic Mine, an Energy Draining Net, whatever the caller passes.
 *
 * The originals both delegate here, so there is exactly one copy of each routine.
 *
 * Design record: WALKERS_OF_SIGMA_PLAN.md section 2.3 (Stage 0).
 */
class HexZone
{
    /* ---------------------------------------------------------------------
       Lines
       --------------------------------------------------------------------- */

    public static function line(OffsetCoordinate $start, OffsetCoordinate $end) {
        $startCube = self::offsetToCube($start);
        $endCube   = self::offsetToCube($end);

        $dx = $endCube[0] - $startCube[0];
        $dy = $endCube[1] - $startCube[1];
        $dz = $endCube[2] - $startCube[2];

        $steps = max(abs($dx), abs($dy), abs($dz));
        if ($steps > 50) $steps = 50;

        $hexes = array();
        for ($i = 0; $i <= $steps; $i++) {
            $t       = $steps == 0 ? 0 : $i / $steps;
            $cx      = $startCube[0] + $dx * $t;
            $cy      = $startCube[1] + $dy * $t;
            $cz      = $startCube[2] + $dz * $t;
            $hexes[] = self::cubeToOffset(self::cubeRound($cx, $cy, $cz));
        }

        return $hexes;
    }

	private static function offsetToCube(OffsetCoordinate $hex) {
		$x = $hex->q - ($hex->r + ($hex->r & 1)) / 2;
		$z = $hex->r;
		$y = -$x - $z;
		return array($x, $y, $z);
	}

    private static function cubeRound($x, $y, $z) {
        $rx = round($x);
        $ry = round($y);
        $rz = round($z);
        $dx = abs($rx - $x);
        $dy = abs($ry - $y);
        $dz = abs($rz - $z);
        if ($dx > $dy && $dx > $dz) {
            $rx = -$ry - $rz;
        } else if ($dy > $dz) {
            $ry = -$rx - $rz;
        } else {
            $rz = -$rx - $ry;
        }
        return array($rx, $ry, $rz);
    }

	private static function cubeToOffset($cube) {
		$q = $cube[0] + ($cube[2] + ($cube[2] & 1)) / 2;
		$r = $cube[2];
		return new OffsetCoordinate($q, $r);
	}

    /* ---------------------------------------------------------------------
       Zones
       --------------------------------------------------------------------- */

    public static function containsUnit(OffsetCoordinate $unitPos, array $minePositions){
        $count = count($minePositions);
        if ($count < 2) return false;

        $unitPx = Mathlib::hexCoToPixel($unitPos);
        $minePx = array();
        foreach ($minePositions as $p) $minePx[] = Mathlib::hexCoToPixel($p);

        //Debug::log("    containsUnit: unitHex=({$unitPos->q},{$unitPos->r}) unitPx=({$unitPx['x']},{$unitPx['y']})");
        //foreach ($minePositions as $i => $p) {
        //    Debug::log("      mine[$i] hex=({$p->q},{$p->r}) px=({$minePx[$i]['x']},{$minePx[$i]['y']})");
        //}

        if ($count === 2) {
            // Rule: draw a line between the centres of the two mine hexes; if that line touches
            // the unit's hex, the unit is eligible. How far the line may pass from the hex centre
            // and still touch it depends on the line's ANGLE (see touchTolerance), so the
            // tolerance is taken per-line rather than fixed at the flat-side half-width.
            // The epsilon matters: a unit exactly on the line - mines in the same column with the
            // unit one half-column off, a very ordinary layout - lands precisely ON the tolerance,
            // where bare floating point was deciding shear-or-no-shear by ~1e-15.
            $tolerance = self::touchTolerance(
                $minePx[1]['x'] - $minePx[0]['x'],
                $minePx[1]['y'] - $minePx[0]['y']
            ) + 1e-9;
            $dist = self::pointToSegmentDistance($unitPx, $minePx[0], $minePx[1]);
            //Debug::log("    2-mine check: segDist={$dist} tolerance={$tolerance} inZone=" . ($dist <= $tolerance ? 'YES' : 'NO'));
            return $dist <= $tolerance;
        }

        // 3+ mines: the unit must be within the zone formed by the mines' arrangement - the convex
        // hull of the mine hex centres.
        $hull = self::hull($minePx);
        if (self::pointInPolygon($unitPx, $hull)) {
            //Debug::log("    3+-mine check: hullSize=" . count($hull) . " centre inside=YES");
            return true;
        }

        // A hex is included whole if the boundary lines cross ANY part of it, exactly as the
        // 2-mine rule counts a line that merely touches the unit's hex - point-in-polygon alone
        // tests the hex CENTRE, so it wrongly drops a hex the zone only partly overlaps. Walking
        // the hull edges with the same per-angle tolerance also covers the degenerate case where
        // every mine is collinear: hull() discards the collinear middles, leaving a 2-point
        // "hull" that pointInPolygon (n < 3) always rejects.
        $sides = count($hull);
        for ($i = 0, $j = $sides - 1; $i < $sides; $j = $i++) {
            $tolerance = self::touchTolerance(
                $hull[$i]['x'] - $hull[$j]['x'],
                $hull[$i]['y'] - $hull[$j]['y']
            ) + 1e-9;
            if (self::pointToSegmentDistance($unitPx, $hull[$j], $hull[$i]) <= $tolerance) {
                //Debug::log("    3+-mine check: hull edge {$j}-{$i} crosses the unit's hex");
                return true;
            }
        }
        //Debug::log("    3+-mine check: hullSize=" . count($hull) . " inside=NO");
        return false;
    }

    /* Greatest distance a line of direction ($abx,$aby) may pass from a hex centre while still
       touching that hex - i.e. the hex's half-width measured perpendicular to the line.
       Mathlib::hexCoToPixel lays out pointy-top hexes of circumradius 1 (column spacing sqrt(3),
       row spacing 1.5), so the vertices are (0,+/-1) and (+/-sqrt(3)/2,+/-0.5) and the answer is
       max|n.v| over those vertices for the line's unit normal n. It runs from sqrt(3)/2 = 0.866
       (line at 0/60 deg, crossing flat side to flat side) up to 1.0 (line at 30/90 deg, clipping
       a corner), so a fixed sqrt(3)/2 misses every corner-clipping line. Never returns less than
       sqrt(3)/2, so this can only ever make a unit MORE eligible than the old fixed value. */
    public static function touchTolerance($abx, $aby){
        $len = sqrt($abx * $abx + $aby * $aby);
        if ($len <= 1e-9) return sqrt(3) / 2; //both mines in one hex: no line to take a normal from, keep the flat-side value
        $nx = -$aby / $len; //unit normal to the line
        $ny =  $abx / $len;
        $s  = sqrt(3) / 2;  //vertex x-offset
        return max(abs($ny), abs($nx * $s + $ny * 0.5), abs($nx * $s - $ny * 0.5));
    }

    public static function pointToSegmentDistance($p, $a, $b){
        $abx = $b['x'] - $a['x'];
        $aby = $b['y'] - $a['y'];
        $lenSq = $abx * $abx + $aby * $aby;
        if ($lenSq <= 1e-9) {
            return sqrt(($p['x'] - $a['x']) ** 2 + ($p['y'] - $a['y']) ** 2);
        }
        $t = (($p['x'] - $a['x']) * $abx + ($p['y'] - $a['y']) * $aby) / $lenSq;
        $t = max(0, min(1, $t));
        $cx = $a['x'] + $t * $abx;
        $cy = $a['y'] + $t * $aby;
        return sqrt(($p['x'] - $cx) ** 2 + ($p['y'] - $cy) ** 2);
    }

    public static function hull(array $points){
        $n = count($points);
        if ($n < 3) return $points;
        usort($points, function($a, $b) {
            if ($a['x'] !== $b['x']) return $a['x'] <=> $b['x'];
            return $a['y'] <=> $b['y'];
        });
        $cross = function($O, $A, $B) {
            return ($A['x'] - $O['x']) * ($B['y'] - $O['y']) - ($A['y'] - $O['y']) * ($B['x'] - $O['x']);
        };
        $lower = array();
        foreach ($points as $p) {
            while (count($lower) >= 2 && $cross($lower[count($lower) - 2], $lower[count($lower) - 1], $p) <= 0) {
                array_pop($lower);
            }
            $lower[] = $p;
        }
        $upper = array();
        foreach (array_reverse($points) as $p) {
            while (count($upper) >= 2 && $cross($upper[count($upper) - 2], $upper[count($upper) - 1], $p) <= 0) {
                array_pop($upper);
            }
            $upper[] = $p;
        }
        array_pop($lower);
        array_pop($upper);
        return array_merge($lower, $upper);
    }

    public static function pointInPolygon($p, array $polygon){
        $n = count($polygon);
        if ($n < 3) return false;
        $inside = false;
        for ($i = 0, $j = $n - 1; $i < $n; $j = $i++) {
            $xi = $polygon[$i]['x']; $yi = $polygon[$i]['y'];
            $xj = $polygon[$j]['x']; $yj = $polygon[$j]['y'];
            $denom = ($yj - $yi);
            if ($denom == 0) $denom = 1e-9;
            $intersect = (($yi > $p['y']) !== ($yj > $p['y'])) &&
                         ($p['x'] < ($xj - $xi) * ($p['y'] - $yi) / $denom + $xi);
            if ($intersect) $inside = !$inside;
        }
        return $inside;
    }
}
