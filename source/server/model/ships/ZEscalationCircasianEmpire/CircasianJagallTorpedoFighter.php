<?php
class CircasianJagallTorpedoFighter extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 36*6;
        $this->faction = "ZEscalation Circasian Empire";
        $this->phpclass = "CircasianJagallTorpedoFighter";
        $this->shipClass = "Jagall Torpedo flight";
			$this->variantOf = "Jaga Medium flight";
			$this->occurence = "uncommon";
		$this->unofficial = true;

        $this->imagePath = "img/ships/EscalationWars/CircasianJagall.png";
		
        $this->isd = 1992;
        
        $this->forwardDefense = 7;
        $this->sideDefense = 7;
        $this->freethrust = 8;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 8;
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
            $fighter = new Fighter("CircasianJagallTorpedoFighter", $armour, 12, $this->id);
            $fighter->displayName = "Jagall";
            $fighter->imagePath = "img/ships/EscalationWars/CircasianJagall.png";
            $fighter->iconPath = "img/ships/EscalationWars/CircasianJagall_Large.png";

			$gun = new PairedParticleGun(330, 30, 2);
			$gun->displayName = "Paired Particle Beam";
			$fighter->addFrontSystem($gun);


            $torpedoLauncher = new EWFighterTorpedoLauncher(2, 330, 30);
            $torpedoLauncher->firingModes = array( 1 => "LPM" );
            $torpedoLauncher->iconPath = "EWLightPlasmaMine.png";
            $torpedoLauncher->displayName = "Light Plasma Mine"; //needed
            $torpedoLauncher->missileArray = array(1 => new EWLightPlasmaMine(330, 30));            	
            $fighter->addFrontSystem($torpedoLauncher);
		
 /*           $torpedoLauncher = new FighterTorpedoLauncher(3, 330, 30);
            $torpedoLauncher->firingModes = array( 1 => "LIT" );
            $torpedoLauncher->iconPath = "lightIonTorpedo.png";
            $torpedoLauncher->displayName = "Light Ion Torpedo"; //needed
            $torpedoLauncher->missileArray = array(1 => new LightIonTorpedo(330, 30));
            $fighter->addFrontSystem($torpedoLauncher);

	*/		
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            
            $this->addSystem($fighter);
        }
    }
}
?>
