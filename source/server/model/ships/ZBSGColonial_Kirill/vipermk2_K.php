<?php
class ViperMk2_K extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 210;
        $this->faction = "ZPlaytest 12 Colonies of Cobol";
        $this->phpclass = "ViperMk2_K";
        $this->shipClass = "Viper Mk-2 flight";
        $this->imagePath = "img/ships/BSG/viperMk2.png";
//	    $this->isd = 2212;
 		$this->unofficial = true;
	    
        $this->forwardDefense = 7;
        $this->sideDefense = 9;
        $this->freethrust = 11;
        $this->offensivebonus = 2;
        $this->jinkinglimit = 8;
        $this->turncost = 0.33;
        
	$this->iniativebonus = 90;
        $this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(2, 1, 2, 2);
            $fighter = new Fighter("ViperMk2_K", $armour, 9, $this->id);
            $fighter->displayName = "Vipper Mk2";
            $fighter->imagePath = "img/ships/BSG/viperMk2.png";
            $fighter->iconPath = "img/ships/BSG/viperMk2_large.png";

            $missileRack = new FighterMissileRack(2, 330, 30);
            $missileRack->firingModes = array(
                1 => "FY"
            );

            $missileRack->missileArray = array(
                1 => new MissileFY(330, 30)
            );

            $frontGun = new PairedParticleGun(330, 30, 2);
            $frontGun->displayName = "MEC Cannon";

            $fighter->addFrontSystem($missileRack);
            $fighter->addFrontSystem($frontGun);

			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack	
            $this->addSystem($fighter);
	}
    }
}
?>
