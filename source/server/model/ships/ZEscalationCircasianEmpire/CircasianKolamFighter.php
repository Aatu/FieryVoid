<?php
class CircasianKolamFighter extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 38*6;
        $this->faction = "ZEscalation Circasian Empire";
        $this->phpclass = "CircasianKolamFighter";
        $this->shipClass = "Kolam Long Range flight";
        $this->imagePath = "img/ships/EscalationWars/CircasianKolam.png";
		$this->unofficial = true;


	    $this->occurence = 'special';
	    $this->notes = 'Special deployment: 1 in 3 fighers.';//let's try this way...
		
        $this->isd = 1963;
        
        $this->forwardDefense = 7;
        $this->sideDefense = 6;
        $this->freethrust = 11;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 8;
        $this->turncost = 0.33;
		$this->turndelay = 0;
        
        $this->iniativebonus = 95;
        $this->hasNavigator = true;
        $this->populate();       

    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){            
            $armour = array(2, 2, 1, 1);
            $fighter = new Fighter("CircasianKolamFighter", $armour, 9, $this->id);
            $fighter->displayName = "Kolam";
            $fighter->imagePath = "img/ships/EscalationWars/CircasianKolam.png";
            $fighter->iconPath = "img/ships/EscalationWars/CircasianKolam_Large.png";

			$frontGun = new PairedParticleGun(330, 30, 2, 1);
			$frontGun->displayName = "Particle Beam";
			$fighter->addFrontSystem($frontGun);
			
			$rearGun = new PairedParticleGun(150, 210, 2, 1);
			$rearGun->displayName = "Particle Beam";
			$fighter->addAftSystem($rearGun);
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            
            $this->addSystem($fighter);
        }
    }
}
?>
