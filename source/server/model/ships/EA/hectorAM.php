<?php
class HectorAM extends OSAT{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 250;
		$this->faction = "EA (defenses)";
        $this->phpclass = "HectorAM";
        $this->imagePath = "img/ships/hector.png";
        $this->shipClass = 'Hector Satellite';

	    $this->isd = 2247;
        
        $this->forwardDefense = 10;
        $this->sideDefense = 10;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60;

        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(120); //pass magazine capacity - 60 rounds per launcher
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 120); //add full load of basic missiles
	    $this->enhancementOptionsEnabled[] = 'AMMO_A';//add enhancement options for other missiles - Class-A
	    $this->enhancementOptionsEnabled[] = 'AMMO_C';//add enhancement options for other missiles - Class-C
	    $this->enhancementOptionsEnabled[] = 'AMMO_F';//add enhancement options for other missiles - Class-F
	    $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-H
	    $this->enhancementOptionsEnabled[] = 'AMMO_K';//add enhancement options for other missiles - Class-K   
	    $this->enhancementOptionsEnabled[] = 'AMMO_L';//add enhancement options for other missiles - Class-L
	    $this->enhancementOptionsEnabled[] = 'AMMO_M';//add enhancement options for other missiles - Class-M	    
		$this->enhancementOptionsEnabled[] = 'AMMO_P';//add enhancement options for other missiles - Class-P
		
        $this->addPrimarySystem(new AmmoMissileRackB(3, 0, 0, 270, 90, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addPrimarySystem(new AmmoMissileRackB(3, 0, 0, 270, 90, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addPrimarySystem(new LightPulse(2, 4, 2, 180, 360));
        $this->addPrimarySystem(new LightPulse(2, 4, 2, 0, 180));
        $this->addPrimarySystem(new InterceptorMkI(2, 4, 1, 0, 360));
        //$this->addPrimarySystem(new InterceptorMkI(2, 4, 1, 0, 360));

        $this->addPrimarySystem(new Reactor(4, 7, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 16, 2, 4));   

        $this->addPrimarySystem(new Thruster(3, 6, 0, 0, 2));
                
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(4, 30));
		
		
		$this->hitChart = array(
			0=> array(
				9 => "Structure",
				11 => "Thruster",
				14 => "Class-B Missile Rack",
				16 => "Light Pulse Cannon",
				18 => "Scanner",
				19 => "Reactor",
				20 => "Interceptor I",
			),
			1=> array(
				9 => "Structure",
				11 => "0:Thruster",
				14 => "0:Class-B Missile Rack",
				16 => "0:Light Pulse Cannon",
				18 => "0:Scanner",
				19 => "0:Reactor",
				20 => "0:Interceptor I",
			),
			2=> array(
				9 => "Structure",
				11 => "0:Thruster",
				14 => "0:Class-B Missile Rack",
				16 => "0:Light Pulse Cannon",
				18 => "0:Scanner",
				19 => "0:Reactor",
				20 => "0:Interceptor I",
			),
        );
    }
}
?>
