<?php
class VostovaAMCPN extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 450;
		$this->faction = "Civilians";
        $this->phpclass = "VostovaAMCPN";
        $this->imagePath = "img/ships/CorillaniVostovaCPN.png";
        $this->shipClass = "Vostova Freighter (CPN)";
        $this->shipSizeClass = 3;
        $this->fighters = array("cargo shuttles"=>8);
	    $this->isd = 2237;
		$this->notes = "Corillani People's Navy (CPN)";
 		$this->unofficial = 'S'; //design released after AoG demise
	    $this->isCombatUnit = false; //not a combat unit, it will never be present in a regular battlegroup

        $this->forwardDefense = 16;
        $this->sideDefense = 19;
        
        $this->turncost = 1.33;
        $this->turndelaycost = 1.33;
        $this->accelcost = 4;
        $this->rollcost = 2;
        $this->pivotcost = 4;


        $this->addPrimarySystem(new Reactor(4, 13, 0, 0));
        $this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 10, 4, 5));
        $this->addPrimarySystem(new Engine(4, 11, 0, 8, 5));
        $this->addPrimarySystem(new Hangar(4, 4, 2));
        $this->addPrimarySystem(new Hangar(4, 4, 2));
		
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));      
        $this->addFrontSystem(new MediumPlasma(3, 5, 3, 300, 60)); 
        $this->addFrontSystem(new MediumPlasma(3, 5, 3, 300, 60));
        $this->addFrontSystem(new TwinArray(2, 6, 2, 270, 90));                
        $this->addFrontSystem(new TwinArray(2, 6, 2, 270, 90));                
		
        $this->addAftSystem(new Thruster(3, 5, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 5, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 5, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 5, 0, 2, 2));
		
        
        $this->addLeftSystem(new TwinArray(2, 6, 2, 180, 360));
        $this->addLeftSystem(new TwinArray(2, 6, 2, 180, 360));
        $this->addLeftSystem(new Thruster(3, 5, 0, 2, 3));
        $this->addLeftSystem(new Thruster(3, 3, 0, 2, 3));
        $this->addLeftSystem(new CargoBay(3, 28));
		$this->addLeftSystem(new CargoBay(3, 28));

        $this->addRightSystem(new TwinArray(2, 6, 2, 0, 180));
        $this->addRightSystem(new TwinArray(2, 6, 2, 0, 180));
        $this->addRightSystem(new Thruster(3, 5, 0, 2, 4));
        $this->addRightSystem(new Thruster(3, 3, 0, 2, 4));
        $this->addRightSystem(new CargoBay(3, 28));        
		$this->addRightSystem(new CargoBay(3, 28));        

		
        $this->addFrontSystem(new Structure(4, 36));
        $this->addAftSystem(new Structure(4, 30));
        $this->addLeftSystem(new Structure(4, 44));
        $this->addRightSystem(new Structure(4, 44));
        $this->addPrimarySystem(new Structure(4, 36));
		
		
		$this->hitChart = array(
			0=> array(
				8 => "Structure",
				11 => "Jump Engine",
				13 => "Scanner",
				15 => "Engine",
				17 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				6 => "Thruster",
				8 => "Particle Cannon",
				10 => "Heavy Plasma Cannon",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				7 => "Thruster",
				9 => "Class-S Missile Rack",
				10 => "Particle Cannon",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array(
				7 => "Thruster",
				9 => "Heavy Plasma Cannon",
				12 => "Twin Array",
				13 => "Hangar",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				7 => "Thruster",
				9 => "Heavy Plasma Cannon",
				12 => "Twin Array",
				13 => "Hangar",
				18 => "Structure",
				20 => "Primary",
			),
		);
    }
}



?>
