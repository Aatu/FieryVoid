<?php
class VelraxHasertAttack extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 30*6;
        $this->faction = "ZPlaytest Velrax";
        $this->phpclass = "VelraxHasertAttack";
        $this->shipClass = "Hasert Attack flight";
			$this->variantOf = "Tassriv Interceptor-A flight";
			$this->occurence = "uncommon";
        $this->imagePath = "img/ships/Nexus/VelraxHasert.png";
		$this->unofficial = true;

        $this->isd = 2108;
        
        $this->forwardDefense = 6;
        $this->sideDefense = 6;
        $this->freethrust = 8;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 10;
        $this->turncost = 0.33;
		$this->turndelay = 0;
        
        $this->iniativebonus = 100;
        $this->populate();       

    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){            
            $armour = array(2, 0, 0, 0);
            $fighter = new Fighter("VelraxHasertAttack", $armour, 9, $this->id);
            $fighter->displayName = "Hasert";
            $fighter->imagePath = "img/ships/Nexus/VelraxHasert.png";
            $fighter->iconPath = "img/ships/Nexus/VelraxHasert_Large.png";

			$mauler = new NexusMauler(330, 30, 1);
			$fighter->addFrontSystem($mauler);
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            
            $this->addSystem($fighter);
        }
    }
}
?>
