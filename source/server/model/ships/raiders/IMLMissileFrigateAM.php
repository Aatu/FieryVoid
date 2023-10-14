<?php
class IMLMissileFrigateAM extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 350;
		$this->faction = "Raiders";
        $this->phpclass = "IMLMissileFrigateAM";
        $this->imagePath = "img/ships/RaiderIMLMissileFrigate.png";
        $this->shipClass = "IML Missile Frigate";
        $this->agile = true;
        $this->canvasSize = 100;
		$this->notes = 'Used only by the Independent Mercenaries League';
	    $this->isd = 2243;
        
        $this->forwardDefense = 10;
        $this->sideDefense = 12;
        
        $this->turncost = 0.50;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 2;
		$this->iniativebonus = 60;

        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(40); //pass magazine capacity - 12 rounds per class-SO rack, 20 most other shipborne racks, 60 class-B rack and 80 Reload Rack
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 40); //add full load of basic missiles
	    $this->enhancementOptionsEnabled[] = 'AMMO_A';//add enhancement options for other missiles - Class-A
	    $this->enhancementOptionsEnabled[] = 'AMMO_F';//add enhancement options for other missiles - Class-F
	    $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-H
	    $this->enhancementOptionsEnabled[] = 'AMMO_L';//add enhancement options for other missiles - Class-L
		//IML ships ave access to basic, antifighter, flash, heavy and long-range missiles.	
         
        $this->addPrimarySystem(new Reactor(3, 10, 0, 0));
        $this->addPrimarySystem(new CnC(3, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 14, 3, 6));
        $this->addPrimarySystem(new Engine(3, 10, 0, 12, 3));
		$this->addPrimarySystem(new Hangar(3, 1));
		$this->addPrimarySystem(new Thruster(3, 10, 0, 5, 3));
		$this->addPrimarySystem(new Thruster(3, 10, 0, 5, 4));
				
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new AmmoMissileRackL(3, 0, 0, 240, 120, $ammoMagazine, false));
		$this->addFrontSystem(new LightPulse(3, 4, 2, 240, 60));		
		$this->addFrontSystem(new LightPulse(3, 4, 2, 300, 120));		
        $this->addFrontSystem(new AmmoMissileRackL(3, 0, 0, 240, 120, $ammoMagazine, false));
		
        $this->addAftSystem(new Thruster(3, 10, 0, 6, 2));
        $this->addAftSystem(new Thruster(3, 10, 0, 6, 2));
		$this->addAftSystem(new LightPulse(2, 4, 2, 180, 60));
        $this->addAftSystem(new LightPulse(2, 4, 2, 300, 180));
       
        $this->addPrimarySystem(new Structure( 3, 48));
	
		$this->hitChart = array(
			0=> array(
				7 => "Thruster",
				11 => "Scanner",
				14 => "Engine",
				16 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				6 => "Thruster",
				8 => "Class-L Missile Rack",
				12 => "Light Pulse Cannon",
				17 => "Structure",
				20 => "Primary",
			),
			2=> array(
				6 => "Thruster",
				10 => "Light Pulse Cannon",
				17 => "Structure",
				20 => "Primary",
			),
		);				
    }
}



?>
