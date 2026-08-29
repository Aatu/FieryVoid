<?php
/* Stage 6 scratch harness - JUMP_POINTS_PLAN.md.
 * Runs INSIDE the php container against the REAL classes:
 *   docker exec -w /usr/src/current fieryvoid-php-1 php tests/replay/stage6harness.php
 * Never opens a database connection - everything here is built in memory.
 */
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_WARNING);
ini_set('display_errors', '1');

$FV_ROOT = dirname(__DIR__, 2);
require_once $FV_ROOT . '/source/autoload.php';

$pass = 0; $fail = 0;
function ok($label, $got, $want) {
    global $pass, $fail;
    $g = var_export($got, true); $w = var_export($want, true);
    if ($g === $w) { $pass++; echo "  ok   $label\n"; }
    else { $fail++; echo "  FAIL $label -- got $g, want $w\n"; }
}
function section($t){ echo "\n== $t ==\n"; }

/* ---------------------------------------------------------------- helpers */
function makeEngine($delay, $openTurn = null, $closeTurn = -1, $vortexId = null) {
    $e = new JumpEngine(6, 25, 3, $delay);
    $e->activeVortexId  = $vortexId;
    $e->vortexOpenTurn  = $openTurn;
    $e->vortexCloseTurn = $closeTurn;
    return $e;
}

/* A real hull, so stripForJson has a real $unit to reach through (enhancements, Hyach
   specialists). Primus carries JumpEngine(6, 25, 3, 16) - the user's own worked example. */
function makePrimus() {
    $ship = new Primus(1, 1, "Testship", 1);
    $ship->id = 1;
    return $ship;
}

/* ================================================================ ITEM 8 */
section('Item 8 - the Jump Engine starts loaded, spends its charge, recharges after closure');

$e = makeEngine(16);
ok('Primus delay',            $e->delay, 16);
ok('loadingtime == delay',    $e->loadingtime, 16);
ok('starts fully loaded',     $e->turnsloaded, 16);
ok('T1 charge, no vortex',    $e->getVortexRechargeLoad(1), 16);
ok('T1 age, no vortex',       $e->getVortexAge(1), null);

// opens on T1, maintained T2 and T3, closes at the end of T3
$e = makeEngine(16, 1, 3, 777);
ok('T1 charge (declared)',    $e->getVortexRechargeLoad(1), 0);
ok('T1 age  (declared)',      $e->getVortexAge(1), 0);
ok('T2 charge (open)',        $e->getVortexRechargeLoad(2), 0);
ok('T2 age  (open)',          $e->getVortexAge(2), 1);
ok('T3 charge (closing)',     $e->getVortexRechargeLoad(3), 0);
ok('T3 age  (closing)',       $e->getVortexAge(3), 2);
ok('T4 charge (recharge 1)',  $e->getVortexRechargeLoad(4), 1);
ok('T4 age  (gone)',          $e->getVortexAge(4), null);
ok('T5 charge',               $e->getVortexRechargeLoad(5), 2);
ok('T19 charge (full)',       $e->getVortexRechargeLoad(19), 16);
ok('T30 charge (capped)',     $e->getVortexRechargeLoad(30), 16);
// a STRING turn, as mysqli hands it over (plan section 5 trap 10)
ok('string turn "4"',         $e->getVortexRechargeLoad("4"), 1);

// still open (closeTurn -1)
$e = makeEngine(16, 1, -1, 777);
ok('open, T4 charge',         $e->getVortexRechargeLoad(4), 0);
ok('open, T4 age',            $e->getVortexAge(4), 3);
ok('open, T5 age at cap',     $e->getVortexAge(5), 4);
// a vortex from a LATER turn, seen from an earlier replay turn
$e = makeEngine(16, 10, -1, 777);
ok('replay T3, later vortex charge', $e->getVortexRechargeLoad(3), 16);
ok('replay T3, later vortex age',    $e->getVortexAge(3), null);
// the one delay-0 hull in the fleet (Drazi Jumphawk)
$e = makeEngine(0);
ok('delay 0 -> loadingtime 1', $e->loadingtime, 1);
ok('delay 0 -> starts loaded', $e->getVortexRechargeLoad(1), 1);
// PhasingDrive inherits all of it
$p = new PhasingDrive(4, 20, 4, 24);
ok('PhasingDrive loadingtime', $p->loadingtime, 24);
ok('PhasingDrive starts full', $p->turnsloaded, 24);

/* --------- the payload the client actually receives --------- */
section('Item 8 - stripForJson sends loading and the vortex counter separately');
$ship = makePrimus();
$engine = $ship->getSystemByName('JumpEngine');
ok('Primus mounts one',       ($engine instanceof JumpEngine), true);
ok('blueprint loadingtime',   $engine->loadingtime, 16);

