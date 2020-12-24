<?php
class ChoukaRadianceExplorer extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 480;
	$this->faction = "ZEscalation Chouka";
        $this->phpclass = "ChoukaRadianceExplorer";
        $this->imagePath = "img/ships/EscalationWars/ChoukaRadianceExplorer.png";
        $this->shipClass = "Radiance Explorer";
        $this->shipSizeClass = 3;
		$this->canvasSize = 200; //img has 200px per side
		$this->unofficial = true;
        $this->limited = 10;

        $this->fighters = array("normal"=>6);


	$this->isd = 1936;
        
        $this->forwardDefense = 14;
        $this->sideDefense = 17;
        
        $this->turncost = 1.5;
        $this->turndelaycost = 1.33;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 4;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(3, 15, 0, 0));
        $this->addPrimarySystem(new CnC(4, 20, 0, 0));
        $this->addPrimarySystem(new ELINTScanner(3, 25, 8, 8));
        $this->addPrimarySystem(new Engine(3, 18, 0, 12, 5));
		$this->addPrimarySystem(new Hangar(3, 10));
		
        $this->addFrontSystem(new Thruster(3, 16, 0, 6, 1));
		$this->addFrontSystem(new MediumPlasma(2, 5, 3, 300, 360));
		$this->addFrontSystem(new MediumPlasma(2, 5, 3, 0, 60));

        $this->addAftSystem(new Thruster(2, 7, 0, 3, 2));
        $this->addAftSystem(new Thruster(2, 7, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 6, 2));
        $this->addAftSystem(new JumpEngine(3, 16, 4, 36));
		$this->addAftSystem(new LightLaser(1, 4, 3, 180, 300));
		$this->addAftSystem(new LightLaser(1, 4, 3, 60, 180));

        $this->addLeftSystem(new LightLaser(2, 4, 3, 180, 360));
        $this->addLeftSystem(new LightLaser(2, 4, 3, 180, 360));
        $this->addLeftSystem(new EWPointPlasmaGun(2, 3, 1, 180, 360));
        $this->addLeftSystem(new Thruster(2, 13, 0, 6, 3));

        $this->addRightSystem(new LightLaser(2, 4, 3, 0, 180));
        $this->addRightSystem(new LightLaser(2, 4, 3, 0, 180));
        $this->addRightSystem(new EWPointPlasmaGun(2, 3, 1, 0, 180));
        $this->addRightSystem(new Thruster(2, 13, 0, 6, 4));


        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(3, 70));
        $this->addAftSystem(new Structure(3, 40));
        $this->addLeftSystem(new Structure(3, 52));
        $this->addRightSystem(new Structure(3, 52));
        $this->addPrimarySystem(new Structure(3, 50));
		
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
					4 => "Thruster",
					7 => "Medium Plasma Cannon",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					8 => "Light Laser",
					11 => "Jump Engine",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					4 => "Thruster",
					7 => "Light Laser",
					8 => "Point Plasma Gun",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					4 => "Thruster",
					7 => "Light Laser",
					8 => "Point Plasma Gun",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
