<?php
class Tortra extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 40*6;
        $this->faction = "Balosian";
        $this->phpclass = "Tortra";
        $this->shipClass = "Tortra Assault Shuttles";
        $this->imagePath = "img/ships/Tortra.png";
		
        $this->isd = 2250;
        
        $this->forwardDefense = 10;
        $this->sideDefense = 10;
        $this->freethrust = 8;
        $this->offensivebonus = 3;
        $this->jinkinglimit = 0;
        $this->turncost = 0.33;
		$this->turndelay = 0;
        
        $this->hangarRequired = 'assault shuttles'; //for fleet check
        $this->iniativebonus = 9*5;
        $this->populate();       

    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){            
            $armour = array(3, 3, 3, 3);
            $fighter = new Fighter("Tortra", $armour, 12, $this->id);
            $fighter->displayName = "Tortra";
            $fighter->imagePath = "img/ships/Tortra.png";
            $fighter->iconPath = "img/ships/Tortra_large.png";

		$gun = new LightParticleBeam(330, 30, 1);
		$gun->displayName = "Ultralight Particle Beam";
			$fighter->addFrontSystem($gun);
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            
            $this->addSystem($fighter);
        }
    }
}
?>
