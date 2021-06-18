<?php
class FlyingFoxStarfury extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 252;
        $this->faction = "EA";
        $this->phpclass = "FlyingFoxStarfury";
        $this->shipClass = "Starfury: Flying Fox Heavy flight";
        $this->imagePath = "img/ships/aries.png";
	    $this->isd = 2180;
 		$this->unofficial = true;
	    
	    $this->notes = 'Non-atmospheric and 1/2 turn cost.';
        
        $this->forwardDefense = 8;
        $this->sideDefense = 9;
        $this->freethrust = 8;
        $this->offensivebonus = 3;
        $this->jinkinglimit = 6;
        $this->turncost = 0.5;
        
	$this->iniativebonus = 75;
        $this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(2, 1, 1, 1);
            $fighter = new Fighter("FlyingFoxStarfury", $armour, 13, $this->id);
            $fighter->displayName = "Flying Fox";
            $fighter->imagePath = "img/ships/aries.png";
            $fighter->iconPath = "img/ships/aries_large.png";

	        $gun = new LightParticleBeamFtr(270, 90, 1); //$startArc, $endArc, $nrOfShots
	        $fighter->addFrontSystem($gun);
//            $frontGun->displayName = "Uni-Pulse Cannon";

			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack	
            $this->addSystem($fighter);
	}
    }
}
?>
