<?php
class Sentri extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 252;
		$this->faction = "Centauri";
        $this->phpclass = "Sentri";
        $this->shipClass = "Sentri flight";
		$this->imagePath = "img/ships/sentri.png";
        
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
			$fighter = new Fighter("sentri", $armour, 10, $this->id);
			$fighter->displayName = "Sentri Medium Fighter";
			$fighter->imagePath = "img/ships/sentri.png";
			$fighter->iconPath = "img/ships/sentri_large.png";
			
			
			$fighter->addFrontSystem(new PairedParticleGun(330, 30, 2));
			
			
			$this->addSystem($fighter);
			
		}
		
		
    }

}



?>
