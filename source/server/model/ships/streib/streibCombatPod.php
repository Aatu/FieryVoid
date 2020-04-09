<?php
class StreibCombatPod extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 90*6;
		$this->faction = "Streib";
        $this->phpclass = "StreibCombatPod";
        $this->shipClass = "Combat Pods";
		$this->imagePath = "img/ships/streibbreachingpod.png";
        	    
		$this->notes = 'No marine units carried.';
	    
        $this->forwardDefense = 8;
        $this->sideDefense = 8;
        $this->freethrust = 10;
        $this->offensivebonus = 5;
        $this->jinkinglimit = 4;
        $this->pivotcost = 2; //shuttles have pivot cost higher
	    $this->gravitic = true;
        $this->turncost = 0.33;
        $this->turndelaycost = 0;
        
		$this->hangarRequired = 'shuttles'; //for fleet check
		$this->iniativebonus = 12*5;
      
        $this->populate();
    }
    
    
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){			
			$armour = array(6, 6, 6, 6);
			$fighter = new Fighter("StreibCombatPod", $armour, 10, $this->id);
			$fighter->displayName = "Combat Pod";
			$fighter->imagePath = "img/ships/streibbreachingpod.png";
			$fighter->iconPath = "img/ships/streibbreachingpod_Large.png";
			

			$frontGun = new LtSurgeBlaster(330, 30, 2); //2 guns - dmg bonus not selectable
			$fighter->addFrontSystem($frontGun);			
			$fighter->addFrontSystem(new LtEMWaveDisruptor(240, 120, 1));	

			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			
			$this->addSystem($fighter);
				
		}
    }
}
?>
