<?php
class SkySerpent extends SuperHeavyFighter{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 130;
        $this->faction = "Drazi";
        $this->phpclass = "SkySerpent";
        $this->shipClass = "Sky Serpent Heavy Fighter";
        $this->imagePath = "img/ships/skyserpent.png";

        $this->forwardDefense = 10;
        $this->sideDefense = 9;
        $this->freethrust = 10;
        $this->offensivebonus = 7;
        $this->jinkinglimit = 4;
        $this->turncost = 0.33;

        $this->iniativebonus = 70;
        $this->hasNavigator = true;
        
        $armour = array(4, 2, 3, 3);
        $fighter = new Fighter("skyserpent", $armour, 30, $this->id);
        $fighter->displayName = "Sky Serpent Heavy Fighter";
        $fighter->imagePath = "img/ships/skyserpent.png";
        $fighter->iconPath = "img/ships/skyserpent_large.png";

        $fighter->addFrontSystem(new PairedParticleGun(330, 30, 5));
        $fighter->addFrontSystem(new FighterMissileRack(6, 330, 30));
        $fighter->addFrontSystem(new FighterMissileRack(6, 330, 30));
        
        $particleBlaster = new ParticleBlaster(0, 0, 0, 330, 30);
        $particleBlaster->fireControl = array(-4, 0, 0);
        $particleBlaster->loadingtime = 3;
        $fighter->addFrontSystem($particleBlaster);
        
        $this->addSystem($fighter);
    }

    public function populate(){
        return;
    }
    
    public function getInitiativebonus($gamedata){
        $initiativeBonusRet = parent::getInitiativebonus($gamedata);
        
        if($gamedata->turn > 0 && $gamedata->phase >= 0 ){
            // If within 5 hexes of a Fanged Serpent,
            // each Sky Serpent gets +1 initiative.
            $pixPos = $this->getCoPos();
            
            $ships = $gamedata->getShipsInDistance($pixPos, ((5*mathlib::$hexWidth) + 1));

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
    }
}
