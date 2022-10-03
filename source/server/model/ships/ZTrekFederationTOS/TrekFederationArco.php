<?php
class TrekFederationArco extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 40 *6;
		$this->faction = "ZTrek Playtest Federation (TOS)";
        $this->phpclass = "TrekFederationArco";
        $this->shipClass = "Arco Attack Sleds";
		$this->imagePath = "img/ships/StarTrek/FederationArco.png";
	    $this->isd = 2262;
 		$this->unofficial = true;
        
        $this->forwardDefense = 6;
        $this->sideDefense = 9;
        $this->freethrust = 6;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 4;
        $this->turncost = 0.33;
        
		$this->iniativebonus = 15 *5;
        $this->populate();
		
		$this->hangarRequired = "heavy"; //Initiative and jinking limit are misleading, but it's in fact a heavy fighter
        $this->customFtrName = "Human small craft"; //requires hangar space on Andorian ships
		
		$this->enhancementOptionsEnabled[] = 'NAVIGATOR'; //this flight can have Navigator enhancement option	
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
		$armour = array(3, 2, 2, 2);
		$fighter = new Fighter("TrekFederationArco", $armour, 12, $this->id);
		$fighter->displayName = "Arco";
		$fighter->imagePath = "img/ships/StarTrek/FederationArco.png";
		$fighter->iconPath = "img/ships/StarTrek/FederationArco_Large.png";
		
		
        $microtorpedo = new FighterMissileRack(3, 330, 30);
        $microtorpedo->displayName = "Micro Torpedo";
        $fighter->addFrontSystem($microtorpedo);	
		
        /*$frontGun = new PairedParticleGun(330, 30, 1, 2);
        $frontGun->displayName = "Ultralight Phaser Beam";*/
			$frontGun = new TrekFtrPhaser(330, 30, 1, 2);
        $fighter->addFrontSystem($frontGun);
		
        $microtorpedo = new FighterMissileRack(3, 330, 30);
        $microtorpedo->displayName = "Micro Torpedo";
        $fighter->addFrontSystem($microtorpedo);
			
			
		$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
		
		$this->addSystem($fighter);
	}
    }
}


?>