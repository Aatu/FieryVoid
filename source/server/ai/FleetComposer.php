<?php
/**
 * CPU opponent - AUTO-COMPOSE: a fleet for a CPU slot its creator left empty (TACTICAL_AI_PLAN.md section 8.2, stage T8).
 *
 * The user's ruling of 2026-11-12: an empty CPU slot is auto-composed, and the fleet must pass the Fleet Checker. The checker is
 * client-side (gamelobby.js checkChoices, ~1,300 lines). The user's ruling of 2026-11-22: no PHP port of it now - the composer is
 * built TO the checker's rules (problems() holds the ones a plain fleet of the ready factions can break) and PROVEN against the real
 * checker by tests/ai/fleetCheckOracle.js, which runs checkChoices under node on every fleet tests/ai/fleetComposerHarness.php
 * composes across the factions, point values and in-service dates. The full port waits for the campaign (stage T10), which will
 * have to check fleets it did not build.
 *
 * WHAT IT BUYS: from the faction's own catalogue - static/json/<faction>.json, the file the lobby's Store and checker read - plain
 * combat units only: common and uncommon (no rare, unique or special variant), official, in service by the game's date, no base /
 * OSAT / mine, nothing out of tier, no craft that needs any hangar but a fighter's, no hull whose hangars carry a minimum the
 * composer does not fill (Fighter Squadrons, Mapmaker Probes), nothing a CPU player cannot fly (AIConfig::cpuUnitBlock). Fighters in
 * full flights of six. No enhancements, refits or officers (plan 8.2: "auto-composed fleets are plain").
 *
 * HOW: capital ships first (the checker wants one per 5,000 points from 3,000) with a jump engine among them, then ships towards a
 * share of the points drawn from the seed, then fighters until every carrier's hangars are at least half full (a carrier the points
 * cannot fill goes, and the fill is tried again), then whatever still fits, the dearest first. Every pick keeps the per-hull,
 * rarity and deployment-bracket limits. Each attempt is checked (problems()); the seed varies the picks; of the clean attempts the
 * one spending most is kept, and it stops at the first within 2% of the points (plan 8.2).
 * Deterministic for a seed: a generator of its own, so mt_rand's global state is left alone.
 */
class FleetComposer
{
    const TRIES = 24;
    const FLIGHT_SIZE = 6;
    //Hangar categories the checker holds to a MINIMUM (gamelobby.js: 'Fighter Squadrons', and noHangarMaxCraftTypes) - a hull
    //declaring one would need craft the composer does not buy, so it is not bought.
    const MINIMUM_CATEGORIES = array('Fighter Squadrons', 'Mapmaker Probes');

    private static $catalogues = array(); //"faction|isd" => the catalogue as candidates (a static file is megabytes of JSON)

    /* A faction for a CPU slot nobody chose one for: one of AIConfig::READY_FACTIONS, drawn from $seed. */
    public static function factionFor($seed)
    {
        $factions = array_values(AIConfig::READY_FACTIONS);
        $rng = self::rng((int)$seed * 31 + 7);
        return $factions[$rng(count($factions))];
    }

    /* The fleet for $faction within $points, in service by $isd (a year; null, 0 or '' for no cutoff), drawn from $seed:
         'faction', 'points' (spent), 'problems' (what the checker would refuse - EMPTY when it is clean),
         'units': list of array('phpclass', 'name', 'flightSize' (null for a ship), 'pointCost').
       Throws when the faction has no catalogue. */
    public static function compose($faction, $points, $isd = null, $seed = 1)
    {
        $points = max(0, (int)$points);
        $isd = ($isd === null || $isd === '' || (int)$isd <= 0) ? null : (int)$isd;
        $catalogue = self::catalogue($faction, $isd);

        $best = null;
        for ($try = 0; $try < self::TRIES; $try++) {
            $rng = self::rng((int)$seed * 7919 + $try * 104729 + 17);
            $units = self::attempt($catalogue, $points, $rng);
            $problems = self::problems($units, $points);
            if (count($problems) == 0) $units = self::polish($catalogue, $units, $points);
            $spent = self::spent($units);
            if ($best === null || count($problems) < count($best['problems'])
                || (count($problems) == count($best['problems']) && $spent > $best['points'])) {
                $best = array('faction' => $faction, 'points' => $spent, 'units' => $units, 'problems' => $problems);
            }
            if (count($problems) == 0 && $spent >= 0.98 * $points) break;
        }

        $named = array();
        $counts = array();
        foreach ($best['units'] as $unit) {
            $counts[$unit['class']] = isset($counts[$unit['class']]) ? $counts[$unit['class']] + 1 : 1;
            $named[] = array('phpclass' => $unit['phpclass'], 'name' => $unit['class'] . ' ' . $counts[$unit['class']],
                'flightSize' => $unit['flight'] ? self::FLIGHT_SIZE : null, 'pointCost' => $unit['cost']);
        }
        $best['units'] = $named;
        return $best;
    }

