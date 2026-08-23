<?php
/* THE FIXED JUMP GATE - the official AoG unit, and the ONLY gate JUMP_GATES_PLAN.md covers.
 *
 * ⚠️ JumpgateNew (terrain) and the civilian Jumpgate also mount a JumpEngine and will turn up in
 * any grep for one. Both are OBSOLETE and explicitly out of scope (user ruling 2026-08-23, plan
 * trap 12): neither is marked as a gate, and nothing in the gate feature keys off a hull name.
 *
 * ⚠️ THE FILENAME MUST MATCH THE CLASS NAME (bar case) - ShipLoader::getShipClassnamesStatic
 * enumerates ship classes by stripping ".php" off every file under model/ships/, so a mismatch is
 * SILENTLY not a ship at all. See SpawnJumpPoint.php for the full note.
 */
class JumpgateCapital  extends BaseShip{

    /* ALWAYS-ON FACING ARROW, and on a gate it is not decoration (JUMP_GATES_PLAN.md section 2.2).
       The vortex a gate opens ALWAYS takes the gate's own facing, that facing is fixed when the
       gate is placed, and a unit may only enter travelling in direction (facing + 3) % 6. The
       player never chooses any of that - so the mouth MUST be readable straight off the map, or the
       one rule that governs using the gate is invisible.

       Same asset SpawnJumpPoint uses, so the arrow looks identical at all three points of a gate
       vortex's life: the gate itself, the "Jump Gate Signalled" marker, and the open vortex.

       DECLARED as a property, not assigned in the constructor: PHP 8.2 deprecates dynamic
       properties and Manager's error handler rethrows every diagnostic, E_DEPRECATED included.
       Declared HERE and never on BaseShip - a base-class default would write "facingArrow":null
       into every entry of every static blueprint, and absent already reads as falsy on the client. */
    public $facingArrow = "img/directionOfVortex.png";

    //This si the official Jump Gate unit from AoG
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 10;
		$this->faction = "Terrain";  
        $this->phpclass = "JumpgateCapital";
        $this->imagePath = "img/ships/JumpGate1.png";
        $this->canvasSize = 200;
        $this->shipClass = "Fixed Jump Gate";
        $this->Enormous = false; //classify it as a Capital just so it doesn't auto-ram passing units / block LoS!
		$this->iniativebonus = -200; //no voluntary movement anyway
        $this->isd = '2000';
        $this->shipSizeClass = 5; //5 is used to identify Terrain is certain Front End functions.      
	            
		$this->base = true;
		$this->smallBase = true;
		$this->nonRotating = true;  //completely immobile, doesn't even rotate
		
        $this->forwardDefense = 20;
        $this->sideDefense = 20;
        
        $this->turncost = 99;
        $this->turndelaycost = 99;
        $this->accelcost = 99;
        $this->rollcost = 99;
        $this->pivotcost = 99;	
	    
        //Block all enhancements for Terrain units when bought
		Enhancements::nonstandardEnhancementSet($this, 'Terrain');        

        /* THE REACTOR IS THE WHOLE DAMAGE MODEL, AND IT ROLLS NO CRITICALS (plan section 2.5).
           Damage on this Reactor is read as a NUMBER - it lengthens the recharge (20 + D/3 turns),
           caps the programmable hold (4 - D/15, minimum 1), and total loss destroys the gate - so
           an OutputReduced or a ContainmentBreach on top of it would be a second, contradictory
           model of the same wound. clearPossibleCriticals() silences the chart from out here;
           $possibleCriticals is protected and unserialised, so the static blueprint is unchanged. */
        $this->addPrimarySystem((new Reactor(6, 50, 0, 0))->clearPossibleCriticals());
        $this->addPrimarySystem(new CnC(6, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 6, 2, 2));
        $this->addPrimarySystem(new Hangar(3, 1, 1));
        /* markGate() IS WHAT MAKES THIS A GATE (plan section 3.2) - a per-instance flag, never a
           subclass, so phpclass stays "JumpEngine" and no autoload regeneration is needed.
           It turns $range into a 10-hex SIGNAL range and the firing modes into the programmed open
           duration (1-4 turns). The 4th constructor argument, 20, is already the recharge time in
           turns - Phase 1 Stage 6 gave $delay that meaning - so the 20-turn gate recharge needs no
           code of its own. ⚠️ TEN BOXES: the end-of-turn jump-failure roll is a percentage of boxes
           lost, so each point of engine damage is a flat 10% chance of destroying the gate the next
           time it opens a vortex, and the engine is on the d20 hit chart at 15 (plan section 2.5). */
        $this->addPrimarySystem((new JumpEngine(8, 10, 20, 20))->markGate());
	
				
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(2, 200));
        $this->addAftSystem(new Structure(2, 200));
        $this->addLeftSystem(new Structure(2, 240));
        $this->addRightSystem(new Structure(2, 240));
        $this->addPrimarySystem(new Structure(3, 160));

        $this->hitChart = array(
                0=> array(
                        9 => "Structure",
                        10 => "Scanner",
                        11 => "Hangar",
                        15 => "Jump Engine",
                		19 => "Reactor",
                		20 => "C&C",
                ),
                1=> array(
                        18 => "Structure",                        
                        20 => "Primary",
                ),
                2=> array(
                        18 => "Structure",                        
                        20 => "Primary",
                ),
                3=> array(
                        18 => "Structure",                        
                        20 => "Primary",
                ),
                4=> array(
                        18 => "Structure",                        
                        20 => "Primary",
                ),                
        );
    }
}
?>
