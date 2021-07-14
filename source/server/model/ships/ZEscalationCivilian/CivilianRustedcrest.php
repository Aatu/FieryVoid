<?php
class CivilianRustedcrest extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 225;
	$this->faction = "ZEscalation Civilian";
        $this->phpclass = "CivilianRustedcrest";
        $this->imagePath = "img/ships/EscalationWars/CivilianRustedcrest.png";
        $this->shipClass = "Rustedcrest Ore Barge";
        $this->shipSizeClass = 3;
		$this->canvasSize = 175; //img has 200px per side
		$this->unofficial = true;

 		$this->notes = 'Kastan';

		$this->isd = 1916;
        
        $this->forwardDefense = 16;
        $this->sideDefense = 18;
        
        $this->turncost = 1.0;
        $this->turndelaycost = 1.0;
        $this->accelcost = 3;
        $this->rollcost = 4;
        $this->pivotcost = 4;
        $this->iniativebonus = -30;
        
        $this->addPrimarySystem(new Reactor(3, 15, 0, 0));
        $this->addPrimarySystem(new CnC(4, 9, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 12, 2, 3));
        $this->addPrimarySystem(new Engine(3, 13, 0, 6, 3));
		$this->addPrimarySystem(new Hangar(3, 6));
		$this->addPrimarySystem(new CargoBay(2, 200));
   
        $this->addFrontSystem(new Thruster(2, 13, 0, 3, 1));
        $this->addFrontSystem(new Thruster(2, 13, 0, 3, 1));
		$this->addFrontSystem(new EWLaserBolt(1, 4, 2, 300, 60));

        $this->addAftSystem(new Thruster(2, 21, 0, 6, 2));
        $this->addAftSystem(new EWLaserBolt(1, 4, 2, 180, 300));
        $this->addAftSystem(new EWLaserBolt(1, 4, 2, 60, 180));

        $this->addLeftSystem(new EWLaserBolt(1, 4, 2, 240, 360));
        $this->addLeftSystem(new Thruster(2, 13, 0, 3, 3));
		
        $this->addRightSystem(new EWLaserBolt(1, 4, 2, 0, 120));
        $this->addRightSystem(new Thruster(2, 13, 0, 3, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(3, 30));
        $this->addAftSystem(new Structure(3, 30));
        $this->addLeftSystem(new Structure(3, 40));
        $this->addRightSystem(new Structure(3, 40));
        $this->addPrimarySystem(new Structure(4, 38));
		
		$this->hitChart = array(
			0=> array(
					7 => "Structure",
					12 => "Cargo Bay",
					14 => "Scanner",
					16 => "Engine",
					18 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					6 => "Thruster",
					8 => "Laser Bolt",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					9 => "Laser Bolt",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					6 => "Thruster",
					8 => "Laser Bolt",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					6 => "Thruster",
					8 => "Laser Bolt",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
