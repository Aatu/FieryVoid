<?php
class ArmedMissileShuttleEA extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 20 * 6;
        $this->faction = "Earth Alliance";
        $this->phpclass = "ArmedMissileShuttleEA";
        $this->shipClass = "Armed Shuttles (Missile)";
        $this->imagePath = "img/ships/shuttle.png";
        $this->isd = 2200;
        $this->variantOf = 'Armed Shuttles';
        
		Enhancements::nonstandardEnhancementSet($this, 'Shuttles');
        
        $this->forwardDefense = 8;
        $this->sideDefense = 8;
        $this->freethrust = 6;
        $this->offensivebonus = 3;
        $this->jinkinglimit = 0;
        $this->pivotcost = 2; //shuttles have pivot cost higher        
        $this->turncost = 0.33;
        $this->minesweeper = false; //denotes if shuttle is a minesweeping type        
        
		$this->hangarRequired = 'shuttles'; //for fleet check
    	$this->iniativebonus = 9 * 5;
        $this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(1, 1, 1, 1);
            $fighter = new Fighter("ArmedShuttleMissileEA", $armour, 10, $this->id);
            $fighter->displayName = "Armed Shuttle";
            $fighter->imagePath = "img/ships/shuttle.png";
            $fighter->iconPath = "img/ships/shuttle_large.png";

			$ammoMagazine = new AmmoMagazine(2); //pass magazine capacity - actual number of rounds, NOT number of salvoes
			$fighter->addAftSystem($ammoMagazine); //fit to ship immediately
			$ammoMagazine->addAmmoEntry(new AmmoMissileFB(), 0); //add basic missile as an option - but do NOT load any actual missiles at this moment - so weapon data is actually filled with _something_!
			$this->enhancementOptionsEnabled[] = 'AMMO_FB';//add enhancement options for missiles - Class-FB
			$this->enhancementOptionsEnabled[] = 'AMMO_DUM';//add enhancement options for missiles - Class-FDUM			
			
			$fighter->addFrontSystem(new AmmoFighterRack(330, 30, $ammoMagazine, false)); //$startArc, $endArc, $magazine, $base	
			$fighter->addFrontSystem(new AmmoFighterRack(330, 30, $ammoMagazine, false)); //$startArc, $endArc, $magazine, $base	

			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			
			$this->addSystem($fighter);
       }
    }
}
?>