    // ------------------------------------------------------------------ one attempt

    private static function attempt(array $catalogue, $points, callable $rng)
    {
        $ships = $catalogue['ships'];
        $flights = $catalogue['flights'];
        $units = array();
        if (count($ships) == 0) return $units;

        //1. the capital ships the checker wants (one per 5,000 points from 3,000), a jump engine among them; a jump engine whatever
        //   the fleet's size
        $capitals = array_values(array_filter($ships, function ($s) { return $s['size'] >= 3; }));
        $jumpers = array_values(array_filter($ships, function ($s) { return $s['jump']; }));
        for ($i = self::capitalsNeeded($points); $i > 0; $i--) {
            $pool = self::hasJump($units) ? $capitals : array_values(array_filter($capitals, function ($s) { return $s['jump']; }));
            $pick = self::pick($pool, $units, $points, $points - self::spent($units), $rng);
            if ($pick === null) break; //problems() will say so
            $units[] = $pick;
        }
        if (!self::hasJump($units)) {
            $pick = self::pick($jumpers, $units, $points, $points - self::spent($units), $rng);
            if ($pick !== null) $units[] = $pick;
        }

        //2. ships towards a share of the points - the rest is for fighters and the top-up
        $share = 0.55 + 0.25 * ($rng(1000) / 1000);
        for ($guard = 0; $guard < 80 && self::spent($units) < $share * $points; $guard++) {
            $budget = min($points - self::spent($units), $share * $points - self::spent($units) + 200);
            $pick = self::pick($ships, $units, $points, $budget, $rng);
            if ($pick === null) break;
            $units[] = $pick;
        }

        //3. fighters until every hangar is at least half full; a carrier the points cannot fill goes (the last one bought that the
        //   capital-ship and jump-engine rules can spare), with the fighters bought for it, and the fill is tried again
        for ($guard = 0; $guard < 12; $guard++) {
            if (self::fillHangars($units, $flights, $points, $rng)) break;
            $units = array_values(array_filter($units, function ($u) { return !$u['flight']; }));
            $drop = null;
            foreach ($units as $index => $unit) {
                if ($unit['hangar']['total'] == 0) continue;
                $without = $units;
                array_splice($without, $index, 1);
                if (self::hasJump($without) && self::capitals($without) >= self::capitalsNeeded($points)) $drop = $index;
            }
            if ($drop === null) break; //problems() will say so, and the next try draws other carriers
            array_splice($units, $drop, 1);
        }

        //4. whatever still fits, one of the three dearest each time: a ship (a carrier only while the fighters still fill half of
        //   the hangars it adds) or a flight the hangars have room for
        for ($guard = 0; $guard < 80; $guard++) {
            $left = $points - self::spent($units);
            $options = array();
            foreach (array_merge($ships, $flights) as $candidate) {
                if ($candidate['cost'] > $left || !self::allowed($units, $candidate, $points)) continue;
                $with = $units;
                $with[] = $candidate;
                if (count(self::hangarProblems(self::hangars($with))) > 0) continue;
                $options[] = $candidate;
            }
            if (count($options) == 0) break;
            usort($options, function ($a, $b) { return $b['cost'] <=> $a['cost']; });
            $top = array_slice($options, 0, 3);
            $units[] = $top[$rng(count($top))];
        }
        return $units;
    }

    //A clean fleet short of 98% of the points: one unit at a time traded for a dearer one of the catalogue that the points pay for
    //and that leaves the fleet clean - the trade spending most each round, a few rounds at most.
    private static function polish(array $catalogue, array $units, $points)
    {
        $all = array_merge($catalogue['ships'], $catalogue['flights']);
        for ($round = 0; $round < 6 && self::spent($units) < 0.98 * $points; $round++) {
            $left = $points - self::spent($units);
            $best = null;
            $bestGain = 0;
            foreach ($units as $index => $unit) {
                foreach ($all as $candidate) {
                    $gain = $candidate['cost'] - $unit['cost'];
                    if ($gain <= $bestGain || $gain > $left) continue;
                    $traded = $units;
                    $traded[$index] = $candidate;
                    if (count(self::problems($traded, $points)) > 0) continue;
                    $best = $traded;
                    $bestGain = $gain;
                }
            }
            if ($best === null) break;
            $units = array_values($best);
        }
        return $units;
    }

