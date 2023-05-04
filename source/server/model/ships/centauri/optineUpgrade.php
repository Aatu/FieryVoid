<?php
class OptineUpgrade extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 775;
        $this->faction = "Centauri";
        $this->phpclass = "OptineUpgrade";
        $this->imagePath = "img/ships/optine.png";
        $this->shipClass = "Optine Battlecruiser (Upgraded)";
        $this->shipSizeClass = 3;
	    $this->isd = 2206;

	    $this->notes .= '<br>Ablated armor.';
	    $this->notes .= '<br>Pre-existing damage.';
	    $this->notes .= '<br>Weapon misfires.';

        $this->forwardDefense = 16;
        $this->sideDefense = 18;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 4;

		$this->critRollMod += 1;
		$this->enhancementOptionsDisabled[] = 'VULN_CRIT';
       
        $this->addPrimarySystem(new Reactor(6, 25, 0, 0));
        $this->addPrimarySystem(new CnC(6, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(6, 22, 4, 8));
        $this->addPrimarySystem(new Engine(5, 20, 0, 9, 3));
		$this->addPrimarySystem(new Hangar(4, 2));
        
		$this->addFrontSystem(new Thruster(4, 8, 0, 4, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
        $this->addFrontSystem(new UnreliableMatterCannon(2, 5, 4, 240, 360));
		$this->addFrontSystem(new UnreliableBattleLaser(3, 3, 6, 300, 60));
        $this->addFrontSystem(new UnreliableTwinArray(3, 6, 2, 270, 90));
		$this->addFrontSystem(new UnreliableBattleLaser(2, 6, 6, 300, 60));
        $this->addFrontSystem(new UnreliableMatterCannon(3, 6, 4, 0, 120));
		
        $this->addAftSystem(new Thruster(3, 9, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 12, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 10, 0, 3, 2));
		$this->addAftSystem(new UnreliableTwinArray(3, 3, 2, 120, 360));
		$this->addAftSystem(new JumpEngine(5, 22, 3, 20));
		$this->addAftSystem(new UnreliableTwinArray(3, 3, 2, 0, 240));
		
		$this->addLeftSystem(new Thruster(5, 14, 0, 6, 3));
		$this->addLeftSystem(new UnreliableTwinArray(3, 6, 2, 180, 360));
		$this->addLeftSystem(new UnreliableBattleLaser(3, 6, 6, 300, 360));

		$this->addRightSystem(new Thruster(5, 14, 0, 6, 4));
		$this->addRightSystem(new UnreliableTwinArray(2, 4, 2, 0, 180));
		$this->addRightSystem(new UnreliableBattleLaser(3, 6, 6, 0, 60));
		
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 39));
        $this->addAftSystem(new Structure( 4, 33));
        $this->addLeftSystem(new Structure( 4, 47));
        $this->addRightSystem(new Structure( 4, 48));
        $this->addPrimarySystem(new Structure( 6, 50));
	    
	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			10 => "Structure",
			13 => "Scanner",
			16 => "Engine",
			17 => "Hangar",
			19 => "Reactor",
			20 => "C&C",
		),
		1=> array(
			5 => "Thruster",
			8 => "Battle Laser",
			9 => "Twin Array",
			11 => "Matter Cannon",
			18 => "Structure",
			20 => "Primary",
		),
		2=> array(
			5 => "Thruster",
			8 => "Jump Engine",
			11 => "Twin Array",
			18 => "Structure",
			20 => "Primary",
		),
		3=> array(
			4 => "Thruster",
			6 => "Battle Laser",
			8 => "Twin Array",
			18 => "Structure",
			20 => "Primary",
		),
		4=> array(
			4 => "Thruster",
			6 => "Battle Laser",
			8 => "Twin Array",
			18 => "Structure",
			20 => "Primary",
		),
	);
	    
	    
    }
}
?>
