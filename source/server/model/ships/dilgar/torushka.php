<?php
class Torushka extends FighterFlight{
	/*old Dilgar fighter, unofficial - Showdowns-10*/
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 37*6;
        $this->faction = "Dilgar";
        $this->phpclass = "Torushka";
        $this->shipClass = "Torushka Stingfighters";
        $this->imagePath = "img/ships/thorun.png";


        $this->forwardDefense = 7;
        $this->sideDefense = 6;
        $this->freethrust = 9;
        $this->offensivebonus = 3;
        $this->jinkinglimit = 8; //Medium fighter
        $this->turncost = 0.33;

        $this->iniativebonus = 18 *5;

        $this->dropOutBonus = -2;
        $this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(2, 1, 1, 1);
            
            $fighter = new Fighter("Torushka", $armour, 8, $this->id);
            
            $fighter->imagePath = "img/ships/thorun.png";
            $fighter->iconPath = "img/ships/thorun_large.png";
            
            if(count($this->systems) == 0 ){
                $this->flightLeader = $fighter;
            } 
			$fighter->displayName = "Torushka";
            
            $gun = new PairedLightBoltCannon(330, 30, 2);
			$gun->displayName = 'Light Bolter';
            $fighter->addFrontSystem($gun);
            
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
            
            $this->addSystem($fighter);
        }
    }

    public function getInitiativebonus($gamedata){
        $initiativeBonusRet = parent::getInitiativebonus($gamedata);
        
        // Check if flightleader is still uninjured and alive
        if($this->flightLeader!=null
                && !$this->flightLeader->isDisengaged($gamedata->turn)
                && $this->flightLeader->getRemainingHealth() == 8){
					$initiativeBonusRet += 5;
				}
        
        return $initiativeBonusRet;
    }
}

?>
