<?php
class ChoukaInquisitorLightCruiser extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 375;
	$this->faction = "ZEscalation Chouka Theocracy";
        $this->phpclass = "ChoukaInquisitorLightCruiser";
        $this->imagePath = "img/ships/EscalationWars/ChoukaInquisitor.png";
        $this->shipClass = "Inquisitor Light Cruiser";
        $this->shipSizeClass = 3;
		$this->canvasSize = 160; 
		$this->unofficial = true;

        $this->fighters = array("normal"=>6);

		$this->isd = 1921;
        
        $this->forwardDefense = 14;
        $this->sideDefense = 16;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(3, 13, 0, 0));
        $this->addPrimarySystem(new CnC(4, 11, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 13, 5, 5));
        $this->addPrimarySystem(new Engine(3, 13, 0, 9, 4));
		$this->addPrimarySystem(new Hangar(3, 8));
		
        $this->addFrontSystem(new Thruster(3, 10, 0, 6, 1));
		$this->addFrontSystem(new MediumPlasma(3, 5, 3, 300, 60));
		$this->addFrontSystem(new MediumPlasma(3, 5, 3, 300, 60));
		$this->addFrontSystem(new MediumPlasma(3, 5, 3, 300, 60));
		$this->addFrontSystem(new MediumPlasma(3, 5, 3, 300, 60));
		$this->addFrontSystem(new EWPointPlasmaGun(2, 3, 2, 270, 90));
		
		$this->addAftSystem(new Thruster(2, 6, 0, 1, 2));
		$this->addAftSystem(new Thruster(2, 6, 0, 1, 2));
		$this->addAftSystem(new Thruster(2, 6, 0, 1, 2));
		$this->addAftSystem(new Thruster(2, 6, 0, 1, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 5, 2));
		$this->addAftSystem(new EWPointPlasmaGun(2, 3, 2, 180, 360));
		$this->addAftSystem(new EWPointPlasmaGun(2, 3, 2, 0, 180));

        $this->addLeftSystem(new LightPlasma(1, 4, 2, 240, 360));
        $this->addLeftSystem(new LightPlasma(1, 4, 2, 180, 300));
		$this->addLeftSystem(new EWPointPlasmaGun(2, 3, 2, 180, 360));
        $this->addLeftSystem(new Thruster(3, 11, 0, 3, 3));

        $this->addRightSystem(new LightPlasma(1, 4, 2, 0, 120));
        $this->addRightSystem(new LightPlasma(1, 4, 2, 60, 180));
		$this->addRightSystem(new EWPointPlasmaGun(2, 3, 2, 0, 180));
        $this->addRightSystem(new Thruster(3, 11, 0, 3, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(3, 32));
        $this->addAftSystem(new Structure(3, 30));
        $this->addLeftSystem(new Structure(3, 30));
        $this->addRightSystem(new Structure(3, 30));
        $this->addPrimarySystem(new Structure(4, 30));
		
		$this->hitChart = array(
			0=> array(
					10 => "Structure",
					12 => "Scanner",
					15 => "Engine",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					8 => "Medium Plasma Cannon",
					10 => "Point Plasma Gun",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					8 => "Thruster",
					11 => "Point Plasma Gun",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					5 => "Thruster",
					8 => "Light Plasma Cannon",
					10 => "Point Plasma Gun",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					5 => "Thruster",
					8 => "Light Plasma Cannon",
					10 => "Point Plasma Gun",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
