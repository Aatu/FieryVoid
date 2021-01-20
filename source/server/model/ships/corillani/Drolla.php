<?php
class Drolla extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		    $this->pointCost = 35*6;
		    $this->faction = "Corillani";
        $this->phpclass = "Drolla";
        $this->shipClass = "Drolla Assault Shuttles";
		    $this->imagePath = "img/ships/Drolla.png";
        
        $this->forwardDefense = 8;
        $this->sideDefense = 8;
        $this->freethrust = 7;
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
			
			$armour = array(3, 3, 3, 3);
			$fighter = new Fighter("Drolla", $armour, 18, $this->id);
			$fighter->displayName = "Drolla";
			$fighter->imagePath = "img/ships/Drolla.png";
			$fighter->iconPath = "img/ships/Drolla_large.png";
			
			
			$fighter->addFrontSystem(new PairedParticleGun(330, 30, 5, 1)); //1 gun d6+5
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			
			$this->addSystem($fighter);
			
		}
		
		
    }
}
?>
