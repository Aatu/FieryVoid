<?php
class PalaceShip extends BaseShip{
/*Wolfgang Lackner's ongoing campaign*/
/*MiMaTau Palace Ship, refit ISD 2255*/
/*enormous ship implemented as a capital*/
    
    	function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 1100;
        $this->faction = "Llort";
        $this->phpclass = "palaceship";
        $this->shipClass = "Mi'Ma'Tau Palace Ship";
        $this->fighters = array("heavy"=>24); 
		$this->unofficial = true;
		$this->isd = 2255;
        $this->shipSizeClass = 3; //Enormous is not implemented
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
        $this->addPrimarySystem(new CnC(6, 25, 0, 0)); 
        $this->addPrimarySystem(new CnC(7, 16, 0, 0)); 
        $this->addPrimarySystem(new Scanner(5, 25, 6, 9));
        $this->addPrimarySystem(new Engine(5, 36, 0, 12, 3));
		$this->addPrimarySystem(new Hangar(5, 12));
		$this->addPrimarySystem(new MediumBolter(4, 8, 4, 0, 360));
		$this->addPrimarySystem(new TwinArray(3, 6, 2, 0, 360));
        
		$this->addFrontSystem(new Thruster(3, 20, 0, 5, 1));
        $this->addFrontSystem(new Thruster(3, 20, 0, 5, 1));
		$this->addFrontSystem(new Hangar(3, 18));
        $this->addFrontSystem(new IonTorpedo(3, 5, 4, 240, 360));
		$this->addFrontSystem(new IonTorpedo(3, 5, 4, 300, 60));
		$this->addFrontSystem(new IonTorpedo(3, 5, 4, 300, 60));
		$this->addFrontSystem(new IonTorpedo(3, 5, 4, 0, 120));
        $this->addFrontSystem(new ScatterGun(3, 8, 3, 240, 60));
		$this->addFrontSystem(new ScatterGun(3, 8, 3, 300, 120));
		$this->addFrontSystem(new StdParticleBeam(2, 4, 1, 240, 60));
		$this->addFrontSystem(new StdParticleBeam(2, 4, 1, 300, 120));
		
        $this->addAftSystem(new Thruster(4, 16, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 16, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 16, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 16, 0, 3, 2));
		$this->addAftSystem(new JumpEngine(5, 32, 6, 20));
		$this->addAftSystem(new MediumBolter(3, 8, 4, 120, 240));
		$this->addAftSystem(new MediumBolter(3, 8, 4, 120, 240));
		$this->addAftSystem(new ScatterGun(3, 8, 3, 120, 300));
		$this->addAftSystem(new ScatterGun(3, 8, 3, 60, 240));
		$this->addAftSystem(new StdParticleBeam(2, 4, 1, 90, 270));
		
		$this->addLeftSystem(new Thruster(4, 30, 0, 8, 3));
        $this->addLeftSystem(new MediumBolter(3, 8, 4, 240, 360));
		$this->addLeftSystem(new TwinArray(3, 6, 2, 180, 360));
		$this->addLeftSystem(new TwinArray(3, 6, 2, 180, 360));
		$this->addLeftSystem(new CargoBay(3, 12));
		
		$this->addRightSystem(new Thruster(4, 30, 0, 8, 4));
        $this->addRightSystem(new MediumBolter(3, 8, 4, 0, 120));
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
				10 => "Medium Bolter",
				13 => "Scanner",
				15 => "Engine",
				17 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				5 => "Thruster",
				7 => "Ion Torpedo",
				9 => "Standard Particle Beam",
				10 => "Scatter Gun",
				12 => "Hangar",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				5 => "Thruster",
				7 => "Jump Engine",
				8 => "Standard Particle Beam",
				9 => "Scatter Gun",
				11 => "Medium Bolter",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array(
				5 => "Thruster",
				8 => "Twin Array",
				10 => "Medium Bolter",
				11 => "CargoBay",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				5 => "Thruster",
				8 => "Twin Array",
				10 => "Medium Bolter",
				11 => "Cargo Bay",
				18 => "Structure",
				20 => "Primary",
			),
		);
    }
}

?>
