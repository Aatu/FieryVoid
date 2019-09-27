<?php
class PhalanMNav extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 210; //6*25+Navigators
        $this->faction = "Centauri (WotCR)";
        $this->phpclass = "PhalanMNav";
        $this->shipClass = "Phalan-M Missile Fighters (with navigator)";
	    $this->variantOf = "Phalan Assault Fighters";
	$this->variantOf = 'DISABLED'; //disabled because basic fighter will get Navigator as option, no need for separate hull now!
		$this->imagePath = "img/ships/phalan.png";
        
        $this->forwardDefense = 9;
        $this->sideDefense = 8;
        $this->freethrust = 9;
        $this->offensivebonus = 3;
        $this->jinkinglimit = 6;
        $this->turncost = 0.33;

	$this->hasNavigator = true;
        
		$this->iniativebonus = 80;
        $this->populate();
    }
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
			
			$armour = array(2, 1, 1, 1);
			$fighter = new Fighter("PhalanMNav", $armour, 12, $this->id);
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
