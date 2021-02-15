<?php
class JashakarN extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 425;
	$this->faction = "Dilgar";
        $this->phpclass = "JashakarN";
        $this->imagePath = "img/ships/jashakar.png";
        $this->shipClass = "Jashakar-N Minesweeper";
        $this->canvasSize = 100;
        $this->isd = 2228;
                
        $this->forwardDefense = 12;
        $this->sideDefense = 12;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.5;
        $this->accelcost = 1;
        $this->rollcost = 1;
        $this->pivotcost = 1;
	$this->iniativebonus = 65;

        $this->occurence = "uncommon";
        $this->variantOf = "Jashakar Frigate";
        $this->minesweeperbonus = 4;
        
        $this->addPrimarySystem(new LightBolter(1, 6, 2, 240, 360));
        $this->addPrimarySystem(new LightBOlter(1, 6, 2, 0, 120));
        $this->addPrimarySystem(new Reactor(4, 13, 0, 0));
        $this->addPrimarySystem(new CnC(4, 11, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 16, 5, 6));
        $this->addPrimarySystem(new Engine(4, 9, 0, 4, 2));
	$this->addPrimarySystem(new Hangar(2, 2));
	$this->addPrimarySystem(new Thruster(2, 8, 0, 3, 3));
	$this->addPrimarySystem(new Thruster(2, 8, 0, 3, 4));
		
        $this->addFrontSystem(new MediumBolter(3, 8, 4, 240, 360));
        $this->addFrontSystem(new MediumBolter(3, 8, 4, 300, 60));
        $this->addFrontSystem(new MediumBolter(3, 8, 4, 300, 60));
        $this->addFrontSystem(new MediumBolter(3, 8, 4, 0, 120));
        $this->addFrontSystem(new Thruster(2, 6, 0, 2, 1));
        $this->addFrontSystem(new Thruster(2, 6, 0, 2, 1));
        
        $this->addAftSystem(new Thruster(2, 8, 0, 3, 2));
        $this->addAftSystem(new Engine(2, 5, 0, 2, 2));
        $this->addAftSystem(new Thruster(2, 8, 0, 3, 2));
        $this->addAftSystem(new LightBolter(1, 6, 2, 240, 360));
        $this->addAftSystem(new PlasmaTorch(1, 4, 2, 120, 300));
        $this->addAftSystem(new PlasmaTorch(1, 4, 2, 60, 240));
        $this->addAftSystem(new LightBolter(1, 6, 2, 0, 120));
		
        $this->addPrimarySystem(new Structure( 4, 48));
	
	$this->hitChart = array(
		0=> array(
			8 => "Thruster",
			10 => "Light Bolter",
			13 => "Scanner",
			15 => "Engine",
			16 => "Hangar",
			19 => "Reactor",
			20 => "C&C",
		),
		1=> array(
			4 => "Thruster",
			8 => "Medium Bolter",
			17 => "Structure",
			20 => "Primary",
		),
		2=> array(
			6 => "Thruster",
			8 => "Light Bolter",
			10 => "Plasma Torch",
			11 => "Engine",
			17 => "Structure",
			20 => "Primary",
		),
	);
    }
}
?>
