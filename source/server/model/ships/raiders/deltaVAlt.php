<?php
class DeltaVAlt extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 168;
        $this->faction = "Raiders";
        $this->phpclass = "DeltaVAlt";
        $this->shipClass = "Delta-V Alternate Light Fighters";
        $this->imagePath = "img/ships/deltaV.png";
        $this->variantOf = "Delta-V Light Fighters";
        
        $this->forwardDefense = 5;
        $this->sideDefense = 5;
        $this->freethrust = 9;
        $this->offensivebonus = 3;
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
		$armour = array(2, 0, 1, 1);
		$fighter = new Fighter("deltaV", $armour, 7, $this->id);
		$fighter->displayName = "Delta-V";
		$fighter->imagePath = "img/ships/deltaV.png";
		$fighter->iconPath = "img/ships/deltaV_large.png";
			
		$fighter->addFrontSystem(new PairedParticleGun(330, 30, 1));
			
		$this->addSystem($fighter);
	}
    }
}


