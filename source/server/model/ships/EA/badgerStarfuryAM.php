<?php
class BadgerStarfuryAM extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 420;
        $this->faction = "Earth Alliance";
        $this->phpclass = "BadgerStarfuryAM";
        $this->shipClass = "Badger Long-Range flight";
        $this->imagePath = "img/ships/badgerStarfury.png";
	    
	    /*
	$this->occurence = 'uncommon';//by original rules: not a variant, but special deployment restrictions (1 in 4 fighter flights)
	$this->variantOf = "Starfury: Aurora Heavy flight";//I think Uncommon variant of Aurora is close enough
	*/
	    $this->occurence = 'special';
	    $this->notes = 'Special deployment: 1 in 4 fighers.';//let's try this way...
	    $this->notes .= '<br>Non-atmospheric.';
	    
	    $this->isd = 2255;

        $this->forwardDefense = 9;
        $this->sideDefense = 6;
        $this->freethrust = 10;
        $this->offensivebonus = 6;
        $this->jinkinglimit = 6;
        $this->turncost = 0.33;

        $this->iniativebonus = 80;
        $this->hasNavigator = true;
        $this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(3, 3, 2, 2);
            $fighter = new Fighter("badgerStarfury", $armour, 15, $this->id);
            $fighter->displayName = "Badger";
            $fighter->imagePath = "img/ships/badgerStarfury.png";
            $fighter->iconPath = "img/ships/badgerStarfury_large.png";

			//ammo magazine itself (AND its missile options)
			$ammoMagazine = new AmmoMagazine(8); //pass magazine capacity - actual number of rounds, NOT number of salvoes
			$fighter->addAftSystem($ammoMagazine); //fit to ship immediately
			$ammoMagazine->addAmmoEntry(new AmmoMissileFB(), 0); //add basic missile as an option - but do NOT load any actual missiles at this moment - so weapon data is actually filled with _something_!
			$this->enhancementOptionsEnabled[] = 'AMMO_FB';//add enhancement options for missiles - Class-FB
			$this->enhancementOptionsEnabled[] = 'AMMO_FL';//add enhancement options for missiles - Class-FL
			$this->enhancementOptionsEnabled[] = 'AMMO_FH';//add enhancement options for missiles - Class-FH
			$this->enhancementOptionsEnabled[] = 'AMMO_FY';//add enhancement options for missiles - Class-FY
			$this->enhancementOptionsEnabled[] = 'AMMO_FD';//add enhancement options for missiles - Class-FD
			
            $frontGun = new PairedParticleGun(330, 30, 4);
            $frontGun->displayName = "Uni-Pulse Cannon";

            $fighter->addFrontSystem($frontGun);
			$fighter->addFrontSystem(new AmmoFighterRack(330, 30, $ammoMagazine, false)); //$startArc, $endArc, $magazine, $base
			$fighter->addFrontSystem(new AmmoFighterRack(330, 30, $ammoMagazine, false)); //$startArc, $endArc, $magazine, $base
            
			
            $aftGun = new PairedParticleGun(150, 210, 4, 1);
            $aftGun->displayName = "Uni-Pulse Cannon";
            $fighter->addAftSystem($aftGun);
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack	
            
            $this->addSystem($fighter);
	   }
    }
}

?>
