<?php
class IMLArmedTransportAM extends HeavyCombatVesselLeftRight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 450;
	$this->faction = "Raiders";
        $this->phpclass = "IMLArmedTransportAM";
        $this->imagePath = "img/ships/RaiderIMLTransport.png";
        $this->shipClass = "IML Armed Transport";
        
		$this->notes = 'Used only by the Independent Mercenaries League';
		$this->isd = 2234;
		
        $this->forwardDefense = 14;
        $this->sideDefense = 15;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 30;

        $this->gravitic = true;

        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(80); //pass magazine capacity - 12 rounds per class-SO rack, 20 most other shipborne racks, 60 class-B rack and 80 Reload Rack
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 80); //add full load of basic missiles
	    $this->enhancementOptionsEnabled[] = 'AMMO_A';//add enhancement options for other missiles - Class-A
	    $this->enhancementOptionsEnabled[] = 'AMMO_F';//add enhancement options for other missiles - Class-F
	    $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-H
	    $this->enhancementOptionsEnabled[] = 'AMMO_L';//add enhancement options for other missiles - Class-L
		//IML ships ave access to basic, antifighter, flash, heavy and long-range missiles.		
		
        $this->addPrimarySystem(new Reactor(6, 15, 0, 0));
        $this->addPrimarySystem(new CnC(6, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(6, 12, 5, 6));
        $this->addPrimarySystem(new Engine(6, 14, 0, 10, 3));
        $this->addPrimarySystem(new Hangar(6, 6));
        $this->addPrimarySystem(new GraviticThruster(5, 15, 0, 6, 1));
        $this->addPrimarySystem(new GraviticThruster(5, 18, 0, 10, 2));
        $this->addPrimarySystem(new GraviticCannon(4, 6, 5, 90, 270));

        $this->addLeftSystem(new GraviticCannon(5, 6, 5, 240, 0));
        $this->addLeftSystem(new AmmoMissileRackS(4, 0, 0, 180, 360, $ammoMagazine, false));
        $this->addLeftSystem(new AmmoMissileRackS(4, 0, 0, 240, 60, $ammoMagazine, false));
        $this->addLeftSystem(new GraviticThruster(5, 15, 0, 6, 3));

        $this->addRightSystem(new GraviticCannon(5, 6, 5, 0, 120));
        $this->addRightSystem(new AmmoMissileRackS(4, 0, 0, 0, 180, $ammoMagazine, false));
        $this->addRightSystem(new AmmoMissileRackS(4, 0, 0, 300, 120, $ammoMagazine, false));
        $this->addRightSystem(new GraviticThruster(5, 15, 0, 6, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(6, 40));
        $this->addLeftSystem(new Structure(5, 48));
        $this->addRightSystem(new Structure(5, 48));
		
		$this->hitChart = array(
			0=> array(
					8 => "Structure",
					11 => "Thruster",
					12 => "Gravitic Cannon",
					14 => "Scanner",
					16 => "Engine",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			3=> array(
					4 => "Thruster",
					6 => "Class-S Missile Rack",
					8 => "Gravitic Cannon",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					4 => "Thruster",
					6 => "Class-S Missile Rack",
					8 => "Gravitic Cannon",
					18 => "Structure",
					20 => "Primary",
			),
		);
		
    }
}
