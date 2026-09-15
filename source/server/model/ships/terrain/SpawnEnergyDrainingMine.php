<?php
/* WALKERS OF SIGMA-957 - THE ENERGY DRAINING MINE'S ORB (WALKERS_OF_SIGMA_PLAN.md 3.6, Stage 6).
 *
 * "They appear like chromatic pulse orbs, but are approximately four times the diameter ... they
 *  produce an Energy Draining Field, with all applicable rules, covering the destination hex and
 *  those immediately surrounding that hex (seven hexes in total). This field lasts for one turn."
 *
 * Spawned mid-game by EnergyDrainingMine::fire() (AoE.php), never bought. It is a Terrain unit for
 * the same reasons SpawnJumpPoint is: shipSizeClass 5 makes isTerrain() true, and that one fact
 * keeps it out of every selection path, the movement sequence, the fleet list and every points
 * total at once.
 *
 * ⭐ IT MOUNTS AN ORDINARY EnergyDrainingField, AND THAT IS THE WHOLE FEATURE. TacGamedata::
 * setEdfHexes() collects anything implementing EdfSource, so the drain, the targeting penalty, the
 * overlap collapse, the own-fleet immunity and the map overlay all pick this up with no code that
 * knows what a mine is. A bespoke field class would have needed a client twin, a blueprint entry
 * and its own name in PhaseStrategy.getEdfRadiusForShip / shipCarriesEdf, which match on
 * `system.name === 'EnergyDrainingField'` - three places to forget.
 *
 * ⭐⭐ ITS ONE-TURN LIFE IS ENCODED IN ITS NAME ("EDM<turn>", written by spawnFieldOrb) and read
 * back in onConstructed() below. tac_ship has no column for a spawn turn - spawnHyperspaceWaveform
 * solves the same problem the same way - and deriving the whole lifetime from that one number
 * means there is NO cleanup sweep to run, nothing to persist, nothing to get wrong on a reload,
 * and the orb still expires on time if the launcher that fired it is destroyed first.
 *
 * ⚠️ Enormous = FALSE, deliberately, like SpawnJumpPoint. Enormous terrain auto-rams everything
 * that flies through it (RammingAttack::beforePreFiringOrderResolution) and joins
 * gamedata->blockedHexes, which blocks line of sight. A drifting energy field does neither.
 *
 * ⚠️ THE FILENAME MUST MATCH THE CLASS NAME (bar case) - ShipLoader::getShipClassnamesStatic
 * enumerates ship classes by stripping ".php" off every file under model/ships/, so a mismatched
 * file is SILENTLY skipped by the static generator and by every lobby path built on it.
 * variantOf 'NONE' is what hides it from the lobby list; never use an autoload exclude for that.
 */
class SpawnEnergyDrainingMine extends Terrain{

    /* The turn the probe landed, parsed out of $name. 0 on a blueprint (the static generator and
       the ship-data validator both construct this class with a dummy name), which is exactly the
       case onConstructed() skips. */
    public $spawnTurn = 0;

