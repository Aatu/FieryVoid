<?php
class ChoukaWraithbladeFighterAM extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 25*6;
        $this->faction = "Escalation Wars Chouka Theocracy";
        $this->phpclass = "ChoukaWraithbladeFighterAM";
        $this->shipClass = "Wraithblade Heavy flight";
        $this->imagePath = "img/ships/EscalationWars/ChoukaWraithblade.png";
		$this->unofficial = true;
		
        $this->isd = 1880;
        
        $this->forwardDefense = 8;
        $this->sideDefense = 7;
        $this->freethrust = 7;
        $this->offensivebonus = 3;
        $this->jinkinglimit = 6;
        $this->turncost = 0.33;
		$this->turndelay = 0;
        
        $this->iniativebonus = 80;
        $this->populate();       

    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){            
            $armour = array(2, 0, 1, 1);
            $fighter = new Fighter("ChoukaWraithbladeFighter", $armour, 11, $this->id);
            $fighter->displayName = "Wraithblade";
            $fighter->imagePath = "img/ships/EscalationWars/ChoukaWraithblade.png";
            $fighter->iconPath = "img/ships/EscalationWars/ChoukaWraithblade_Large.png";

			//ammo magazine itself (AND its missile options)
			$ammoMagazine = new AmmoMagazine(2); //pass magazine capacity - actual number of rounds, NOT number of salvoes
			$fighter->addAftSystem($ammoMagazine); //fit to ship immediately
			$ammoMagazine->addAmmoEntry(new AmmoMissileFB(), 0); //add basic missile as an option - but do NOT load any actual missiles at this moment - so weapon data is actually filled with _something_!
			//Hurr have option of using Basic or Dogfight missiles
			$this->enhancementOptionsEnabled[] = 'AMMO_FB';//add enhancement options for missiles - Class-FB
			$this->enhancementOptionsEnabled[] = 'AMMO_FY';//add enhancement options for missiles - Class-FY

            $fighter->addFrontSystem(new PairedPlasmaBlaster(330, 30, 2, 2));
			$fighter->addFrontSystem(new AmmoFighterRack(330, 30, $ammoMagazine, false)); //$startArc, $endArc, $magazine, $base	
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            
            $this->addSystem($fighter);
        }
    }
}
?>
