<?php
class ChoukaWraithbladeFighter extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 24*6;
        $this->faction = "ZEscalation Chouka";
        $this->phpclass = "ChoukaWraithbladeFighter";
        $this->shipClass = "Wraithblade Heavy flight";
        $this->imagePath = "img/ships/EscalationWars/ChoukaWraithblade.png";
        $this->customFtrName = "Wraithblade";
		$this->unofficial = true;

		
        $this->isd = 1880;
        
        $this->forwardDefense = 8;
        $this->sideDefense = 7;
        $this->freethrust = 7;
        $this->offensivebonus = 3;
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
            $armour = array(2, 0, 1, 1);
            $fighter = new Fighter("ChoukaWraithbladeFighter", $armour, 11, $this->id);
            $fighter->displayName = "Wraithblade";
            $fighter->imagePath = "img/ships/EscalationWars/ChoukaWraithblade.png";
            $fighter->iconPath = "img/ships/EscalationWars/ChoukaWraithblade_Large.png";

            $fighter->addFrontSystem(new FighterMissileRack(2, 330, 30));
            $fighter->addFrontSystem(new PairedPlasmaBlaster(330, 30, 2, 2));
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            
            $this->addSystem($fighter);
        }
    }
}
?>
