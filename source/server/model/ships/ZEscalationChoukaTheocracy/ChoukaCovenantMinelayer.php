<?php
class ChoukaCovenantMinelayer extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 500;
	$this->faction = "ZEscalation Chouka Theocracy";
        $this->phpclass = "ChoukaCovenantMinelayer";
        $this->imagePath = "img/ships/EscalationWars/ChoukaCovenant.png";
        $this->shipClass = "Covenant Minelayer";
        $this->shipSizeClass = 3;
		$this->canvasSize = 160; //img has 200px per side
		$this->unofficial = true;
        $this->limited = 10;

		$this->isd = 1957;
        
        $this->forwardDefense = 16;
        $this->sideDefense = 16;
        
        $this->turncost = 1.0;
        $this->turndelaycost = 1.0;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 2;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(3, 18, 0, 0));
        $this->addPrimarySystem(new CnC(4, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 12, 6, 6));
        $this->addPrimarySystem(new Engine(3, 15, 0, 9, 4));
		$this->addPrimarySystem(new Hangar(3, 12));
   
        $this->addFrontSystem(new Thruster(2, 11, 0, 4, 1));
        $this->addFrontSystem(new Thruster(2, 11, 0, 4, 1));
		$this->addFrontSystem(new EWPointPlasmaGun(2, 3, 1, 270, 90));
		$this->addFrontSystem(new EWPointPlasmaGun(2, 3, 1, 270, 90));
		$this->addFrontSystem(new LightEnergyMine(2, 3, 2, 300, 60));
		$this->addFrontSystem(new LightEnergyMine(2, 3, 2, 300, 60));
		$this->addFrontSystem(new LightEnergyMine(2, 3, 2, 300, 60));

        $this->addAftSystem(new Thruster(2, 21, 0, 9, 2));
		$this->addAftSystem(new LightEnergyMine(2, 3, 2, 120, 240));
		$this->addAftSystem(new EWPointPlasmaGun(2, 3, 1, 90, 270));
		$this->addAftSystem(new EWPointPlasmaGun(2, 3, 1, 90, 270));
		$this->addAftSystem(new CargoBay(2, 24));
		$this->addAftSystem(new CargoBay(2, 24));

        $this->addLeftSystem(new HeavyPlasma(3, 8, 5, 240, 360));
		$this->addLeftSystem(new LightEnergyMine(2, 3, 2, 240, 360));
		$this->addLeftSystem(new LightEnergyMine(2, 3, 2, 240, 360));
        $this->addLeftSystem(new Thruster(2, 15, 0, 3, 3));
		$this->addLeftSystem(new CargoBay(2, 30));
		
        $this->addRightSystem(new HeavyPlasma(3, 8, 5, 0, 120));
		$this->addRightSystem(new LightEnergyMine(2, 3, 2, 0, 120));
		$this->addRightSystem(new LightEnergyMine(2, 3, 2, 0, 120));
        $this->addRightSystem(new Thruster(2, 15, 0, 3, 4));
		$this->addRightSystem(new CargoBay(2, 30));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(3, 60));
        $this->addAftSystem(new Structure(3, 60));
        $this->addLeftSystem(new Structure(3, 64));
        $this->addRightSystem(new Structure(3, 64));
        $this->addPrimarySystem(new Structure(3, 68));
		
		$this->hitChart = array(
			0=> array(
					9 => "Structure",
					11 => "Scanner",
					14 => "Engine",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					8 => "Light Energy Mine",
					10 => "Point Plasma Gun",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					7 => "Light Energy Mine",
					10 => "Cargo Bay",
					12 => "Point Plasma Gun",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					3 => "Thruster",
					5 => "Heavy Plasma Cannon",
					8 => "Light Energy Mine",
					11 => "Cargo Bay",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					3 => "Thruster",
					5 => "Heavy Plasma Cannon",
					8 => "Light Energy Mine",
					11 => "Cargo Bay",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
