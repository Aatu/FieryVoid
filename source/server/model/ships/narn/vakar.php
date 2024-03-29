<?php
class Vakar extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 650;
        $this->faction = "Narn Regime";
        $this->phpclass = "Vakar";
        $this->imagePath = "img/ships/varkar.png";
        $this->shipClass = "Va'Kar Long-Range Scout";
        $this->shipSizeClass = 3;
        $this->fighters = array("medium"=>6);
        $this->limited = 33;
        $this->occurence = "uncommon";
        $this->variantOf = "Var'Nic Long Range Destroyer";
	    $this->isd = 2253;
	    
        
        $this->forwardDefense = 14;
        $this->sideDefense = 14;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 10;

        //primary
        $this->addPrimarySystem(new Reactor(5, 18, 0, 0));
        $this->addPrimarySystem(new CnC(5, 16, 0, 0));
        $this->addPrimarySystem(new ElintScanner(5, 21, 6, 10));
        $this->addPrimarySystem(new Engine(4, 14, 0, 12, 2));
        $this->addPrimarySystem(new JumpEngine(5, 18, 3, 20));
        $this->addPrimarySystem(new Hangar(5, 7));
		
        $this->addFrontSystem(new TwinArray(5, 6, 2, 300, 60));
        $this->addFrontSystem(new Thruster(4, 8, 0, 4, 1));
        $this->addFrontSystem(new Thruster(4, 8, 0, 4, 1));
		
        $this->addAftSystem(new LightPulse(3, 4, 2, 90, 270));
        $this->addAftSystem(new LightPulse(3, 4, 2, 90, 270));        
        $this->addAftSystem(new Thruster(3, 24, 0, 12, 2));        
        
        $this->addLeftSystem(new TwinArray(3, 6, 2, 240, 60));
        $this->addLeftSystem(new mediumPulse(3, 6, 3, 240, 60));
        $this->addLeftSystem(new mediumPulse(3, 6, 3, 240, 60));
        $this->addLeftSystem(new Thruster(4, 15, 0, 5, 3));
		
        $this->addRightSystem(new TwinArray(3, 6, 2, 300, 120));
        $this->addRightSystem(new IonTorpedo(4, 5, 4, 0, 120));
        $this->addRightSystem(new Thruster(4, 13, 0, 4, 4));        
        
        //structures
        $this->addFrontSystem(new Structure(4, 40));
        $this->addAftSystem(new Structure(4, 38));
        $this->addLeftSystem(new Structure(5, 60));
        $this->addRightSystem(new Structure(4, 39));
        $this->addPrimarySystem(new Structure(5, 36));
		
		
	
		$this->hitChart = array(
			0=> array(
				8 => "Structure",
				11 => "Jump Engine",
				13 => "ELINT Scanner",
				15 => "Engine",
				17 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
				),
			1=> array(
				5 => "Thruster",
				8 => "Twin Array",
				18 => "Structure",
				20 => "Primary",
				),
			2=> array(
				6 => "Thruster",
				9 => "Light Pulse Cannon",
				18 => "Structure",
				20 => "Primary",
				),
			3=> array(
				4 => "Thruster",
				7 => "Medium Pulse Cannon",
				9 => "Twin Array",
				18 => "Structure",
				20 => "Primary",
				),
			4=> array(
				4 => "Thruster",
				7 => "Ion Torpedo",
				9 => "Twin Array",
				18 => "Structure",
				20 => "Primary",
			),
		);        
    }
}



?>
