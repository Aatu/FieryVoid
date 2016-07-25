<?php
class zzunoffDagkur extends MediumShip{
    
    function __construct($id, $userid, $name,  $movement){
        parent::__construct($id, $userid, $name,  $movement);
        
        $this->pointCost = 580;
        $this->faction = "Narn";
        $this->phpclass = "zzunoffDagkur";
        $this->imagePath = "img/ships/dagkar.png";
        $this->shipClass = "Dag'Kur Bombardment Frigate";
        $this->forwardDefense = 14;
        $this->sideDefense = 14;
        
        $this->limited = 33;
	$this->isd = 2218;
	$this->unofficial = true;

        $this->turncost = 0.50;
        $this->turndelaycost = 0.50;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        
        $this->iniativebonus = 60;
        
        $this->addPrimarySystem(new Reactor(4, 8, 0, 0));
        $this->addPrimarySystem(new CnC(5, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 2, 5));
        $this->addPrimarySystem(new Engine(4, 12, 0, 9, 3));
        $this->addPrimarySystem(new Hangar(4, 2));
        $this->addPrimarySystem(new Thruster(4, 13, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(4, 13, 0, 4, 4));
        
        
        //front
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new BombRack(4, 6, 0, 300, 60));
        $this->addFrontSystem(new BombRack(4, 6, 0, 300, 60));
        $this->addFrontSystem(new BombRack(4, 6, 0, 300, 60));
        $this->addFrontSystem(new BombRack(4, 6, 0, 300, 60));
        $this->addFrontSystem(new BombRack(4, 6, 0, 300, 60));
        $this->addFrontSystem(new BombRack(4, 6, 0, 300, 60));
        $this->addFrontSystem(new BombRack(4, 6, 0, 300, 60));
        $this->addFrontSystem(new BombRack(4, 6, 0, 300, 60));
        
        //aft
        $this->addAftSystem(new Thruster(4, 10, 0, 5, 2));
        $this->addAftSystem(new Thruster(4, 10, 0, 5, 2));
        
        //structures
        $this->addPrimarySystem(new Structure(4, 55));
        
		
		$this->hitChart = array(
			0=> array(
				8 => "Thruster",
				11 => "Scanner",
				14 => "Engine",
				16 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				4 => "Thruster",
				12 => "Bomb Rack",
				17 => "Structure",
				20 => "Primary",
			),
			2=> array(
				8 => "Thruster",
				14 => "Structure",
				20 => "Primary",
			),
		);
	
    }
	
}