TacGamedata::$currentTurn = 2;
$engine->activeVortexId = 777; $engine->vortexOpenTurn = 1; $engine->vortexCloseTurn = -1;
$json = $engine->stripForJson();
ok('payload turnsloaded (open)',  $json->turnsloaded, 0);
ok('payload loadingtime',         $json->loadingtime, 16);
ok('payload vortexTurnsOpen',     $json->vortexTurnsOpen, 1);
ok('payload vortexMaxTurns',      $json->vortexMaxTurns, 4);

TacGamedata::$currentTurn = 6;
$engine->vortexCloseTurn = 3;
$json2 = $engine->stripForJson();
ok('payload turnsloaded (recharged 3)', $json2->turnsloaded, 3);
ok('no vortex counter when closed',     isset($json2->vortexTurnsOpen), false);

TacGamedata::$currentTurn = 1;
$fresh = makePrimus()->getSystemByName('JumpEngine')->stripForJson();
ok('turn 1, never used - 16/16',  $fresh->turnsloaded, 16);
ok('turn 1 loadingtime',          $fresh->loadingtime, 16);

/* ================================================================ ITEM 2 */
section('Item 2 - a HyperspaceJump damage entry survives the Historical aggregation');
TacGamedata::$currentTurn = 8;
$ship = makePrimus();
$struct = $ship->getStructureSystem(0);
// ordinary damage on turn 2 plus the jump entry on turn 4; the aggregation threshold is turn 7.
$struct->damage[] = new DamageEntry(-1, 1, -1, 2, $struct->id, 6, 0, 0, -1, false, false, "", "Standard");
$struct->damage[] = new DamageEntry(-1, 1, -1, 4, $struct->id, $struct->maxhealth, 0, 0, -1, true, false, "", "HyperspaceJump");
$s = $struct->stripForJson();
$classes = array();
foreach ($s->damage as $d) $classes[] = $d->damageclass;
ok('jump entry kept whole',     in_array('HyperspaceJump', $classes, true), true);
ok('history still aggregated',  in_array('Historical', $classes, true), true);

// and the verdict the client reaches off that payload
$nonJump = 0; $jumpSeen = false;
foreach ($s->damage as $d) {
    if ($d->damageclass === 'HyperspaceJump') { $jumpSeen = true; continue; }
    $nonJump += max(0, $d->damage - $d->armour);
}
ok('reads as JUMPED, not destroyed', ($jumpSeen && $nonJump < $struct->maxhealth), true);

// the server's own answer has to agree
ok('hasJumpedToHyperspace', $ship->hasJumpedToHyperspace(), true);

/* ================================================================ ITEM 6 */
section('Item 6 - closure reasons, including the holder leaving through a jump point');

/* getVortexClosureReason is protected; drive it through reflection with a stub holder, so the
   RULE is what is under test rather than a live game load. */
class FakeShipForVortex {
    public $id = 1;
    public $systems = array();
    public $destroyed = false;
    public $jumped = false;
    private $pos;
    public function __construct($pos){ $this->pos = $pos; }
    public function isDestroyed($turn = false){ return $this->destroyed; }
    public function hasJumpedToHyperspace(){ return $this->jumped; }
    public function getHexPos(){ return $this->pos; }
    public function isTerrain(){ return false; }
}

function closureReason($engine, $ship, $vortex, $turn) {
    $gd = new stdClass();
    $gd->turn = $turn;
    $m = new ReflectionMethod('JumpEngine', 'getVortexClosureReason');
    $m->setAccessible(true);
    return $m->invoke($engine, $ship, $vortex, $gd);
}

$vortex = new SpawnJumpPoint(1, 1, 'Jump Point', 1);
$vortex->id = 777;
$vortex->movement[] = new MovementOrder(null, 'deploy', new OffsetCoordinate(0, 0), 0, 0, 0, 0, 0, false, 1, 0, 0);

$holder = new FakeShipForVortex(new OffsetCoordinate(1, 0));
$engine = makeEngine(16, 1, -1, 777);

ok('declaring turn - survives',        closureReason($engine, $holder, $vortex, 1), null);
ok('T2, not maintained - closes',      closureReason($engine, $holder, $vortex, 2), 'not maintained');

$holder->destroyed = true;
ok('holder destroyed',                 closureReason($engine, $holder, $vortex, 2), 'holder destroyed');
$holder->jumped = true;
ok('holder left through a jump point', closureReason($engine, $holder, $vortex, 2), 'holder left through a vortex');
$holder->destroyed = false; $holder->jumped = false;

