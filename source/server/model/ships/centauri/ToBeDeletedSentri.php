<?php
class ToBeDeletedSentri extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 252;
		$this->faction = "Centauri";
		$this->phpclass = "ToBeDeletedSentri";
		$this->variantOf = "to be deleted";
		$this->shipClass = "Sentri Interceptors";
		$this->imagePath = "img/ships/sentri.png";
	    
		$this->isd = 2202;
        
		$this->forwardDefense = 7;
		$this->sideDefense = 5;
		$this->freethrust = 12;
		$this->offensivebonus = 7;
		$this->jinkinglimit = 8;
		$this->turncost = 0.33;
        
		$this->iniativebonus = 90;
        	$this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
			
			$armour = array(3, 2, 3, 3);
			$fighter = new Fighter("ToBeDeletedSentri", $armour, 10, $this->id);
			$fighter->displayName = "Sentri";
			$fighter->imagePath = "img/ships/sentri.png";
			$fighter->iconPath = "img/ships/sentri_large.png";
			
			
			$fighter->addFrontSystem(new PairedParticleGun(330, 30, 2));
			//$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
					
			
			$this->addSystem($fighter);
			
		}
		
		
    }

}



?>
