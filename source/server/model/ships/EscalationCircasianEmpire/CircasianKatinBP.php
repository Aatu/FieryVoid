<?php
class CircasianKatinBP extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 35 * 6;
        $this->faction = "Escalation Wars Circasian Empire";
        $this->phpclass = "CircasianKatinBP";
        $this->shipClass = "Kat'in Breaching Pods";
        $this->imagePath = "img/ships/EscalationWars/CircasianKatin.png";
        $this->isd = 1875;
        
//	    $this->notes = 'Non-atmospheric.';
        
        $this->forwardDefense = 10;
        $this->sideDefense = 10;
        $this->freethrust = 9;
        $this->offensivebonus = 0;
        $this->jinkinglimit = 0;
        $this->pivotcost = 2; //shuttles have pivot cost higher        
        $this->turncost = 0.33;

        $this->maxFlightSize = 2;//this is an unusual type of 'fighter', limit flight size.      
        $this->hangarRequired = 'Breaching Pods'; //for fleet check   
		$this->unitSize = 1; 		
		
		
    	$this->iniativebonus = 9 * 5;
        $this->populate();
    
    	$this->enhancementOptionsEnabled[] = 'ELT_MAR'; //To enable Elite Marines enhancement
		$this->enhancementOptionsEnabled[] = 'EXT_MAR'; //To enable extra Marines enhancement
		
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(2, 2, 2, 2);
            $fighter = new Fighter("CircasianKatinBP", $armour, 15, $this->id);
            $fighter->displayName = "Kat'in";
            $fighter->imagePath = "img/ships/EscalationWars/CircasianKatin.png";
            $fighter->iconPath = "img/ships/EscalationWars/CircasianKatin_Large.png";

			$fighter->addFrontSystem(new Marines(0, 360, 0, false)); //startarc, endarc, damagebonus, elite.

			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            $this->addSystem($fighter);
       }
    }
}
?>
