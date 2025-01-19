<?php
class TrekNausicaanGliderSHF extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 70 *6; //for 6
        $this->faction = "ZTrek Playtest Other Factions";
        $this->phpclass = "TrekNausicaanGliderSHF";
        $this->imagePath = "img/ships/StarTrek/NausicaanGlider.png";
        $this->shipClass = "Nausicaan Gliders";
		$this->unofficial = true;
		
        $this->isd = 2177;
		$this->notes = "Warp Engine";
		$this->notes .= "<br>Takes up two regular fighter slots.";

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
            $fighter = new Fighter("TrekNausicaanGliderSHF", $armour, 24, $this->id);
            $fighter->displayName = "Nausicaan Glider";
			
            $fighter->imagePath = "img/ships/StarTrek/NausicaanGlider.png";
            $fighter->iconPath = "img/ships/StarTrek/NausicaanGlider_Large.png";
			
			$gun = new RogolonLtPlasmaGun(240, 360, 4, 2);//d3+4, dual mount
			//$gun->displayName = "Dual Plasma Guns";
			$fighter->addFrontSystem($gun);


            $disabler = new TrekFighterDisabler(330, 30, 2, 3); //damage d6+2, triple mount
            $disabler->exclusive = false; //can be fired together with other weapons!
            $fighter->addFrontSystem($disabler);

			$gun = new RogolonLtPlasmaGun(0, 120, 4, 2);//d3+4, dual mount
			//$gun->displayName = "Dual Plasma Guns";
			$fighter->addFrontSystem($gun);

			$fighter->addAftSystem(new TrekShieldFtr(2, 6, 3, 2) ); //armor, health, rating, recharge
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
		
            $this->addSystem($fighter);
        }
    }
}
?>