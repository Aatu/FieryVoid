<?php
class ColonialAssaultRaptorAM extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 660;
        $this->faction = "BSG Colonials";
        $this->phpclass = "ColonialAssaultRaptorAM";
        $this->shipClass = "Assault Raptor";
			$this->variantOf = "Raptor";
			$this->occurence = "common";
			$this->limited = 33;
        $this->imagePath = "img/ships/BSG/raptor.png";
//	    $this->isd = ;
//        $this->canvasSize = 60;
		$this->unofficial = true;

	    $this->notes = 'Atmospheric.';
	    $this->notes .= '<br>Gains +5 initiative when within 5 hexes of a standard Raptor.';
		
        $this->forwardDefense = 7;
        $this->sideDefense = 9;
        $this->freethrust = 10;
        $this->offensivebonus = 5;
        $this->jinkinglimit = 4;
        $this->turncost = 0.33;
        $this->turndelaycost = 0.10;

		$this->hangarRequired = 'superheavy'; //for fleet check
        $this->iniativebonus = 70;
        $this->hasNavigator = true;
    	$this->superheavy = true;
        $this->maxFlightSize = 3;//this is a superheavy fighter originally intended as single unit, limit flight size to 3

		$this->populate();
		
	}
        
    public function populate(){        

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
		
		for ($i = 0; $i < $toAdd; $i++) {
			$armour = array(4, 3, 3, 3);
			$fighter = new Fighter("raptor", $armour, 20, $this->id);
			$fighter->displayName = "Assault Raptor";
			$fighter->imagePath = "img/ships/BSG/raptor.png";
			$fighter->iconPath = "img/ships/BSG/raptor_large.png";

			//ammo magazine itself (AND its missile options)
			$ammoMagazine = new AmmoMagazine(6); //pass magazine capacity - actual number of rounds, NOT number of salvoes
			$fighter->addAftSystem($ammoMagazine); //fit to ship immediately
			$ammoMagazine->addAmmoEntry(new AmmoMissileFB(), 0); //add basic missile as an option - but do NOT load any actual missiles at this moment - so weapon data is actually filled with _something_!
			$this->enhancementOptionsEnabled[] = 'AMMO_FB';//add enhancement options for missiles - Class-FB
			$this->enhancementOptionsEnabled[] = 'AMMO_FL';//add enhancement options for missiles - Class-FL
			$this->enhancementOptionsEnabled[] = 'AMMO_FH';//add enhancement options for missiles - Class-FH
			$this->enhancementOptionsEnabled[] = 'AMMO_FY';//add enhancement options for missiles - Class-FY

//			$frontGun = new BSGKineticEnergyWeapon(330, 30, 2, 4);
//			$frontGun->displayName = "Kinetic Energy Cannon";

            $frontGun = new BSGLtKineticEnergyWeaponVA(345, 15, 15, 3, 4);//narower arc (from/to), difference between narrow and wide arc, damage bonus, number of shots
            $fighter->addFrontSystem($frontGun);
			$hvyGun = new BSGHvyKineticEnergyWeapon(330, 30, 1); //$startArc, $endArc, $nrOfShots
			$hvyGun->displayName = "Heavy Kinetic Energy Cannon";
			
			$missile1 = new AmmoFighterRack(330, 30, $ammoMagazine, false); //$startArc, $endArc, $magazine, $base
			$missile1->displayName = "Missile Pod";
			$missile2 = new AmmoFighterRack(330, 30, $ammoMagazine, false); //$startArc, $endArc, $magazine, $base
			$missile2->displayName = "Missile Pod";
			
			
/*			
			$missile1 = new FighterMissileRack(3, 330, 30);
			$missile1->displayName = "Missile Pod";
			$missile2 = new FighterMissileRack(3, 330, 30);
			$missile2->displayName = "Missile Pod";
*/

//			$fighter->addFrontSystem($frontGun);
			$fighter->addFrontSystem($hvyGun);

			$fighter->addAftSystem($missile1);
			$fighter->addAftSystem($missile2);
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			$this->addSystem($fighter);
		}
    }
    
    public function getInitiativebonus($gamedata){
        $initiativeBonusRet = parent::getInitiativebonus($gamedata);
        
        if($gamedata->turn > 0 && $gamedata->phase >= 0 ){
            // If within 5 hexes of a Raptor,
            // each Assault Raptor gets +2 initiative.
            
            $ships = $gamedata->getShipsInDistance($this, 5);

            foreach($ships as $ship){
                if(!$ship->isDestroyed()
                        && ($this->userid == $ship->userid)
                        && ($ship instanceof ColonialRaptor)){
                    $initiativeBonusRet+=5;
                    break;
                }
            }
        }
        
        return $initiativeBonusRet;
    }	
	
}