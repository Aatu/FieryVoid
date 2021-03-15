<?php
class ViperMk7_K extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 360;
        $this->faction = "ZPlaytest 12 Colonies of Cobol";
        $this->phpclass = "ViperMk7_K";
        $this->shipClass = "Viper Mk-7 flight (2212)";
        $this->imagePath = "img/ships/BSG/vipermk7.png";
//	    $this->isd = 2212;
 		$this->unofficial = true;
	    
        $this->forwardDefense = 6;
        $this->sideDefense = 7;
        $this->freethrust = 15;
        $this->offensivebonus = 4;
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
            $armour = array(3, 2, 2, 2);
            $fighter = new Fighter("ViperMk2_K", $armour, 10, $this->id);
            $fighter->displayName = "Vipper Mk2";
            $fighter->imagePath = "img/ships/BSG/vipermk7.png";
            $fighter->iconPath = "img/ships/BSG/vipermk7_large.png";

            $frontGun = new PairedParticleGun(330, 30, 3);
            $frontGun->displayName = "MEC Cannon Mk2";

            $fighter->addFrontSystem(new FighterMissileRack(2, 330, 30));
            $fighter->addFrontSystem($frontGun);
            $fighter->addFrontSystem(new FighterMissileRack(2, 330, 30));

			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack	
            $this->addSystem($fighter);
	}
    }
}
?>
