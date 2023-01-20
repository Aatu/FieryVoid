<?php
class KoethAM extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 30*6;//for flight of 6
        $this->faction = "Hurr";
        $this->phpclass = "KoethAM";
        $this->shipClass = "Koeth Light Fighters";
        $this->imagePath = "img/ships/Hurrkoeth.png";

        $this->isd = 2230;
        
        $this->forwardDefense = 5;
        $this->sideDefense = 5;
        $this->freethrust = 9;
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
		$armour = array(2, 0, 1, 1);
		$fighter = new Fighter("KoethAM", $armour, 7, $this->id);
		$fighter->displayName = "Koeth";
		$fighter->imagePath = "img/ships/Hurrkoeth.png";
		$fighter->iconPath = "img/ships/Hurrkoeth_large.png";
			

			//ammo magazine itself (AND its missile options)
			$ammoMagazine = new AmmoMagazine(2); //pass magazine capacity - actual number of rounds, NOT number of salvoes
			$fighter->addAftSystem($ammoMagazine); //fit to ship immediately
			$ammoMagazine->addAmmoEntry(new AmmoMissileFB(), 0); //add basic missile as an option - but do NOT load any actual missiles at this moment - so weapon data is actually filled with _something_!
			//Hurr have option of using Basic or Dogfight missiles
			$this->enhancementOptionsEnabled[] = 'AMMO_FB';//add enhancement options for missiles - Class-FB
			$this->enhancementOptionsEnabled[] = 'AMMO_FY';//add enhancement options for missiles - Class-FY
			
			
		$fighter->addFrontSystem(new PairedParticleGun(330, 30, 1));
		$fighter->addFrontSystem(new AmmoFighterRack(330, 30, $ammoMagazine, false)); //$startArc, $endArc, $magazine, $base	
	
		$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
		$this->addSystem($fighter);
	}
    }
}
?>
