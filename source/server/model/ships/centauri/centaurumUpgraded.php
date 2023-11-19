<?php
class centaurumUpgraded extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 1000;
        $this->faction = "Centauri Republic";
        $this->phpclass = "centaurumUpgraded";
        $this->imagePath = "img/ships/octurion.png";
        $this->shipClass = "Centaurum Battleship (Upgraded)";
        $this->shipSizeClass = 3;
        $this->limited = 33; //limited deployment
        $this->fighters = array("normal"=>12);
	    $this->isd = 2209;
        $this->forwardDefense = 17;
        $this->sideDefense = 19;
        
        $this->turncost = 1;
        $this->turndelaycost = 1.33;
        $this->accelcost = 5;
        $this->rollcost = 4;
        $this->pivotcost = 4;

		$this->critRollMod += 1;
		$this->enhancementOptionsDisabled[] = 'VULN_CRIT';

	    $this->notes .= '<br>Ablated armor.';
         
        $this->addPrimarySystem(new Reactor(6, 30, 0, 0));
        $this->addPrimarySystem(new CnC(6, 25, 0, 0));
		$sensors = new Scanner(6, 25, 4, 8);
			$sensors->markSensorFlux();
			$this->addPrimarySystem($sensors); 
        $this->addPrimarySystem(new Engine(6, 25, 0, 9, 3));
		$this->addPrimarySystem(new Hangar(5, 16));
        
        $this->addFrontSystem(new Thruster(4, 15, 0, 4, 1));
        $this->addFrontSystem(new Thruster(4, 15, 0, 4, 1));
        $this->addFrontSystem(new TwinArray(2, 6, 2, 240, 120));
		$this->addFrontSystem(new BattleLaser(3, 6, 6, 300, 60));
        $this->addFrontSystem(new BattleLaser(2, 6, 6, 300, 60));
        $this->addFrontSystem(new MatterCannon(3, 7, 4, 300, 60));
        $this->addFrontSystem(new MatterCannon(3, 7, 4, 300, 60));
		
        $this->addAftSystem(new Thruster(4, 10, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 12, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 12, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 10, 0, 2, 2));
		$this->addAftSystem(new JumpEngine(6, 24, 4, 20));      
        $this->addAftSystem(new TwinArray(3, 6, 2, 90, 270));  
        $this->addAftSystem(new TwinArray(2, 6, 2, 90, 270));  
        $this->addAftSystem(new MatterCannon(3, 7, 4, 180, 300));
        $this->addAftSystem(new MatterCannon(3, 7, 4, 60, 180));
	    
		$this->addLeftSystem(new Thruster(4, 20, 0, 6, 3));
        $this->addLeftSystem(new BattleLaser(3, 6, 3, 240, 360));
        $this->addLeftSystem(new TwinArray(3, 6, 2, 180, 360));
        $this->addLeftSystem(new TwinArray(3, 6, 2, 180, 360));
        $this->addLeftSystem(new TwinArray(3, 6, 2, 180, 360));
	    
		$this->addRightSystem(new Thruster(4, 20, 0, 6, 4));
        $this->addRightSystem(new BattleLaser(3, 6, 6, 0, 120));
        $this->addRightSystem(new TwinArray(2, 6, 2, 0, 180));
        $this->addRightSystem(new TwinArray(3, 6, 2, 0, 180));
        $this->addRightSystem(new TwinArray(3, 6, 2, 0, 180));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 55));
        $this->addAftSystem(new Structure( 5, 48));
        $this->addLeftSystem(new Structure( 5, 60));
        $this->addRightSystem(new Structure( 5, 60));
        $this->addPrimarySystem(new Structure( 5, 56));
	    
	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			9 => "Structure",
			12 => "Scanner",
			15 => "Engine",
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
			11 => "Matter Cannon",
			13 => "Twin Array",
			18 => "Structure",
			20 => "Primary",
		),
		3=> array(
			5 => "Thruster",
			7 => "Battle Laser",
			10 => "Twin Array",
			18 => "Structure",
			20 => "Primary",
		),
		4=> array(
			5 => "Thruster",
			7 => "Battle Laser",
			10 => "Twin Array",
			18 => "Structure",
			20 => "Primary",
		),
	);
	    
    }
}
?>
