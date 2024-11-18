<?php
class TrekNausicaanShefaFighter extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 33 *6;
        $this->faction = "ZTrek Playtest Other Factions";
        $this->phpclass = "TrekNausicaanShefaFighter";
        $this->shipClass = "Nausicaan Shefa Fighters";
	    $this->imagePath = "img/ships/StarTrek/NausicaanShefalitayal.png";
	    $this->isd = 2175;
        
        $this->forwardDefense = 6;
        $this->sideDefense = 7;
        $this->freethrust = 10;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 10;
        $this->turncost = 0.33;
        $this->iniativebonus = 100;
        $this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(2, 1, 1, 1);
            $fighter = new Fighter("TrekNausicaanShefaFighter", $armour, 9, $this->id);
            $fighter->displayName = "Shefa";
            $fighter->imagePath = "img/ships/StarTrek/NausicaanShefalitayal.png";
            $fighter->iconPath = "img/ships/StarTrek/NausicaanShefalitayal_Large.png";


			$gun = new RogolonLtPlasmaGun(330, 30, 4, 1);
			$gun->displayName = "Light Plasma Gun";
			$fighter->addFrontSystem($gun);

  		        //$largeGun = new PlasmaGun(330, 30); 
            		//$largeGun->exclusive = true; 
            		//$fighter->addFrontSystem($largeGun);

            $Disabler = new SWFighterIon(330, 30, 3, 1); //Ion Cannon borrowed from Star Wars
            $Disabler->exclusive = true; //either this or other weapons!
            $fighter->addFrontSystem($Disabler);

			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
            $this->addSystem($fighter);
        }
    }
}
?>
