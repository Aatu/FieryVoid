<?php
require_once("FighterFlight.php");

/* Generic single-craft shuttle. Used to auto-populate empty hangar slots
 * (see HangarOps::populateInitialHangarUsage) and as the base class for
 * specialised shuttles (e.g. MinesweepingShuttle).
 *
 * Per B5W §10.1: "shuttles may be operated from any fighter box, but under
 * no circumstances may any fighter replace a shuttle." Shuttles are not
 * combat units and don't count toward fleet point totals — $isCombatUnit
 * is set false (existing flag, see civilians/skylark.php for prior art).
 */
class Shuttle extends FighterFlight
{
    public $shuttle = true;       //distinguishes shuttles from non-combat freighters
    public $maxFlightSize = 6;    //shuttles can launch in flights of 1-6 (stored 1-per-record in $hangarUsage)

    function __construct($id, $userid, $name, $slot)
    {
        parent::__construct($id, $userid, $name, $slot);
        $this->setShuttleDefaults();   //subclass can override to change phpclass etc.
        $this->populate();             //populate() uses $this->phpclass set above
    }

    /* Override in subclasses to change phpclass / image / minesweeperbonus etc.
     * Called before populate(), so any field consulted by populate() must be
     * set here.
     */
    protected function setShuttleDefaults()
    {
        $this->pointCost = 0;                     //auto-populated, never bought
        $this->isCombatUnit = false;              //skipped by fleet builder / battlegroup checks
        $this->faction = "Generic";
        $this->phpclass = "Shuttle";
        $this->shipClass = "Shuttle";
        $this->imagePath = "img/ships/shuttle.png";  //placeholder; faction subclasses can override

        $this->forwardDefense = 8;
        $this->sideDefense = 10;
        $this->freethrust = 3;
        $this->offensivebonus = 0;
        $this->jinkinglimit = 0;
        $this->pivotcost = 2;                     //shuttles pivot slowly
        $this->turncost = 0.33;

        $this->hangarRequired = 'shuttles';       //matches "shuttles" key in carrier $fighters
        $this->iniativebonus = 9 * 5;             //slow
    }

    public function populate()
    {
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++) {
            $armour = array(1, 1, 1, 1);
            $fighter = new Fighter($this->phpclass, $armour, 10, $this->id);
            $fighter->displayName = $this->shipClass;
			$fighter->imagePath = "img/ships/shuttle.png";
			$fighter->iconPath = "img/ships/shuttle_large.png";

            $fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0));

            $this->addSystem($fighter);
        }
    }
}


/* Minesweeping shuttle variant. Auto-populated when a carrier declares
 * 'minesweeping shuttles' in $fighters, or when leftover hangar capacity
 * exists on a ship with $minesweeperbonus > 0.
 */
class MinesweepingShuttle extends Shuttle
{
    protected function setShuttleDefaults()
    {
        parent::setShuttleDefaults();
        $this->phpclass = "MinesweepingShuttle";
        $this->shipClass = "Minesweeping Shuttle";
        $this->hangarRequired = 'minesweeping shuttles';
        $this->offensivebonus = 4;
    }
}
