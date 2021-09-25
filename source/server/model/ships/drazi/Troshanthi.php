<?php
class Troshanthi extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		    $this->pointCost = 30*6;
		    $this->faction = "Drazi";
        $this->phpclass = "Troshanthi";
        $this->shipClass = "Troshanthi Assault Shuttles";
		    $this->imagePath = "img/ships/drazi/DraziTroshanthi.png";
        
        $this->forwardDefense = 7;
        $this->sideDefense = 9;
        $this->freethrust = 6;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 0;
        $this->pivotcost = 2; //shuttles have pivot cost higher
        $this->turncost = 0.33;
        
		$this->hangarRequired = 'assault shuttles'; //for fleet check
		    $this->iniativebonus = 11*5;
      
        $this->populate();
    }
    
    
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
			
			$armour = array(3, 3, 3, 3);
			$fighter = new Fighter("Troshanthi", $armour, 10, $this->id);
			$fighter->displayName = "Troshanthi";
			$fighter->imagePath = "img/ships/drazi/DraziTroshanthi.png";
			$fighter->iconPath = "img/ships/drazi/DraziTroshanthi_large.png";
			
			
			$fighter->addFrontSystem(new PairedParticleGun(330, 30, 2, 1)); //1 gun d6+2
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			
			$this->addSystem($fighter);
			
		}
		
		
    }
}
?>
