<?php
class VelraxSoressPDCRefit extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 100*6;
        $this->faction = "Nexus Velrax Republic";
        $this->phpclass = "VelraxSoressPDCRefit";
        $this->shipClass = "Soress Planetary Defense Craft (2112)";
        $this->imagePath = "img/ships/Nexus/velraxHisskar.png";
		$this->unofficial = true;
	    $this->isd = 2112;
        $this->canvasSize = 120;

        $this->notes = 'Has Navigator';
//        $this->notes .= '<br>Each counts as 2 heavy fighters.';

        $this->forwardDefense = 9;
        $this->sideDefense = 11;
        $this->freethrust = 6;
        $this->offensivebonus = 5;
        $this->jinkinglimit = 0;
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
		
		$this->hangarRequired = "superheavy"; //Velrax Hisskar are housed in regular hangars, as heavy fighters
//		$this->unitSize = 0.5; //one craft requires 2 hangar slots
        $this->iniativebonus = 60;
        $this->hasNavigator = true;
    	$this->superheavy = true;
        $this->maxFlightSize = 3;//this is a superheavy fighter originally intended as single unit, limit flight size to 3
	
		$this->populate();

	}

    public function populate(){        

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
		
		for ($i = 0; $i < $toAdd; $i++) {
			$armour = array(4, 2, 2, 2);
			$fighter = new Fighter("VelraxSoressPDCRefit", $armour, 34, $this->id);
			$fighter->displayName = "Soress";
			$fighter->imagePath = "img/ships/Nexus/velraxHisskar.png.png";
			$fighter->iconPath = "img/ships/Nexus/velraxHisskar_large.png";

			//ammo magazine itself (AND its missile options)
			$ammoMagazine = new AmmoMagazine(6); //pass magazine capacity - actual number of rounds, NOT number of salvoes
			$fighter->addAftSystem($ammoMagazine); //fit to ship immediately
			$ammoMagazine->addAmmoEntry(new AmmoMissileFY(), 0); //add basic missile as an option - but do NOT load any actual missiles at this moment - so weapon data is actually filled with _something_!
			$this->enhancementOptionsEnabled[] = 'AMMO_FY';//add enhancement options for missiles - Class-FY
			$this->enhancementOptionsEnabled[] = 'AMMO_DUM';//add enhancement options for missiles - Class-FD             

			$fighter->addFrontSystem(new AmmoFighterRack(330, 30, $ammoMagazine, false)); //$startArc, $endArc, $magazine, $base

            $frontGun = new NexusLightIonBolter(270, 90, 0);
            $frontGun->displayName = "Light Ion Gun";
            $fighter->addFrontSystem($frontGun);

			$fighter->addFrontSystem(new AmmoFighterRack(330, 30, $ammoMagazine, false)); //$startArc, $endArc, $magazine, $base

			$hvyGun = new IonBolt(330,30);
			$hvyGun->displayName = "Ion Bolt";
			$fighter->addAftSystem($hvyGun);

//            $aftGun = new NexusLightIonGun(120, 240, 0);
//            $aftGun->displayName = "Light Ion Gun";
//            $fighter->addFrontSystem($aftGun);
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			$this->addSystem($fighter);
		}
    }

}

?>
