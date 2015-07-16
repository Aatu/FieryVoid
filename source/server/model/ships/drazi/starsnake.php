<?php
class Starsnake extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 240;
	$this->faction = "Drazi";
        $this->phpclass = "Starsnake";
        $this->shipClass = "Starsnake Light Fighters";
	$this->imagePath = "img/ships/starsnake.png";
        
        $this->forwardDefense = 6;
        $this->sideDefense = 8;
        $this->freethrust = 13;
        $this->offensivebonus = 5;
        $this->jinkinglimit = 10;
        $this->turncost = 0.33;
        $this->iniativebonus = 110;
        $this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(1, 1, 1, 1);
            $fighter = new Fighter("starsnake", $armour, 8, $this->id);
            $fighter->displayName = "Starsnake Light Fighter";
            $fighter->imagePath = "img/ships/starsnake.png";
            $fighter->iconPath = "img/ships/starsnake_large.png";


            $fighter->addFrontSystem(new LightParticleBlaster(330, 30, 5));

            $this->addSystem($fighter);
        }
    }
}
?>