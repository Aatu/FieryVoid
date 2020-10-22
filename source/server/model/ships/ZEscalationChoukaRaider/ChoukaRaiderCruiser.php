<?php
class ChoukaRaiderCruiser extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 375;
		$this->faction = "ZEscalation Chouka Raider";
        $this->phpclass = "ChoukaRaiderCruiser";
        $this->imagePath = "img/ships/EscalationWars/ChoukaRaiderCruiser.png";
        $this->shipClass = "Hand of God Raiding Cruiser";
        $this->shipSizeClass = 3;
		$this->canvasSize = 175; //img has 200px per side
		$this->unofficial = true;
        $this->limited = 10;

        $this->fighters = array("normal"=>24);


	$this->isd = 1943;
        
        $this->forwardDefense = 15;
        $this->sideDefense = 15;
        
        $this->turncost = 1.0;
        $this->turndelaycost = 1.0;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(3, 15, 0, 0));
        $this->addPrimarySystem(new CnC(4, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 13, 4, 6));
        $this->addPrimarySystem(new Engine(3, 14, 0, 9, 5));
		$this->addPrimarySystem(new Hangar(3, 14));
		
   
        $this->addFrontSystem(new Thruster(2, 15, 0, 4, 1));
		$this->addFrontSystem(new MediumPlasma(3, 5, 3, 300, 60));
		$this->addFrontSystem(new EWPointPlasmaGun(2, 3, 1, 240, 60));
		$this->addFrontSystem(new EWPointPlasmaGun(2, 3, 1, 300, 120));

        $this->addAftSystem(new Thruster(1, 3, 0, 2, 2));
        $this->addAftSystem(new Thruster(1, 3, 0, 2, 2));
        $this->addAftSystem(new Thruster(1, 18, 0, 6, 2));
		$this->addAftSystem(new CargoBay(1, 18));
		$this->addAftSystem(new Hangar(2, 6));
		$this->addAftSystem(new Hangar(2, 6));
		$this->addAftSystem(new EWPointPlasmaGun(2, 3, 1, 120, 300));
		$this->addAftSystem(new EWPointPlasmaGun(2, 3, 1, 60, 240));


        $this->addLeftSystem(new MediumPlasma(3, 5, 3, 240, 360));
        $this->addLeftSystem(new LightPlasma(1, 4, 2, 240, 60));
        $this->addLeftSystem(new Thruster(2, 11, 0, 4, 3));
		$this->addLeftSystem(new EWPointPlasmaGun(2, 3, 1, 180, 360));
		$this->addLeftSystem(new CargoBay(1, 24));
		
		
        $this->addRightSystem(new MediumPlasma(3, 5, 3, 0, 120));
        $this->addRightSystem(new LightPlasma(1, 4, 2, 300, 120));
        $this->addRightSystem(new Thruster(2, 11, 0, 4, 4));
		$this->addRightSystem(new EWPointPlasmaGun(2, 3, 1, 0, 180));
		$this->addRightSystem(new CargoBay(1, 24));		


        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(3, 35));
        $this->addAftSystem(new Structure(3, 35));
        $this->addLeftSystem(new Structure(3, 30));
        $this->addRightSystem(new Structure(3, 30));
        $this->addPrimarySystem(new Structure(3, 35));
		
		$this->hitChart = array(
			0=> array(
					8 => "Structure",
					11 => "Scanner",
					13 => "Engine",
					16 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					5 => "Thruster",
					7 => "Medium Plasma Cannon",
					9 => "Point Plasma Gun",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					5 => "Thruster",
					8 => "Cargo Bay",
					10 => "Hangar",
					11 => "Point Plasma Gun",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					4 => "Thruster",
					5 => "Medium Plasma Cannon",
					6 => "Light Plasma Cannon",
					7 => "Point Plasma Gun",
					9 => "Cargo Bay",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					4 => "Thruster",
					5 => "Medium Plasma Cannon",
					6 => "Light Plasma Cannon",
					7 => "Point Plasma Gun",
					9 => "Cargo Bay",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
