<?php
class Gtal extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 900;
	$this->faction = "Narn";
        $this->phpclass = "Gtal";
        $this->imagePath = "img/ships/gquan.png";
        $this->shipClass = "G'Tal Command Cruiser";
        $this->occurence = "rare";
	$this->variantOf = "G'Quan Heavy Cruiser";
        $this->fighters = array("normal"=>12);
	    $this->isd = 2263;

        $this->forwardDefense = 15;
        $this->sideDefense = 17;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 2;
        $this->iniativebonus = 5;
	    $this->notes = "Provides +5 Initiative for all friendly Narn units";
        
        $this->addPrimarySystem(new Reactor(6, 25, 0, 2));
        $this->addPrimarySystem(new CnC(7, 24, 0, 0));
        $this->addPrimarySystem(new Scanner(6, 27, 5, 10));
        $this->addPrimarySystem(new Engine(6, 20, 0, 12, 3));
        $this->addPrimarySystem(new JumpEngine(6, 30, 3, 20));
        $this->addPrimarySystem(new Hangar(6, 16));
        
        $this->addFrontSystem(new HeavyLaser(4, 8, 6, 300, 60));
		$this->addFrontSystem(new HeavyLaser(4, 8, 6, 300, 60));
        $this->addFrontSystem(new HeavyLaser(4, 8, 6, 300, 60));
        $this->addFrontSystem(new Thruster(5, 10, 0, 4, 1));
        $this->addFrontSystem(new Thruster(5, 10, 0, 4, 1));
        $this->addFrontSystem(new EnergyMine(4, 5, 4, 300, 60));
        $this->addFrontSystem(new EnergyMine(4, 5, 4, 300, 60));

        $this->addAftSystem(new TwinArray(3, 6, 2, 90, 270));	
        $this->addAftSystem(new LightPulse(2, 4, 2, 90, 270));
        $this->addAftSystem(new LightPulse(2, 4, 2, 90, 270));
        $this->addAftSystem(new TwinArray(3, 6, 2, 90, 270));
		$this->addAftSystem(new HeavyLaser(4, 8, 6, 120, 240));
        $this->addAftSystem(new Thruster(4, 12, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 12, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 12, 0, 4, 2));

        $this->addLeftSystem(new LightPulse(2, 4, 2, 270, 90));
        $this->addLeftSystem(new TwinArray(3, 6, 2, 270, 90));
        $this->addLeftSystem(new Thruster(4, 15, 0, 5, 3));

        $this->addRightSystem(new LightPulse(2, 4, 2, 270, 90));
        $this->addRightSystem(new TwinArray(3, 6, 2, 270, 90));	
        $this->addRightSystem(new Thruster(4, 15, 0, 5, 4));

        $this->addFrontSystem(new Structure(5, 70));
        $this->addAftSystem(new Structure(4, 50));
        $this->addLeftSystem(new Structure(4, 70));
        $this->addRightSystem(new Structure(4, 70));
        $this->addPrimarySystem(new Structure(6, 50));
		
			
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
				3 => "Thruster",
				8 => "Heavy Laser",
				12 => "Energy Mine",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				5 => "Thruster",
				7 => "Heavy Laser",
				9 => "Twin Array",
				11 => "Light Pulse Cannon",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array(
				4 => "Thruster",
				7 => "Light Pulse Cannon",
				9 => "Twin Array",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				4 => "Thruster",
				7 => "Light Pulse Cannon",
				9 => "Twin Array",
				18 => "Structure",
				20 => "Primary",
			),
		);
    }
	

}
?>
