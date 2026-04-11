<?php
class SorithianZolorIIAM extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 222;
        $this->faction = "Small Races";
        $this->phpclass = "SorithianZolorIIAM";
        $this->shipClass = "Sorithian Zolor II Fighters";
        $this->imagePath = "img/ships/BAStarFox.png"; 

        $this->forwardDefense = 7;
        $this->sideDefense = 7;
        $this->freethrust = 9;
        $this->offensivebonus = 2;
        $this->jinkinglimit = 8;
        $this->turncost = 0.33;
        
 		$this->unofficial = 'S'; //design released after AoG demise

        $this->isd = 2207;
        
    	$this->iniativebonus = 90;
        $this->populate();        
    }
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){   
            $armour = array(1, 0, 1, 1);
            $fighter = new Fighter("SorithianZolorII", $armour, 7, $this->id);
            $fighter->displayName = "Zolor II";
            $fighter->imagePath = "img/ships/BAStarFox.png";
            $fighter->iconPath = "img/ships/BAStarFox_large.png";

            //ammo magazine itself (AND its missile options)
            $ammoMagazine = new AmmoMagazine(4); //pass magazine capacity - actual number of rounds, NOT number of salvoes
            $fighter->addAftSystem($ammoMagazine); //fit to ship immediately
            $ammoMagazine->addAmmoEntry(new AmmoMissileFY(), 0); //add Dogfight missile as an option - but do NOT load any actual missiles at this moment - so weapon data is actually filled with _something_!
            $this->enhancementOptionsEnabled[] = 'AMMO_FY';//add enhancement options for missiles - Class-FY
			$this->enhancementOptionsEnabled[] = 'AMMO_DUM';//add enhancement options for missiles - Class-FD            


            $frontGun = new LightParticleBeam(330, 30, 1, 2);
            $frontGun->displayName = "Ultralight Particle Beam";
            $fighter->addFrontSystem(new AmmoFighterRack(330, 30, $ammoMagazine, false)); //$startArc, $endArc, $magazine, $base 
            $fighter->addFrontSystem($frontGun);
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
            $this->addSystem($fighter);
        }
    }
}
?>
