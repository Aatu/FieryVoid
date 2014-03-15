<?php
class ThorunHeavyNav extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 372;
	$this->faction = "Dilgar";
        $this->phpclass = "ThorunHeavyNav";
        $this->shipClass = "Thorun Heavy Dartfighters (with navigator)";
	$this->imagePath = "img/ships/thorun.png";
        $this->hasNavigator = true;
        
        $this->forwardDefense = 8;
        $this->sideDefense = 7;
        $this->freethrust = 10;
        $this->offensivebonus = 5;
        $this->jinkinglimit = 6;
        $this->turncost = 0.33;
        
	$this->iniativebonus = 80;
        
        $this->dropOutBonus = -2;
        
        for ($i = 0; $i<6; $i++){
            $armour = array(3, 2, 2, 2);
            
            $fighter = new Fighter("thorun", $armour, 11, $this->id);
            
            $fighter->imagePath = "img/ships/thorun.png";
            $fighter->iconPath = "img/ships/thorun_large.png";
            
            if($i == 0){
                $fighter->displayName = "Thorun Heavy Dartfighter Leader";  
                $this->flightLeader = $fighter;
                $fighter->iconPath = "img/ships/thorun_leader_large.png";
            } else {
                $fighter->displayName = "Thorun Heavy Dartfighter";
            }
            
            $fighter->addFrontSystem(new PairedLightBoltCannon(330, 30, 4));
            $fighter->addFrontSystem(new FighterMissileRack(4, 330, 30));
            
            $this->addSystem($fighter);
        }
    }

    public function getInitiativebonus($gamedata){
        $initiativeBonusRet = parent::getInitiativebonus($gamedata);
        
        // Check if flightleader is still uninjured and alive
        if($this->flightLeader!=null
                && !$this->flightLeader->isDisengaged($gamedata->turn)
                && $this->flightLeader->getRemainingHealth() == 11){
            $initiativeBonusRet += 5;
        }
        
        return $initiativeBonusRet;
    }
}

?>