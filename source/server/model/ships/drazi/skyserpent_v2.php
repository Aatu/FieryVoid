<?php
class SkySerpent_v2 extends SuperHeavyFighter{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 130*6;
        $this->faction = "Drazi";
        $this->phpclass = "SkySerpent_v2";
        $this->shipClass = "Sky Serpent Heavy Assault Fighters";
        $this->imagePath = "img/ships/drazi/DraziSkyserpent.png";
	    $this->isd = 2220;
        $this->canvasSize = 64;

        $this->forwardDefense = 10;
        $this->sideDefense = 9;
        $this->freethrust = 10;
        $this->offensivebonus = 7;
        $this->jinkinglimit = 4;
        $this->turncost = 0.33;
		$this->turndelaycost = 0.25;

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
			$armour = array(4, 2, 3, 3);
			$fighter = new Fighter("skyserpent", $armour, 30, $this->id);
			$fighter->displayName = "Sky Serpent";
			$fighter->imagePath = "img/ships/drazi/DraziSkyserpent.png";
			$fighter->iconPath = "img/ships/drazi/DraziSkyserpent_large.png";

			$fighter->addFrontSystem(new PairedParticleGun(330, 30, 5));
			$fighter->addFrontSystem(new FighterMissileRack(6, 330, 30));
			$fighter->addFrontSystem(new FighterMissileRack(6, 330, 30));
        
			$particleBlaster = new ParticleBlasterFtr(330, 30, 1); //$startArc, $endArc, $nrOfShots
			$fighter->addAftSystem($particleBlaster);
        
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			$this->addSystem($fighter);
		}
    }

    public function getInitiativebonus($gamedata){
        $initiativeBonusRet = parent::getInitiativebonus($gamedata);
        
        if($gamedata->turn > 0 && $gamedata->phase >= 0 ){
            // If within 5 hexes of a Fanged Serpent,
            // each Sky Serpent gets +1 initiative.
            
            $ships = $gamedata->getShipsInDistance($this, 5);

            foreach($ships as $ship){
                if(!$ship->isDestroyed()
                        && ($this->userid == $ship->userid)
                        && ($ship instanceof FangedSerpent_v2)){
                    $initiativeBonusRet+=5;
                    break;
                }
            }
        }
        
        return $initiativeBonusRet;
    }
}

?>