    //One candidate from $pool costing no more than $budget and breaking no limit, drawn from the seed - a common one twice as likely.
    private static function pick(array $pool, array $units, $points, $budget, callable $rng)
    {
        $fits = array();
        foreach ($pool as $candidate) {
            if ($candidate['cost'] > $budget || !self::allowed($units, $candidate, $points)) continue;
            $fits[] = $candidate;
            if ($candidate['rarity'] === 'C') $fits[] = $candidate;
        }
        return (count($fits) == 0) ? null : $fits[$rng(count($fits))];
    }

    //Flights until the hangars are half full: true once they are (or there are none), false when nothing more fits first.
    private static function fillHangars(array &$units, array $flights, $points, callable $rng)
    {
        for ($guard = 0; $guard < 40; $guard++) {
            $h = self::hangars($units);
            if ($h['present'] >= (int)ceil($h['available'] / 2)) return true;
            $left = $points - self::spent($units);
            $options = array();
            foreach ($flights as $flight) {
                if ($flight['cost'] > $left || !self::allowed($units, $flight, $points)) continue;
                $with = $units;
                $with[] = $flight;
                $after = self::hangars($with);
                if ($after['present'] > $after['available'] || !self::rowsOk($after)) continue;
                $options[] = $flight;
            }
            if (count($options) == 0) return false;
            usort($options, function ($a, $b) { return $a['cost'] <=> $b['cost']; });
            $cheapest = array_slice($options, 0, 2);
            $units[] = $cheapest[$rng(count($cheapest))];
        }
        return false;
    }

    //Does adding $candidate keep every bracket, per-hull and rarity limit?
    private static function allowed(array $units, array $candidate, $points)
    {
        $units[] = $candidate;
        return count(self::limitProblems($units, $points)) == 0;
    }

    // ------------------------------------------------------------------ the checker's rules (gamelobby.js checkChoices)

    /* What gamelobby.js checkChoices would refuse in this fleet, as short lines - EMPTY when it would pass. Only the rules a fleet of
       the composer's catalogue can break (it leaves out bases, OSATs, non-combat, custom and out-of-tier units, rare / unique /
       special variants, special hangars and craft, enhancements and officers - and buys every flight at six, so none is small).
       tests/ai/fleetCheckOracle.js holds the real checker to it. */
    public static function problems(array $units, $points)
    {
        $problems = self::limitProblems($units, $points);
        if (!self::hasJump($units)) $problems[] = 'no jump engine (at least one is required)';
        $needed = self::capitalsNeeded($points);
        if (self::capitals($units) < $needed) $problems[] = 'capital ships ' . self::capitals($units) . " (min. $needed)";
        foreach (self::hangarProblems(self::hangars($units)) as $problem) $problems[] = $problem;
        if (self::spent($units) > $points) $problems[] = 'over the points (' . self::spent($units) . " of $points)";
        if (count($units) == 0) $problems[] = 'no units';
        return $problems;
    }

    //The deployment brackets (10 / 33 / 50% of the points on base cost - one unit alone in a bracket may break it), the escort a
    //restricted unit needs, the per-hull limit and the uncommon limits per hull and per fleet.
    private static function limitProblems(array $units, $points)
    {
        $problems = array();
        foreach (array(10 => 0.1, 33 => 0.33, 50 => 0.5) as $bracket => $share) {
            $sum = 0;
            $count = 0;
            foreach ($units as $unit) {
                if ($unit['limited'] != $bracket) continue;
                $sum += $unit['cost'];
                $count++;
            }
            if ($count > 1 && $sum > floor($points * $share)) $problems[] = "$bracket% units $sum points (allowed " . floor($points * $share) . ')';
        }
        foreach ($units as $unit) {
            if ($unit['limited'] == 10 && count($units) < 2) $problems[] = 'a restricted unit needs another unit present';
        }

        $perHull = max(2, (int)floor($points / 1100));
        $hulls = array();
        $totalU = 0;
        foreach ($units as $unit) {
            $hull = $unit['hull'];
            if (!isset($hulls[$hull])) $hulls[$hull] = array('total' => 0, 'U' => 0, 'flight' => false);
            $hulls[$hull]['total']++;
            if ($unit['flight']) $hulls[$hull]['flight'] = true; //hangarRequired: no per-hull limit
            if ($unit['rarity'] === 'U') { $hulls[$hull]['U']++; $totalU++; }
        }
        foreach ($hulls as $name => $row) {
            if (!$row['flight'] && $row['total'] > $perHull) $problems[] = "$name: $row[total] of the hull (allowed $perHull)";
            if ($row['U'] > (int)ceil($row['total'] / 3)) $problems[] = "$name: $row[U] uncommon of $row[total]";
        }
        $limitU = max(2, (int)floor(($points - 1500) / 1000));
        if ($totalU > $limitU) $problems[] = "uncommon units $totalU (allowed $limitU)";
        return $problems;
    }