$far = new FakeShipForVortex(new OffsetCoordinate(9, 0));
ok('holder out of range',              closureReason($engine, $far, $vortex, 2), 'holder is 9 hexes away');
ok('four-turn cap (openTurn+4)',       closureReason($engine, $holder, $vortex, 5), 'four-turn limit reached');
ok('one turn short of the cap',        closureReason($engine, $holder, $vortex, 4), 'not maintained');
ok('vortex unit gone',                 closureReason($engine, $holder, null, 2),   'vortex unit is gone');

/* ================================================================ ITEM 10 */
section('Item 10 - a fighter flight can leave through a jump point');

$flight = new Kotha(1, 1, "Testflight", 1);
$flight->id = 55;
$sysId = 1;
foreach ($flight->systems as $craft){
    $craft->id = $sysId++;
    foreach ($craft->systems as $sub) $sub->id = $sysId++;
}
ok('flight has no primary structure', $flight->getStructureSystem(0), null);
ok('flight has a RammingAttack',      ($flight->getSystemByName('RammingAttack') instanceof RammingAttack), true);
ok('flight has no jump engine',       $flight->getSystemByName('JumpEngine'), null);

$cvBefore = $flight->calculateCombatValue();
ok('undamaged flight CV', (int)$cvBefore, 100);

$gdJump = new stdClass(); $gdJump->turn = 4; $gdJump->phase = 2; $gdJump->id = 1;
$m = new ReflectionMethod('Movement', 'applyJumpOut');
$m->setAccessible(true);
$m->invoke(null, $flight, $gdJump, ' jumps out of the battle through a jump vortex.');

ok('every craft carries a jump entry', count($flight->systems), count(array_filter($flight->systems, function($c){
    foreach ($c->damage as $d) if ($d->damageclass === 'HyperspaceJump') return true;
    return false;
})));
ok('flight now reads destroyed',   $flight->isDestroyed(4), true);
ok('flight reads as JUMPED',       $flight->hasJumpedToHyperspace(), true);

/* the CV note is written on the sample fighter, BEFORE the damage, so it snapshots the live value */
$noteRef = new ReflectionProperty('ShipSystem', 'individualNotes');
$noteRef->setAccessible(true);
$notes = $noteRef->getValue($flight->getSampleFighter());
$jumpNote = null;
foreach ($notes as $n) if ($n->notekey === 'jumped') $jumpNote = $n;
ok('CV note on the sample fighter', ($jumpNote !== null), true);
ok('CV snapshot is pre-jump',       $jumpNote ? (int)$jumpNote->notevalue : -1, (int)$cvBefore);

/* and the read-back path the next load takes */
$flight->getSampleFighter()->onIndividualNotesLoaded($gdJump);
ok('getCVBeforeJump reads it back', (int)$flight->getCVBeforeJump(), (int)$cvBefore);
ok('CV preserved, not zeroed',      (int)$flight->calculateCombatValue(), (int)$cvBefore);

/* a flight genuinely SHOT to pieces must not read as jumped */
$dead = new Kotha(1, 1, "Deadflight", 1);
$dead->id = 56;
$sysId = 1;
foreach ($dead->systems as $craft){ $craft->id = $sysId++; foreach ($craft->systems as $sub) $sub->id = $sysId++; }
foreach ($dead->systems as $craft){
    $craft->damage[] = new DamageEntry(-1, 56, -1, 4, $craft->id, $craft->maxhealth + 5, 0, 0, -1, true, false, "", "Standard");
}
ok('shot-down flight is destroyed', $dead->isDestroyed(4), true);
ok('shot-down flight has NOT jumped', $dead->hasJumpedToHyperspace(), false);
ok('shot-down flight CV is 0',       (int)$dead->calculateCombatValue(), 0);

/* ================================================================ Log orders */
section('Stage 6 - the combat-log orders');
$logShip = makePrimus();
$gd = new stdClass(); $gd->turn = 3; $gd->id = 1;
$m = new ReflectionMethod('JumpEngine', 'writeVortexLogOrder');
$m->setAccessible(true);
$m->invoke(null, $logShip, $gd, ' opens a jump point 3 hexes away.');

$ram = $logShip->getSystemByName('RammingAttack');
ok('one log order written',       count($ram->fireOrders), 1);
ok('damageclass JumpVortex',      $ram->fireOrders[0]->damageclass, 'JumpVortex');
ok('addToDB',                     $ram->fireOrders[0]->addToDB, true);
ok('skipped by the fire gathers', Firing::isHyperspaceLogOrder($ram->fireOrders[0]), true);
ok('NOT on the jump engine',      count($logShip->getSystemByName('JumpEngine')->fireOrders), 0);
ok('so it is not read as a declaration',
   $logShip->getSystemByName('JumpEngine')->getVortexDeclaration(3), null);

echo "\n$pass passed, $fail failed\n";
exit($fail > 0 ? 1 : 0);
