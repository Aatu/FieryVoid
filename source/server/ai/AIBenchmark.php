<?php
/**
 * CPU opponent - the DECISION BENCHMARK for a live host (TACTICAL_AI_PLAN.md section 12, the holistic review's item 1: decision
 * times "on the Docker stack AND the live host (lsphp; its CPU and its 503 memory limit)"). Live has no shell, so this runs one bot
 * decision on a board built in memory - no database, no game, nothing written - inside an ordinary request, and reports what it
 * cost: CPU and wall seconds, the slowest ship decision, how many ran out of time, and the process's memory (PHP's peak and the
 * resident set the host's lsphp limit is measured against). The page is public/aiBenchmark.php, behind MaintenanceGate.
 *
 * The boards are tests/ai/decisionBench.php's (kept apart: that one also runs against old code): both sides fly the same fleet, the
 * bot's west heading east, the enemy's east heading west, the fronts 28 / 16 / 9 hexes apart; the enemy has yet to move in Movement
 * (every enemy ship is forecast - the worst case) and everybody has moved in Firing; units carry turn 1 and last turn's movement
 * rows as the loader brings them. Movement puts every unit of the bot in ONE activation (simultaneous movement: what one submission
 * carries at worst).
 */
class AIBenchmark
{
    const TURN = 3;
    const BOT = 1;
    const ENEMY = 2;
    const DISTANCES = array(28, 16, 9);

    //fleet => size => classes (sizes: the median side of the live games is 10 units, the 75th percentile 16 - 2026-11 export)
    const FLEETS = array(
        'ea' => array(
            'small' => array('Omega', 'Hyperion', 'Tethys', 'AuroraStarfury'),
            'live' => array('Omega', 'Hyperion', 'Hyperion', 'OlympusBeta', 'OlympusBeta', 'Tethys', 'Tethys', 'AuroraStarfury', 'AuroraStarfury', 'ThunderboltStarfuryAM'),
            'large' => array('Omega', 'Omega', 'Hyperion', 'Hyperion', 'Hyperion', 'OlympusBeta', 'OlympusBeta', 'OlympusBeta', 'Tethys', 'Tethys', 'Tethys', 'Tethys',
                'AuroraStarfury', 'AuroraStarfury', 'AuroraStarfury', 'ThunderboltStarfuryAM')),
        'raiders' => array(
            'small' => array('Battlewagon', 'Galleon', 'Corsair', 'DeltaV'),
            'live' => array('Battlewagon', 'Battlewagon', 'Galleon', 'Galleon', 'Brigand', 'Brigand', 'Corsair', 'Corsair', 'DeltaV', 'DeltaV'),
            'large' => array('Battlewagon', 'Battlewagon', 'Battlewagon', 'Galleon', 'Galleon', 'Galleon', 'Brigand', 'Brigand', 'Brigand', 'Corsair', 'Corsair', 'Corsair',
                'DeltaV', 'DeltaV', 'DeltaV', 'DeltaV'))
    );

    /* One decision timed: array('cpu', 'wall', 'ships' (ship decisions), 'slowest', 'cut', 'units', 'error' or null). $phase 1 =
       Initial Orders, 2 = Movement, 3 = Firing; $board 0-2 (the distance); $budget null = the game's budgets, 0 = none. */
    public static function run($fleet, $size, $level, $phase, $board, $budget = null)
    {
        if (!isset(self::FLEETS[$fleet][$size])) throw new Exception("Unknown fleet or size: $fleet / $size");
        if (!isset(AIConfig::SKILLS[$level])) throw new Exception("Unknown level: $level");
        $classes = self::FLEETS[$fleet][$size];

        $kept = array(ShipDecider::$budget, EnemyForecast::$budget, ShipDecider::$timings);
        if ($budget !== null) {
            ShipDecider::$budget = (float)$budget;
            EnemyForecast::$budget = (float)$budget;
        }
        ShipDecider::$timings = array();
        try {
            $gamedata = self::board($classes, self::DISTANCES[(int)$board % count(self::DISTANCES)], 1000 + (int)$board, (int)$phase);
            $seat = array('playerid' => self::BOT, 'cpu' => array('level' => $level, 'seed' => 1));
            $cpu = AIConfig::cpuSeconds();
            $wall = microtime(true);
            if ($phase == 1) TacticalAI::initialOrders($gamedata, self::BOT, $seat);
            else if ($phase == 2) MoveDecider::orders($gamedata, self::BOT, $seat);
            else FireDecider::orders($gamedata, self::BOT, $seat);
            $result = array('cpu' => AIConfig::cpuSeconds() - $cpu, 'wall' => microtime(true) - $wall, 'error' => null);
        } catch (Throwable $e) {
            $result = array('cpu' => 0, 'wall' => 0, 'error' => get_class($e) . ': ' . $e->getMessage() . ' (' . basename($e->getFile()) . ':' . $e->getLine() . ')');
        }
        $ships = isset(ShipDecider::$timings[$level]) ? ShipDecider::$timings[$level] : array();
        $result['ships'] = count($ships);
        $result['slowest'] = count($ships) ? max(array_column($ships, 'seconds')) : 0;
        $result['cut'] = count(array_filter($ships, function ($t) { return $t['cut']; }));
        $result['units'] = count($classes);
        list(ShipDecider::$budget, EnemyForecast::$budget, ShipDecider::$timings) = $kept;
        return $result;
    }

