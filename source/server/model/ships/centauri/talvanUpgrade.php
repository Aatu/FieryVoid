<?php
class TalvanUpgrade extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 640;
        $this->faction = "Centauri";
        $this->phpclass = "TalvanUpgrade";
        $this->imagePath = "img/ships/talvan.png";
        $this->shipClass = "Talvan Attack Cruiser (2202)";
//        $this->variantOf = "Talvan Attack Cruiser";
        $this->shipSizeClass = 3;
        //$this->limited = 33; //limited deployment
        //$this->fighters = array("heavy"=>12);
	    $this->isd = 2202;
        $this->forwardDefense = 15;
        $this->sideDefense = 17;

		$this->critRollMod += 1;
		$this->enhancementOptionsDisabled[] = 'VULN_CRIT';
		$this->enhancementOptionsDisabled[] = 'IMPR_ENG';
		
//		$this->notes = "Engine fluctuations. Rolls for engine critical every turn with +5% penalty. Effect lasts one turn.";
        
        $this->turncost = 1;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 1*5;  
         
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
		
        $this->addAftSystem(new Thruster(4, 14, 0, 5, 2));
        $this->addAftSystem(new Thruster(4, 14, 0, 5, 2));
		$this->addAftSystem(new JumpEngine(4, 25, 3, 20));      
	    
		$this->addLeftSystem(new Thruster(4, 15, 0, 5, 3));
        $this->addLeftSystem(new HeavyPlasma(3, 8, 5, 240, 360));
        $this->addLeftSystem(new MatterCannon(3, 7, 4, 240, 360));
	    
		$this->addRightSystem(new Thruster(4, 15, 0, 5, 4));
        $this->addRightSystem(new HeavyPlasma(3, 8, 5, 0, 120));
        $this->addRightSystem(new MatterCannon(3, 7, 4, 0, 120));
        
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
			6 => "Heavy Plasma Cannon",
			9 => "Matter Cannon",
			18 => "Structure",
			20 => "Primary",
		),
		4=> array(
			3 => "Thruster",
			6 => "Heavy Plasma Cannon",
			9 => "Matter Cannon",
			18 => "Structure",
			20 => "Primary",
		),
	);
	    
    }
}
?>
