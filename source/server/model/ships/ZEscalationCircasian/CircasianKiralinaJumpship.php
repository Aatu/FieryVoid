<?php
class CircasianKiralinaJumpship extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 700;
		$this->faction = "ZEscalation Circasian";
        $this->phpclass = "CircasianKiralinaJumpship";
        $this->imagePath = "img/ships/EscalationWars/CircasianKiralina.png";
			$this->canvasSize = 300; //img has 300px per side
		$this->unofficial = true;
        $this->shipClass = "Kiralina Jump Ship";
        $this->fighters = array("normal"=>12);
			$this->limited = 10;
		$this->isd = 1918;
	    
        $this->Enormous = true;
        $this->forwardDefense = 17;
        $this->sideDefense = 20;
		
        $this->turncost = 1.5;
        $this->turndelaycost = 1.5;
        $this->accelcost = 4;
        $this->rollcost = 5; 
        $this->pivotcost = 8;    

        $this->iniativebonus = -2*5; //-2 Ini
         
        $this->addPrimarySystem(new Reactor(4, 20, 0, 0));
        $this->addPrimarySystem(new CnC(4, 28, 0, 0));
        $this->addPrimarySystem(new ElintScanner(3, 25, 6, 7));
        $this->addPrimarySystem(new Engine(4, 18, 0, 8, 5));
		$this->addPrimarySystem(new Hangar(3, 18));
		$this->addPrimarySystem(new JumpEngine(4, 20, 5, 36));

        $this->addFrontSystem(new Thruster(3, 15, 0, 2, 1));
        $this->addFrontSystem(new Thruster(3, 15, 0, 2, 1));
		$this->addFrontSystem(new CargoBay(2, 30));
		$this->addFrontSystem(new CargoBay(2, 30));
		$this->addFrontSystem(new EWRocketLauncher(2, 4, 1, 270, 90));
		$this->addFrontSystem(new EWRocketLauncher(2, 4, 1, 270, 90));
		$this->addFrontSystem(new LightRailGun(1, 6, 3, 240, 60));
		$this->addFrontSystem(new LightRailGun(1, 6, 3, 300, 120));

        $this->addAftSystem(new Thruster(2, 16, 0, 2, 2));
        $this->addAftSystem(new Thruster(2, 16, 0, 2, 2));
        $this->addAftSystem(new Thruster(2, 16, 0, 2, 2));
        $this->addAftSystem(new Thruster(2, 16, 0, 2, 2));
		$this->addAftSystem(new LightRailGun(1, 6, 3, 180, 360));
		$this->addAftSystem(new LightRailGun(1, 6, 3, 120, 300));
		$this->addAftSystem(new LightRailGun(1, 6, 3, 60, 240));
		$this->addAftSystem(new LightRailGun(1, 6, 3, 0, 180));
		$this->addAftSystem(new CargoBay(2, 50));
        
		$this->addLeftSystem(new Thruster(2, 20, 0, 6, 3));
		$this->addLeftSystem(new CargoBay(3, 30));
		$this->addLeftSystem(new EWRocketLauncher(1, 4, 1, 180, 360));
		$this->addLeftSystem(new EWRocketLauncher(1, 4, 1, 180, 360));
		$this->addLeftSystem(new EWRocketLauncher(1, 4, 1, 180, 360));
		
		$this->addRightSystem(new Thruster(2, 20, 0, 6, 4));
		$this->addRightSystem(new CargoBay(3, 30));
		$this->addRightSystem(new EWRocketLauncher(1, 4, 1, 0, 180));
		$this->addRightSystem(new EWRocketLauncher(1, 4, 1, 0, 180));
		$this->addRightSystem(new EWRocketLauncher(1, 4, 1, 0, 180));


        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 3, 60));
        $this->addAftSystem(new Structure( 3, 62));
        $this->addLeftSystem(new Structure( 3, 90));
        $this->addRightSystem(new Structure( 3, 90));
        $this->addPrimarySystem(new Structure( 3, 84));
		
        $this->hitChart = array(
			0=> array(
					8 => "Structure",
					11 => "Jump Engine",
					13 => "ELINT Scanner",
					15 => "Engine",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					6 => "Rocket Launcher",
					8 => "Light Railgun",
					11 => "Cargo Bay",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					8 => "Light Railgun",
					11 => "Cargo Bay",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					5 => "Thruster",
					8 => "Rocket Launcher",
					11 => "Cargo Bay",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					5 => "Thruster",
					8 => "Rocket Launcher",
					11 => "Cargo Bay",
					18 => "Structure",
					20 => "Primary",
             ),
        );
    }
}

?>
