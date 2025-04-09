<?php
class ThunderboltStarfuryAM extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 80*6;
        $this->faction = "Earth Alliance";
        $this->phpclass = "ThunderboltStarfuryAM";
        $this->shipClass = "Thunderbolt Assault flight";
        $this->imagePath = "img/ships/thunderboltStarfury.png";
        $this->customFtrName = "Thunderbolt";
		
        $this->isd = 2259;
        $this->notes = 'Needs updated hangars to handle.';
        
        $this->forwardDefense = 8;
        $this->sideDefense = 7;
        $this->freethrust = 13;
        $this->offensivebonus = 5;
        $this->jinkinglimit = 6;
        $this->turncost = 0.33;
        
        $this->iniativebonus = 80;
        $this->populate();       

		$this->enhancementOptionsEnabled[] = 'NAVIGATOR'; //this flight can have Navigator enhancement option	
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){            
            $armour = array(3, 2, 2, 2);
            $fighter = new Fighter("thunderboltStarfury", $armour, 15, $this->id);
            $fighter->displayName = "Thunderbolt";
            $fighter->imagePath = "img/ships/thunderboltStarfury.png";
            $fighter->iconPath = "img/ships/thunderboltStarfury_large.png";

			//ammo magazine itself (AND its missile options)
			$ammoMagazine = new AmmoMagazine(6); //pass magazine capacity - actual number of rounds, NOT number of salvoes
			$fighter->addAftSystem($ammoMagazine); //fit to ship immediately
			$ammoMagazine->addAmmoEntry(new AmmoMissileFB(), 0); //add basic missile as an option - but do NOT load any actual missiles at this moment - so weapon data is actually filled with _something_!
			$this->enhancementOptionsEnabled[] = 'AMMO_FB';//add enhancement options for missiles - Class-FB
			$this->enhancementOptionsEnabled[] = 'AMMO_FL';//add enhancement options for missiles - Class-FL
			$this->enhancementOptionsEnabled[] = 'AMMO_FH';//add enhancement options for missiles - Class-FH
			$this->enhancementOptionsEnabled[] = 'AMMO_FY';//add enhancement options for missiles - Class-FY
			$this->enhancementOptionsEnabled[] = 'AMMO_FD';//add enhancement options for missiles - Class-FD

            $fighter->addFrontSystem(new GatlingPulseCannon(330, 30));
			$fighter->addFrontSystem(new AmmoFighterRack(330, 30, $ammoMagazine, false)); //$startArc, $endArc, $magazine, $base
			$fighter->addFrontSystem(new AmmoFighterRack(330, 30, $ammoMagazine, false)); //$startArc, $endArc, $magazine, $base
		
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack	
            
            $this->addSystem($fighter);
        }
    }
}
?>
