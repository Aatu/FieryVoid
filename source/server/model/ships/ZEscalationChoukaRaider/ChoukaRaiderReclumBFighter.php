<?php
class ChoukaRaiderReclumBFighter extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 24*6;
        $this->faction = "ZEscalation Chouka Raider";
        $this->phpclass = "ChoukaRaiderReclumBFighter";
        $this->shipClass = "Reclum-B Light flight";
        $this->imagePath = "img/ships/EscalationWars/ChoukaRaiderReclum.png";
        $this->customFtrName = "Reclum-A";
		$this->unofficial = true;
		
        $this->isd = 1904;
        
        $this->forwardDefense = 6;
        $this->sideDefense = 6;
        $this->freethrust = 8;
        $this->offensivebonus = 3;
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
            $fighter = new Fighter("ChoukaRaiderReclumBFighter", $armour, 7, $this->id);
            $fighter->displayName = "Reclum-B Light Fighter";
            $fighter->imagePath = "img/ships/EscalationWars/ChoukaRaiderReclum.png";
            $fighter->iconPath = "img/ships/EscalationWars/ChoukaRaiderReclum_Large.png";

			$gun = new EWUltralightPlasmaGun(330, 30, 3, 2);
			$gun->displayName = "Ultralight Plasma Gun";
			$fighter->addFrontSystem($gun);
		
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            
            $this->addSystem($fighter);
        }
    }
}
?>
