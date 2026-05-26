<?php
class gaimKrastFighterAM extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 37*6;
        $this->faction = "Gaim Intelligence";
        $this->variantOf = "OBSELETE";
        $this->phpclass = "gaimKrastFighterAM";
        $this->shipClass = "Krast Recon flight";
        $this->variantOf = "Koist Medium flight";
        $this->imagePath = "img/ships/GaimKoist.png";
		
        $this->isd = 2252;
        
        $this->forwardDefense = 7;
        $this->sideDefense = 8;
        $this->freethrust = 9;
        $this->offensivebonus = 5;
        $this->jinkinglimit = 8;
        $this->turncost = 0.33;
		$this->turndelay = 0;
        
        $this->iniativebonus = 90;
        $this->populate();       

    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){            
            $armour = array(2, 1, 2, 2);
            $fighter = new Fighter("gaimKrastFighter", $armour, 11, $this->id);
            $fighter->displayName = "Krast";
            $fighter->imagePath = "img/ships/GaimKoist.png";
            $fighter->iconPath = "img/ships/GaimKoist_large.png";

			//ammo magazine itself (AND its missile options)
			$ammoMagazine = new AmmoMagazine(2); //pass magazine capacity - actual number of rounds, NOT number of salvoes
			$fighter->addAftSystem($ammoMagazine); //fit to ship immediately
			$ammoMagazine->addAmmoEntry(new AmmoMissileFY(), 0); //add basic missile as an option - but do NOT load any actual missiles at this moment - so weapon data is actually filled with _something_!
			$this->enhancementOptionsEnabled[] = 'AMMO_FY';//add enhancement options for missiles - Class-FY

			$gun = new PairedParticleGun(330, 30, 2);
			$gun->displayName = "Light Particle Gun";
			$fighter->addFrontSystem($gun);
			$fighter->addFrontSystem(new AmmoFighterRack(330, 30, $ammoMagazine, false)); //$startArc, $endArc, $magazine,
			
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            
            $this->addSystem($fighter);
        }
    }
}
?>
