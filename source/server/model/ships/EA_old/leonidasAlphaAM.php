<?php
class LeonidasAlphaAM extends OSAT{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 100;
        $this->faction = "Earth Alliance (Early)";
        $this->phpclass = "LeonidasAlphaAM";
        $this->imagePath = "img/ships/hector.png";
        $this->shipClass = 'Leonidas Satellite (Alpha)';
 		$this->unofficial = 'S'; //HRT design released after AoG demise

	    $this->isd = 2128;
        
        $this->forwardDefense = 10;
        $this->sideDefense = 10;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60;

        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(16); //pass magazine capacity - 12 rounds per launcher
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 16); //add full load of basic missiles
        $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-L

        $this->addFrontSystem(new AmmoMissileRackO(3, 0, 0, 270, 90, $ammoMagazine, true));
        $this->addFrontSystem(new AmmoMissileRackO(3, 0, 0, 270, 90, $ammoMagazine, true));
        $this->addFrontSystem(new LtBlastCannon(2, 4, 1, 180, 360));
        $this->addFrontSystem(new LtBlastCannon(2, 4, 1, 0, 360));
        $this->addFrontSystem(new LtBlastCannon(2, 4, 1, 0, 180));
        //$this->addPrimarySystem(new InterceptorMkI(2, 4, 1, 0, 360));

        $this->addPrimarySystem(new OSATCnC(0, 1, 0, 0));
        $this->addPrimarySystem(new Reactor(4, 6, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 14, 2, 3));   

        $this->addAftSystem(new Thruster(2, 6, 0, 0, 2));
                
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(3, 30));
		
		$this->hitChart = array(
			0=> array(
				9 => "Structure",
				11 => "2:Thruster",
				14 => "1:Class-O Missile Rack",
				17 => "1:Light Blast Cannon",
				19 => "Scanner",
				20 => "Reactor",
			),
			1=> array(
				20 => "Primary",
			),
			2=> array(
				20 => "Primary",
			),
        );
    }
}
?>
