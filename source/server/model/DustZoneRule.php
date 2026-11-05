<?php

/* Create Game terrain: the DUST ZONE (B5W "Dust").

   B5W: "Some areas of space are clouded with fine dust particles. If a scenario takes place in a dust
   zone, the entire map will be considered covered with dust, and units will be damaged each turn as
   they move through it." The tabletop rule is the 100% option and the default; 50% and 25% are one
   organic clump of dust round the map's centre, covering that share of the map (user, 2026-11-05).

   rules key `dustZone`: {"coverage": 100|50|25|0, "spans": [[r, qFrom, qTo], ...]}
     coverage - 100 = every hex is dust, and there are no spans. 50 / 25 = the clump. 0 = no random
                dust: the spans are only a map template's pre-placed dust hexes.
     spans    - every dust hex, row by row: row r, columns qFrom..qTo inclusive. Written ONCE, by
                generateDustHexes() when the game is created (Manager::createGame via
                GameRules::finaliseDustZone), so the lobby's Map Preview, the game and the server all
                read the same hexes. Anything a client posts here is thrown away and regenerated.

   The rule is the dust; the unit is only a shooter. BuyingGamePhase gives the game ONE hidden DustZone
   terrain unit (ships/terrain/DustZone.php) to own the DustCollision orders, and RammingAttack asks this
   rule which hexes are dust. The client draws the same hexes as one mesh (BallisticIconContainer).

   ⚠️ GameRules::hasRule() / callRule() find a rule by METHOD NAME across every rule, so each public
   method here carries a name no other rule could plausibly share. */
class DustZoneRule implements JsonSerializable {

    public static $coverages = array(100, 50, 25);
    public static $maxSpans = 4000;

    private $coverage = 0;
    private $spans = array();
    private $hexSet = null; //"q,r" => true, built from $spans on first ask

    function __construct($coverage, $spans = null) {
        $coverage = (int)$coverage;
        $this->coverage = in_array($coverage, self::$coverages, true) ? $coverage : 0;
        if ($this->coverage === 100 || !is_array($spans)) return;

        foreach ($spans as $span) {
            if (is_object($span)) $span = array_values((array)$span);
            if (!is_array($span) || count($span) < 3) continue;
            $r = (int)$span[0];
            $from = (int)$span[1];
            $to = (int)$span[2];
            if ($to < $from) continue;
            $this->spans[] = array($r, $from, $to);
            if (count($this->spans) >= self::$maxSpans) break;
        }
    }

    public function getRuleName() {
        return 'dustZone';
    }

    public function isEmpty() {
        return $this->coverage === 0 && count($this->spans) === 0;
    }

    public function getDustCoverage() {
        return $this->coverage;
    }

    public function isDustEverywhere() {
        return $this->coverage === 100;
    }

    public function isDustHex($q, $r) {
        if ($this->coverage === 100) return true;

        if ($this->hexSet === null) {
            $this->hexSet = array();
            foreach ($this->spans as $span) {
                for ($q2 = $span[1]; $q2 <= $span[2]; $q2++) $this->hexSet[$q2 . ',' . $span[0]] = true;
            }
        }
        return isset($this->hexSet[(int)$q . ',' . (int)$r]);
    }

    /* Creation only (GameRules::finaliseDustZone). Grows the 50% / 25% clump on this map and adds the
       map template's dust hexes ($extraHexes, [[q, r], ...]) to it. Full coverage needs no hexes. */
    public function generateDustHexes($gamespace, $extraHexes = array()) {
        $this->hexSet = null;
        $this->spans = array();
        if ($this->coverage === 100) return;

        $hexes = array();
        if ($this->coverage > 0) {
            foreach (self::growDustClump($gamespace, $this->coverage / 100) as $hex) $hexes[$hex[0] . ',' . $hex[1]] = $hex;
        }
        foreach ($extraHexes as $hex) $hexes[(int)$hex[0] . ',' . (int)$hex[1]] = array((int)$hex[0], (int)$hex[1]);

        $this->spans = self::toDustSpans($hexes);
    }

    /* "100x40" (or "-1x-1", an open map) -> ['width', 'height'], as BuyingGamePhase::getGamespace reads
       it: an open map counts as 60 x 40, where the random terrain is placed too. */
    public static function parseDustGamespace($gamespace) {
        $width = -1;
        $height = -1;
        sscanf((string)$gamespace, "%dx%d", $width, $height);
        if ((int)$width <= 0 || (int)$height <= 0) return array('width' => 60, 'height' => 40);
        return array('width' => (int)$width, 'height' => (int)$height);
    }

