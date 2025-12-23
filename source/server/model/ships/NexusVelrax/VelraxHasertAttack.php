<?php
class VelraxHasertAttack extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 35*6;
        $this->faction = "Nexus Velrax Republic";
        $this->phpclass = "VelraxHasertAttack";
        $this->shipClass = "Hasert Attack flight";
	$this->variantOf = "Tassriv-B Interceptor flight";
	$this->occurence = "uncommon";
        $this->imagePath = "img/ships/Nexus/velraxTassriv.png";
	$this->unofficial = true;

        $this->isd = 2108;
        
        $this->forwardDefense = 6;
        $this->sideDefense = 7;
        $this->freethrust = 8;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 10;
        $this->turncost = 0.33;
	$this->turndelay = 0;
        
        $this->iniativebonus = 20 *5;
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
            $fighter->imagePath = "img/ships/Nexus/velraxTassrv.png";
            $fighter->iconPath = "img/ships/Nexus/velraxTassriv_large.png";

//			$mauler = new NexusMauler(330, 30, 1);
//			$fighter->addFrontSystem($mauler);
			$fighter->addFrontSystem(new IonBolt(330, 30));
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            
            $this->addSystem($fighter);
        }
    }
}
?>
