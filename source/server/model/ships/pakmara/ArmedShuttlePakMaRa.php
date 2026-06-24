<?php
class ArmedShuttlePakMaRa extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 24 * 6;
        $this->faction = "Pak'ma'ra Confederacy";
        $this->phpclass = "ArmedShuttlePakMaRa";
        $this->shipClass = "Armed Shuttles";
        $this->imagePath = "img/ships/shuttlePakmara.png";
        $this->isd = 2200;
        
		Enhancements::nonstandardEnhancementSet($this, 'Shuttles');
        
        $this->forwardDefense = 9;
        $this->sideDefense = 9;
        $this->freethrust = 6;
        $this->offensivebonus = 5;
        $this->jinkinglimit = 0;
        $this->pivotcost = 2; //shuttles have pivot cost higher        
        $this->turncost = 0.33;
        $this->minesweeper = false; //denotes if shuttle is a minesweeping type        
        
		$this->hangarRequired = 'shuttles'; //for fleet check
    	$this->iniativebonus = 8 * 5;
        $this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(0, 0, 0, 0);
            $fighter = new Fighter("ArmedShuttlePakMaRa", $armour, 9, $this->id);
            $fighter->displayName = "Armed Shuttle";
            $fighter->imagePath = "img/ships/shuttlePakmara.png";
            $fighter->iconPath = "img/ships/shuttlePakmara_large.png";

		    $gun = new RogolonLtPlasmaGun(300, 60, 5, 1);
			$gun->displayName = "Light Plasma Cannon";
			$fighter->addFrontSystem($gun);
		    $fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            $this->addSystem($fighter);
       }
    }
}
?>
