<?php
/* A GRAVITY WELL - GRAVITY_WELL_PLAN.md. One hex of a gravity source (a black hole, a planet's edge): after
   movement every unit on the map is pulled towards it (GravityWells::resolvePull), and any unit that enters its
   hex is destroyed (GravityWellRammingAttack). Enormous, so it blocks line of sight like any Enormous unit.

   One class per strength (R1): this is strength 1; GravityWell2 / GravityWell3 are its variants. Bought from the
   Terrain faction and placed by its owner, or given to the game by Create Game's Gravity Well option
   (GravityWellRule) and placed near the centre at random - the same unit either way. */
class GravityWell extends Terrain{

    public $gravityStrength = 1; //hexes every unit is pulled each turn (an advanced race's: one fewer)
    public $terrainCollisionType = 'GravityWellCollision'; //= GravityWells::COLLISION_CLASS - what RammingAttack's terrain sweep calls a unit entering this hex

    function __construct($id, $userid, $name, $slot){
        parent::__construct($id, $userid, $name, $slot);

        $this->pointCost = 5;
        $this->faction = "Terrain";
        $this->factionAge = 1;
        $this->phpclass = "GravityWell";
        $this->imagePath = "img/ships/Singularity.png";
        $this->canvasSize = 400;
        $this->Enormous = true;
        $this->Huge = 0;
        $this->iniativebonus = -200; //no voluntary movement anyway
        $this->isd = 0;

        $this->base = true;
        $this->smallBase = true;
        $this->nonRotating = true;  //completely immobile, doesn't even rotate

        $this->forwardDefense = 28;
        $this->sideDefense = 28;

        //No engine/thrusters!!
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;

        //Block all enhancements for Terrain units when bought
        Enhancements::nonstandardEnhancementSet($this, 'Terrain');

        $this->setGravityStrength(1);

        //Added BEFORE the Structure, so addPrimarySystem does not give it an ordinary Ramming Attack as well.
        $this->addPrimarySystem(new GravityWellRammingAttack(0, 0, 360, 0, 0));
        $this->addPrimarySystem(new OSATCnC(10, 1, 0, 0)); //Required for some checks.
        $this->addPrimarySystem(new Structure(8, 5000));

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

    //The strength, and everything that names it: the class name the lobby lists and the notes.
    protected function setGravityStrength($strength){
        $this->gravityStrength = $strength;
        $this->shipClass = "Gravity Well (Strength " . $strength . ")";
        $this->notes = "Blocks line of sight";
        $this->notes .= "<br>After movement, every unit is pulled " . $strength . " " . ($strength == 1 ? "hex" : "hexes")
            . " towards this hex (advanced races " . ($strength - 1) . ")";
        $this->notes .= "<br>Bases, OSATs and terrain are not moved";
        $this->notes .= "<br>Any unit entering this hex is destroyed";
    }
}
?>
