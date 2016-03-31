<?php
class Devout2002 extends MediumShip{
    /*Orieni Devout Escort Frigate, variant ISD 2002; source: WoCR*/
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 260;
        $this->faction = "Orieni";
        $this->phpclass = "devout2002";
        $this->imagePath = "img/ships/obedient.png";
        $this->shipClass = "Devout Escort Frigate (2002)";
        $this->agile = true;
        $this->canvasSize = 100;
        
        $this->forwardDefense = 11;
        $this->sideDefense = 11;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 2;
        $this->iniativebonus = 60;
        
         
        $this->addPrimarySystem(new Reactor(4, 12, 0, 2));
        $this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 11, 2, 4));
        $this->addPrimarySystem(new Engine(3, 16, 0, 12, 2));
        $this->addPrimarySystem(new Hangar(1, 1));
        $this->addPrimarySystem(new Thruster(2, 10, 0, 6, 3));
        $this->addPrimarySystem(new Thruster(2, 10, 0, 6, 4));        
        
        $this->addFrontSystem(new Thruster(1, 7, 0, 3, 1));
        $this->addFrontSystem(new Thruster(1, 7, 0, 3, 1)); 
        $this->addFrontSystem(new OrieniGatlingRG(1, 4, 1, 240, 0));
        $this->addFrontSystem(new OrieniGatlingRG(1, 4, 1, 240, 120));
        $this->addFrontSystem(new OrieniGatlingRG(1, 4, 1, 0, 120));
        $this->addFrontSystem(new LightLaser(1, 4, 3, 270, 90));
        $this->addAftSystem(new Thruster(1, 6, 0, 4, 2));
        $this->addAftSystem(new Thruster(1, 6, 0, 4, 2));
        $this->addAftSystem(new Thruster(1, 6, 0, 4, 2));    
        $this->addAftSystem(new OrieniGatlingRG(1, 4, 1, 120, 360));
        $this->addAftSystem(new OrieniGatlingRG(1, 4, 1, 0, 240));
       
        $this->addPrimarySystem(new Structure(4, 36));


	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			8 => "Thruster",
			11 => "Scanner",
			15 => "Engine",
			17 => "Hangar",
			19 => "Reactor",
			20 => "C&C",
		),

		1=> array(
			5 => "Thruster",
			6 => "Light Laser",
			9 => "Gatling Railgun",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
			7 => "Thruster",
			9 => "Gatling Railgun",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>
