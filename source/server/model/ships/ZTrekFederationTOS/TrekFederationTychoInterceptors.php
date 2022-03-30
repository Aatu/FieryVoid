<?php
class TrekFederationTychoInterceptors extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 25 *6;
		$this->faction = "ZTrek Playtest Federation (TOS)";
        $this->phpclass = "TrekFederationTychoInterceptors";
        $this->shipClass = "Tycho Interceptors";
		$this->imagePath = "img/ships/StarTrek/FederationTycho.png";
	    $this->isd = 2258;
 		$this->unofficial = true;
        
        $this->forwardDefense = 5;
        $this->sideDefense = 6;
        $this->freethrust = 8;
        $this->offensivebonus = 3;
        $this->jinkinglimit = 10;
        $this->turncost = 0.33;
        
		$this->iniativebonus = 19 *5;
        $this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
		$armour = array(1, 1, 1, 1);
		$fighter = new Fighter("TrekFederationTychoInterceptors", $armour, 7, $this->id);
		$fighter->displayName = "Tycho";
		$fighter->imagePath = "img/ships/StarTrek/FederationTycho.png";
		$fighter->iconPath = "img/ships/StarTrek/FederationTycho_Large.png";
			
        $frontGun = new PairedParticleGun(330, 30, 1);
        $frontGun->displayName = "Ultralight Phaser Beam";
        $fighter->addFrontSystem($frontGun);
			
		$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
		
		$this->addSystem($fighter);
	}
    }
}


?>