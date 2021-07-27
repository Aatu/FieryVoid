<?php
class KastanKatana extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 32*6;
        $this->faction = "ZEscalation Kastan Monarchy";
        $this->phpclass = "KastanKatana";
        $this->shipClass = "Katana Light Fighter";
        $this->imagePath = "img/ships/EscalationWars/KastanKatana.png";
		$this->unofficial = true;

        $this->isd = 1860;
        
        $this->forwardDefense = 5;
        $this->sideDefense = 6;
        $this->freethrust = 12;
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
            $armour = array(1, 1, 1, 1);
            $fighter = new Fighter("KastanKatana", $armour, 7, $this->id);
            $fighter->displayName = "Katana";
            $fighter->imagePath = "img/ships/EscalationWars/KastanKatana.png";
            $fighter->iconPath = "img/ships/EscalationWars/KastanKatana_large.png";

			$gun = new EWLaserBoltFtr(330, 30, 1);
			$gun->displayName = "Ultralight Laser Bolt";
			$fighter->addFrontSystem($gun);

			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            
            $this->addSystem($fighter);
        }
    }
}
?>
