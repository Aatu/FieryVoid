<?php
class VelraxResteraxRefit extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 49*6;
        $this->faction = "Nexus Velrax Republic";
        $this->phpclass = "VelraxResteraxRefit";
        $this->shipClass = "Resterax-B Assault flight";
        $this->imagePath = "img/ships/Nexus/velraxResterax.png";
		$this->unofficial = true;

        $this->isd = 2104;
        
        $this->forwardDefense = 7;
        $this->sideDefense = 8;
        $this->freethrust = 9;
        $this->offensivebonus = 5;
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
            $armour = array(3, 2, 2, 2);
            $fighter = new Fighter("VelraxResteraxFighter", $armour, 12, $this->id);
            $fighter->displayName = "Resterax-B";
            $fighter->imagePath = "img/ships/Nexus/velraxResterax.png";
            $fighter->iconPath = "img/ships/Nexus/velraxResterax_large.png";

//			$mauler = new NexusMauler(330, 30, 1);
//			$fighter->addFrontSystem($mauler);
			$fighter->addFrontSystem(new IonBolt(330, 30));
	        $light = new NexusLightIonBolter(330, 30, 0, 1); //$startArc, $endArc, $nrOfShots
	        $fighter->addFrontSystem($light);
			$aftLight = new NexusLightIonBolter(150, 210, 0, 1);
			
			$fighter->addAftSystem($aftLight);
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            
            $this->addSystem($fighter);
        }
    }
}
?>
