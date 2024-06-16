<?php
class breachingpoddrazi extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		    $this->pointCost = 80*6;
		    $this->faction = "Drazi Freehold";
        $this->phpclass = "breachingpoddrazi";
        $this->shipClass = "Shallana Armed Breaching Pod";
		    $this->imagePath = "img/ships/drazi/DraziTroshanthi.png";
        
        $this->forwardDefense = 8;
        $this->sideDefense = 10;
        $this->freethrust = 8;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 0;
        $this->pivotcost = 2; //shuttles have pivot cost higher
        $this->turncost = 0.33;
        
		$this->hangarRequired = 'assault shuttles'; //for fleet check
		    $this->iniativebonus = 10*5;
      
        $this->populate();
    }
    
    
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
			
			$armour = array(2, 2, 2, 2);
			$fighter = new Fighter("breachingpoddrazi", $armour, 18, $this->id);
			$fighter->displayName = "Shallana";
			$fighter->imagePath = "img/ships/drazi/DraziTroshanthi.png";
			$fighter->iconPath = "img/ships/drazi/DraziTroshanthi_large.png";

			$fighter->addFrontSystem(new PairedParticleGun(330, 30, 2, 2)); //2 gun d6+2			
			$fighter->addFrontSystem(new Marines(330, 30, 0, false)); //startarc, endarc, damagebonus, elite.
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			
			$this->addSystem($fighter);
			
		}
		
		
    }
}
?>
