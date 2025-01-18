<?php
class RogolonRostovAM extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 410;
        $this->faction = "Rogolon Dynasty";
        $this->phpclass = "RogolonRostovAM";
        $this->imagePath = "img/ships/RogolonSmallWarship.png";
        $this->shipClass = "Rostov Outrider";
			$this->variantOf = "Tovin Small Warship";
			$this->occurence = "rare";
 		$this->unofficial = 'S'; //design released after AoG demise

        $this->isd = 1951;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 15;
        
        $this->turncost = 0.75;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 4;
        $this->iniativebonus = 30;
		
        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(24); //pass magazine capacity - 12 rounds per class-SO rack, 20 most other shipborne racks, 60 class-B rack and 80 Reload Rack
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 24); //add full load of basic missiles
        $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-H
		//Rogolons have ONLY Heavy Missiles available (besides Basic)
		
        $this->addPrimarySystem(new Reactor(4, 8, 0, 0));
        $this->addPrimarySystem(new CnC(4, 16, 0, 0));
        $this->addPrimarySystem(new ELINTScanner(4, 12, 5, 5));
        $this->addPrimarySystem(new Engine(4, 10, 0, 8, 3));
		$this->addPrimarySystem(new Hangar(4, 3));
        $this->addPrimarySystem(new Thruster(4, 15, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(4, 15, 0, 4, 4));
        
        $this->addFrontSystem(new Thruster(4, 15, 0, 4, 1));
        $this->addFrontSystem(new Thruster(4, 15, 0, 4, 1));
		$this->addFrontSystem(new MediumPlasma(3, 5, 3, 240, 0));
		$this->addFrontSystem(new MediumPlasma(3, 5, 3, 0, 120));
		$this->addFrontSystem(new CargoBay(2, 9));
		
        $this->addAftSystem(new Thruster(4, 8, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 8, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 8, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 8, 0, 2, 2));
		$this->addAftSystem(new AmmoMissileRackSO(3, 0, 0, 180, 360, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addAftSystem(new AmmoMissileRackSO(3, 0, 0, 0, 180, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base

        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 36));
        $this->addPrimarySystem(new Structure( 4, 40));
        $this->addAftSystem(new Structure( 4, 36));
        
        $this->hitChart = array(
        	0=> array(
        		9 => "Structure",
        		12 => "Thruster",
        		13 => "Hangar",
        		15 => "ELINT Scanner",
        		17 => "Engine",
        		19 => "Reactor",
        		20 => "C&C",	
        	),
        	1=> array(
        		6 => "Thruster",
        		8 => "Medium Plasma Cannon",
        		10 => "Cargo",
        		18 => "Structure",
        		20 => "Primary",        			
        	),
        	2=> array(
        		7 => "Thruster",
        		9 => "Class-SO Missile Rack",
        		18 => "Structure",
        		20 => "Primary",        			
        	),
        );
    }
}
?>
