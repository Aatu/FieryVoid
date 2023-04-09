<?php
class kendariUpgraded extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 625;
        $this->faction = "Centauri";
        $this->phpclass = "kendariUpgraded";
        $this->imagePath = "img/ships/kendari.png";
        $this->shipClass = "Kendari Fleet Scout (Upgraded)";	    
        $this->shipSizeClass = 3;
        $this->fighters = array("normal"=>6);
        $this->limited = 33;
	    $this->isd = 2193;
        
        $this->forwardDefense = 15;
        $this->sideDefense = 16;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;    

        $this->iniativebonus = -7;
		$this->critRollMod += 1;
		$this->enhancementOptionsDisabled[] = 'SLUGGISH';
		$this->enhancementOptionsDisabled[] = 'VULN_CRIT';

	    $this->notes .= '<br>Ablated armor.';
	    $this->notes .= '<br>Sluggish.';
         
        $this->addPrimarySystem(new Reactor(6, 14, 0, 0));
        $this->addPrimarySystem(new CnC(5, 16, 0, 0));
        $this->addPrimarySystem(new ElintScanner(5, 25, 7, 12));
        $this->addPrimarySystem(new Engine(4, 18, 0, 8, 3));
		$this->addPrimarySystem(new Hangar(4, 8));      
		
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));        
        $this->addFrontSystem(new TwinArray(3, 6, 2, 240, 120));
        $this->addFrontSystem(new GuardianArray(1, 4, 2, 240, 60));
        $this->addFrontSystem(new GuardianArray(1, 4, 2, 300, 120));
	    
        $this->addAftSystem(new Thruster(3, 12, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 4, 2));
        $this->addAftSystem(new JumpEngine(3, 20, 3, 20));
        $this->addAftSystem(new TwinArray(3, 6, 2, 60, 300));
        
		$this->addLeftSystem(new Thruster(5, 15, 0, 5, 3));
        $this->addLeftSystem(new TwinArray(3, 6, 2, 180, 360));
        
		$this->addRightSystem(new Thruster(5, 15, 0, 5, 4));
        $this->addRightSystem(new TwinArray(3, 6, 2, 0 , 180));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 3, 40));
        $this->addAftSystem(new Structure( 4, 44));
        $this->addLeftSystem(new Structure( 4, 45));
        $this->addRightSystem(new Structure( 4, 45));
        $this->addPrimarySystem(new Structure( 5, 40));
	    
	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			10 => "Structure",
			13 => "ELINT Scanner",
			15 => "Engine",
			17 => "Hangar",
			19 => "Reactor",
			20 => "C&C",
		),
		1=> array(
			6 => "Thruster",
			8 => "Guardian Array",
			10 => "Twin Array",
			18 => "Structure",
			20 => "Primary",
		),
		2=> array(
			6 => "Thruster",
			7 => "Twin Array",
			12 => "Jump Engine",
			18 => "Structure",
			20 => "Primary",
		),
		3=> array(
			6 => "Thruster",
			9 => "Twin Array",
			18 => "Structure",
			20 => "Primary",
		),
		4=> array(
			6 => "Thruster",
			9 => "Twin Array",
			18 => "Structure",
			20 => "Primary",
		),
	);
	    
    }
}

?>
