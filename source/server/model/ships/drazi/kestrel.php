<?php
class Kestrel extends MediumShip{
    /*Kestrel Corvette Leader is Rare variant of Kestrel Corvette*/
    /*present on Drazi Raider list as Throkan Corvette*/
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 390;
		$this->faction = "Drazi";
        $this->phpclass = "Kestrel";
        $this->imagePath = "img/ships/drazi/kestrel.png";
        $this->shipClass = "Kestrel Corvette Leader";
        $this->agile = true;
        $this->canvasSize = 128;
        
        $this->forwardDefense = 12;
        $this->sideDefense = 11;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 1;
        $this->rollcost = 1;
        $this->pivotcost = 2;
	    $this->iniativebonus = 70;
	
	    $this->isd = 2229;
	    $this->occurence = "rare";
	    $this->variantOf = "Kestrel Corvette";
	
         
        $this->addPrimarySystem(new Reactor(4, 10, 0, 4));
        $this->addPrimarySystem(new CnC(4, 10, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 10, 4, 6));
        $this->addPrimarySystem(new Engine(4, 10, 0, 7, 2));
		$this->addPrimarySystem(new Hangar(4, 1));
		$this->addPrimarySystem(new Thruster(3, 11, 0, 3, 3));
		$this->addPrimarySystem(new Thruster(3, 11, 0, 3, 4));
		
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new ParticleBlaster(3, 8, 5, 240, 0));
        $this->addFrontSystem(new ParticleRepeater(2, 6, 4, 240, 120));
        $this->addFrontSystem(new ParticleBlaster(3, 8, 5, 0, 120));
		
        $this->addAftSystem(new Thruster(4, 13, 0, 7, 2));
       
        $this->addPrimarySystem(new Structure( 4, 52));
        
        $this->hitChart = array(
        		0=> array(
        				8=> "Thruster",
        				11=> "Scanner",
        				14=> "Engine",
        				16=> "Hangar",
        				19=> "Reactor",
        				20=> "C&C",
        		),
        		1=> array(
        				5=> "Thruster",
        				8=> "Particle Blaster",
        				10=> "Particle Repeater",
        				17=> "Structure",
        				20=> "Primary",
        		),
        		2=> array(
        				9=> "Thruster",
        				17=> "Structure",
        				20=> "Primary",
        		),
        );
    }

}



?>
