<?php
class ColonialRaptor extends SuperHeavyFighter{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 480;
        $this->faction = "ZPlaytest BSG Colonials";
        $this->phpclass = "ColonialRaptor";
        $this->shipClass = "Raptor";
        $this->imagePath = "img/ships/BSG/raptor.png";
//	    $this->isd = ;
//        $this->canvasSize = 60;
		$this->unofficial = true;

	    $this->notes = 'Atmospheric.';
		$this->notes .= '<br>Provides +5 Initiative all Vipers and Assault Raptors within 5 hexes.';
		
        $this->forwardDefense = 7;
        $this->sideDefense = 9;
        $this->freethrust = 10;
        $this->offensivebonus = 5;
        $this->jinkinglimit = 4;
        $this->turncost = 0.33;
        $this->turndelay = 0.10;

		$this->hangarRequired = 'superheavy'; //for fleet check
        $this->iniativebonus = 70;
        $this->hasNavigator = true;
        $this->maxFlightSize = 3;//this is a superheavy fighter originally intended as single unit, limit flight size to 3
        
        $armour = array(4, 2, 3, 3);
        $fighter = new Fighter("raptor", $armour, 30, $this->id);
        $fighter->displayName = "Raptor Basic Fit";
        $fighter->imagePath = "img/ships/BSG/raptor.png";
        $fighter->iconPath = "img/ships/BSG/raptor_large.png";

            $frontGun = new BSGKineticEnergyWeapon(340, 20, 2, 4);
            $frontGun->displayName = "Kinetic Energy Cannon";
			$ewGun = new SensorSpearFtr(240, 120, 0);

            $fighter->addFrontSystem($frontGun);
			$fighter->addFrontSystem($ewGun);
        
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
        $this->addSystem($fighter);
    }

    public function populate(){
        return;
    }
    
/*    public function getInitiativebonus($gamedata){
        $initiativeBonusRet = parent::getInitiativebonus($gamedata);
        
        if($gamedata->turn > 0 && $gamedata->phase >= 0 ){
            // If within 5 hexes of a Fanged Serpent,
            // each Sky Serpent gets +1 initiative.
            
            $ships = $gamedata->getShipsInDistance($this, 5);

            foreach($ships as $ship){
                if(!$ship->isDestroyed()
                        && ($this->userid == $ship->userid)
                        && ($ship instanceof FangedSerpent)){
                    $initiativeBonusRet+=5;
                    break;
                }
            }
        }
        
        return $initiativeBonusRet;
    } */
}