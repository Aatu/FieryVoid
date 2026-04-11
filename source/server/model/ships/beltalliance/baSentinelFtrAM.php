<?php
class baSentinelFtrAM extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 228;
        $this->faction = "Belt Alliance";
        $this->phpclass = "baSentinelFtrAM";
        $this->shipClass = "BA Sentinel Fighters";
        $this->imagePath = "img/ships/BASentinel.png";    

        $this->isd = 2261;
        
        $this->forwardDefense = 5;
        $this->sideDefense = 6;
        $this->freethrust = 11;
        $this->offensivebonus = 3;
        $this->jinkinglimit = 10;
        $this->turncost = 0.33;
        
	$this->iniativebonus = 100;
        $this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
		$armour = array(2, 1, 1, 1);
		$fighter = new Fighter("Sentinel", $armour, 8, $this->id);
		$fighter->displayName = "Sentinel";
		$fighter->imagePath = "img/ships/BASentinel.png";
		$fighter->iconPath = "img/ships/BASentinel_Large.png";
			
            $frontGun = new PairedParticleGun(330, 30, 2);
            $frontGun->displayName = "Light Particle Gun";
            $fighter->addFrontSystem($frontGun);

            //ammo magazine itself (AND its missile options)
            $ammoMagazine = new AmmoMagazine(2); //pass magazine capacity - actual number of rounds, NOT number of salvoes
            $fighter->addAftSystem($ammoMagazine); //fit to ship immediately
            $ammoMagazine->addAmmoEntry(new AmmoMissileFY(), 0); //add Dogfight missile as an option - but do NOT load any actual missiles at this moment - so weapon data is actually filled with _something_!
            $this->enhancementOptionsEnabled[] = 'AMMO_FY';//add enhancement options for missiles - Class-FY
			$this->enhancementOptionsEnabled[] = 'AMMO_DUM';//add enhancement options for missiles - Class-FD            

            $fighter->addFrontSystem(new AmmoFighterRack(330, 30, $ammoMagazine, false)); //$startArc, $endArc, $magazine, $base 
			
		$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
		
		$this->addSystem($fighter);
	}
    }
}


?>
