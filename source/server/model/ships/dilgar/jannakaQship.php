<?php
class JannakaQship extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 230;
	$this->faction = "Civilians";
        $this->phpclass = "JannakaQship";
        $this->imagePath = "img/ships/jonkur.png";
        $this->shipClass = "Dilgar Jannaka Q-ship";
        $this->canvasSize = 100;
        $this->isd = 2212;
	    $this->isCombatUnit = false; //not a combat unit, it will never be present in a regular battlegroup
        
        $this->forwardDefense = 11;
        $this->sideDefense = 16;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 999;
        $this->pivotcost = 999;
        $this->iniativebonus = 20;

        $this->addPrimarySystem(new LightLaser(2, 4, 3, 0, 360));
        $this->addPrimarySystem(new Reactor(3, 12, 0, 0));
        $this->addPrimarySystem(new CnC(3, 5, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 9, 1, 4));
        $this->addPrimarySystem(new Engine(3, 8, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(2, 8, 0, 3, 3));
        $this->addPrimarySystem(new Thruster(2, 8, 0, 3, 4));

        $this->addFrontSystem(new Thruster(2, 6, 0, 2, 1));
        $this->addFrontSystem(new Thruster(2, 6, 0, 2, 1));
        $this->addFrontSystem(new Hangar(2, 1));
        $this->addFrontSystem(new MediumPlasma(2, 5, 3, 240, 360));
        $this->addFrontSystem(new MediumPlasma(2, 5, 3, 0, 120));

        $this->addAftSystem(new Thruster(2, 8, 0, 3, 2));
        $this->addAftSystem(new Engine(2, 6, 0, 2, 3));
        $this->addAftSystem(new Thruster(2, 8, 0, 3, 2));
        $this->addAftSystem(new MediumPlasma(2, 5, 3, 180, 300));
        $this->addAftSystem(new MediumPlasma(2, 5, 3, 60, 180));

        $this->addPrimarySystem(new Structure( 4, 42));
	
	$this->hitChart = array(
		0=> array(
			9 => "Thruster",
			12 => "Light Laser",
			14 => "Scanner",
			16 => "Engine",
			19 => "Reactor",
			20 => "C&C",
		),
		1=> array(
			4 => "Thruster",
			5 => "Hangar",
			9 => "Medium Plasma Cannon",
			17 => "Structure",
			20 => "Primary",
		),
		2=> array(
			5 => "Thruster",
			7 => "Engine",
			10 => "Medium Plasma Cannon",
			17 => "Structure",
			20 => "Primary",
		),
	 );
	    
    
    }
}
?>
