<?php
class tkharas extends FighterFlight{
    /*T'Khar Narn Assault Shuttle*/
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		    $this->pointCost = 30*6;
		    $this->faction = "Narn";
        $this->phpclass = "tkharas";
        $this->shipClass = "T'Khar Assault Shuttles";
		    $this->imagePath = "img/ships/gorith.png";
        
        $this->forwardDefense = 9;
        $this->sideDefense = 9;
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
			
			$armour = array(2, 2, 2, 2);
			$fighter = new Fighter("tkharas", $armour, 8, $this->id);
			$fighter->displayName = "T'Khar";
			$fighter->imagePath = "img/ships/gorith.png";
			$fighter->iconPath = "img/ships/gorith_large.png";
			
			
			$fighter->addFrontSystem(new PairedParticleGun(330, 30, 5, 1)); //1 gun d6+5
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			
			$this->addSystem($fighter);
			
		}
		
		
    }
}
?>
