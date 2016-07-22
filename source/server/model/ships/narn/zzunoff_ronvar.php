<?php
class Ronvar extends HeavyCombatVessel{
	/*Narn Ronvar Pulsar Destroyer, Showdowns-10 (unofficial)*/
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 475;
        $this->faction = "Narn";
        $this->phpclass = "Ronvar";
        $this->imagePath = "img/ships/rongoth.png";
        $this->shipClass = "Ronvar Pulsar Destroyer";
        
        //$this->limited = 33;
	$this->occurence = "uncommon";
	$this->isd = 2238;
	$this->unofficial = true;

        $this->forwardDefense = 12;
        $this->sideDefense = 14;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.5;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
        $this->iniativebonus = 40;
                 
        $this->addPrimarySystem(new Reactor(5, 13, 0, 0));
        $this->addPrimarySystem(new CnC(5, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 18, 3, 7));
        $this->addPrimarySystem(new Engine(5, 16, 0, 12, 2));
        $this->addPrimarySystem(new Hangar(4, 2));
        $this->addPrimarySystem(new Thruster(4, 15, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(4, 15, 0, 4, 4));
        
        $this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));
        $this->addFrontSystem(new TwinArray(3, 6, 2, 240, 60));
        $this->addFrontSystem(new TwinArray(3, 6, 2, 300, 120));
        $this->addFrontSystem(new QuadPulsar(5, 10, 4, 300, 60));
        $this->addFrontSystem(new QuadPulsar(5, 10, 4, 300, 60));
        
        $this->addAftSystem(new Thruster(4, 6, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 6, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 6, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 6, 0, 3, 2));
        $this->addAftSystem(new ScatterPulsar(3, 4, 2, 120, 300));
        $this->addAftSystem(new ScatterPulsar(3, 4, 2, 60, 240));
        $this->addAftSystem(new TwinArray(3, 6, 2, 120, 300));
        $this->addAftSystem(new TwinArray(3, 6, 2, 60, 240));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 50));
        $this->addAftSystem(new Structure( 4, 50));
        $this->addPrimarySystem(new Structure( 5, 34));
		


		$this->hitChart = array(
			0=> array(
				7 => "Structure",
				11 => "Thruster",
				13 => "Scanner",
				15 => "Engine",
				17 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				5 => "Thruster",
				9 => "Quad Pulsar",
				12 => "Twin Array",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				7 => "Thruster",
				9 => "Twin Array",
				11 => "Scatter Pulsar",
				18 => "Structure",
				20 => "Primary",
			),
		); 


    }
}
?>
