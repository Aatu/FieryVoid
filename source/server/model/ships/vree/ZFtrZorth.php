<?php
class ZFtrZorth extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 35*6;
		$this->faction = "Vree";
		$this->phpclass = "ZFtrZorth";
        $this->shipClass = "Zorth Light Fighters";
        $this->imagePath = "img/ships/VreeZorth.png";
        
        $this->faction = "Vree";
	    $this->isd = 2162;
        $this->notes = 'Each hangar space on a ship can stack two Zorth fighters.';	    

        $this->forwardDefense = 6;
        $this->sideDefense = 6;
        $this->freethrust = 12;
        $this->offensivebonus = 6;
        $this->jinkinglimit = 10;
        $this->turncost = 0.33;
        $this->iniativebonus = 100;
		$this->unitSize = 2;        

        
        $this->populate();
    }

    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(1, 1, 1, 1);
            $fighter = new Fighter("Zorth", $armour, 9, $this->id);
            $fighter->displayName = "Zorth";
            $fighter->imagePath = "img/ships/VreeZorth.png";
            $fighter->iconPath = "img/ships/VreeZorth_Large.png";
            $fighter->addFrontSystem(new LightAntiprotonGun(300, 60, 1));
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
            $this->addSystem($fighter);
        }
    }
}
?>
