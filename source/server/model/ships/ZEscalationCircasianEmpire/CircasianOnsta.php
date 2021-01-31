<?php
class CircasianOnsta extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 22*6;
        $this->faction = "ZEscalation Circasian Empire";
        $this->phpclass = "CircasianOnsta";
        $this->shipClass = "Onsta Assault Shuttles";
        $this->imagePath = "img/ships/EscalationWars/CircasianOnsta.png";
		$this->unofficial = true;

		
        $this->isd = 1970;
        
        $this->forwardDefense = 9;
        $this->sideDefense = 9;
        $this->freethrust = 6;
        $this->offensivebonus = 2;
        $this->jinkinglimit = 0;
        $this->turncost = 0.33;
		$this->turndelay = 0;
        
        $this->pivotcost = 2; //shuttles have pivot cost higher
		$this->hangarRequired = 'assault shuttles'; //for fleet check
		
        $this->iniativebonus = 45;
        $this->populate();       

    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){            
            $armour = array(2, 2, 2, 2);
            $fighter = new Fighter("CircasianOnsta", $armour, 8, $this->id);
            $fighter->displayName = "Onsta";
            $fighter->imagePath = "img/ships/EscalationWars/CircasianOnsta.png";
            $fighter->iconPath = "img/ships/EscalationWars/CircasianOnsta_Large.png";

			$gun = new PairedParticleGun(330, 30, 2, 1);
			$gun->displayName = "Particle Beam";
			$fighter->addFrontSystem($gun);
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            
            $this->addSystem($fighter);
        }
    }
}
?>