    function __construct($id, $userid, $name, $slot){
        parent::__construct($id, $userid, $name, $slot);

        $this->pointCost   = 0;   //never bought, and must not disturb any fleet value it slips past
        $this->faction     = "Terrain";
        $this->factionAge  = 3;   //Ancient - it is a Walker construct
        $this->phpclass    = "SpawnEnergyDrainingMine";
        //⚠️ Case-sensitive on live (arch_dockingcollar_icon_case).
        $this->imagePath   = "img/ships/WalkerEDMine.png";
        $this->canvasSize  = 100;
        $this->shipClass   = "Energy Draining Field";
        $this->variantOf   = 'NONE'; //hidden from the lobby - see the class comment
        $this->Enormous    = false;  //MUST stay false - see the class comment
        $this->iniativebonus = -200; //no voluntary movement anyway
        $this->isd         = 0;
        $this->notes       = "A Walker sensor probe, projecting an Energy Draining Field.";
        $this->notes      .= "<br>Covers this hex and the six around it for one turn, then dissipates.";
        $this->notes      .= "<br>The fleet that launched it is unaffected by the field.";
        $this->notes      .= "<br>Destroying this probe has no in-game effect.";        
        $this->occurence   = "common";

        $this->base        = true;
        $this->smallBase   = true;
        $this->nonRotating = true;  //completely immobile, doesn't even rotate

        $this->forwardDefense = 20;
        $this->sideDefense    = 20;

        //No engine/thrusters.
        $this->turncost      = 0;
        $this->turndelaycost = 0;
        $this->accelcost     = 0;
        $this->rollcost      = 0;
        $this->pivotcost     = 0;

        //Block all enhancements for Terrain units when bought
        Enhancements::nonstandardEnhancementSet($this, 'Terrain');

        //Required for some checks - a unit with no C&C at all trips isDisabled() and friends.
        $this->addPrimarySystem(new OSATCnC(0, 1, 0, 0));

        /* THE FIELD. Radius 1 = the target hex plus the six around it, which is the rules' "seven
           hexes in total". Fixed, never variable: an orb has no power allocation to boost with.
           Health/power are nominal - the hit chart below routes every shot to Structure, so this
           system is not reachable by damage. */
        $this->addPrimarySystem(new EnergyDrainingField(0, 1, 1, EnergyDrainingMine::FIELD_RADIUS, false));

        /* INDESTRUCTIBLE primary Structure, as SpawnJumpPoint has: nothing can shoot the field
           away. Its life is one turn, decided by onConstructed() below, and letting damage end it
           early would mean a second, unwritten rule about when a field stops. A shot at the orb is
           a wasted shot rather than an error. */
        $this->addPrimarySystem(new Structure(0, 1, true));

        $this->hitChart = array(
                0=> array(
                        20 => "Structure",
                ),
                1=> array(
                        20 => "Primary",
                ),
                2=> array(
                        20 => "Primary",
                ),
        );
    }

    /* THE LIFETIME, rebuilt from the name on every gamedata load.
     *
     * The probe lands during the Firing phase of turn N and is on the board for turns N and N+1,
     * so it drains TWICE: once at the Critical Hit step of turn N - the step immediately after the
     * one it landed in, which is why EnergyDrainingMine queues its hexes into the map mid-request
     * (see $pendingFields there) - and once on turn N+1, by which point it is an ordinary ship and
     * setEdfHexes() finds its EnergyDrainingField the usual way. A unit caught on both turns
     * therefore escalates: one die on the landing turn, two on the next (user ruling 2026-09-05).
     *
     * ⚠️⚠️ $removed IS SET PER LOAD, NOT ONCE AND FOR EVER, and that is not a style choice.
     * BaseShip::isDestroyed() with no argument answers TRUE for any unit whose $removed is set,
     * whatever $removedTurn says - so stamping the flag at spawn time would kill the field on the
     * very turn it is supposed to work. JumpEngine::restoreVortexState sets it conditionally for
     * the same reason. Setting it for turns BEFORE the spawn as well is what keeps a replay of an
     * earlier turn honest: setEdfHexes() skips destroyed units, so the field cannot appear a turn
     * early on the map or in anybody's hit chances.
     *
     * ⚠️ THIS RUNS BEFORE TacGamedata::setEdfHexes(), and has to. onConstructed() is called from
     * the per-ship loop in TacGamedata::onConstructed(); the EDF map is built after that loop.
     */
    public function onConstructed($turn, $phase, $gamedata)
    {
        parent::onConstructed($turn, $phase, $gamedata);

        $this->loadSpawnTurn();
        if ($this->spawnTurn <= 0) return; //a blueprint, not a unit in play

        $this->spawned = $this->spawnTurn;

        if ($turn < $this->spawnTurn || $turn > $this->spawnTurn + 1){
            $this->removed     = true;
            $this->removedTurn = $this->spawnTurn + 2; //the first turn it is gone
        }
    }

    /* "EDM7" -> 7. Same shape as spawnHyperspaceWaveform::loadSpawnTurn(). */
    public function loadSpawnTurn()
    {
        if (strpos($this->name, 'EDM') === 0) {
            $this->spawnTurn = (int)substr($this->name, 3);
        }
    }
}
?>
