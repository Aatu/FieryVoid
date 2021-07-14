<?php
class BAStarfoxFtr extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 240;
        $this->faction = "Small Races";
        $this->phpclass = "BAStarfoxFtr";
        $this->shipClass = "BA Starfox Fighters";
        $this->imagePath = "img/ships/BAStarFox.png";
        $this->forwardDefense = 7;
        $this->sideDefense = 9;
        $this->freethrust = 9;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 8;
        $this->turncost = 0.33;

        $this->isd = 2208;
        
    	$this->iniativebonus = 90;
        $this->populate();        
    }
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){   
            $armour = array(3, 1, 2, 2);
            $fighter = new Fighter("Starfox", $armour, 10, $this->id);
            $fighter->displayName = "Starfox";
            $fighter->imagePath = "img/ships/BAStarFox.png";
            $fighter->iconPath = "img/ships/BAStarFox_large.png";

            $missileRack = new FighterMissileRack(4, 330, 30);
            $missileRack->firingModes = array(
                1 => "FY"
            );
            $missileRack->missileArray = array(
                1 => new MissileFY(330, 30)
            );
            $frontGun = new MatterGun(330, 30);
            //$frontGun->displayName = "Matter Gun";
            $fighter->addFrontSystem($missileRack);
            $fighter->addFrontSystem($frontGun);
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack

            $this->addSystem($fighter);
        }
    }
}
?>
