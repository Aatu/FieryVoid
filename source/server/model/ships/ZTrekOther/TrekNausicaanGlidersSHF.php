<?php
class TrekNausicaanGlidersSHF extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 80 *6; //for 6
        $this->faction = "ZTrek Playtest Other Factions";
        $this->phpclass = "TrekNausicaanGlidersSHF";
        $this->imagePath = "img/ships/StarTrek/NausicaanGlider.png";
        $this->shipClass = "Nausicaan Gliders";
		$this->unofficial = true;
		
        $this->isd = 2145;
		$this->notes = "Warp Engine";
		$this->notes .= "<br>Takes up two fighter slots.";

	$this->hangarRequired = "heavy"; //Nausicaan smaller Gliders require hangar space just like heavy fighters
	$this->unitSize = 0.5; //one craft requires 2 hangar slots


        $this->forwardDefense = 9;
        $this->sideDefense = 9;
        $this->freethrust = 8;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 2;
        $this->turncost = 0.33;
        $this->turndelaycost = 0.25;
		
        $this->pivotcost = 2;

	//$this->unitSize = 3; //number of craft in squadron
    	$this->superheavy = true;
        $this->maxFlightSize = 3;//this is a superheavy fighter originally intended as single unit, limit flight size to 3
        
       	$this->iniativebonus = 14 *5; //default SHF ini
        $this->populate();
    }
	
	
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){   
            $armour = array(2, 2, 2, 2);
            $fighter = new Fighter("TrekNausicaanGlidersSHF", $armour, 20, $this->id);
            $fighter->displayName = "Nausicaan Glider";
			
            $fighter->imagePath = "img/ships/StarTrek/NausicaanGlider.png";
            $fighter->iconPath = "img/ships/StarTrek/NausicaanGlider_Large.png";
			
			$fighter->addFrontSystem(new PairedPlasmaBlaster(240, 360));
		        $largeGun = new PlasmaGun(330, 30); 
            		$fighter->addFrontSystem($largeGun);
			$fighter->addFrontSystem(new PairedPlasmaBlaster(0, 120));		

			$fighter->addAftSystem(new TrekShieldFtr(1, 4, 3, 1) ); //armor, health, rating, recharge
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
		
            $this->addSystem($fighter);
        }
    }
}
?>