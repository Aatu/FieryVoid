<?php
require_once("FighterFlight.php");

/* Generic single-craft shuttle. Used to auto-populate empty hangar slots
 * (see HangarOps::populateInitialHangarUsage) and as the base class for
 * specialised shuttles (e.g. MinesweepingShuttle, Flyer).
 *
 * Per B5W §10.1: "shuttles may be operated from any fighter box, but under
 * no circumstances may any fighter replace a shuttle." Shuttles are not
 * combat units and don't count toward fleet point totals — $isCombatUnit
 * is set false (existing flag, see civilians/skylark.php for prior art).
 *
 * Image resolution goes through getImage(), which keys off $this->faction.
 * That lets faction-flavoured art (e.g. Minbari Flyer) be picked up either
 * by a dedicated subclass that sets $this->faction in setShuttleDefaults
 * (preferred for stat-different variants like Flyer) or by adding a faction
 * branch to getImage()'s switch (cheaper for image-only variants).
 */
class Shuttle extends FighterFlight
{
    public $shuttle = true;       //distinguishes shuttles from non-combat freighters
    public $maxFlightSize = 6;    //shuttles can launch in flights of 1-6 (stored 1-per-record in $hangarUsage)

    function __construct($id, $userid, $name, $slot)
    {
        parent::__construct($id, $userid, $name, $slot);
        $this->setShuttleDefaults();   //subclass can override to change phpclass / faction / stats
        //Resolve image AFTER setShuttleDefaults so subclass faction overrides are honoured.
        list($this->imagePath, $this->iconPath) = $this->getImage();
        $this->populate();             //populate() uses $this->phpclass + $this->imagePath set above
    }

    /* Override in subclasses to change phpclass / faction / stats.
     * Called before getImage(), so set $this->faction here to get the right
     * art automatically. Do NOT set $this->imagePath / $this->iconPath here
     * unless your subclass overrides getImage() to bypass the faction map.
     */
    protected function setShuttleDefaults()
    {
        $this->pointCost = 0;                     //auto-populated, never bought
        $this->isCombatUnit = false;              //skipped by fleet builder / battlegroup checks
        $this->faction = "Generic";
        $this->phpclass = "Shuttle";
        $this->shipClass = "Shuttle";

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

    /* Returns array($imagePath, $iconPath) for this craft. Default lookup
     * keys off $this->faction so the base Shuttle / MinesweepingShuttle
     * classes can show faction-flavoured art without a dedicated subclass.
     * Subclasses with their own art (or stat-different variants like Flyer)
     * may instead just set $this->faction in setShuttleDefaults and let
     * this switch pick the right files.
     */
    public function getImage()
    {
        switch ($this->faction) {
            case 'Minbari Federation':
            case 'Minbari Protectorate':
                return array('img/ships/MinbariFlyer.png', 'img/ships/MinbariFlyer_Large.png');
            default:
                return array('img/ships/shuttle.png', 'img/ships/shuttle_large.png');
        }
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
            $fighter->imagePath = $this->imagePath;
            $fighter->iconPath = $this->iconPath;

            $fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0));

            $this->addSystem($fighter);
        }
    }
}


/* Minesweeping shuttle variant. Auto-populated when a carrier declares
 * 'minesweeping shuttles' in $fighters, or when leftover hangar capacity
 * exists on a ship with $minesweeperbonus > 0. Kept faction-agnostic at
 * present — getImage() inherits Shuttle's switch, so if a future launch
 * path passes a carrier faction through, faction-flavoured minesweeping
 * art will follow automatically.
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
        $this->minesweeper = true;
    }
}


/* Minbari Flyer — the Minbari Federation / Minbari Protectorate equivalent
 * of the generic Shuttle. Gravitic with higher base thrust. Selected for
 * Minbari carriers by HangarOps::factionShuttleClass during initial hangar
 * population; the inherited getImage() picks the MinbariFlyer art via the
 * faction switch above.
 */
class Flyer extends Shuttle
{
    protected function setShuttleDefaults()
    {
        parent::setShuttleDefaults();
        $this->phpclass = "Flyer";
        $this->shipClass = "Flyer";
        $this->faction = "Minbari Federation";

        $this->freethrust = 8;
        $this->gravitic = true;
    }
}
