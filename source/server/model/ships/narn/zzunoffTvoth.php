<?php
class zzunoffTVoth extends BaseShip{
    	/*Narn G'Voth Command Cruiser, Showdowns-10 (unofficial)*/

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 600;
		$this->faction = "Narn";
        $this->phpclass = "zzunoffTVoth";
        $this->imagePath = "img/ships/tloth.png";
        $this->shipClass = "T'Voth Command Cruiser";
        $this->fighters = array("normal"=>12);
        

	$this->occurence = "rare";
	$this->isd = 2231;
	$this->unofficial = true;


        $this->forwardDefense = 16;
        $this->sideDefense = 18;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 2;
        $this->iniativebonus = 5; //+1 Init


        $this->addPrimarySystem(new Reactor(5, 21, 0, 0));
        $this->addPrimarySystem(new CnC(5, 20, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 21, 3, 7));
        $this->addPrimarySystem(new Engine(5, 20, 0, 12, 3));
		$this->addPrimarySystem(new JumpEngine(5, 24, 3, 20));
		$this->addPrimarySystem(new Hangar(5, 26));
        
	//fwd
        $this->addFrontSystem(new EnergyPulsar(2, 6, 3, 270, 90));
        $this->addFrontSystem(new Thruster(4, 10, 0, 4, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 4, 1));
        $this->addFrontSystem(new EnergyPulsar(2, 6, 3, 270, 90));
      
		//aft
		$this->addAftSystem(new LightBolter(2, 6, 2, 90, 270));
		$this->addAftSystem(new LightBolter(2, 6, 2, 90, 270));
		$this->addAftSystem(new Thruster(4, 9, 0, 3, 2));
		$this->addAftSystem(new Thruster(4, 9, 0, 3, 2));
		$this->addAftSystem(new Thruster(4, 9, 0, 3, 2));
		$this->addAftSystem(new Thruster(4, 9, 0, 3, 2));
        
		//left
		$this->addLeftSystem(new ScatterPulsar(2, 4, 2, 180, 0));
		$this->addLeftSystem(new ImperialLaser(4, 8, 5, 300, 0));
		$this->addLeftSystem(new MediumBolter(4, 8, 4, 300, 0));
        $this->addLeftSystem(new Thruster(4, 15, 0, 4, 3));
              
		//right
		$this->addRightSystem(new ScatterPulsar(2, 4, 2, 0, 180));
		$this->addRightSystem(new ImperialLaser(4, 8, 5, 0, 60));
		$this->addRightSystem(new MediumBolter(4, 8, 4, 0, 60));
		$this->addRightSystem(new Thruster(4, 15, 0, 4, 4));

		//structures
        $this->addFrontSystem(new Structure(4, 44));
        $this->addAftSystem(new Structure(4, 48));
        $this->addLeftSystem(new Structure(4, 62));
        $this->addRightSystem(new Structure(4, 62));
        $this->addPrimarySystem(new Structure(5, 45));
		
		
		$this->hitChart = array(
			0=> array(
				9 => "Structure",
				10 => "Jump Engine",
				12 => "Scanner",
				14 => "Engine",
				17 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				5 => "Thruster",
				8 => "Energy Pulsar",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				10 => "Thruster",
				12 => "Light Bolter",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array(
				4 => "Thruster",
				6 => "Imperial Laser",
				7 => "Medium Bolter",
				9 => "Scatter Pulsar",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				4 => "Thruster",
				6 => "Imperial Laser",
				7 => "Medium Bolter",
				9 => "Scatter Pulsar",
				18 => "Structure",
				20 => "Primary",
			),
		);	
    }
}
?>
