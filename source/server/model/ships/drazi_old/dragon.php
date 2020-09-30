<?php
class Dragon extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
    	$this->pointCost = 210;
        $this->faction = "Drazi (WotCR)";
        $this->phpclass = "Dragon";
        $this->shipClass = "Dragon Light Fighters";
    	$this->imagePath = "img/ships/dragon.png";
        
        $this->forwardDefense = 6;
        $this->sideDefense = 7;
        $this->freethrust = 12;
        $this->offensivebonus = 3;
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
            $armour = array(2, 0, 0, 0);
            $fighter = new Fighter("dragon", $armour, 6, $this->id);
            $fighter->displayName = "Dragon";
            $fighter->imagePath = "img/ships/dragon.png";
            $fighter->iconPath = "img/ships/dragon_large.png";


            $fighter->addFrontSystem(new LightParticleBeam(330, 30, 3));

			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
            $this->addSystem($fighter);
        }
    }
}
?>
