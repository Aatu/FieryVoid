<?php
class ArmedShuttleVreeCannon extends FighterFlight{
    /*generic armed shuttles*/
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 48*6;
        $this->faction = "Vree Conglomerate";
        $this->phpclass = "ArmedShuttleVreeCannon";
        $this->shipClass = "Armed Shuttles (Cannon)";
		$this->imagePath = "img/ships/VreeZeoth.png"; //more appropriate image needed
        $this->isd = 2200;
        $this->variantOf = "Armed Shuttles";
        
		Enhancements::nonstandardEnhancementSet($this, 'Shuttles');
        
		
        $this->forwardDefense = 7;
        $this->sideDefense = 7;
        $this->freethrust = 4;
        $this->offensivebonus = 5;
        $this->jinkinglimit = 0;
        $this->pivotcost = 2; //shuttles have pivot cost higher
        $this->turncost = 0.33;
        
		$this->hangarRequired = 'shuttles'; //for fleet check - draws from the default-shuttle pool
		$this->iniativebonus = 9*5;
        $this->gravitic = true;                    
      
        $this->populate();
    }
    
    
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
			
			$armour = array(1, 1, 1, 1);
			$fighter = new Fighter("ArmedShuttleVree", $armour, 7, $this->id);
			$fighter->displayName = "Armed Shuttle";
			$fighter->imagePath = "img/ships/VreeZeoth.png";
			$fighter->iconPath = "img/ships/VreeZeoth_Large.png";
			
			
			$fighter->addFrontSystem(new LtAntimatterCannonUnreliable(330, 30));
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			
			$this->addSystem($fighter);
			
		}
		
		
    }
}
?>
