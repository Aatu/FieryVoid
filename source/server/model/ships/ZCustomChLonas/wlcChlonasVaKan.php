<?php
class wlcChlonasVaKan extends MediumShipLeftRight{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 525;
        $this->phpclass = "wlcChlonasVaKan";
        $this->imagePath = "img/ships/ChlonasVaKan.png";
        $this->canvasSize = 200;
        $this->shipClass = "Va'Kan Battle Frigate";
        $this->agile = true;
	    $this->limited = 33;
		$this->faction = "Ch'Lonas";
		$this->isd = 2258;
		$this->unofficial = true;
        
        $this->forwardDefense = 12;
        $this->sideDefense = 14;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
		$this->iniativebonus = 13 *5;
         
        $this->addPrimarySystem(new Reactor(5, 14, 0, 0));
        $this->addPrimarySystem(new CnC(5, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 13, 3, 6));
        $this->addPrimarySystem(new Engine(4, 11, 0, 10, 3));
		$this->addPrimarySystem(new Hangar(3, 1));
      	$this->addPrimarySystem(new CustomGatlingMattergunHeavy(4, 0, 0, 300, 60));  
      	$this->addPrimarySystem(new Thruster(4, 10, 0, 5, 1));
        $this->addPrimarySystem(new Thruster(4, 21, 0, 10, 2));


        $this->addLeftSystem(new AssaultLaser(3, 6, 4, 240, 360));
        $this->addLeftSystem(new CustomMatterStream(3, 0, 0, 240, 60));
        $this->addLeftSystem(new CustomGatlingMattergunLight(2, 0, 0, 180, 360));
        $this->addLeftSystem(new CustomGatlingMattergunLight(2, 0, 0, 180, 360));
       	$this->addLeftSystem(new Thruster(4, 11, 0, 6, 3));


        $this->addRightSystem(new AssaultLaser(3, 6, 4, 0, 120));
        $this->addRightSystem(new CustomMatterStream(3, 0, 0, 300, 120));
        $this->addRightSystem(new CustomGatlingMattergunLight(2, 0, 0, 0, 180));
        $this->addRightSystem(new CustomGatlingMattergunLight(2, 0, 0, 0, 180));
 		$this->addRightSystem(new Thruster(4, 11, 0, 6, 4));
 		      
        $this->addPrimarySystem(new Structure( 4, 44));


	//d20 hit chart
		$this->hitChart = array(
			
			0=> array(
				8 => "Thruster",
				11 => "Heavy Gatling Mattergun",
				13 => "Scanner",
				15 => "Engine",
				17 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),

			3=> array(
				4 => "Thruster",
				6 => "Assault Laser",
				8 => "Matter Stream",
				10 => "Light Gatling Mattergun",
				16 => "Structure",
				20 => "Primary",
			),

			4=> array(
				4 => "Thruster",
				6 => "Assault Laser",
				8 => "Matter Stream",
				10 => "Light Gatling Mattergun",
				16 => "Structure",
				20 => "Primary",
			),


		);


    }
}
?>