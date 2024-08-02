<?php
class ChoukaHellfireBOSATAM extends OSAT{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 120;
	$this->faction = "ZEscalation Chouka Theocracy";
        $this->phpclass = "ChoukaHellfireBOSATAM";
        $this->imagePath = "img/ships/EscalationWars/ChoukaHellfireOSAT.png";
        $this->canvasSize = 60;
        $this->shipClass = 'Hellfire-B Defense Satellite';
			$this->variantOf = "Hellfire-A Defense Satellite";
			$this->occurence = "common";
        
        $this->isd = 1962;
        $this->unofficial = true;
        
        $this->forwardDefense = 10;
        $this->sideDefense = 10;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60;

        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(20); //pass magazine capacity - 20 rounds per launcher, plus reload rack 80
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 20); //add full load of basic missiles
        $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-L

        $this->addPrimarySystem(new OSATCnC(0, 1, 0, 0));
        $this->addPrimarySystem(new Reactor(3, 6, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 6, 4, 4)); 
        $this->addPrimarySystem(new Thruster(2, 6, 0, 0, 2)); 
        $this->addPrimarySystem(new HeavyPlasma(2, 8, 5, 300, 60)); 
		$this->addPrimarySystem(new AmmoMissileRackS(3, 0, 0, 270, 90, $ammoMagazine, true));
        $this->addPrimarySystem(new LightLaser(0, 4, 3, 180, 360));
        $this->addPrimarySystem(new LightLaser(0, 4, 3, 0, 180));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        
        $this->addPrimarySystem(new Structure(3, 24));
        
		
		$this->hitChart = array(
			0=> array(
					9 => "Structure",
					11 => "Thruster",
					13 => "Heavy Plasma Cannon",
					14 => "Class-S Missile Rack",
          			16 => "Light Laser",
					18 => "Scanner",
					20 => "Reactor",
			)
		);
    
    
        
    }
}
?>
