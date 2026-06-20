<?php
class ArmedShuttleNarn extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 30 * 6;
        $this->faction = "Narn Regime";
        $this->phpclass = "ArmedShuttleNarn";
        $this->shipClass = "Armed Shuttles";
        $this->imagePath = "img/ships/ShuttleNarn.png";
        $this->isd = 2200;
        
		Enhancements::nonstandardEnhancementSet($this, 'Shuttles');
        
        $this->forwardDefense = 10;
        $this->sideDefense = 12;
        $this->freethrust = 4;
        $this->offensivebonus = 2;
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
            $fighter = new Fighter("ArmedShuttleNarn", $armour, 8, $this->id);
            $fighter->displayName = "Armed Shuttle";
            $fighter->imagePath = "img/ships/ShuttleNarn.png";
            $fighter->iconPath = "img/ships/ShuttleNarn_large.png";

            $frontGun = new PairedParticleGun(330, 30, 5, 1); //1 gun d6+5
            $frontGun->displayName = "Particle Gun";
            $fighter->addFrontSystem($frontGun);
		    $fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            $this->addSystem($fighter);
       }
    }
}
?>
