<?php
class ChoukaScriptureScout extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 350;
	$this->faction = "ZEscalation Chouka";
        $this->phpclass = "ChoukaScriptureScout";
        $this->imagePath = "img/ships/EscalationWars/ChoukaScripture.png";
        $this->shipClass = "Scripture Intelligence Cruiser";
        $this->shipSizeClass = 3;
		$this->canvasSize = 150; //img has 200px per side
		$this->unofficial = true;
        $this->limited = 33;

	$this->isd = 1940;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 14;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 2*5;
        
        $this->addPrimarySystem(new Reactor(4, 10, 0, 0));
        $this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new ELINTScanner(4, 16, 6, 6));
        $this->addPrimarySystem(new Engine(4, 13, 0, 10, 3));
		$this->addPrimarySystem(new Hangar(4, 2));
   
        $this->addFrontSystem(new Thruster(3, 13, 0, 5, 1));
		$this->addFrontSystem(new HeavyPlasma(3, 8, 5, 300, 60));
		$this->addFrontSystem(new EWPointPlasmaGun(2, 3, 1, 180, 360));
		$this->addFrontSystem(new EWPointPlasmaGun(2, 3, 1, 0, 180));

        $this->addAftSystem(new Thruster(3, 15, 0, 10, 2));
		$this->addAftSystem(new LightLaser(1, 4, 3, 180, 360));
		$this->addAftSystem(new LightLaser(1, 4, 3, 0, 180));
        $this->addAftSystem(new JumpEngine(3, 10, 3, 36));

        $this->addLeftSystem(new LightPlasma(2, 4, 2, 240, 360));
		$this->addLeftSystem(new EWPointPlasmaGun(2, 3, 1, 180, 360));
        $this->addLeftSystem(new Thruster(3, 11, 0, 3, 3));

        $this->addRightSystem(new LightPlasma(2, 4, 2, 0, 120));
		$this->addRightSystem(new EWPointPlasmaGun(2, 3, 1, 0, 180));
        $this->addRightSystem(new Thruster(3, 11, 0, 3, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(3, 30));
        $this->addAftSystem(new Structure(3, 32));
        $this->addLeftSystem(new Structure(3, 30));
        $this->addRightSystem(new Structure(3, 30));
        $this->addPrimarySystem(new Structure(4, 32));
		
		$this->hitChart = array(
			0=> array(
					10 => "Structure",
					13 => "ELINT Scanner",
					15 => "Engine",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					5 => "Thruster",
					7 => "Heavy Plasma Cannon",
					9 => "Point Plasma Gun",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					8 => "Light Laser",
					10 => "Jump Engine",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					5 => "Thruster",
					8 => "Light Plasma Cannon",
					9 => "Point Plasma Gun",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					5 => "Thruster",
					8 => "Light Plasma Cannon",
					9 => "Point Plasma Gun",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
