<?php

class HighTemplarAM extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 40*6;
        $this->faction = "Orieni Imperium";
        $this->phpclass = "HighTemplarAM";
        $this->shipClass = "High Templar Interceptor flight";
        $this->imagePath = "img/ships/highTemplar.png";
        
        $this->variantOf = "Templar Interceptor flight";
        $this->occurence = "uncommon";
        $this->isd = 2008;

 		$this->unofficial = 'S'; //design released after AoG demise
		
        $this->forwardDefense = 7;
        $this->sideDefense = 6;
        $this->freethrust = 12;
        $this->offensivebonus = 4;
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
            $fighter = new Fighter("HighTemplarAM", $armour, 9, $this->id);
            $fighter->displayName = "High Templar";
            $fighter->imagePath = "img/ships/highTemplar.png";
            $fighter->iconPath = "img/ships/highTemplar_large.png";


			//ammo magazine itself (AND its missile options)
			$ammoMagazine = new AmmoMagazine(2); //pass magazine capacity - actual number of rounds, NOT number of salvoes
			$fighter->addAftSystem($ammoMagazine); //fit to ship immediately
			$ammoMagazine->addAmmoEntry(new AmmoMissileFB(), 0); //add basic missile as an option - but do NOT load any actual missiles at this moment - so weapon data is actually filled with _something_!
			//reasoning: Centauir-Orieni war is long time before general use of fighter missile s(or missiles in general). Nonetheless Orieni have them, so obviously 'generic' timeline is not applicable to Orieni.
			//sourcebooks indicate what kinds of missiles their ships have available (namely, L, H, and their own KK (the fact that they developed the latter means their know how was generally very high in regards to missiles)
			//we are NOT given explicit information about fighter missiles - obviously they have FBs, but what else?
			//OPTIONS are: nothing (they started to use fighter missiles late in the war, and didn't have time to develop anything beyond basics)
			//or similar to shipborne ones (they could develop the same alterations to basic missile frame as they did for shipborne missiles simultaneously, and have them available).
			//I decided to go with the latter reasoning - fighter missiles are pricy, and High Templar is not a very good missile platform, so it should not be destabilizing, and options are always interesting).
			//as for Dogfight missiles - their availability is generally the same as Basic missiles, so I'm allowing them too!
			$this->enhancementOptionsEnabled[] = 'AMMO_FB';//add enhancement options for missiles - Class-FB
			$this->enhancementOptionsEnabled[] = 'AMMO_FL';//add enhancement options for missiles - Class-FL
			$this->enhancementOptionsEnabled[] = 'AMMO_FH';//add enhancement options for missiles - Class-FH
			$this->enhancementOptionsEnabled[] = 'AMMO_FY';//add enhancement options for missiles - Class-FY
			//$this->enhancementOptionsEnabled[] = 'AMMO_FD';//add enhancement options for missiles - Class-FD

            $fighter->addFrontSystem(new PairedGatlingGun(330, 30));
			$fighter->addFrontSystem(new AmmoFighterRack(330, 30, $ammoMagazine, false)); //$startArc, $endArc, $magazine, $base

			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
            $this->addSystem($fighter);
        }
    }
}

?>
