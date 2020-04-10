<?php
class ThorunEarly extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 264;
        $this->faction = "Dilgar";
        $this->phpclass = "ThorunEarly";
        $this->shipClass = "Thorun Dartfighters (early)";
        $this->imagePath = "img/ships/thorun.png";
        
        $this->variantOf = "Thorun Dartfighters";

        $this->forwardDefense = 8;
        $this->sideDefense = 7;
        $this->freethrust = 12;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 6;
        $this->turncost = 0.33;

        $this->iniativebonus = 80;

        $this->dropOutBonus = -2;
        $this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(2, 1, 2, 2);
            
            $fighter = new Fighter("thorun", $armour, 11, $this->id);
            
            $fighter->imagePath = "img/ships/thorun.png";
            $fighter->iconPath = "img/ships/thorun_large.png";

            if(count($this->systems) == 0 ){
                //$fighter->displayName = "Thorun Leader";  
                $this->flightLeader = $fighter;
                //$fighter->iconPath = "img/ships/thorun_leader_large.png";
            } //else {
                $fighter->displayName = "Thorun (early)";
            //}
            
            $fighter->addFrontSystem(new PairedLightBoltCannon(330, 30, 2));
            
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
            
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
