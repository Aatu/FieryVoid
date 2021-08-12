<?php
class KobolEWRaptor extends SuperHeavyFighter{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 420;
        $this->faction = "ZPlaytest 12 Colonies of Kobol (Tier 1)";
        $this->phpclass = "KobolEWRaptor";
        $this->shipClass = "EW Raptor (Beta prototype)";
			$this->variantOf = "Raptor (Beta prototype)";
			$this->occurence = "common";
        $this->imagePath = "img/ships/BSG/raptor.png";
//	    $this->isd = ;
//        $this->canvasSize = 60;
		$this->unofficial = true;

	    $this->notes = 'Atmospheric.';
	    $this->notes .= '<br>Cannot fire both weapons on the same turn.';
	    $this->notes .= '<br>Gains +10 initiative when within 5 hexes of a standard Raptor.';
		
        $this->forwardDefense = 8;
        $this->sideDefense = 9;
        $this->freethrust = 10;
        $this->offensivebonus = 6;
        $this->jinkinglimit = 4;
        $this->turncost = 0.33;
//        $this->turndelaycost = 0.10;

		$this->hangarRequired = 'superheavy'; //for fleet check
        $this->iniativebonus = 70;
        $this->hasNavigator = true;
        $this->maxFlightSize = 3;//this is a superheavy fighter originally intended as single unit, limit flight size to 3

		$this->populate();
		
	}
        
    public function populate(){        

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
		
		for ($i = 0; $i < $toAdd; $i++) {
			$armour = array(2, 1, 2, 2);
			$fighter = new Fighter("raptor", $armour, 20, $this->id);
			$fighter->displayName = "EW Raptor";
			$fighter->imagePath = "img/ships/BSG/raptor.png";
			$fighter->iconPath = "img/ships/BSG/raptor_large.png";

			$ewGun = new SensorSpikeFtr(300, 60, 0);
			$fighter->addFrontSystem($ewGun);
			$commGun = new CommJammerFtr(300, 60, 0);
			$fighter->addFrontSystem($commGun);

			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			$this->addSystem($fighter);
		}
    }

    public function getInitiativebonus($gamedata){
        $initiativeBonusRet = parent::getInitiativebonus($gamedata);
        
        if($gamedata->turn > 0 && $gamedata->phase >= 0 ){
            // If within 5 hexes of a Raptor,
            // each EW Raptor gets +2 initiative.
            
            $ships = $gamedata->getShipsInDistance($this, 5);

            foreach($ships as $ship){
                if(!$ship->isDestroyed()
                        && ($this->userid == $ship->userid)
                        && ($ship instanceof KobolRaptor)){
                    $initiativeBonusRet+=10;
                    break;
                }
            }
        }
        
        return $initiativeBonusRet;
    }	


}