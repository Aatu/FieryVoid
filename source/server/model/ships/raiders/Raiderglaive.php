<?php
class Raiderglaive extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 28*6;
        $this->faction = "Raiders";
        $this->phpclass = "Raiderglaive";
        $this->shipClass = "Raider Glaive Light Fighters";
		$this->imagePath = "img/ships/glaive.png";
		$this->isd = 2012;
	    
	    $this->variantOf = "DISABLED";  //no point in having two identical Glaives in one directory
	    
	    $this->notes = "For Centauri Privateers - available even before 2000.";
        
        $this->forwardDefense = 7;
        $this->sideDefense = 7;
        $this->freethrust = 12;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 10;
        $this->turncost = 0.33;
        
		$this->iniativebonus = 100;
        $this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
			
			$armour = array(1, 1, 1, 1);
			$fighter = new Fighter("glaive", $armour, 8, $this->id);
			$fighter->displayName = "Glaive";
			$fighter->imagePath = "img/ships/glaive.png";
			$fighter->iconPath = "img/ships/glaive_large.png";
			
			
			$fighter->addFrontSystem(new PairedPlasmaBlaster(330, 30));
			
			
			$this->addSystem($fighter);
			
		}
		
		
    }

}



?>
