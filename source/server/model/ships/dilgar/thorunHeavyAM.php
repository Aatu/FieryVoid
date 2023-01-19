<?php
class ThorunHeavyAM extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 312;
		$this->faction = "Dilgar";
        $this->phpclass = "ThorunHeavyAM";
        $this->shipClass = "Thorun Heavy Dartfighters";
		$this->imagePath = "img/ships/thorun.png";
		$this->isd = 2232;
				
        $this->notes = '+5 Initiative bonus as long as flight leader is alive and uninjured.';


        $this->occurence = "rare";
        $this->variantOf = "Thorun Dartfighters";
		
		$this->enhancementOptionsEnabled[] = 'NAVIGATOR'; //this flight can take Navigator enhancement option	
        
        $this->forwardDefense = 8;
        $this->sideDefense = 7;
        $this->freethrust = 10;
        $this->offensivebonus = 5;
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
            $armour = array(3, 2, 2, 2);
            
            $fighter = new Fighter("thorun", $armour, 11, $this->id);
            
            $fighter->imagePath = "img/ships/thorun.png";
            $fighter->iconPath = "img/ships/thorun_large.png";
            
            if(count($this->systems) == 0 ){
                $this->flightLeader = $fighter;
            } 
            $fighter->displayName = "Thorun Heavy";

			
			
			//ammo magazine itself (AND its missile options)
			$ammoMagazine = new AmmoMagazine(4); //pass magazine capacity - actual number of rounds, NOT number of salvoes
			$fighter->addAftSystem($ammoMagazine); //fit to ship immediately
			$ammoMagazine->addAmmoEntry(new AmmoMissileFB(), 0); //add basic missile as an option - but do NOT load any actual missiles at this moment - so weapon data is actually filled with _something_!
			$this->enhancementOptionsEnabled[] = 'AMMO_FB';//add enhancement options for missiles - Class-FB
			$this->enhancementOptionsEnabled[] = 'AMMO_FL';//add enhancement options for missiles - Class-FL
			$this->enhancementOptionsEnabled[] = 'AMMO_FH';//add enhancement options for missiles - Class-FH
			//$this->enhancementOptionsEnabled[] = 'AMMO_FY';//add enhancement options for missiles - Class-FY
			//$this->enhancementOptionsEnabled[] = 'AMMO_FD';//add enhancement options for missiles - Class-FD
			
            
            $fighter->addFrontSystem(new PairedLightBoltCannon(330, 30, 4));
			$fighter->addFrontSystem(new AmmoFighterRack(330, 30, $ammoMagazine, false)); //$startArc, $endArc, $magazine, $base
            
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
