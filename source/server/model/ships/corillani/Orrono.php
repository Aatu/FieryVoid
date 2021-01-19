<?php
class Orrono extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 700;
		$this->faction = "Corillani";
        $this->phpclass = "Orrono";
        $this->imagePath = "img/ships/CorillaniOrrono.png";
        $this->shipClass = "Orrono Watchcruiser";
        $this->shipSizeClass = 3;
	    $this->isd = 2230;
		$this->notes = 'Defenders of Corillan (DoC)';
        $this->limited = 33;						    
		
        $this->forwardDefense = 14;
        $this->sideDefense = 17;
        
        $this->turncost = 1.33;
        $this->turndelaycost = 1.33;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 3;

        
        $this->addPrimarySystem(new Reactor(4, 16, 0, 0));
        $this->addPrimarySystem(new CnC(4, 16, 0, 0));
        $this->addPrimarySystem(new ElintScanner(5, 20, 5, 10));
        $this->addPrimarySystem(new Engine(4, 18, 0, 12, 4));
        $this->addPrimarySystem(new JumpEngine(4, 15, 4, 36));       
      
        $this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));
        $this->addFrontSystem(new LightParticleCannon(3, 6, 5, 300, 60));	
        $this->addFrontSystem(new LightParticleCannon(3, 6, 5, 300, 60)); 
        $this->addFrontSystem(new TwinArray(2, 6, 2, 270, 90));                 

        $this->addAftSystem(new TwinArray(2, 6, 2, 90, 270));
        $this->addAftSystem(new Thruster(4, 10, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 10, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 10, 0, 4, 2));     
		
		$this->addLeftSystem(new MediumPlasma(3, 5, 3, 240, 360));
        $this->addLeftSystem(new TwinArray(2, 6, 2, 240, 60));
        $this->addLeftSystem(new Thruster(3, 8, 0, 3, 3));
        $this->addLeftSystem(new Thruster(3, 8, 0, 3, 3));
        $this->addLeftSystem(new Hangar(2, 2));        
		
 		$this->addRightSystem(new MediumPlasma(3, 5, 3, 0, 120));
        $this->addRightSystem(new TwinArray(2, 6, 2, 300, 120));
        $this->addRightSystem(new Thruster(3, 8, 0, 3, 3));
        $this->addRightSystem(new Thruster(3, 8, 0, 3, 3));
        $this->addRightSystem(new Hangar(2, 2));        
        
		
        $this->addFrontSystem(new Structure(4, 40));
        $this->addAftSystem(new Structure(4, 40));
        $this->addLeftSystem(new Structure(4, 48));
        $this->addRightSystem(new Structure(4, 48));
        $this->addPrimarySystem(new Structure(5, 40));
		
		
		$this->hitChart = array(
			0=> array(
				9 => "Structure",
				12 => "Jump Engine",
				15 => "Scanner",
				17 => "Engine",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				6 => "Thruster",
				8 => "Light Particle Cannon",
				9 => "Twin Array",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				8 => "Thruster",
				10 => "Twin Array",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array(
				7 => "Thruster",
				9 => "Medium Plasma Cannon",
				11 => "Twin Array",
				12 => "Hangar",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				7 => "Thruster",
				9 => "Medium Plasma Cannon",
				11 => "Twin Array",
				12 => "Hangar",
				18 => "Structure",
				20 => "Primary",
			),
		);
    }
}



?>
