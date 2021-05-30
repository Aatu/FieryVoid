<?php
class MaRiu extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 340;
		$this->faction = "Raiders";
        $this->phpclass = "MaRiu";
        $this->imagePath = "img/ships/pinnace.png"; //need to change
        $this->shipClass = "Ma'Ri'u Frigate";
        $this->canvasSize = 100;
        
		$this->notes = "Generic raider unit.";
		$this->notes .= "<br> ";
		$this->notes .= "<br>More detailed deployment restrictions are in the Faction List document.";
		$this->notes .= "<br> ";

		$this->isd = 2233;
        
        $this->forwardDefense = 12;
        $this->sideDefense = 14;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 2;
        $this->iniativebonus = 60;
         
        $this->addPrimarySystem(new Reactor(3, 12, 0, 0));
        $this->addPrimarySystem(new CnC(3, 9, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 6, 4, 5));
        $this->addPrimarySystem(new Engine(3, 11, 0, 10, 2));
		$this->addPrimarySystem(new Hangar(2, 2));
		$this->addPrimarySystem(new Thruster(4, 10, 0, 4, 3));
		$this->addPrimarySystem(new Thruster(4, 10, 0, 4, 4));
		$this->addPrimarySystem(new CargoBay(3, 10));
		$this->addPrimarySystem(new CargoBay(2, 12));
		        		
        $this->addFrontSystem(new Thruster(3, 8, 0, 4, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 4, 1));
		$this->addFrontSystem(new MediumBolter(2, 8, 4, 240, 60));
		$this->addFrontSystem(new MediumLaser(3, 6, 5, 300, 60));
		$this->addFrontSystem(new MediumBolter(2, 8, 4, 300, 120));
		
        $this->addAftSystem(new Thruster(2, 10, 0, 5, 2));
        $this->addAftSystem(new Thruster(2, 10, 0, 5, 2));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 0, 360));
        $this->addAftSystem(new StdParticleBeam(2, 4, 1, 90, 270));
               
        $this->addPrimarySystem(new Structure(3, 54));
		
        $this->hitChart = array (
        		0=> array (
        				7=>"Thruster",
        				11=>"Cargo Bay",
        				14=>"Scanner",
        				16=>"Engine",
        				17=>"Hangar",
        				19=>"Reactor",
        				20=>"C&C",
        		),
        		1=> array (
        				5=>"Thruster",
        				7=>"Medium Laser",
        				9=>"Medium Bolter",
        				17=>"Structure",
        				20=>"Primary",
        		),
        		2=> array(
        				6=>"Thruster",
        				7=>"Standard Particle Beam",
        				8=>"Light Particle Beam",
        				17=>"Structure",
        				20=>"Primary",
        		),
        );
    }

}



?>
