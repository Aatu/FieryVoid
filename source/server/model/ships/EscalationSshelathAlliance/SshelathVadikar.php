<?php
class SshelathVadikar extends MicroSAT{
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 130*6;
        $this->faction = "Escalation Wars Sshel'ath Alliance";
        $this->phpclass = "SshelathVadikar";
        $this->shipClass = "Vadikar MicroSAT Cluster";
        $this->imagePath = "img/ships/EscalationWars/SshelathVadikar.png";
		$this->unofficial = true;
        
		$this->isd = 1897;
        
        $this->forwardDefense = 10;
        $this->sideDefense = 10;
        $this->freethrust = 2;
        $this->offensivebonus = 2; 
        $this->turncost = 0.33; //actually not all that relevant...
        
		$this->hangarRequired = ""; //they don't require any hangars... although of course cannot be used in pickup battle either!
		$this->unitSize = 3; //number of craft in squadron
		
    	$this->iniativebonus = 15 *5; 
    	$this->superheavy = true;
        $this->maxFlightSize = 3;//this is a superheavy fighter originally intended as single unit, limit flight size to 3
		
        $this->populate();
		
    }
    
    
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(2, 2, 2, 2);
            $fighter = new Fighter("Vadkikar", $armour, 30, $this->id);
            $fighter->displayName = "Vadkikar";
            $fighter->imagePath = "img/ships/EscalationWars/SshelathVadikar.png";
            $fighter->iconPath = "img/ships/EscalationWars/SshelathVadikar_large.png"; 

			//ammo magazine itself (AND its missile options)
			$ammoMagazine = new AmmoMagazine(12); //pass magazine capacity - actual number of rounds, NOT number of salvoes
			$fighter->addAftSystem($ammoMagazine); //fit to ship immediately
			$ammoMagazine->addAmmoEntry(new AmmoMissileFB(), 0); //add basic missile as an option - but do NOT load any actual missiles at this moment - so weapon data is actually filled with _something_!
			$this->enhancementOptionsEnabled[] = 'AMMO_FB';//add enhancement options for missiles - Class-FB
			$this->enhancementOptionsEnabled[] = 'AMMO_DUM';//add enhancement options for missiles - Class-FD            

			$fighter->addFrontSystem(new AmmoFighterRack(330, 30, $ammoMagazine, true)); //$startArc, $endArc, $magazine, $base
			$fighter->addFrontSystem(new AmmoFighterRack(330, 30, $ammoMagazine, true)); //$startArc, $endArc, $magazine, $base
			$fighter->addFrontSystem(new AmmoFighterRack(330, 30, $ammoMagazine, true)); //$startArc, $endArc, $magazine, $base

			$hvyGun = new LightLaser(0, 1, 0, 330, 30);
			$hvyGun->fireControl = array(-4, 0, 0); 
			$fighter->addAftSystem($hvyGun);

			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			$this->addSystem($fighter);
       }
    }
    
    
}
?>
