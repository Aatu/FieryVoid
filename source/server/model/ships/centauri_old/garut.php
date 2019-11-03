<?php
class Garut extends BaseShip{
/*Wolfgang Lackner's ongoing campaign*/
/*Centauri Garut Survey Ship, refit ISD 1966*/
/*OFFICIAL (WoCR) enormous ship implemented as a capital*/

    	function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 750;
        $this->faction = "Centauri (WotCR)";
        $this->phpclass = "Garut";
        $this->shipClass = "Garut Survey Ship";
	$this->limited = 10;   
        //$this->occurence = "rare"; 
        $this->fighters = array("heavy"=>18); 
		$this->isd = 1966;
		//$this->unofficial = true; //this is an official ship, just Enormous - no reason to call it unofficial!
		$this->notes = "Not a combat ship (->not eligible for pickup battles)";

        $this->shipSizeClass = 3; //Enormous is not implemented
        $this->forwardDefense = 19;
        $this->sideDefense = 20;
	$this->iniativebonus = 0;

        $this->imagePath = "img/ships/sakar.png";
	
        $this->Enormous = true;


        $this->turncost = 2;
        $this->turndelaycost = 2;
        $this->accelcost = 8;
        $this->rollcost = 6;
        $this->pivotcost = 6;
       
        $this->addPrimarySystem(new Reactor(6, 25, 0, 0));
        $this->addPrimarySystem(new CnC(6, 25, 0, 0)); 
        $this->addPrimarySystem(new ElintScanner(5, 30, 6, 10));
        $this->addPrimarySystem(new Engine(5, 36, 0, 12, 3));
	$this->addPrimarySystem(new Hangar(5, 8));
	$this->addPrimarySystem(new ImperialLaser(4, 8, 6, 0, 360));
        
	$this->addFrontSystem(new Thruster(3, 20, 0, 5, 1));
        $this->addFrontSystem(new Thruster(3, 20, 0, 5, 1));
	$this->addFrontSystem(new Hangar(3, 18));
        $this->addFrontSystem(new TacLaser(3, 5, 4, 240, 360));
	$this->addFrontSystem(new TacLaser(3, 5, 4, 0, 120));
	$this->addFrontSystem(new SentinelPointDefense(2, 4, 2, 240, 60));
	$this->addFrontSystem(new SentinelPointDefense(2, 4, 2, 300, 120));
		
        $this->addAftSystem(new Thruster(4, 16, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 16, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 16, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 16, 0, 3, 2));
	$this->addAftSystem(new JumpEngine(5, 32, 6, 20));
	$this->addAftSystem(new SentinelPointDefense(2, 4, 2, 90, 270));
		
	$this->addLeftSystem(new Thruster(4, 30, 0, 8, 3));
	$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
	$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
	$this->addLeftSystem(new CargoBay(3, 64));
		
	$this->addRightSystem(new Thruster(4, 30, 0, 8, 4));
	$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
	$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
	$this->addRightSystem(new CargoBay(3, 64));
		
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
			10 => "Imperial Laser",
			13 => "ELINT Scanner",
			15 => "Engine",
			17 => "Hangar",
			19 => "Reactor",
			20 => "C&C",
		),

		1=> array(
			5 => "Thruster",
			7 => "Tactical Laser",
			9 => "Sentinel Point Defense",
			11 => "Hangar",
			18 => "Structure",
			20 => "Primary",
		),

		2=> array(
			5 => "Thruster",
			8 => "Jump Engine",
			9 => "Sentinel Point Defense",
			18 => "Structure",
			20 => "Primary",
		),

		3=> array(
			5 => "Thruster",
			7 => "Light Particle Beam",
			10 => "Cargo Bay",
			18 => "Structure",
			20 => "Primary",
		),

		4=> array(
			5 => "Thruster",
			7 => "Light Particle Beam",
			10 => "Cargo Bay",
			18 => "Structure",
			20 => "Primary",
		),

	);


    }
}