    //The fleet's hangar boxes and craft by the checker's sizes ('normal' and 'heavy' hangars are heavy; an ultralight counts
    //one per craft, a half box each).
    private static function hangars(array $units)
    {
        $capacity = array('heavy' => 0, 'medium' => 0, 'light' => 0, 'ultralight' => 0);
        $craft = array('heavy' => 0, 'medium' => 0, 'light' => 0, 'ultralight' => 0);
        foreach ($units as $unit) {
            if ($unit['flight']) {
                $craft[$unit['craftSize']] += ($unit['craftSize'] === 'ultralight') ? self::FLIGHT_SIZE : self::FLIGHT_SIZE / $unit['unitSize'];
                continue;
            }
            foreach ($capacity as $size => $boxes) $capacity[$size] += $unit['hangar'][$size];
        }
        return array('capacity' => $capacity, 'craft' => $craft,
            'available' => $capacity['heavy'] + $capacity['medium'] + $capacity['light'] + $capacity['ultralight'] / 2,
            'present' => $craft['heavy'] + $craft['medium'] + $craft['light'] + $craft['ultralight'] / 2);
    }

    private static function hangarProblems(array $h)
    {
        $problems = array();
        $min = (int)ceil($h['available'] / 2);
        if ($h['present'] > $h['available'] || $h['present'] < $min) $problems[] = "fighters $h[present] (between $min and $h[available])";
        if (!self::rowsOk($h)) $problems[] = 'fighters of a size beyond the hangars for it';
        return $problems;
    }

    //Each size of fighter within the hangars of its size and larger (the checker's four rows).
    private static function rowsOk(array $h)
    {
        $c = $h['capacity'];
        $f = $h['craft'];
        return $f['heavy'] <= $c['heavy']
            && $f['medium'] <= $c['heavy'] + $c['medium']
            && $f['light'] <= $c['heavy'] + $c['medium'] + $c['light']
            && $f['ultralight'] <= ($c['heavy'] + $c['medium'] + $c['light']) * 2 + $c['ultralight'];
    }

    private static function capitalsNeeded($points)
    {
        return ($points >= 3000) ? (int)ceil($points / 5000) : 0;
    }

    private static function capitals(array $units)
    {
        $count = 0;
        foreach ($units as $unit) if (!$unit['flight'] && $unit['size'] >= 3) $count++;
        return $count;
    }

    private static function hasJump(array $units)
    {
        foreach ($units as $unit) if ($unit['jump']) return true;
        return false;
    }

    private static function spent(array $units)
    {
        $sum = 0;
        foreach ($units as $unit) $sum += $unit['cost'];
        return $sum;
    }

    // ------------------------------------------------------------------ the catalogue

