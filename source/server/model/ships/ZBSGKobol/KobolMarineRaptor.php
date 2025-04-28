<?php
class KobolMarineRaptor extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 420;
        $this->faction = "ZPlaytest 12 Colonies of Kobol";
        $this->phpclass = "KobolMarineRaptor";
        $this->shipClass = "Marine Assault Raptor (Alpha prototype)";
			$this->variantOf = "Raptor C2 (Beta prototype)";
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
    	$this->superheavy = true;
        $this->maxFlightSize = 3;//this is a superheavy fighter originally intended as single unit, limit flight size to 3

		$this->populate();
		
	}
        
    public function populate(){        

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
		
		for ($i = 0; $i < $toAdd; $i++) {
			$armour = array(3, 1, 2, 2);
			$fighter = new Fighter("raptor", $armour, 20, $this->id);
			$fighter->displayName = "Marine Raptor";
			$fighter->imagePath = "img/ships/BSG/raptor.png";
			$fighter->iconPath = "img/ships/BSG/raptor_large.png";

            $frontGun = new LightScattergun(330, 30); //always a single mount for this weapon
            $frontGun->displayName = "Heavy MEC Cannon";
			$marines = new BSGMarineAssault(0, 360);

            $fighter->addFrontSystem($frontGun);
			$fighter->addFrontSystem($marines);

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