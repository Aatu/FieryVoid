<?php
class genericArmedShuttle extends FighterFlight{
    /*generic armed shuttles*/
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		    $this->pointCost = 15*6;
		    $this->faction = "Civilians";
        $this->phpclass = "genericArmedShuttle";
        $this->shipClass = "Armed Shuttles";
		    $this->imagePath = "img/ships/LlortLeteerum.png"; //more appropriate image needed
        
		$this->notes = "Usually housed in common shuttle bays (not mentioned in FV). Most ships can take a pair if they have to.";
		
        $this->forwardDefense = 8;
        $this->sideDefense = 10;
        $this->freethrust = 6;
        $this->offensivebonus = 2;
        $this->jinkinglimit = 0;
        $this->pivotcost = 2; //shuttles have pivot cost higher
        $this->turncost = 0.33;
        
		$this->hangarRequired = 'assault shuttles'; //for fleet check
		$this->iniativebonus = 9*5;
      
        $this->populate();
    }
    
    
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
			
			$armour = array(2, 2, 2, 2);
			$fighter = new Fighter("genericArmedShuttle", $armour, 10, $this->id);
			$fighter->displayName = "Armed Shuttle";
			$fighter->imagePath = "img/ships/LlortLeteerum.png";
			$fighter->iconPath = "img/ships/LlortLeteerum_Large.png";
			
			
			$fighter->addFrontSystem(new PairedParticleGun(330, 30, 2, 1)); //1 gun d6+2
	    		$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			
			$this->addSystem($fighter);
			
		}
		
		
    }
}
?>