    /* The faction's catalogue as candidates: 'ships' and 'flights', each a list of array('phpclass', 'class', 'hull', 'cost' (a
       flight's for six craft, as the catalogue prices it), 'limited', 'rarity' ('C' / 'U'), 'size', 'flight', 'jump', 'hangar'
       (heavy / medium / light / ultralight boxes and their 'total'), 'craftSize', 'unitSize'). */
    private static function catalogue($faction, $isd)
    {
        $key = $faction . '|' . (int)$isd;
        if (isset(self::$catalogues[$key])) return self::$catalogues[$key];

        $file = __DIR__ . '/../../public/static/json/' . $faction . '.json';
        $json = is_file($file) ? json_decode((string)file_get_contents($file), true) : null;
        if (!is_array($json) || !isset($json[$faction]) || !is_array($json[$faction])) throw new Exception("No ship catalogue for $faction.");

        $out = array('ships' => array(), 'flights' => array());
        foreach ($json[$faction] as $s) {
            if (!is_array($s) || empty($s['phpclass']) || !class_exists($s['phpclass'])) continue;
            if (empty($s['isCombatUnit']) || !empty($s['base']) || !empty($s['smallBase']) || !empty($s['osat']) || !empty($s['mine'])) continue;
            //Custom units only: the checker's `lship.unofficial == true` - a SEMI-official unit ('S': 102 of them, every jump-engine
            //hull of Earth Alliance (Early) among them) is official to it (stage T9, 2026-11-23)
            if (($s['unofficial'] ?? false) === true || (int)($s['factionAge'] ?? 1) > 2) continue;
            $occurence = (string)($s['occurence'] ?? '');
            if ($occurence !== 'common' && $occurence !== 'uncommon') continue;
            if ($isd !== null && (int)($s['isd'] ?? 0) > $isd) continue; //an ISD of 0 or text always passes, as in the lobby
            if (array_sum(array_map('intval', (array)($s['outOfTier'] ?? array()))) > 0) continue;
            if (!empty($s['customFtrName']) || !empty($s['customFighter']) || !empty($s['noHangarRequired'])) continue;
            $flight = !empty($s['flight']);
            $hangarRequired = (string)($s['hangarRequired'] ?? '');
            if ($flight ? ($hangarRequired !== 'fighters') : ($hangarRequired !== '')) continue;
            $maxFlightSize = (int)($s['maxFlightSize'] ?? 0);
            if ($flight && $maxFlightSize > 0 && $maxFlightSize < self::FLIGHT_SIZE) continue;
            $fighters = is_array($s['fighters'] ?? null) ? $s['fighters'] : array();
            if (count(array_intersect(array_keys($fighters), self::MINIMUM_CATEGORIES)) > 0) continue;

            $hangar = array('heavy' => 0, 'medium' => 0, 'light' => 0, 'ultralight' => 0, 'total' => 0);
            foreach ($fighters as $size => $boxes) {
                $slot = ($size === 'normal' || $size === 'heavy') ? 'heavy' : $size;
                if (!in_array($slot, array('heavy', 'medium', 'light', 'ultralight'), true)) continue; //shuttles, assault shuttles, ...
                $hangar[$slot] += (int)$boxes;
                $hangar['total'] += (int)$boxes;
            }
            $jinking = (int)($s['jinkinglimit'] ?? 0);
            $craftSize = ($jinking >= 99) ? 'ultralight' : (($jinking >= 10) ? 'light' : (($jinking >= 8) ? 'medium' : (($jinking >= 6) ? 'heavy' : null)));
            $unitSize = (float)($s['unitSize'] ?? 1);
            if ($flight && ($craftSize === null || $unitSize <= 0)) continue;

            $jump = false;
            foreach ((array)($s['systems'] ?? array()) as $system) {
                if (is_array($system) && ($system['name'] ?? '') === 'jumpEngine') $jump = true;
            }
            if (self::cpuBlocked($s['phpclass'])) continue;

            $class = (string)($s['shipClass'] ?? $s['phpclass']);
            $out[$flight ? 'flights' : 'ships'][] = array(
                'phpclass' => $s['phpclass'], 'class' => $class,
                'hull' => ((string)($s['variantOf'] ?? '') !== '') ? (string)$s['variantOf'] : $class,
                'cost' => (int)$s['pointCost'], 'limited' => (int)($s['limited'] ?? 0), 'rarity' => ($occurence === 'common') ? 'C' : 'U',
                'size' => (int)($s['shipSizeClass'] ?? 0), 'flight' => $flight, 'jump' => $jump, 'hangar' => $hangar,
                'craftSize' => $craftSize, 'unitSize' => $unitSize);
        }
        return self::$catalogues[$key] = $out;
    }

    /* Is this class left out? A unit a CPU player cannot field (AIConfig::cpuUnitBlock: the readiness list, the hulls ShipMover never
       plots, the missing systems), or a CUSTOM design - `unofficial` true as the CLASS says, whatever the static file says (the user,
       2026-11-23: "Prevent the AI FleetComposer from auto-picking any ships marked as custom"; a SEMI-custom 'S' design stays allowed,
       his ruling the same day - the Fleet Checker counts it as official, and every EA (Early) jump-engine hull is one). */
    private static function cpuBlocked($phpclass)
    {
        try {
            $unit = new $phpclass(-1, -1, 'composer', 0);
            return ($unit->unofficial ?? false) === true || AIConfig::cpuUnitBlock($unit) !== null;
        } catch (Throwable $e) {
            return true;
        }
    }

    //A generator of its own: $rng($n) is 0..$n-1, the same sequence for the same seed.
    private static function rng($seed)
    {
        $state = ((int)$seed & 0x7fffffff) ?: 1;
        return function ($n) use (&$state) {
            $state = ($state * 1103515245 + 12345) & 0x7fffffff;
            return ($n <= 1) ? 0 : (int)(($state >> 8) % $n);
        };
    }
}
