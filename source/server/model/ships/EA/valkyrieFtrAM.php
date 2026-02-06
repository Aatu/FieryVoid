<?php
class ValkyrieFtrAM extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 300;
        $this->faction = "Earth Alliance (Early)";
        $this->phpclass = "ValkyrieFtrAM";
        $this->shipClass = "EA Valkyrie Aerospace Medium flight";
        $this->imagePath = "img/ships/valkyrieFtr.png";
	    $this->isd = 2237;
 		$this->unofficial = true;
	    
        $this->forwardDefense = 7;
        $this->sideDefense = 7;
        $this->freethrust = 9;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 8;
        $this->turncost = 0.33;
        
	$this->iniativebonus = 90;
        $this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(2, 1, 1, 1);
            $fighter = new Fighter("ValkyrieFtr", $armour, 9, $this->id);
            $fighter->displayName = "Valkyrie";
            $fighter->imagePath = "img/ships/valkyrieFtr.png";
            $fighter->iconPath = "img/ships/valkyrieFtr_large.png";

			//ammo magazine itself (AND its missile options)
			$ammoMagazine = new AmmoMagazine(2); //pass magazine capacity - actual number of rounds, NOT number of salvoes
			$fighter->addAftSystem($ammoMagazine); //fit to ship immediately
			$ammoMagazine->addAmmoEntry(new AmmoMissileFB(), 0); //add basic missile as an option - but do NOT load any actual missiles at this moment - so weapon data is actually filled with _something_!
			$this->enhancementOptionsEnabled[] = 'AMMO_FB';//add enhancement options for missiles - Class-FB
			$this->enhancementOptionsEnabled[] = 'AMMO_FY';//add enhancement options for missiles - Class-FY

            $frontGun = new PairedParticleGun(330, 30, 4);
            $frontGun->displayName = "Uni-Pulse Cannon";

            $fighter->addFrontSystem($frontGun);
			$fighter->addFrontSystem(new AmmoFighterRack(330, 30, $ammoMagazine, false)); //$startArc, $endArc, $magazine, $base
//            $fighter->addFrontSystem(new FighterMissileRack(2, 330, 30));

			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack	
            $this->addSystem($fighter);
	}
    }
}
?>
