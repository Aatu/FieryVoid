<?php
class BloodSwordEpee extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 27*6;
        $this->faction = "ZEscalation Blood Sword Raiders";
        $this->phpclass = "BloodSwordEpee";
        $this->shipClass = "Epee Light Fighter";
        $this->imagePath = "img/ships/EscalationWars/BloodSwordEpee.png";
		$this->unofficial = true;

        $this->isd = 1935;
        
        $this->forwardDefense = 5;
        $this->sideDefense = 6;
        $this->freethrust = 10;
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
            $armour = array(1, 0, 1, 1);
            $fighter = new Fighter("BloodSwordEpee", $armour, 7, $this->id);
            $fighter->displayName = "Epee";
            $fighter->imagePath = "img/ships/EscalationWars/BloodSwordEpee.png";
            $fighter->iconPath = "img/ships/EscalationWars/BloodSwordEpee_large.png";

			$gun = new EWLaserBoltFtr(330, 30, 1);
			$gun->displayName = "Ultralight Laser Bolt";
			$fighter->addFrontSystem($gun);

			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            
            $this->addSystem($fighter);
        }
    }
}
?>
