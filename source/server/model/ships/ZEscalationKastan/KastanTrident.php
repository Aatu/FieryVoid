<?php
class KastanTrident extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 45*6;
        $this->faction = "ZEscalation Kastan Monarchy";
        $this->phpclass = "KastanTrident";
        $this->shipClass = "Trident Medium Fighter";
        $this->imagePath = "img/ships/EscalationWars/KastanTrident.png";
		$this->unofficial = true;

        $this->isd = 1928;
        
        $this->forwardDefense = 6;
        $this->sideDefense = 7;
        $this->freethrust = 10;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 10;
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
            $armour = array(2, 2, 1, 1);
            $fighter = new Fighter("KastanTrident", $armour, 9, $this->id);
            $fighter->displayName = "Trident";
            $fighter->imagePath = "img/ships/EscalationWars/KastanTrident.png";
            $fighter->iconPath = "img/ships/EscalationWars/KastanTrident_large.png";

			$gun = new EWLaserBoltFtr(330, 30, 1, 3);
			$gun->displayName = "Ultralight Laser Bolt";
			$fighter->addFrontSystem($gun);

			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            
            $this->addSystem($fighter);
        }
    }
}
?>
