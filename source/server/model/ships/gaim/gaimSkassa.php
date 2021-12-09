<?php
class gaimSkassa extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 700;
        $this->faction = "Gaim";
        $this->phpclass = "gaimSkassa";
        $this->imagePath = "img/ships/GaimSkassa.png";
        $this->shipClass = "Skassa Cruiser";
        $this->shipSizeClass = 3;
	    $this->isd = 2253;
        $this->forwardDefense = 15;
        $this->sideDefense = 17;

		$this->critRollMod += 1;
		$this->enhancementOptionsDisabled[] = 'VULN_CRIT';
		
		$this->notes = "Engine fluctuations. Rolls for engine critical every turn with +5% penalty. Effect lasts one turn.";
        
        $this->turncost = 1;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;  
         
        $this->addPrimarySystem(new Reactor(6, 22, 0, 0));
        $this->addPrimarySystem(new CnC(6, 18, 0, 0));
        $this->addPrimarySystem(new Scanner(6, 20, 4, 8));
		$engine = new Engine(5, 20, 0, 9, 3);
			$engine->markEngineFlux();
			$this->addPrimarySystem($engine);
		$this->addPrimarySystem(new Hangar(5, 2));
        
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new BattleLaser(4, 6, 6, 300, 60));
		$this->addFrontSystem(new TwinArray(3, 6, 2, 180, 60));
		$this->addFrontSystem(new TwinArray(3, 6, 2, 300, 180));
		$this->addFrontSystem(new TwinArray(3, 6, 2, 240, 120));
		$this->addFrontSystem(new Bulkhead(0, 2));
		$this->addFrontSystem(new Bulkhead(0, 2));
		
        $this->addAftSystem(new Thruster(4, 14, 0, 5, 2));
        $this->addAftSystem(new Thruster(4, 14, 0, 5, 2));
		$this->addAftSystem(new JumpEngine(4, 25, 3, 20));      
	    
		$this->addLeftSystem(new Thruster(4, 15, 0, 5, 3));
        $this->addLeftSystem(new BattleLaser(3, 6, 6, 240, 360));
        $this->addLeftSystem(new ScatterGun(3, 8, 3, 240, 360));
		$this->addLeftSystem(new Bulkhead(0, 3));
	    
		$this->addRightSystem(new Thruster(4, 15, 0, 5, 4));
        $this->addRightSystem(new BattleLaser(3, 6, 6, 0, 120));
        $this->addRightSystem(new ScatterGun(3, 8, 3, 0, 120));
 		$this->addRightSystem(new Bulkhead(0, 3));
		
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 42));
        $this->addAftSystem(new Structure( 4, 40));
        $this->addLeftSystem(new Structure( 5, 54));
        $this->addRightSystem(new Structure( 5, 54));
        $this->addPrimarySystem(new Structure( 6, 36));
	    
	    
	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			10 => "Structure",
			12 => "Scanner",
			15 => "Engine",
			17 => "Hangar",
			19 => "Reactor",
			20 => "C&C",
		),
		1=> array(
			3 => "Thruster",
			5 => "Battle Laser",
			9 => "Twin Array",
			18 => "Structure",
			20 => "Primary",
		),
		2=> array(
			7 => "Thruster",
			12 => "Jump Engine",
			18 => "Structure",
			20 => "Primary",
		),
		3=> array(
			3 => "Thruster",
			6 => "Battle Laser",
			9 => "Scattergun",
			18 => "Structure",
			20 => "Primary",
		),
		4=> array(
			3 => "Thruster",
			6 => "Battle Laser",
			9 => "Scattergun",
			18 => "Structure",
			20 => "Primary",
		),
	);
	    
    }
}
?>
