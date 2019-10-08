<?php
class PhalanI extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 180; //6*30
        $this->faction = "Centauri (WotCR)";
        $this->phpclass = "PhalanI";
        $this->shipClass = "Phalan-I Heavy Interceptors";
	    $this->variantOf = "Phalan Assault Fighters";
		$this->imagePath = "img/ships/phalan.png";
        
        $this->forwardDefense = 9;
        $this->sideDefense = 8;
        $this->freethrust = 9;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 6;
        $this->turncost = 0.33;
                $this->occurence = "uncommon";
        
		$this->iniativebonus = 80;
        $this->populate();
    }
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
			
			$armour = array(2, 1, 1, 1);
			$fighter = new Fighter("phalani", $armour, 12, $this->id);
			$fighter->displayName = "Phalan-I";
			$fighter->imagePath = "img/ships/phalan.png";
			$fighter->iconPath = "img/ships/phalan_large.png";
			
			
			$fighter->addFrontSystem(new PairedPlasmaBlaster(330, 30, 0, 3)); //triple-linked

			
			
			$this->addSystem($fighter);
			
		}
		
		
    }
}
?>
