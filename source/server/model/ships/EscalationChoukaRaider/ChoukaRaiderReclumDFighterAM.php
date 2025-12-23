<?php
class ChoukaRaiderReclumDFighterAM extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 20*6;
        $this->faction = "Escalation Wars Chouka Raider";
        $this->phpclass = "ChoukaRaiderReclumDFighterAM";
        $this->shipClass = "Reclum-D Light flight (missile)";
			$this->variantOf = "Reclum-A Light flight";
			$this->occurence = "common";
        $this->imagePath = "img/ships/EscalationWars/ChoukaRaiderReclum.png";
		$this->unofficial = true;
		
        $this->isd = 1957;
        
        $this->forwardDefense = 6;
        $this->sideDefense = 6;
        $this->freethrust = 10;
        $this->offensivebonus = 3;
        $this->jinkinglimit = 10;
        $this->turncost = 0.33;
		$this->turndelay = 0;
        
        $this->iniativebonus = 100;
        $this->populate();       

    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){            
            $armour = array(1, 1, 1, 1);
            $fighter = new Fighter("ChoukaRaiderReclumCFighter", $armour, 7, $this->id);
            $fighter->displayName = "Reclum-D";
            $fighter->imagePath = "img/ships/EscalationWars/ChoukaRaiderReclum.png";
            $fighter->iconPath = "img/ships/EscalationWars/ChoukaRaiderReclum_Large.png";

			//ammo magazine itself (AND its missile options)
			$ammoMagazine = new AmmoMagazine(2); //pass magazine capacity - actual number of rounds, NOT number of salvoes
			$fighter->addAftSystem($ammoMagazine); //fit to ship immediately
			$ammoMagazine->addAmmoEntry(new AmmoMissileFB(), 0); //add basic missile as an option - but do NOT load any actual missiles at this moment - so weapon data is actually filled with _something_!
			//Hurr have option of using Basic or Dogfight missiles
			$this->enhancementOptionsEnabled[] = 'AMMO_FB';//add enhancement options for missiles - Class-FB
			$this->enhancementOptionsEnabled[] = 'AMMO_FY';//add enhancement options for missiles - Class-FY

			$gun = new LightParticleBeam(330, 30, 1, 1);
			$gun->displayName = "Ultralight Particle Beam";
			$fighter->addFrontSystem($gun);
			$fighter->addFrontSystem(new AmmoFighterRack(330, 30, $ammoMagazine, false)); //$startArc, $endArc, $magazine, $base	
		
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            
            $this->addSystem($fighter);
        }
    }
}
?>
