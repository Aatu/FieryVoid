<?php
class Tzymm extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 85*6;
        $this->phpclass = "Tzymm";
        $this->shipClass = "Tzymm Heavy Fighters";
        $this->imagePath = "img/ships/VreeTyzmm.png";
        
        $this->faction = "Vree";
	    $this->isd = 2210;

        $this->forwardDefense = 9;
        $this->sideDefense = 9;
        $this->freethrust = 8;
        $this->offensivebonus = 5;
        $this->jinkinglimit = 6;
        $this->turncost = 0.33;
        $this->iniativebonus = 80;

        
        $this->populate();
    }

    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(3, 3, 3, 3);
            $fighter = new Fighter("Tzymm", $armour, 17, $this->id);
            $fighter->displayName = "Tzymm";
            $fighter->imagePath = "img/ships/VreeTyzmm.png";
            $fighter->iconPath = "img/ships/VreeTyzmm_Large.png";
            $fighter->addFrontSystem(new LightAntiprotonGun(330, 30, 2));
            $fighter->addFrontSystem(new LtAntimatterCannon(330, 30));
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
            $this->addSystem($fighter);
        }
    }
}
?>
