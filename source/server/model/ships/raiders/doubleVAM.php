<?php
class DoubleVAM extends FighterFlight{
				
		function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 288;
		$this->faction = "Raiders";
		$this->phpclass = "DoubleVAM";
		$this->shipClass = "Double-V Medium Flight";
		$this->imagePath = "img/ships/doubleV.png";

		$this->forwardDefense = 5;
		$this->sideDefense = 7;
		$this->freethrust = 10;
		$this->offensivebonus = 4;
		$this->jinkinglimit = 8;
		$this->turncost = 0.33;
		
        $this->notes = 'Needs 2 rail slots.';
		$this->enhancementOptionsEnabled[] = 'NAVIGATOR'; //this flight can take Navigator enhancement option	

		$this->notes = "Generic raider unit.";
		$this->notes .= "<br> ";
		$this->notes .= "<br>More detailed deployment restrictions are in the Faction List document.";
		$this->notes .= "<br> ";

		$this->isd = 2236;
        
		$this->iniativebonus = 90;
        $this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
		$armour = array(3, 0, 2, 2);
		$fighter = new Fighter("doubleV", $armour, 10, $this->id);
		$fighter->displayName = "Double-V";
		$fighter->imagePath = "img/ships/doubleV.png";
		$fighter->iconPath = "img/ships/doubleV_large.png";

		//ammo magazine itself (AND its missile options)
		$ammoMagazine = new AmmoMagazine(2); //pass magazine capacity - actual number of rounds, NOT number of salvoes
		$fighter->addAftSystem($ammoMagazine); //fit to ship immediately
		$ammoMagazine->addAmmoEntry(new AmmoMissileFB(), 0); //add basic missile as an option - but do NOT load any actual missiles at this moment - so weapon data is actually filled with _something_!
		$this->enhancementOptionsEnabled[] = 'AMMO_FB';//add enhancement options for missiles - Class-FB
		$this->enhancementOptionsEnabled[] = 'AMMO_FY';//add enhancement options for missiles - Class-FY				

		$fighter->addFrontSystem(new PairedParticleGun(330, 30, 3));
		$fighter->addFrontSystem(new AmmoFighterRack(330, 30, $ammoMagazine, false)); //$startArc, $endArc, $magazine, $base
			
		$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
		$this->addSystem($fighter);
	}
				}
}

?>