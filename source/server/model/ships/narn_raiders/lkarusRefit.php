<?php
class LkarusRefit extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 450;
		$this->faction = "Raiders";
        $this->phpclass = "LkarusRefit";
        $this->imagePath = "img/ships/lkarus.png";
        $this->shipClass = "Narn Privateer L'Karus Raider Cruiser (2244 refit)";
			$this->occurence = "common";
			$this->variantOf = "Narn Privateer L'Karus Raider Cruiser";        
		$this->canvasSize = 125; //img has 125px per side
        $this->fighters = array("normal"=>6);  
	    $this->isd = 2244;
        $this->limited = 33;

		$this->notes = "Used only by Narn privateers";
		
        $this->forwardDefense = 14;
        $this->sideDefense = 16;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
        
        $this->addPrimarySystem(new Reactor(4, 18, 0, 0));
        $this->addPrimarySystem(new CnC(5, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 10, 3, 5));
        $this->addPrimarySystem(new Engine(5, 18, 0, 12, 3));
		$this->addPrimarySystem(new Hangar(5, 8));
		$this->addPrimarySystem(new CargoBay(4, 10));
        
        $this->addFrontSystem(new LightPulse(2, 4, 2, 270, 90));        
        $this->addFrontSystem(new MediumPlasma(4, 5, 3, 240, 360));        
        $this->addFrontSystem(new Thruster(4, 10, 0, 4, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 4, 1));        
        $this->addFrontSystem(new MediumPlasma(4, 5, 3, 0, 120));        
        $this->addFrontSystem(new LightPulse(2, 4, 2, 270, 90));
			
		$this->addAftSystem(new LightPulse(2, 4, 2, 90, 270));
		$this->addAftSystem(new LightPulse(2, 4, 2, 90, 270));
		$this->addAftSystem(new JumpEngine(3, 15, 3, 32));
        $this->addAftSystem(new Thruster(4, 16, 0, 6, 2));
        $this->addAftSystem(new Thruster(4, 16, 0, 6, 2));
		
		$this->addLeftSystem(new MediumPlasma(4, 5, 3, 240, 360));
        $this->addLeftSystem(new Thruster(4, 15, 0, 5, 3));
              			  
		$this->addRightSystem(new MediumPlasma(4, 5, 3, 0, 120));
		$this->addRightSystem(new Thruster(4, 15, 0, 5, 4));
        
		//structures
        $this->addFrontSystem(new Structure(4, 50));
        $this->addAftSystem(new Structure(3, 50));
        $this->addLeftSystem(new Structure(4, 56));
        $this->addRightSystem(new Structure(4, 56));
        $this->addPrimarySystem(new Structure(4, 50));
		
		$this->hitChart = array(
			0=> array(
				9 => "Structure",
				11 => "Cargo Bay",
				13 => "Scanner",
				15 => "Engine",
				17 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				6 => "Thruster",
				8 => "Medium Plasma Cannon",
				10 => "Light Pulse Cannon",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				6 => "Thruster",
				9 => "Jump Engine",
				11 => "Light Pulse Cannon",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array(
				6 => "Thruster",
				9 => "Medium Plasma Cannon",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				6 => "Thruster",
				9 => "Medium Plasma Cannon",
				18 => "Structure",
				20 => "Primary",
			),
		);        
    }	
}



?>
