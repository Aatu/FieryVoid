<?php
class AndorianFighter extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 40 *6;
        $this->faction = "ZTrek Playtest Federation";
        $this->phpclass = "AndorianFighter";
        $this->shipClass = "Andorian Medium Fighters";
	    $this->imagePath = "img/ships/StarTrek/AndorianFighter.png";
	    $this->isd = 2150;
		$this->hangarRequired = "medium"; //Initiative suggests it's heavy fighter, but it's in fact medium
        
        $this->forwardDefense = 6;
        $this->sideDefense = 8;
        $this->freethrust = 11;
        $this->offensivebonus = 5;
        $this->jinkinglimit = 7;
        $this->turncost = 0.33;
        $this->iniativebonus = 18 *5;
        $this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(3, 1, 1, 1);
            $fighter = new Fighter("AndorianFighter", $armour, 10, $this->id);
            $fighter->displayName = "Andorian Medium Fighter";
            $fighter->imagePath = "img/ships/StarTrek/AndorianFighter.png";
            $fighter->iconPath = "img/ships/StarTrek/AndorianFighter_Large.png";


            $fighter->addFrontSystem(new LightParticleBlaster(330, 30, 4));

			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
            $this->addSystem($fighter);
        }
    }
}
?>