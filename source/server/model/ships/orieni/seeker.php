<?php
class Seeker extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 400;
		$this->faction = "Orieni";
        $this->phpclass = "Seeker";
        $this->imagePath = "img/ships/seeker.png";
        $this->shipClass = "Seeker Recon Frigate";
        $this->agile = true;
        $this->canvasSize = 100;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 14;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 2;
		$this->iniativebonus = 60;
        
        $this->occurence = "rare";

        $this->addPrimarySystem(new Reactor(4, 15, 0, 0));
        $this->addPrimarySystem(new CnC(4, 15, 0, 0));
        $this->addPrimarySystem(new ElintScanner(3, 12, 3, 4));
        $this->addPrimarySystem(new Engine(3, 15, 0, 10, 2));
		$this->addPrimarySystem(new Hangar(1, 1));
		$this->addPrimarySystem(new Thruster(2, 10, 0, 5, 3));
		$this->addPrimarySystem(new Thruster(2, 10, 0, 5, 4));        
		
        $this->addFrontSystem(new Thruster(1, 6, 0, 2, 1));
        $this->addFrontSystem(new Thruster(2, 6, 0, 2, 1));
        $this->addFrontSystem(new Thruster(1, 6, 0, 2, 1));
        $this->addFrontSystem(new GaussCannon(3, 10, 4, 300, 60));
        $this->addFrontSystem(new GaussCannon(3, 10, 4, 300, 60));

        $this->addAftSystem(new Thruster(2, 6, 0, 3, 2));
        $this->addAftSystem(new Thruster(2, 8, 0, 4, 2));
        $this->addAftSystem(new Thruster(2, 6, 0, 3, 2));
        $this->addAftSystem(new RapidGatling(1, 4, 1, 120, 360));
        $this->addAftSystem(new RapidGatling(1, 4, 1, 0, 240));    
        
       
        $this->addPrimarySystem(new Structure(4, 46));
		
		
        }
    }
?>
