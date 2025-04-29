<?php
class Roshanthi extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		    $this->pointCost = 22*6;
		    $this->faction = "Drazi Freehold (WotCR)";
        $this->phpclass = "Roshanthi";
        $this->shipClass = "Roshanthi Assault Shuttles";
		    $this->imagePath = "img/ships/drazi/DraziRoshanthi.png";
            $this->isd = 1940;
        
        $this->forwardDefense = 8;
        $this->sideDefense = 9;
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
			$fighter = new Fighter("Roshanthi", $armour, 8, $this->id);
			$fighter->displayName = "Roshanthi";
			$fighter->imagePath = "img/ships/drazi/DraziRoshanthi.png";
			$fighter->iconPath = "img/ships/drazi/DraziRoshanthi_large.png";
			
			
			$fighter->addFrontSystem(new PairedParticleGun(330, 30, 2, 1)); //1 gun d6+2
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			
			$this->addSystem($fighter);
			
		}
		
		
    }
}
?>
