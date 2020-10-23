<?php
class Llartol extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 750;
		$this->faction = "Corillani";
        $this->phpclass = "Llartol";
        $this->imagePath = "img/ships/CorillaniMollanta.png";
        $this->shipClass = "Llartol Assault Cruiser";
        $this->shipSizeClass = 3;
        $this->fighters = array("assault shuttles"=>12);
	    $this->isd = 2242;
        $this->occurence = "Uncommon";
		$this->variantOf = "Mollanta Heavy Cruiser";	    
		
        $this->forwardDefense = 13;
        $this->sideDefense = 18;
        
        $this->turncost = 1.66;
        $this->turndelaycost = 1.66;
        $this->accelcost = 6;
        $this->rollcost = 2;
        $this->pivotcost = 4;

        
        $this->addPrimarySystem(new Reactor(4, 18, 0, 0));
        $this->addPrimarySystem(new CnC(4, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 16, 4, 8));
        $this->addPrimarySystem(new Engine(4, 20, 0, 16, 4));
        $this->addPrimarySystem(new JumpEngine(4, 15, 4, 36));
        $this->addPrimarySystem(new Hangar(2, 2));
        
        $this->addFrontSystem(new SMissileRack(3, 6, 0, 300, 60));        
        $this->addFrontSystem(new SMissileRack(3, 6, 0, 300, 60));
        $this->addFrontSystem(new SMissileRack(3, 6, 0, 300, 60));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));           

		
        $this->addAftSystem(new SMissileRack(3, 6, 0, 180, 360));	
        $this->addAftSystem(new SMissileRack(3, 6, 0, 0, 180));
        $this->addAftSystem(new SMissileRack(3, 6, 0, 120, 240));
        $this->addAftSystem(new Thruster(4, 10, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 10, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 10, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 10, 0, 4, 2));       
		
        
 		$this->addLeftSystem(new SMissileRack(3, 6, 0, 300, 60));
        $this->addLeftSystem(new TwinArray(2, 6, 2, 180, 360));
        $this->addLeftSystem(new TwinArray(2, 6, 2, 180, 360));
        $this->addLeftSystem(new TwinArray(2, 6, 2, 180, 360));        
        $this->addLeftSystem(new Thruster(4, 10, 0, 3, 3));
        $this->addLeftSystem(new Thruster(4, 10, 0, 3, 3));
        $this->addLeftSystem(new Hangar(2, 6));
		
 		$this->addRightSystem(new SMissileRack(3, 6, 0, 300, 60));
        $this->addRightSystem(new TwinArray(2, 6, 2, 0, 180));
        $this->addRightSystem(new TwinArray(2, 6, 2, 0, 180));
        $this->addRightSystem(new TwinArray(2, 6, 2, 0, 180));        
        $this->addRightSystem(new Thruster(4, 10, 0, 3, 4));
        $this->addRightSystem(new Thruster(4, 10, 0, 3, 4));
        $this->addRightSystem(new Hangar(2, 6));        
		
		
        $this->addFrontSystem(new Structure(4, 48));
        $this->addAftSystem(new Structure(4, 40));
        $this->addLeftSystem(new Structure(4, 60));
        $this->addRightSystem(new Structure(4, 60));
        $this->addPrimarySystem(new Structure(4, 60));
		
		
		$this->hitChart = array(
			0=> array(
				8 => "Structure",
				11 => "Jump Engine",
				13 => "Scanner",
				15 => "Engine",
				17 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				6 => "Thruster",
				11 => "Class-S Missile Rack",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				7 => "Thruster",
				10 => "Class-S Missile Rack",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array(
				7 => "Thruster",
				9 => "Class-S Missile Rack",
				12 => "Twin Array",
				13 => "Hangar",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				7 => "Thruster",
				9 => "Class-S Missile Rack",
				12 => "Twin Array",
				13 => "Hangar",
				18 => "Structure",
				20 => "Primary",
			),
		);
    }
}



?>
