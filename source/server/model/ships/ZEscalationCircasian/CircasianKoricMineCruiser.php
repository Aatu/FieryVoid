<?php
class CircasianKoricMineCruiser extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 450;
	$this->faction = "ZEscalation Circasian";
        $this->phpclass = "CircasianKoricMineCruiser";
        $this->imagePath = "img/ships/EscalationWars/CircasianKolanis.png";
        $this->shipClass = "Koric Mine Carrier";
			$this->variantOf = "Kolanis Cruiser";
			$this->occurence = "common";
			$this->limited = 33;			
			$this->unofficial = true;
        $this->shipSizeClass = 3;
		$this->canvasSize = 175; //img has 200px per side


	$this->isd = 1992;
        
        $this->forwardDefense = 15;
        $this->sideDefense = 17;
        
        $this->turncost = 1.0;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 2;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(4, 19, 0, 0));
        $this->addPrimarySystem(new CnC(5, 15, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 14, 3, 6));
        $this->addPrimarySystem(new Engine(4, 15, 0, 9, 3));
		$this->addPrimarySystem(new Hangar(4, 4));
   
        $this->addFrontSystem(new Thruster(2, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(2, 10, 0, 3, 1));
        $this->addFrontSystem(new MediumPlasma(3, 5, 3, 300, 60));
        $this->addFrontSystem(new MediumPlasma(3, 5, 3, 300, 60));
        $this->addFrontSystem(new EWPlasmaMine(2, 7, 3, 300, 60));
        $this->addFrontSystem(new EWPlasmaMine(2, 7, 3, 300, 60));

        $this->addAftSystem(new Thruster(2, 12, 0, 3, 2));
        $this->addAftSystem(new Thruster(2, 12, 0, 3, 2));
        $this->addAftSystem(new Thruster(2, 12, 0, 3, 2));

		$this->addLeftSystem(new EWPlasmaMine(2, 7, 3, 240, 360));
		$this->addLeftSystem(new EWPlasmaMine(2, 7, 3, 300, 60));
        $this->addLeftSystem(new Thruster(2, 15, 0, 4, 3));
		$this->addLeftSystem(new CargoBay(1, 30));

		$this->addRightSystem(new EWPlasmaMine(2, 7, 3, 300, 60));
		$this->addRightSystem(new EWPlasmaMine(2, 7, 3, 0, 120));
        $this->addRightSystem(new Thruster(2, 15, 0, 4, 4));
		$this->addRightSystem(new CargoBay(1, 30));

        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(3, 36));
        $this->addAftSystem(new Structure(3, 38));
        $this->addLeftSystem(new Structure(3, 42));
        $this->addRightSystem(new Structure(3, 42));
        $this->addPrimarySystem(new Structure(4, 40));
		
		$this->hitChart = array(
			0=> array(
					10 => "Structure",
					13 => "Scanner",
					15 => "Engine",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					6 => "Medium Plasma Cannon",
					9 => "Plasma Mine",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					8 => "Thruster",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					5 => "Thruster",
					8 => "Plasma Mine",
					11 => "Cargo Bay",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					5 => "Thruster",
					8 => "Plasma Mine",
					11 => "Cargo Bay",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
