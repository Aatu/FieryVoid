<?php
class wlcEmperorDN2090 extends BaseShip{
/*Wolfgang Lackner's ongoing campaign*/
/*Centauri Emperor Dreadnought, refit ISD 2090*/
/*enormous ship implemented as a capital*/
    
    	function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 1100;
        $this->faction = "Centauri (WotCR)";
        $this->phpclass = "wlcEmperorDN2090";
        $this->shipClass = "Emperor Dreadnought";
		$this->variantOf = "Garut Survey Ship";
	$this->limited = 10;   
        //$this->occurence = "rare"; 
        $this->fighters = array("heavy"=>24); 
		$this->unofficial = true;
		$this->isd = 2090;

        $this->shipSizeClass = 3; //Enormousis not implemented
        $this->Enormous = true;		
        $this->forwardDefense = 19;
        $this->sideDefense = 20;
	$this->iniativebonus = 0;

        $this->imagePath = "img/ships/sakar.png";



        $this->turncost = 2;
        $this->turndelaycost = 2;
        $this->accelcost = 8;
        $this->rollcost = 6;
        $this->pivotcost = 6;
       
        $this->addPrimarySystem(new Reactor(6, 36, 0, 0));
        //$this->addPrimarySystem(new CnC(6, 25, 0, 0)); //no dual bridges!!! - combine into one stronger instead
        //$this->addPrimarySystem(new CnC(7, 16, 0, 0)); 
	$this->addPrimarySystem(new CnC(7, 40, 0, 0)); //"combined" bridge
        $this->addPrimarySystem(new Scanner(5, 25, 6, 9));
        $this->addPrimarySystem(new Engine(5, 36, 0, 12, 3));
	$this->addPrimarySystem(new Hangar(5, 12));
	$this->addPrimarySystem(new AssaultLaser(4, 6, 4, 0, 360));
	$this->addPrimarySystem(new TwinArray(3, 6, 2, 0, 360));
        
	$this->addFrontSystem(new Thruster(3, 20, 0, 5, 1));
        $this->addFrontSystem(new Thruster(3, 20, 0, 5, 1));
	$this->addFrontSystem(new Hangar(3, 18));
        $this->addFrontSystem(new AssaultLaser(3, 6, 4, 240, 360));
	$this->addFrontSystem(new AssaultLaser(3, 6, 4, 300, 60));
	$this->addFrontSystem(new AssaultLaser(3, 6, 4, 300, 60));
	$this->addFrontSystem(new AssaultLaser(3, 6, 4, 0, 120));
        $this->addFrontSystem(new TwinArray(3, 6, 2, 240, 60));
	$this->addFrontSystem(new TwinArray(3, 6, 2, 300, 120));
	$this->addFrontSystem(new SentinelPointDefense(2, 4, 2, 240, 60));
	$this->addFrontSystem(new SentinelPointDefense(2, 4, 2, 300, 120));
		
        $this->addAftSystem(new Thruster(4, 16, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 16, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 16, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 16, 0, 3, 2));
	$this->addAftSystem(new JumpEngine(5, 32, 6, 20));
	$this->addAftSystem(new AssaultLaser(3, 6, 4, 120, 240));
	$this->addAftSystem(new AssaultLaser(3, 6, 4, 120, 240));
	$this->addAftSystem(new TwinArray(3, 6, 2, 120, 300));
	$this->addAftSystem(new TwinArray(3, 6, 2, 60, 240));
	$this->addAftSystem(new SentinelPointDefense(2, 4, 2, 90, 270));
		
	$this->addLeftSystem(new Thruster(4, 30, 0, 8, 3));
        $this->addLeftSystem(new AssaultLaser(3, 6, 4, 240, 360));
	$this->addLeftSystem(new TwinArray(3, 6, 2, 180, 360));
	$this->addLeftSystem(new TwinArray(3, 6, 2, 180, 360));
	$this->addLeftSystem(new CargoBay(3, 12));
		
	$this->addRightSystem(new Thruster(4, 30, 0, 8, 4));
        $this->addRightSystem(new AssaultLaser(3, 6, 4, 0, 120));
	$this->addRightSystem(new TwinArray(3, 6, 2, 0, 180));
	$this->addRightSystem(new TwinArray(3, 6, 2, 0, 180));
	$this->addRightSystem(new CargoBay(3, 12));
		
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 110));
        $this->addAftSystem(new Structure( 4, 140));
        $this->addLeftSystem(new Structure( 4, 120));
        $this->addRightSystem(new Structure( 4, 120));
        $this->addPrimarySystem(new Structure( 5, 112));


       
	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			8 => "Structure",
			9 => "Twin Array",
			10 => "Assault Laser",
			13 => "Scanner",
			15 => "Engine",
			17 => "Hangar",
			19 => "Reactor",
			20 => "C&C",
		),

		1=> array(
			5 => "Thruster",
			7 => "Assault Laser",
			9 => "Sentinel Point Defense",
			10 => "Twin Array",
			12 => "Hangar",
			18 => "Structure",
			20 => "Primary",
		),

		2=> array(
			5 => "Thruster",
			7 => "Jump Engine",
			8 => "Sentinel Point Defense",
			9 => "Twin Array",
			11 => "Assault Laser",
			18 => "Structure",
			20 => "Primary",
		),

		3=> array(
			5 => "Thruster",
			8 => "Twin Array",
			10 => "Assault Laser",
			11 => "CargoBay",
			18 => "Structure",
			20 => "Primary",
		),

		4=> array(
			5 => "Thruster",
			8 => "Twin Array",
			10 => "Assault Laser",
			11 => "Cargo Bay",
			18 => "Structure",
			20 => "Primary",
		),

	);


    }
}
