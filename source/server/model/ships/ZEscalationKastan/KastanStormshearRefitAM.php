<?php
class KastanStormshearRefitAM extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 400;
        $this->faction = "ZEscalation Kastan Monarchy";
        $this->phpclass = "KastanStormshearRefitAM";
        $this->imagePath = "img/ships/EscalationWars/KastanIronshear.png";
			$this->canvasSize = 125; //img has 200px per side
        $this->shipClass = "Stormshear War Cruiser (1890 refit)";
			$this->variantOf = "Ironshear Supply Ship";
			$this->occurence = "uncommon";
		$this->unofficial = true;

        $this->isd = 1890;
        
        $this->forwardDefense = 15;
        $this->sideDefense = 15;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 40;

        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(48); //pass magazine capacity - 20 rounds per launcher, plus reload rack 80
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 48); //add full load of basic missiles
        $this->enhancementOptionsEnabled[] = 'AMMO_L';//add enhancement options for other missiles - Class-L
        $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-L
        $this->enhancementOptionsEnabled[] = 'AMMO_F';//add enhancement options for other missiles - Class-L
         
        $this->addPrimarySystem(new Reactor(4, 16, 0, 0));
        $this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 2, 5));
        $this->addPrimarySystem(new Engine(4, 16, 0, 9, 3));
        $this->addPrimarySystem(new Hangar(3, 2));
        $this->addPrimarySystem(new Thruster(3, 13, 0, 3, 3));
        $this->addPrimarySystem(new Thruster(3, 13, 0, 3, 4));
      
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
		$this->addFrontSystem(new EWLaserBolt(2, 4, 2, 240, 60));
		$this->addFrontSystem(new EWRoyalLaser(3, 6, 4, 300, 60));
		$this->addFrontSystem(new EWLaserBolt(2, 4, 2, 300, 120));
		$this->addFrontSystem(new AmmoMissileRackSO(2, 0, 0, 240, 60, $ammoMagazine, false));
		$this->addFrontSystem(new AmmoMissileRackSO(2, 0, 0, 300, 120, $ammoMagazine, false));
                
        $this->addAftSystem(new Thruster(2, 6, 0, 2, 2));
        $this->addAftSystem(new Thruster(2, 6, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 15, 0, 5, 2));
        $this->addAftSystem(new EWLaserBolt(2, 4, 2, 90, 270));
        $this->addAftSystem(new EWLaserBolt(2, 4, 2, 90, 270));
		$this->addAftSystem(new AmmoMissileRackSO(2, 0, 0, 180, 360, $ammoMagazine, false));
		$this->addAftSystem(new AmmoMissileRackSO(2, 0, 0, 0, 180, $ammoMagazine, false));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 3, 40));
        $this->addAftSystem(new Structure( 3, 40));
        $this->addPrimarySystem(new Structure( 4, 40));
		
        $this->hitChart = array(
            0=> array(
                    8 => "Structure",
                    11 => "Thruster",
                    13 => "Scanner",
                    15 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    4 => "Thruster",
                    5 => "Royal Laser",
					7 => "Laser Bolt",
					9 => "Class-SO Missile Rack",
					18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    6 => "Thruster",
					7 => "Laser Bolt",
					11 => "Class-SO Missile Rack",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>
