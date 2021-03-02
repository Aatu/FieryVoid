<?php
class VelraxResteraxFighter extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 38*6;
        $this->faction = "ZPlaytest Velrax";
        $this->phpclass = "VelraxResteraxFighter";
        $this->shipClass = "Resterax Assault flight";
        $this->imagePath = "img/ships/Playtest/VelraxResterax.png";
		$this->unofficial = true;
        $this->canvasSize = 5;

        $this->isd = 2062;
        
        $this->forwardDefense = 7;
        $this->sideDefense = 8;
        $this->freethrust = 9;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 6;
        $this->turncost = 0.33;
		$this->turndelay = 0;
        
        $this->iniativebonus = 80;
        $this->populate();       

    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){            
            $armour = array(3, 1, 1, 1);
            $fighter = new Fighter("VelraxResteraxFighter", $armour, 12, $this->id);
            $fighter->displayName = "Resterax";
            $fighter->imagePath = "img/ships/Playtest/VelraxResterax.png";
            $fighter->iconPath = "img/ships/Playtest/VelraxResterax_Large.png";

			$mauler = new NexusMauler(330, 30, 1);
			$fighter->addFrontSystem($mauler);
	        $light = new NexusFighterArray(330, 30, 1); //$startArc, $endArc, $nrOfShots
	        $fighter->addFrontSystem($light);
			$aftLight = new NexusFighterArray(150, 210, 1, 1);
			
			$fighter->addAftSystem($aftLight);
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            
            $this->addSystem($fighter);
        }
    }
}
?>
