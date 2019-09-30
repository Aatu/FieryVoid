<?php
class ThunderboltStarfuryNav extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
    $this->pointCost = 540;
    $this->faction = "EA";
    $this->phpclass = "ThunderboltStarfuryNav";
    $this->shipClass = "Starfury: Thunderbolt Heavy flight (with navigator)";
    $this->imagePath = "img/ships/thunderboltStarfury.png";
	    
    //$this->variantOf = 'Starfury: Thunderbolt Heavy flight';
	$this->variantOf = 'DISABLED'; //disabled because basic TBolt will get Navigator as option, no need for separate hull now!
	$this->isd = 2259;
	$this->notes = 'Needs updated hangars to handle.';

    $this->forwardDefense = 8;
    $this->sideDefense = 7;
    $this->freethrust = 13;
    $this->offensivebonus = 5;
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
            $armour = array(3, 2, 2, 2);
            $fighter = new Fighter("ThunderboltStarfuryNav", $armour, 15, $this->id);
            $fighter->displayName = "Thunderbolt";
            $fighter->imagePath = "img/ships/thunderboltStarfury.png";
            $fighter->iconPath = "img/ships/thunderboltStarfury_large.png";

            $fighter->addFrontSystem(new FighterMissileRack(3, 330, 30));
            $fighter->addFrontSystem(new GatlingPulseCannon(330, 30));
            $fighter->addFrontSystem(new FighterMissileRack(3, 330, 30));
            
            $this->addSystem($fighter);
        }
    }
}
?>
