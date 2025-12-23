<?php
class VelraxAxrinFighter extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 37*6;
        $this->faction = "Nexus Velrax Republic (early)";
        $this->phpclass = "VelraxAxrinFighter";
        $this->shipClass = "Axrin Strike flight";
        $this->imagePath = "img/ships/Nexus/velraxResterax.png";
		$this->unofficial = true;

        $this->isd = 2032;
        
        $this->forwardDefense = 8;
        $this->sideDefense = 8;
        $this->freethrust = 8;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 8;
        $this->turncost = 0.33;
		$this->turndelay = 0;
        
        $this->iniativebonus = 90;
        $this->populate();       

    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){            
            $armour = array(2, 1, 2, 2);
            $fighter = new Fighter("VelraxAxrinFighter", $armour, 10, $this->id);
            $fighter->displayName = "Axrin";
            $fighter->imagePath = "img/ships/Nexus/velraxResterax.png";
            $fighter->iconPath = "img/ships/Nexus/velraxResterax_large.png";

			$fighter->addFrontSystem(new IonBolt(330, 30));

			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            
            $this->addSystem($fighter);
        }
    }
}
?>
