<?php
class BAStarfoxFtrAM extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 240;
        $this->faction = "Belt Alliance";
        $this->phpclass = "BAStarfoxFtrAM";
        $this->shipClass = "BA Starfox Fighters";
        $this->imagePath = "img/ships/BAStarFox.png"; 
        $this->forwardDefense = 7;
        $this->sideDefense = 9;
        $this->freethrust = 9;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 8;
        $this->turncost = 0.33;

        $this->isd = 2208;
        
    	$this->iniativebonus = 90;
        $this->populate();        
    
        $this->enhancementOptionsEnabled[] = 'EXT_AMMO'; //To enable extra Ammo for main gun.
        	
    }
    
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){   
            $armour = array(3, 1, 2, 2);
            $fighter = new Fighter("Starfox", $armour, 10, $this->id);
            $fighter->displayName = "Starfox";
            $fighter->imagePath = "img/ships/BAStarFox.png";
            $fighter->iconPath = "img/ships/BAStarFox_large.png";

            //ammo magazine itself (AND its missile options)
            $ammoMagazine = new AmmoMagazine(4); //pass magazine capacity - actual number of rounds, NOT number of salvoes
            $fighter->addAftSystem($ammoMagazine); //fit to ship immediately
            $ammoMagazine->addAmmoEntry(new AmmoMissileFY(), 0); //add Dogfight missile as an option - but do NOT load any actual missiles at this moment - so weapon data is actually filled with _something_!
            $this->enhancementOptionsEnabled[] = 'AMMO_FY';//add enhancement options for missiles - Class-FY
			$this->enhancementOptionsEnabled[] = 'AMMO_DUM';//add enhancement options for missiles - Class-FD            

            $frontGun = new MatterGun(330, 30);
            $fighter->addFrontSystem(new AmmoFighterRack(330, 30, $ammoMagazine, false)); //$startArc, $endArc, $magazine, $base 
            $fighter->addFrontSystem($frontGun);
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack

            $this->addSystem($fighter);
        }
    }
}
?>
