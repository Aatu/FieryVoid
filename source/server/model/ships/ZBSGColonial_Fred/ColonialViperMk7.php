<?php
class ColonialViperMk7 extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 210;
        $this->faction = "ZPlaytest BSG Colonials";
        $this->phpclass = "ColonialViperMk7";
        $this->shipClass = "Viper Mk7 Medium Flight";
        $this->imagePath = "img/ships/BSG/viperMk7.png";
		$this->unofficial = true;

        $this->isd = 1948;

	    $this->notes = 'Atmospheric.';
        
        $this->forwardDefense = 5;
        $this->sideDefense = 7;
        $this->freethrust = 12;
        $this->offensivebonus = 3;
        $this->jinkinglimit = 8;
        $this->turncost = 0.33;
		$this->turndelay = 0;
        
        $this->iniativebonus = 95;
        $this->populate();       

    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){            
            $armour = array(3, 1, 2, 2);
            $fighter = new Fighter("ColonialViperMk7", $armour, 9, $this->id);
            $fighter->displayName = "Viper Mk7";
            $fighter->imagePath = "img/ships/BSG/viperMk7.png";
            $fighter->iconPath = "img/ships/BSG/viperMk7_large.png";

            $frontGun = new PairedParticleGun(340, 20, 0, 4);
            $frontGun->displayName = "Kinetic Energy Cannon";

            $fighter->addFrontSystem(new FighterMissileRack(2, 330, 30));
            $fighter->addFrontSystem($frontGun);
            $fighter->addFrontSystem(new FighterMissileRack(2, 330, 30));

			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            
            $this->addSystem($fighter);
        }
    }
}
?>
