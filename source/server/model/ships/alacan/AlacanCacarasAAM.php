<?php
class AlacanCacarasAAM extends OSAT{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 100;
		$this->faction = 'Alacan Republic';
        $this->phpclass = "AlacanCacarasAAM";
        $this->imagePath = "img/ships/AlacanCacaras.png";
			$this->canvasSize = 100; //img has 100px per side
        $this->shipClass = "Cacaras A Defense Satellite";
//		$this->unofficial = true;
		$this->isd = 2202;

		$this->notes = "Only used light missiles and cannot pivot.";
        
        $this->forwardDefense = 7;
        $this->sideDefense = 7;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60;

        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(24); //pass magazine capacity - 12 rounds per class-SO rack, 20 most other shipborne racks, 60 class-B rack and 80 Reload Rack
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileD(), 24); //add full load of basic missiles
		//Rogolons have ONLY Light Missiles available (besides Basic)

        $this->addFrontSystem(new LightParticleBeamShip(1, 2, 1, 180, 360));
		$this->addFrontSystem(new AmmoMissileRackSO(3, 0, 0, 270, 90, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addFrontSystem(new LightParticleBeamShip(1, 2, 1, 0, 360));
		$this->addFrontSystem(new AmmoMissileRackSO(3, 0, 0, 270, 90, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addFrontSystem(new LightParticleBeamShip(1, 2, 1, 0, 180));
        
        $this->addPrimarySystem(new OSATCnC(0, 1, 0, 0));        
        $this->addPrimarySystem(new Reactor(3, 4, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 3, 2, 4));   
                
		//lack of Thruster is deliberate - Alacan satellites are explicitly described as incapable of turning
		
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(4, 20));
		
		$this->hitChart = array(
			0=> array(
				10 => "Structure",
				13 => "1:Class-SO Missile Rack",
				15 => "1:Light Particle Beam",
				17 => "Scanner",
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
