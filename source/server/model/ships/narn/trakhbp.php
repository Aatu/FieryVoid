<?php
class trakhbp extends FighterFlight{
    /*T'Rakh Narn Breaching Pod*/
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 40*6;
		$this->faction = "Narn Regime";
        $this->phpclass = "trakhbp";
        $this->shipClass = "T'Rakh Breaching Pods";
		$this->imagePath = "img/ships/NarnTRakh.png";
        $this->isd = 2241;
        
        $this->forwardDefense = 9;
        $this->sideDefense = 10;
        $this->freethrust = 5;
        $this->offensivebonus = 0;
        $this->jinkinglimit = 0;
        $this->pivotcost = 2; //shuttles have pivot cost higher
        $this->turncost = 0.33;
        
        $this->maxFlightSize = 3;//this is an unusual type of 'fighter', limit flight size.      
		$this->hangarRequired = 'assault shuttles'; //for fleet check
		$this->unitSize = 1; 
		$this->iniativebonus = 8*5;
      
        $this->populate();
        
		$this->enhancementOptionsEnabled[] = 'EXT_MAR'; //To enable extra Marines enhancement        
        
    }
    
    
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
			
			$armour = array(4, 4, 4, 4);
			$fighter = new Fighter("trakhbp", $armour, 19, $this->id);
			$fighter->displayName = "T'Rakh";
			$fighter->imagePath = "img/ships/NarnTRakh.png";
			$fighter->iconPath = "img/ships/NarnTRakh_Large.png";
			
			$fighter->addFrontSystem(new Marines(0, 360, 0, true)); //startarc, endarc, damagebonus, elite.
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			
			$this->addSystem($fighter);
			
			
		}
		
		
    }
}
?>
