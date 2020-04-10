<?php
class wlcChlonasOnKashiDD extends HeavyCombatVesselLeftRight{
    /*Ch'Lonas On'Kashi destroyer*/
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 450;
        $this->phpclass = "wlcChlonasOnKashiDD";
        $this->imagePath = "img/ships/ChlonasEsKashi.png";
        $this->canvasSize = 200;
        $this->shipClass = "On'Kashi Support Destroyer";
        $this->fighters = array("light"=>6);
	    
	    
        $this->faction = "Ch'Lonas";
        $this->variantOf = "Es'Kashi Destroyer";
	    $this->occurence = "uncommon";
		$this->isd = 2247;
		$this->unofficial = true;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 14;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.50;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 35;
        
         
        $this->addPrimarySystem(new Reactor(5, 14, 0, 0));
        $this->addPrimarySystem(new CnC(5, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 13, 4, 6));
        $this->addPrimarySystem(new Engine(5, 11, 0, 8, 3));
        $this->addPrimarySystem(new Hangar(3, 10)); //for a flight of light fighters
        $this->addPrimarySystem(new Thruster(4, 15, 0, 4, 1));
        $this->addPrimarySystem(new Thruster(4, 21, 0, 8, 2));
        $this->addPrimarySystem(new CustomGatlingMattergunMedium(4, 0, 0, 270, 90));
        
        $this->addLeftSystem(new Thruster(4, 15, 0, 4, 3));
        $this->addLeftSystem(new CustomGatlingMattergunMedium(3, 0, 0, 240, 360));
        $this->addLeftSystem(new CustomGatlingMattergunLight(2, 0, 0, 180, 360));
        $this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
        $this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));

        $this->addRightSystem(new Thruster(4, 15, 0, 4, 4));
        $this->addRightSystem(new CustomGatlingMattergunMedium(3, 0, 0, 0, 120));
        $this->addRightSystem(new CustomGatlingMattergunLight(2, 0, 0, 0, 180));
        $this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
        $this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));

        
        
        
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addLeftSystem(new Structure( 4, 44));
        $this->addRightSystem(new Structure( 4, 44));
        $this->addPrimarySystem(new Structure( 5, 36 ));
        
        
		//d20 hit chart
		$this->hitChart = array(
			
			0=> array(
				5 => "Structure",
				7 => "Gatling Mattergun",
				11 => "Thruster",
				13 => "Scanner",
				15 => "Engine",
				17 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			3=> array(
				6 => "Thruster",
				9 => "Gatling Mattergun",
				10 => "Light Gatling Mattergun",
				12 => "Light Particle Beam",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				6 => "Thruster",
				9 => "Gatling Mattergun",
				10 => "Light Gatling Mattergun",
				12 => "Light Particle Beam",
				18 => "Structure",
				20 => "Primary",
			),
		);
    }
}
?>
