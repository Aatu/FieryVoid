<?php
class sentryFF extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 420;
	$this->faction = "Deneth";
        $this->phpclass = "sentryFF";
        $this->imagePath = "img/ships/DenethSentry.png";
        $this->shipClass = "Sentry Frigate";
        $this->canvasSize = 100;
	    
	$this->isd = 2224;
        
        $this->forwardDefense = 12;
        $this->sideDefense = 12;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
	    
	    $this->iniativebonus = 12 *5;
        
         
        $this->addPrimarySystem(new Reactor(4, 16, 0, 0));
        $this->addPrimarySystem(new CnC(4, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 14, 4, 6));
        $this->addPrimarySystem(new Engine(4, 15, 0, 8, 2));
	$this->addPrimarySystem(new Hangar(3, 2));
	$this->addPrimarySystem(new Thruster(3, 10, 0, 4, 3));
	$this->addPrimarySystem(new Thruster(3, 10, 0, 4, 4));
		        		
        $this->addFrontSystem(new Thruster(3, 8, 0, 2, 1));
	$this->addFrontSystem(new Thruster(3, 8, 0, 2, 1));
        $this->addFrontSystem(new AssaultLaser(4, 6, 4, 240, 60));
        $this->addFrontSystem(new AssaultLaser(4, 6, 4, 300, 120));
	$this->addFrontSystem(new TwinArray(2, 6, 2, 240, 60));
	$this->addFrontSystem(new TwinArray(2, 6, 2, 270, 90));
	$this->addFrontSystem(new TwinArray(2, 6, 2, 300, 120));

	$this->addAftSystem(new Thruster(3, 10, 0, 4, 2));
	$this->addAftSystem(new Thruster(3, 10, 0, 4, 2));
        $this->addAftSystem(new TwinArray(2, 6, 2, 90, 270));        
               
        $this->addPrimarySystem(new Structure(5, 50));
		
        $this->hitChart = array (
        		0=> array (
        				10=>"Thruster",
        				13=>"Scanner",
        				16=>"Engine",
        				17=>"Hangar",
        				19=>"Reactor",
        				20=>"C&C",
        		),
        		1=> array (
        				5=>"Thruster",
        				7=>"Assault Laser",
					10=>"Twin Array",
        				17=>"Structure",
        				20=>"Primary",
        		),
        		2=> array(
        				7=>"Thruster",
        				9=>"Twin Array",
        				17=>"Structure",
        				20=>"Primary",
        		),
        );
    }
}
?>
