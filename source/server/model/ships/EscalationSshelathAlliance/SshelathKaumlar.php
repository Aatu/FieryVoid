<?php
class SshelathKaumlar extends OSAT{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 240;
        $this->faction = "Escalation Wars Sshel'ath Alliance";       
        $this->phpclass = "SshelathKaumlar";
        $this->imagePath = "img/ships/EscalationWars/SshelathKaumlar.png";
        $this->shipClass = "Kaumlar Command Fortress";
        $this->isd = 1908;
		$this->limited = 33;
        
        $this->forwardDefense = 12;
        $this->sideDefense = 12;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60;


        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(32); //pass magazine capacity - 60 rounds per launcher
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileD(), 32); //add full load of basic missiles

        $this->addFrontSystem(new AmmoMissileRackO(2, 0, 0, 270, 90, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addFrontSystem(new AmmoMissileRackO(2, 0, 0, 270, 90, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addFrontSystem(new AmmoMissileRackO(2, 0, 0, 270, 90, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addFrontSystem(new AmmoMissileRackO(2, 0, 0, 270, 90, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base

		$this->addPrimarySystem(new EWDefenseLaser(0, 2, 1, 0, 360));
		$this->addPrimarySystem(new EWDefenseLaser(0, 2, 1, 0, 360));
		$this->addPrimarySystem(new EWDefenseLaser(0, 2, 1, 0, 360));
		$this->addPrimarySystem(new EWDefenseLaser(0, 2, 1, 0, 360));
		$this->addPrimarySystem(new EWDefenseLaser(0, 2, 1, 0, 360));
		$this->addPrimarySystem(new EWDefenseLaser(0, 2, 1, 0, 360));

        $this->addAftSystem(new AmmoMissileRackO(2, 0, 0, 90, 270, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addAftSystem(new AmmoMissileRackO(2, 0, 0, 90, 270, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addAftSystem(new AmmoMissileRackO(2, 0, 0, 90, 270, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addAftSystem(new AmmoMissileRackO(2, 0, 0, 90, 270, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base

        $this->addPrimarySystem(new OSATCnC(0, 1, 0, 0));
        $this->addPrimarySystem(new Reactor(2, 7, 0, 0));
        $this->addPrimarySystem(new Scanner(2, 6, 1, 2));   
                
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(2, 45));

		$this->hitChart = array(
                0=> array(
                        9 => "Structure",
                        11 => "1:Class-O Missile Rack",
                        13 => "2:Class-O Missile Rack",
                        16 => "Defense Laser",
                        18 => "Scanner",
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
