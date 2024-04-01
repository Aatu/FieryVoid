<?php
class TrekSulibanCellshipLarge extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 120 *6; //a bit add-on for not requiring carrier
        $this->faction = "ZTrek Playtest Other Factions";
        $this->phpclass = "TrekSulibanCellshipLarge";
        $this->shipClass = "Suliban Large Cellships";
        $this->imagePath = "img/ships/StarTrek/SulibanCellshipLarge.png";
		$this->unofficial = true;
		
        $this->isd = 2149;

        $this->forwardDefense = 9;
        $this->sideDefense = 11;
        $this->freethrust = 8;
        $this->offensivebonus = 5;
        $this->jinkinglimit = 2;
        $this->turncost = 0.33;
       	$this->iniativebonus = 14 *5; 
        $this->pivotcost = 2;

	//$this->unitSize = 3; //number of craft in squadron
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
            $fighter = new Fighter("TrekSulibanCellshipLarge", $armour, 24, $this->id);
            $fighter->displayName = "Suliban Large Cellship";
			
            $fighter->imagePath = "img/ships/StarTrek/SulibanCellshipLarge.png";
            $fighter->iconPath = "img/ships/StarTrek/SulibanCellshipLarge_Large.png";

            $heavyGun = new TrekFtrPhaseCannon(300, 60, 4, 2, 8, "Phaser"); //arc from/to, damage bonus, number of shots, rake size, base weapon name
            $fighter->addFrontSystem($heavyGun);
			
			$frontGun = new TrekFtrPhaser(270, 90, 2, 2, "Phase Cannons");
            $fighter->addFrontSystem($frontGun);
			
			$fighter->addAftSystem(new TrekShieldFtr(0, 10, 5, 3) ); //armor, health, rating, recharge
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
		
            $this->addSystem($fighter);
        }
    }
}
?>