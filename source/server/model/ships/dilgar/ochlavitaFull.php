<?php
class OchlavitaFull extends HeavyCombatVessel{
    /*Ochlavita with Bomb Rack full of Flash missiles!*/
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 525+25;
	$this->faction = "Dilgar";
        $this->phpclass = "OchlavitaFull";
        $this->imagePath = "img/ships/ochlavita.png";
        $this->shipClass = "Ochlavita Destroyer (full)";
	    $this->unofficial = true;
        $this->variantOf = "Ochlavita Destroyer";
        
        $this->forwardDefense = 13;
        $this->sideDefense = 15;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 2;
        $this->iniativebonus = 35;
	$this->addPrimarySystem(new Reactor(4, 20, 0, 0));
	$this->addPrimarySystem(new CnC(5, 15, 0, 0));
	$this->addPrimarySystem(new Scanner(5, 12, 4, 8));
	$this->addPrimarySystem(new Engine(5, 13, 0, 6, 2));
	$this->addPrimarySystem(new Hangar(3, 2));
	$this->addPrimarySystem(new Thruster(3, 11, 0, 4, 3));
	$this->addPrimarySystem(new Thruster(3, 11, 0, 4, 4));
	  
	$this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
	$this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
	$this->addFrontSystem(new PointPulsar(2, 6, 3, 240, 360));	
	$this->addFrontSystem(new MediumBolter(3, 8, 4, 240, 360));
	$this->addFrontSystem(new MediumLaser(3, 6, 5, 300, 60));
        //$bombrack1 = new BombRack(2, 6, 0, 300, 60);
        //$bombrack1->addAmmo("B", 8);
	$this->addFrontSystem(new MultiBombRack(2, 'B', 300, 60));
	$this->addFrontSystem(new MediumLaser(3, 6, 5, 300, 60));	
	$this->addFrontSystem(new MediumBolter(3, 8, 4, 0, 120));
	$this->addFrontSystem(new PointPulsar(2, 6, 3, 0, 120));	
	$this->addAftSystem(new ScatterPulsar(1, 4, 2, 120, 300));
	$this->addAftSystem(new MediumBolter(3, 8, 4, 120, 240));        
	$this->addAftSystem(new Thruster(2, 4, 0, 2, 2));
	$this->addAftSystem(new Thruster(3, 8, 0, 3, 2));
	$this->addAftSystem(new Thruster(3, 8, 0, 3, 2));
	$this->addAftSystem(new Thruster(2, 4, 0, 2, 2));
	$this->addAftSystem(new Engine(3, 9, 0, 4, 2));
	$this->addAftSystem(new MediumBolter(3, 8, 4, 120, 240));        
	$this->addAftSystem(new ScatterPulsar(1, 4, 2, 60, 240));
 
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(5, 40));
        $this->addAftSystem(new Structure(5, 40));
        $this->addPrimarySystem(new Structure(5, 52));
	$this->hitChart = array(
		0=> array(
			10 => "Structure",
			13 => "Thruster",
			15 => "Scanner",
			16 => "Engine",
			17 => "Hangar",
			19 => "Reactor",
			20 => "C&C",
		),
		1=> array(
			4 => "Thruster",
			6 => "Medium Laser",
			8 => "Medium Bolter",
			10 => "Point Pulsar",
			11 => "Bomb Rack",
			18 => "Structure",
			20 => "Primary",
		),
		2=> array(
			6 => "Thruster",
			8 => "Medium Bolter",
			10 => "Scatter Pulsar",
			11 => "Engine",
			18 => "Structure",
			20 => "Primary",
		),
	 );
    }
}
?>
