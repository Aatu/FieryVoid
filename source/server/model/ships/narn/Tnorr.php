<?php
class Tnorr extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 425;
		$this->faction = "Narn";
        $this->phpclass = "Tnorr";
        $this->imagePath = "img/ships/trakk.png";
        $this->shipClass = "T'Norr Frigate";
        $this->forwardDefense = 10;
        $this->sideDefense = 16;
        $this->occurence = "uncommon";
	    $this->isd = 2255;
	    $this->variantOf = "T'Rakk Frigate";
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.50;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 1;
        $this->iniativebonus = 30;

        $this->addPrimarySystem(new Reactor(4, 12, 0, 0));
        $this->addPrimarySystem(new CnC(4, 7, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 3, 6));
        $this->addPrimarySystem(new Engine(4, 8, 0, 6, 3));
		$this->addPrimarySystem(new Hangar(3, 1));
		$this->addPrimarySystem(new Thruster(4, 10, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(4, 10, 0, 4, 4));
        
        $this->addFrontSystem(new HeavyPulse(4, 6, 4, 300, 60));
        $this->addFrontSystem(new HeavyPulse(4, 6, 4, 300, 60));
        $this->addFrontSystem(new Thruster(4, 10, 0, 4, 1));
        $this->addFrontSystem(new LightPulse(2, 4, 2, 240, 120));

		$this->addAftSystem(new LightPulse(2, 4, 2, 60, 300));
        $this->addAftSystem(new Thruster(4, 9, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 9, 0, 3, 2));

        $this->addFrontSystem(new Structure(4, 36));
        $this->addAftSystem(new Structure(4, 40));
        $this->addPrimarySystem(new Structure(4, 36));
		
		
		$this->hitChart = array(
			0=> array(
				7 => "Structure",
				12 => "Thruster",
				14 => "Scanner",
				16 => "Engine",
				17 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				4 => "Thruster",
				8 => "Heavy Pulse Cannon",
				10 => "Light Pulse Cannon",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				6 => "Thruster",
				8 => "Light Pulse Cannon",
				18 => "Structure",
				20 => "Primary",
			),
		);		
    }
}

?>
