<?php
class RogolonChelekFtr extends FighterFlight{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 37*6;
        $this->faction = "Small Races";
        $this->phpclass = "RogolonChelekFtr";
        $this->shipClass = "Rogolon Chelek Strike Fighters";
        $this->imagePath = "img/ships/RogolonChelek.png";
        
        $this->isd = 1951;
        
        $this->forwardDefense = 5;
        $this->sideDefense = 7;
        $this->freethrust = 15;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 10;
        $this->turncost = 0.33;
        
    	$this->iniativebonus = 21 *5; 
        
        $this->populate();
    }
    
    
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(1, 0, 1, 1);
            $fighter = new Fighter("RogolonChelekFtr", $armour, 7, $this->id);
            $fighter->displayName = "Chelek";
            $fighter->imagePath = "img/ships/RogolonChelek.png";
            $fighter->iconPath = "img/ships/RogolonChelek_Large.png"; 

            $fighter->addFrontSystem(new RogolonLtPlasmaGun(330, 30, 5, 2));
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
            $this->addSystem($fighter);
       }
    }
    
    
}
?>
