<?php
class PrivateerHactrus extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 325;
		$this->faction = "Raiders";
        $this->phpclass = "PrivateerHactrus";
        $this->imagePath = "img/ships/TorataPrivateerHactrus.png"; 
        $this->shipClass = "Torata Privateer Hactrus Corvette";
		$this->variantOf = "Raider Hactrus Corvette";
		$this->occurence = "common";
        $this->canvasSize = 100;
        
		$this->notes = "Used only by Torata privateers.";
		$this->notes .= "<br> ";
		$this->notes .= "<br>More detailed deployment restrictions are in the Faction List document.";
		$this->notes .= "<br> ";


		$this->unofficial = true;



		$this->isd = 2229;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 12;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 60;
         
        $this->addPrimarySystem(new Reactor(3, 9, 0, 0));
        $this->addPrimarySystem(new CnC(3, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 5, 4, 5));
        $this->addPrimarySystem(new Engine(3, 11, 0, 6, 3));
		$this->addPrimarySystem(new Hangar(3, 1, 1));
		$this->addPrimarySystem(new Thruster(2, 11, 0, 4, 3));
		$this->addPrimarySystem(new Thruster(2, 11, 0, 4, 4));


		$this->addPrimarySystem(new LightParticleBeamShip(2, 1, 1, 180, 60));
		$this->addPrimarySystem(new LightParticleBeamShip(2, 1, 1, 240, 120));
		$this->addPrimarySystem(new LightParticleBeamShip(2, 1, 1, 240, 120));
		$this->addPrimarySystem(new LightParticleBeamShip(2, 1, 1, 300, 180));
		        		
        $this->addFrontSystem(new Thruster(3, 8, 0, 4, 1));
		$this->addFrontSystem(new PlasmaAccelerator(4, 10, 5, 300, 60));
		
        $this->addAftSystem(new Thruster(3, 12, 0, 6, 2));
               
        $this->addPrimarySystem(new Structure(3, 36));
		
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
        				7=>"Plasma Accelerator",
        				9=>"Light Particle Beam",
        				17=>"Structure",
        				20=>"Primary",
        		),
        		2=> array(
        				6=>"Thruster",
        				17=>"Structure",
        				20=>"Primary",
        		),
        );
    }

}



?>
