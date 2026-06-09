<?php
class SshelathRitesh extends OSAT{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 60;
	$this->faction = "Escalation Wars Sshel'ath Alliance";
        $this->phpclass = "SshelathRitesh";
        $this->imagePath = "img/ships/EscalationWars/SshelathRitesh.png";
        $this->shipClass = 'Ritesh OSAT';
	    $this->isd = 1900;
		$this->unofficial = true;
        $this->canvasSize = 80;
        
        $this->forwardDefense = 7;
        $this->sideDefense = 7;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60;

        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(16); //pass magazine capacity - 60 rounds per launcher
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileD(), 16); //add full load of basic missiles

        $this->addFrontSystem(new AmmoMissileRackO(2, 0, 0, 270, 90, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addFrontSystem(new AmmoMissileRackO(2, 0, 0, 270, 90, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addFrontSystem(new EWDefenseLaser(0, 2, 1, 0, 360));
		$this->addFrontSystem(new EWDefenseLaser(0, 2, 1, 0, 360));
	
        $this->addPrimarySystem(new OSATCnC(0, 1, 0, 0));
        $this->addPrimarySystem(new Reactor(2, 4, 0, 0));
        $this->addPrimarySystem(new Scanner(2, 3, 1, 2)); 
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        
        $this->addPrimarySystem(new Structure(2, 20));

	//d20 hit chart
        $this->hitChart = array(
            0=> array( //PRIMARY
				10 => "Structure",
				13 => "1:Class-O Missile Rack",
				15 => "1:Defense Laser",
				17 => "Scanner",
				20 => "Reactor",
            )
        );
	    
	    
    }
}

?>
