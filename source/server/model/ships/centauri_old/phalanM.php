<?php
class PhalanM extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 150; //6*25
        $this->faction = "Centauri (WotCR)";
        $this->phpclass = "PhalanM";
        $this->shipClass = "Phalan-M Missile Fighters";
	    $this->variantOf = "Phalan Assault Fighters";
		$this->imagePath = "img/ships/phalan.png";
        
        $this->forwardDefense = 9;
        $this->sideDefense = 8;
        $this->freethrust = 9;
        $this->offensivebonus = 3;
        $this->jinkinglimit = 6;
        $this->turncost = 0.33;
        
		$this->iniativebonus = 80;
		
		$this->enhancementOptionsEnabled[] = 'NAVIGATOR'; //this flight can take Navigator enhancement option	
		
        $this->populate();
    }
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
			
			$armour = array(2, 1, 1, 1);
			$fighter = new Fighter("PhalanM", $armour, 12, $this->id);
			$fighter->displayName = "Phalan-M Missile Fighter";
			$fighter->imagePath = "img/ships/phalan.png";
			$fighter->iconPath = "img/ships/phalan_large.png";
			
			
			$fighter->addFrontSystem(new PairedPlasmaBlaster(330, 30, 0)); 
			$fighter->addFrontSystem(new FighterMissileRack(4, 330, 30));
			
			
			$this->addSystem($fighter);
			
		}
		
		
    }
}
?>
