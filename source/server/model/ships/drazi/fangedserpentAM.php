<?php
class FangedSerpentAM extends SuperHeavyFighter{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 145*6;
        $this->faction = "Drazi";
        $this->phpclass = "FangedSerpentAM";
        $this->shipClass = "Fanged Serpent Command Fighters";
	    $this->imagePath = "img/ships/drazi/DraziSkyserpent.png";
        $this->occurence = "rare";
	    $this->variantOf = "Sky Serpent Heavy Assault Fighters";
	    $this->isd = 2231;
        $this->canvasSize = 64;

		$this->notes = 'Provides +5 Initiative to all Sky Serpents within 5 hexes.';
        
        $this->forwardDefense = 10;
        $this->sideDefense = 9;
        $this->freethrust = 12;
        $this->offensivebonus = 8;
        $this->jinkinglimit = 4;
        $this->turncost = 0.33;
		$this->turndelaycost = 0.25;
        
		$this->hangarRequired = 'superheavy'; //for fleet check
	    $this->iniativebonus = 15 *5;
        $this->hasNavigator = true;
        $this->maxFlightSize = 3;//this is a superheavy fighter originally intended as single unit, limit flight size to 3
	
		$this->populate();
	
	}

    public function populate(){        

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
		
		for ($i = 0; $i < $toAdd; $i++) {
			$armour = array(4, 3, 4, 4);
			$fighter = new Fighter("fangedserpent", $armour, 32, $this->id);
			$fighter->displayName = "Fanged Serpent";
			$fighter->imagePath = "img/ships/drazi/DraziSkyserpent.png";
			$fighter->iconPath = "img/ships/drazi/DraziSkyserpent_large.png";


			//ammo magazine itself (AND its missile options)
			$ammoMagazine = new AmmoMagazine(12); //pass magazine capacity - actual number of rounds, NOT number of salvoes
			$fighter->addAftSystem($ammoMagazine); //fit to ship immediately
			$ammoMagazine->addAmmoEntry(new AmmoMissileFB(), 0); //add basic missile as an option - but do NOT load any actual missiles at this moment - so weapon data is actually filled with _something_!
			$this->enhancementOptionsEnabled[] = 'AMMO_FB';//add enhancement options for missiles - Class-FB
			$this->enhancementOptionsEnabled[] = 'AMMO_FL';//add enhancement options for missiles - Class-FL
			$this->enhancementOptionsEnabled[] = 'AMMO_FH';//add enhancement options for missiles - Class-FH
			$this->enhancementOptionsEnabled[] = 'AMMO_FY';//add enhancement options for missiles - Class-FY
			$this->enhancementOptionsEnabled[] = 'AMMO_FD';//add enhancement options for missiles - Class-FD


			$fighter->addFrontSystem(new PairedParticleGun(330, 30, 5));
			$fighter->addFrontSystem(new AmmoFighterRack(330, 30, $ammoMagazine, false)); //$startArc, $endArc, $magazine, $base
			$fighter->addFrontSystem(new AmmoFighterRack(330, 30, $ammoMagazine, false)); //$startArc, $endArc, $magazine, $base
        
			$particleBlaster = new ParticleBlasterFtr(330, 30, 1); //$startArc, $endArc, $nrOfShots
			$fighter->addAftSystem($particleBlaster);
		
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
        
			$this->addSystem($fighter);
		}
    }
    
}
