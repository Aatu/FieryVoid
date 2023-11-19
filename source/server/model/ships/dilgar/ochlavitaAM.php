<?php
class OchlavitaAM extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 525;
	$this->faction = "Dilgar Imperium";
        $this->phpclass = "OchlavitaAM";
        $this->imagePath = "img/ships/ochlavita.png";
        $this->shipClass = "Ochlavita Destroyer";
                $this->isd = 2227;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 15;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 2;
        $this->iniativebonus = 35;


        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(8); //pass magazine capacity - 12 rounds per class-SO rack, 20 most other shipborne racks, 60 class-B rack and 80 Reload Rack
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 8); //add full load of basic missiles
        //$this->enhancementOptionsEnabled[] = 'AMMO_L';//add enhancement options for other missiles - Class-L
        //$this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-H
        $this->enhancementOptionsEnabled[] = 'AMMO_F';//add enhancement options for other missiles - Class-F
        //$this->enhancementOptionsEnabled[] = 'AMMO_A';//add enhancement options for other missiles - Class-A
        //$this->enhancementOptionsEnabled[] = 'AMMO_P';//Dilgar were wiped out before Piercing missile was devised
		//Bomb Rack has only Basic and Flash missiles available for bomb racks!
		
	$this->addPrimarySystem(new Reactor(4, 20, 0, 0));
	$this->addPrimarySystem(new CnC(5, 15, 0, 0));
	$this->addPrimarySystem(new Scanner(5, 12, 4, 8));
	$this->addPrimarySystem(new Engine(5, 13, 0, 6, 2));
	$this->addPrimarySystem(new Hangar(3, 2));
	$this->addPrimarySystem(new Thruster(3, 11, 0, 4, 3));
	$this->addPrimarySystem(new Thruster(3, 11, 0, 4, 4));
	  
	$this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
	$this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
	$this->addFrontSystem(new MediumLaser(3, 6, 5, 300, 60));
	$this->addFrontSystem(new MediumLaser(3, 6, 5, 300, 60));	
	$this->addFrontSystem(new MediumBolter(3, 8, 4, 240, 360));
	$this->addFrontSystem(new MediumBolter(3, 8, 4, 0, 120));
	$this->addFrontSystem(new PointPulsar(2, 6, 3, 240, 360));	
	$this->addFrontSystem(new PointPulsar(2, 6, 3, 0, 120));	
		$this->addFrontSystem(new AmmoBombRack(2, 0, 0, 300, 60, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
   
	$this->addAftSystem(new Thruster(2, 4, 0, 2, 2));
	$this->addAftSystem(new Thruster(3, 8, 0, 3, 2));
	$this->addAftSystem(new Thruster(3, 8, 0, 3, 2));
	$this->addAftSystem(new Thruster(2, 4, 0, 2, 2));
	$this->addAftSystem(new Engine(3, 9, 0, 4, 2));
	$this->addAftSystem(new ScatterPulsar(1, 4, 2, 120, 300));     
	$this->addAftSystem(new ScatterPulsar(1, 4, 2, 60, 240));
	$this->addAftSystem(new MediumBolter(3, 8, 4, 120, 240));     
	$this->addAftSystem(new MediumBolter(3, 8, 4, 120, 240));   
 
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(5, 40));
        $this->addAftSystem(new Structure(5, 40));
        $this->addPrimarySystem(new Structure(5, 52));
	$this->hitChart = array(
		0=> array(
			10 => "Structure",
			13 => "Thruster",
			15 => "Scanner",
			16 => "Engine",
			17 => "Hangar",
			19 => "Reactor",
			20 => "C&C",
		),
		1=> array(
			4 => "Thruster",
			6 => "Medium Laser",
			8 => "Medium Bolter",
			10 => "Point Pulsar",
			11 => "Bomb Rack",
			18 => "Structure",
			20 => "Primary",
		),
		2=> array(
			6 => "Thruster",
			8 => "Medium Bolter",
			10 => "Scatter Pulsar",
			11 => "Engine",
			18 => "Structure",
			20 => "Primary",
		),
	 );
    }
}
?>
