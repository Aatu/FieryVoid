<?php
class ChoukaReprisalCarrier extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 415;
	$this->faction = "ZEscalation Chouka Theocracy";
        $this->phpclass = "ChoukaReprisalCarrier";
        $this->imagePath = "img/ships/EscalationWars/ChoukaVengeance.png";
        $this->shipClass = "Reprisal Heavy Carrier";
			$this->variantOf = "Vengeance Heavy Cruiser";
			$this->occurence = "uncommon";		
        $this->shipSizeClass = 3;
		$this->canvasSize = 160; //img has 200px per side
		$this->unofficial = true;

        $this->fighters = array("normal"=>24);

		$this->isd = 1930;
        
        $this->forwardDefense = 15;
        $this->sideDefense = 15;
        
        $this->turncost = 1.0;
        $this->turndelaycost = 1.0;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(4, 15, 0, 0));
        $this->addPrimarySystem(new CnC(4, 15, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 15, 6, 6));
        $this->addPrimarySystem(new Engine(4, 14, 0, 9, 4));
		$this->addPrimarySystem(new Hangar(4, 14));
   
        $this->addFrontSystem(new Thruster(3, 15, 0, 6, 1));
		$this->addFrontSystem(new EWPointPlasmaGun(2, 3, 1, 240, 60));
		$this->addFrontSystem(new EWPointPlasmaGun(2, 3, 1, 300, 120));
		$this->addFrontSystem(new EWPointPlasmaGun(2, 3, 1, 270, 90));

        $this->addAftSystem(new Thruster(2, 6, 0, 2, 2));
        $this->addAftSystem(new Thruster(2, 6, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 16, 0, 5, 2));
		$this->addAftSystem(new JumpEngine(3, 10, 4, 40));
		$this->addAftSystem(new MediumPlasma(3, 5, 3, 120, 240));
		$this->addAftSystem(new MediumPlasma(3, 5, 3, 120, 240));
		$this->addAftSystem(new EWPointPlasmaGun(2, 3, 1, 120, 300));
		$this->addAftSystem(new EWPointPlasmaGun(2, 3, 1, 60, 240));

        $this->addLeftSystem(new MediumPlasma(3, 5, 3, 240, 360));
        $this->addLeftSystem(new Hangar(3, 6));
        $this->addLeftSystem(new Thruster(2, 13, 0, 4, 3));
		$this->addLeftSystem(new EWPointPlasmaGun(2, 3, 1, 180, 360));
		$this->addLeftSystem(new EWPointPlasmaGun(2, 3, 1, 180, 360));
		
        $this->addRightSystem(new MediumPlasma(3, 5, 3, 0, 120));
        $this->addRightSystem(new Hangar(3, 6));
        $this->addRightSystem(new Thruster(2, 13, 0, 4, 4));
		$this->addRightSystem(new EWPointPlasmaGun(2, 3, 1, 0, 180));
		$this->addRightSystem(new EWPointPlasmaGun(2, 3, 1, 0, 180));		

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 40));
        $this->addAftSystem(new Structure(3, 35));
        $this->addLeftSystem(new Structure(3, 44));
        $this->addRightSystem(new Structure(3, 44));
        $this->addPrimarySystem(new Structure(4, 40));
		
		$this->hitChart = array(
			0=> array(
					10 => "Structure",
					12 => "Scanner",
					14 => "Engine",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					5 => "Thruster",
					9 => "Point Plasma Gun",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					8 => "Jump Engine",
					10 => "Medium Plasma Cannon",
					12 => "Point Plasma Gun",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					4 => "Thruster",
					6 => "Medium Plasma Cannon",
					8 => "Hangar",
					11 => "Point Plasma Gun",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					4 => "Thruster",
					6 => "Medium Plasma Cannon",
					8 => "Hangar",
					11 => "Point Plasma Gun",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