    //What the host is: PHP and its SAPI, the limits a request runs under, and the memory this process holds (PHP's peak, and VmRSS).
    public static function host()
    {
        $rss = null;
        $status = @file_get_contents('/proc/self/status');
        if (is_string($status) && preg_match('/^VmRSS:\s+(\d+)\s+kB/m', $status, $m)) $rss = round((int)$m[1] / 1024, 1);
        return array('php' => PHP_VERSION, 'sapi' => PHP_SAPI, 'memoryLimit' => ini_get('memory_limit'), 'maxExecution' => ini_get('max_execution_time'),
            'peakMB' => round(memory_get_peak_usage(true) / 1048576, 1), 'rssMB' => $rss, 'opcache' => function_exists('opcache_get_status') ? 'yes' : 'no');
    }

    private static function row($type, OffsetCoordinate $pos, $speed, $heading, $preturn, $turn)
    {
        $move = new MovementOrder(-1, $type, new OffsetCoordinate($pos->q, $pos->r), 0, 0, $speed, $heading, $heading, $preturn, $turn, 0, 0);
        $move->requiredThrust = array(null, null, null, null, null);
        $move->assignedThrust = array();
        return $move;
    }

    private static function unit($class, $id, $player, $team, OffsetCoordinate $pos, $heading, $speed, $moved)
    {
        $unit = new $class($id, $player, $class . ' ' . $id, $player);
        $unit->team = $team;
        $unit->iniative = 100 + $id;
        if ($unit instanceof FighterFlight) {
            $unit->flightSize = 6;
            $unit->populate();
        }
        $back = Mathlib::addToHexFacing($heading, 3);
        $turns = $moved ? 3 : 2;
        $hexes = array($pos);
        for ($i = 0; $i < $turns * $speed; $i++) $hexes[] = $hexes[count($hexes) - 1]->moveToDirection($back);
        $hexes = array_reverse($hexes);
        $unit->movement = array(self::row('start', $hexes[0], $speed, $heading, true, 1));
        for ($i = 1; $i <= $speed; $i++) $unit->movement[] = self::row('move', $hexes[$i], $speed, $heading, false, 1);
        for ($i = $speed + 1; $i <= 2 * $speed; $i++) $unit->movement[] = self::row('move', $hexes[$i], $speed, $heading, false, self::TURN - 1);
        $unit->movement[] = self::row('end', $hexes[2 * $speed], $speed, $heading, false, self::TURN - 1);
        if ($moved) {
            for ($i = 2 * $speed + 1; $i <= 3 * $speed; $i++) $unit->movement[] = self::row('move', $hexes[$i], $speed, $heading, false, self::TURN);
        }
        foreach ($unit->systems as $system) {
            if ($system instanceof Weapon) $system->setLoading($system->getStartLoading());
            if ($system instanceof Fighter) foreach ($system->systems as $sub) if ($sub instanceof Weapon) $sub->setLoading($sub->getStartLoading());
        }
        return $unit;
    }

    private static function board(array $classes, $distance, $seed, $phase)
    {
        mt_srand($seed);
        $units = array();
        $id = 1;
        foreach (array(self::BOT => 1, self::ENEMY => 2) as $player => $team) {
            $west = ($player === self::BOT);
            $shift = $west ? 0 : mt_rand(-3, 3);
            $row = -(int)floor(count($classes) / 2) * 2;
            foreach ($classes as $i => $class) {
                $flight = is_subclass_of($class, 'FighterFlight');
                $q = ($west ? -1 : 1) * (int)ceil($distance / 2) + ($flight ? ($west ? 1 : -1) : 0) + ($i % 2) * ($west ? -1 : 1);
                $heading = $west ? 0 : 3;
                if (mt_rand(0, 3) == 0) $heading = Mathlib::addToHexFacing($heading, mt_rand(0, 1) ? 1 : -1);
                $units[] = self::unit($class, $id++, $player, $team, new OffsetCoordinate($q, $row + $i * 2 + $shift), $heading, $flight ? 8 : 5, $phase == 3);
            }
        }
        if ($phase != 1) {
            foreach ($units as $unit) {
                if ($unit instanceof FighterFlight) continue;
                $points = EW::getScannerOutput($unit, self::TURN);
                $nearest = null;
                foreach ($units as $other) {
                    if ($other->team == $unit->team || $other instanceof FighterFlight) continue;
                    $d = $unit->getHexPos()->distanceTo($other->getHexPos());
                    if ($nearest === null || $d < $nearest[0]) $nearest = array($d, $other);
                }
                $oew = ($nearest !== null) ? min(2, $points) : 0;
                if ($oew > 0) $unit->EW[] = new EWentry(-1, $unit->id, self::TURN, 'OEW', $oew, $nearest[1]->id);
                if ($points - $oew > 0) $unit->EW[] = new EWentry(-1, $unit->id, self::TURN, 'DEW', $points - $oew, -1);
            }
        }
        $activeship = ($phase == 2) ? range(1, count($classes)) : -1;
        $gamedata = new TacGamedata(1, self::TURN, $phase, $activeship, self::BOT, 'AI benchmark', 'ACTIVE', 1000, '', self::BOT, '', '42x30', array());
        $gamedata->slots = array(
            new PlayerSlot(self::BOT, self::BOT, 1, self::TURN, 2, 'Team 1', 10000, 0, 0, 'box', 10, 10, 1, 'Bot', false, null),
            new PlayerSlot(self::ENEMY, self::ENEMY, 2, self::TURN, 2, 'Team 2', 10000, 0, 0, 'box', 10, 10, 1, 'Enemy', false, null));
        $gamedata->setShips($units);
        $gamedata->onConstructed();
        return $gamedata;
    }
}
