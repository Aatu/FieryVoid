<?php
class zzunoffTdirkRefit extends OSAT{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 200;
		$this->faction = "Narn Regime";
        $this->phpclass = "zzunoffTdirkRefit";
        $this->imagePath = "img/ships/tgan.png";
        $this->shipClass = "T'Dirk Early Satellite (2217)";
		$this->isd = 2217;
 		$this->unofficial = 'S'; //design released after AoG demise
 		
      	$this->occurence = "common";
		$this->variantOf = "T'Dirk Early Satellite";		

	    $this->notes = 'Only fires Class-D light missiles.';
        
        $this->forwardDefense = 10;
        $this->sideDefense = 10;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60;

        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(40); //pass magazine capacity - 12 rounds per class-SO rack, 20 most other shipborne racks, 60 class-B rack and 80 Reload Rack
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileD(), 40); //add full load of basic missiles

        $this->addPrimarySystem(new Reactor(3, 6, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 6, 2, 3)); 
        $this->addAftSystem(new Thruster(3, 6, 0, 0, 2));
        
        $this->addPrimarySystem(new LightParticleBeamShip(1, 2, 1, 180, 360));
        $this->addFrontSystem(new AmmoMissileRackS(2, 6, 0, 240, 360, $ammoMagazine, true));
        $this->addPrimarySystem(new LightParticleBeamShip(1, 2, 1, 0, 180));
        $this->addFrontSystem(new AmmoMissileRackS(2, 6, 0, 0, 120, $ammoMagazine,  true));
        $this->addFrontSystem(new LightParticleBeamShip(1, 2, 1, 270, 90));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        
        $this->addPrimarySystem(new Structure(3, 21));
	    
		$this->hitChart = array(
                0=> array(
                        9 => "Structure",
                        11 => "2:Thruster",
						12 => "1:Light Particle Beam",
						13 => "Light Particle Beam",						
                        16 => "1:Class-S Missile Rack",
						18 => "Scanner",
                        20 => "Reactor",
                ),
        );
    }
}