    /* ONE clump of dust, grown out from the map's centre a hex at a time, always taking the open hex with
       the lowest score next - so it stays in one piece, and covers exactly $fraction of the map's
       width x height hexes.

       The score is the hex's distance from the centre on an ellipse shaped like the map (so 50% leaves
       a similar margin on every side, and neither deployment edge is favoured), stretched or shrunk by a
       few long, random waves - which is what gives the edge its lobes and bays instead of a hexagon.

       Hexes are taken only where the game draws the map box (webglHexGridRenderer): width hex-widths
       by height rows, centred on hex (0, 0) - and half a hex left of it on an even width. Odd rows sit
       half a hex left of even ones (Mathlib::hexCoToPixel). */
    private static function growDustClump($gamespace, $fraction) {
        $width = $gamespace['width'];
        $height = $gamespace['height'];
        $target = (int)round($width * $height * $fraction);
        if ($target <= 0) return array();

        $centreX = ($width % 2 == 0) ? -0.5 : 0; //in hex widths
        $halfW = $width / 2;
        $halfH = $height / 2;                    //in rows
        $rowPitch = sqrt(3) / 2;                 //a row is this many hex widths tall

        $onMap = function ($q, $r) use ($centreX, $halfW, $halfH) {
            if (abs($r) > $halfH) return false;
            $x = $q - 0.5 * ($r & 1);
            return abs($x - $centreX) <= $halfW + 1e-9;
        };

        //Three long waves, 6-14 hex widths from crest to crest, at random angles and phases.
        $waves = array();
        for ($i = 0; $i < 3; $i++) {
            $angle = mt_rand(0, 359) * M_PI / 180;
            $length = mt_rand(60, 140) / 10;
            $waves[] = array(cos($angle) * 2 * M_PI / $length, sin($angle) * 2 * M_PI / $length, mt_rand(0, 628) / 100);
        }
        $semiX = $halfW;
        $semiY = $halfH * $rowPitch;
        $score = function ($q, $r) use ($centreX, $rowPitch, $semiX, $semiY, $waves) {
            $px = ($q - 0.5 * ($r & 1)) - $centreX;
            $py = $r * $rowPitch;
            $distance = sqrt(pow($px / $semiX, 2) + pow($py / $semiY, 2));
            $noise = 0;
            foreach ($waves as $wave) $noise += sin($px * $wave[0] + $py * $wave[1] + $wave[2]);
            return $distance * (1 + 0.3 * $noise / 3);
        };

        $clump = array();
        $seen = array('0,0' => true);
        $frontier = new SplPriorityQueue();
        $frontier->setExtractFlags(SplPriorityQueue::EXTR_DATA);
        $frontier->insert(array(0, 0), -$score(0, 0));

        while (count($clump) < $target && !$frontier->isEmpty()) {
            $hex = $frontier->extract();
            $clump[] = $hex;

            foreach (self::getDustNeighbours($hex[0], $hex[1]) as $next) {
                $key = $next[0] . ',' . $next[1];
                if (isset($seen[$key])) continue;
                $seen[$key] = true;
                if (!$onMap($next[0], $next[1])) continue;
                $frontier->insert($next, -$score($next[0], $next[1]));
            }
        }

        return $clump;
    }

    //The six hexes round (q, r): an odd row sits half a hex left of an even one (Mathlib::getNeighbouringHexes).
    private static function getDustNeighbours($q, $r) {
        $side = ($r & 1) ? $q - 1 : $q; //the left one of the two neighbours above, and of the two below
        return array(
            array($q + 1, $r), array($q - 1, $r),
            array($side, $r + 1), array($side + 1, $r + 1),
            array($side, $r - 1), array($side + 1, $r - 1)
        );
    }

    //"q,r" => [q, r] -> [[r, qFrom, qTo], ...], row by row, each row's runs left to right.
    private static function toDustSpans($hexes) {
        $rows = array();
        foreach ($hexes as $hex) $rows[$hex[1]][] = $hex[0];
        ksort($rows);

        $spans = array();
        foreach ($rows as $r => $columns) {
            sort($columns);
            $from = $columns[0];
            $to = $from;
            for ($i = 1; $i < count($columns); $i++) {
                if ($columns[$i] == $to + 1) {
                    $to = $columns[$i];
                    continue;
                }
                $spans[] = array((int)$r, $from, $to);
                $from = $to = $columns[$i];
            }
            $spans[] = array((int)$r, $from, $to);
        }
        return $spans;
    }

    public function jsonSerialize(): mixed {
        $out = array('coverage' => $this->coverage);
        if ($this->coverage !== 100) $out['spans'] = $this->spans;
        return $out;
    }
}
