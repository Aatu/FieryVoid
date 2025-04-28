<?php
class Olgata extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	    $this->pointCost = 30*6;
	    $this->faction = "Dilgar Imperium";
        $this->phpclass = "Olgata";
        $this->shipClass = "Olgata Assault Shuttles";
	    $this->imagePath = "img/ships/Torushka.png";
        $this->isd = 2226;
        
        $this->forwardDefense = 10;
        $this->sideDefense = 10;
        $this->freethrust = 8;
        $this->offensivebonus = 3;
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
			$fighter = new Fighter("Olgata", $armour, 8, $this->id);
			$fighter->displayName = "Olgata";
			$fighter->imagePath = "img/ships/Torushka.png";
			$fighter->iconPath = "img/ships/Torushka_large.png";
			
			$fighter->addFrontSystem(new PairedLightBoltCannon(330, 30, 4, 1)); //1 gun d6+4
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			
			$this->addSystem($fighter);
			
		}
		
		
    }
}
?>
