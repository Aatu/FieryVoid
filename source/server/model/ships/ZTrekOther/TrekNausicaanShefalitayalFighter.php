<?php
class TrekNausicaanShefalitayalFighter extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 40 *6;
        $this->faction = "ZTrek Playtest Other Factions";
        $this->phpclass = "TrekNausicaanShefalitayalFighter";
        $this->shipClass = "Nausicaan Shefalitayal Fighters";
	    $this->imagePath = "img/ships/StarTrek/NausicaanShefalitayal.png"; // Shefalitayal is name of artist creating this picture

        $this->variantOf = "Nausicaan Shefa Fighters";

	    $this->isd = 2255;
        
        $this->forwardDefense = 6;
        $this->sideDefense = 7;
        $this->freethrust = 11;
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
            $armour = array(3, 1, 2, 2);
            $fighter = new Fighter("TrekNausicaanShefalitayalFighter", $armour, 9, $this->id);
            $fighter->displayName = "Shefalitayal";
            $fighter->imagePath = "img/ships/StarTrek/NausicaanShefalitayal.png";
            $fighter->iconPath = "img/ships/StarTrek/NausicaanShefalitayal_Large.png";


			$gun = new RogolonLtPlasmaGun(330, 30, 4, 2);
			$gun->displayName = "Dual Plasma Guns";
			$fighter->addFrontSystem($gun);

  		        //$largeGun = new PlasmaGun(330, 30); 
            		//$largeGun->exclusive = true; 
            		//$fighter->addFrontSystem($largeGun);

            $Disabler = new SWFighterIon(330, 30, 4, 1); //Ion Cannon borrowed from Star Wars
            $Disabler->exclusive = true; //either this or other weapons!
            $fighter->addFrontSystem($Disabler);

			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
            $this->addSystem($fighter);
        }
    }
}
?>
