<?php
class Gsten extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 575;
		$this->faction = "Narn";
        $this->phpclass = "Gsten";
        $this->imagePath = "img/ships/gkarith.png";
        $this->shipClass = "G'Sten";
        $this->shipSizeClass = 3;
        $this->occurence = "special";
        $this->fighters = array("normal"=>6);
	    
	    $this->notes = "Nominally Rare variant of G'Karith cruiser (not present in FV)";
	    $this->notes .= "It's not unduly powerful though, so just don't use hordes of them and it should be fine :)";
	    
		
        $this->forwardDefense = 15;
        $this->sideDefense = 16;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
        
        $this->iniativebonus = 5;

        
        $this->addPrimarySystem(new Reactor(6, 18, 0, 2));
        $this->addPrimarySystem(new CnC(6, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 18, 4, 8));
        $this->addPrimarySystem(new Engine(5, 18, 0, 12, 3));
		$this->addPrimarySystem(new JumpEngine(5, 24, 3, 20));
		$this->addPrimarySystem(new Hangar(5, 8));
		
		
        $this->addFrontSystem(new HeavyPulse(5, 6, 4, 300, 60));        
        $this->addFrontSystem(new LightPulse(2, 4, 2, 270, 90));
        $this->addFrontSystem(new LightPulse(2, 4, 2, 270, 90));
        $this->addFrontSystem(new Thruster(4, 10, 0, 4, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 4, 1));
        
        $this->addAftSystem(new LightPulse(2, 4, 2, 90, 270));
        $this->addAftSystem(new LightPulse(2, 4, 2, 90, 270));
		$this->addAftSystem(new LightPulse(2, 4, 2, 90, 270));
        $this->addAftSystem(new Thruster(4, 8, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 16, 0, 6, 2));
        $this->addAftSystem(new Thruster(4, 8, 0, 3, 2));		
		
		$this->addLeftSystem(new mediumPulse(3, 6, 3, 240, 0));
        $this->addLeftSystem(new mediumPulse(3, 6, 3, 240, 0));
        $this->addLeftSystem(new Thruster(4, 15, 0, 4, 3));
		
		$this->addRightSystem(new mediumPulse(3, 6, 3, 0, 120));
        $this->addRightSystem(new mediumPulse(3, 6, 3, 0, 120));
		$this->addRightSystem(new Thruster(4, 15, 0, 4, 4));
		
		//structures
        $this->addFrontSystem(new Structure(5, 54));
        $this->addAftSystem(new Structure(4, 48));
        $this->addLeftSystem(new Structure(4, 56));
        $this->addRightSystem(new Structure(4, 56));
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
				6 => "Thruster",
				8 => "Heavy Pulse Cannon",
				10 => "Light Pulse Cannon",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				7 => "Thruster",
				10 => "Light Pulse Cannon",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array(
				5 => "Thruster",
				9 => "Medium Pulse Cannon",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				5 => "Thruster",
				9 => "Medium Pulse Cannon",
				18 => "Structure",
				20 => "Primary",
			),
		);        
    }

}



?>
