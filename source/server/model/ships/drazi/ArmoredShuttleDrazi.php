<?php
class ArmoredShuttleDrazi extends FighterFlight{
    /*generic armed shuttles*/
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 32*6;
        $this->faction = "Drazi Freehold";
        $this->phpclass = "ArmoredShuttleDrazi";
        $this->shipClass = "Armored Shuttles";
		$this->imagePath = "img/ships/shuttleDrazi.png"; //more appropriate image needed
        $this->isd = 2200;
        $this->variantOf = "Armed Shuttles";
        
		Enhancements::nonstandardEnhancementSet($this, 'Shuttles');
        
		
        $this->forwardDefense = 8;
        $this->sideDefense = 9;
        $this->freethrust = 5;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 0;
        $this->pivotcost = 2; //shuttles have pivot cost higher
        $this->turncost = 0.33;
        
		$this->hangarRequired = 'shuttles'; //for fleet check - draws from the default-shuttle pool
		$this->iniativebonus = 11*5;                 
      
        $this->populate();
    }
    
    
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
			
			$armour = array(1, 1, 1, 1);
			$fighter = new Fighter("ArmoredShuttleDrazi", $armour, 10, $this->id);
			$fighter->displayName = "Armed Shuttle";
			$fighter->imagePath = "img/ships/shuttleDrazi.png";
			$fighter->iconPath = "img/ships/shuttleDrazi_large.png";

			$ammoMagazine = new AmmoMagazine(2); //pass magazine capacity - actual number of rounds, NOT number of salvoes
			$fighter->addAftSystem($ammoMagazine); //fit to ship immediately
			$ammoMagazine->addAmmoEntry(new AmmoMissileFB(), 0); //add basic missile as an option - but do NOT load any actual missiles at this moment - so weapon data is actually filled with _something_!
			$this->enhancementOptionsEnabled[] = 'AMMO_FB';//add enhancement options for missiles - Class-FB
			$this->enhancementOptionsEnabled[] = 'AMMO_DUM';//add enhancement options for missiles - Class-FDUM			
			
			$fighter->addFrontSystem(new AmmoFighterRack(330, 30, $ammoMagazine, false)); //$startArc, $endArc, $magazine, $base	           
			
			$fighter->addFrontSystem(new LightParticleBlaster(330, 30, 5, 1)); //1 gun D3+5 damage
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			
			$this->addSystem($fighter);
			
		}
		
		
    }
}
?>
