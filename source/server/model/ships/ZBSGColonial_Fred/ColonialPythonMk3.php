<?php
class ColonialPythonMk3 extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 75*6;
        $this->faction = "ZPlaytest BSG Colonials";
        $this->phpclass = "ColonialPythonMk3";
        $this->shipClass = "Python Mk3 Attack Flight";
        $this->imagePath = "img/ships/BSG/pythonMk3.png";
		$this->unofficial = true;
        $this->customFtrName = "Python";

//        $this->isd = 1948;

	    $this->notes = 'Atmospheric.';
        
        $this->forwardDefense = 6;
        $this->sideDefense = 8;
        $this->freethrust = 10;
        $this->offensivebonus = 3;
        $this->jinkinglimit = 6;
        $this->turncost = 0.33;
		$this->turndelay = 0;
        
        $this->iniativebonus = 80;
        $this->populate();       

    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){            
            $armour = array(3, 2, 3, 3);
            $fighter = new Fighter("ColonialPythonMk3", $armour, 18, $this->id);
            $fighter->displayName = "Python Mk3";
            $fighter->imagePath = "img/ships/BSG/pythonMk3.png";
            $fighter->iconPath = "img/ships/BSG/pythonMk3_Large.png";

            $frontGun = new BSGKineticEnergyWeapon(340, 20, 2, 4);
            $frontGun->displayName = "Kinetic Energy Cannon";
			$hvyGun = new BSGHvyKineticEnergyWeapon(340, 20, 1); //$startArc, $endArc, $nrOfShots
			$hvyGun->displayName = "Heavy Kinetic Energy Cannon";

            $fighter->addFrontSystem($frontGun);
			$fighter->addFrontSystem($hvyGun);

			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            
            $this->addSystem($fighter);
        }
    }
	
    public function getInitiativebonus($gamedata){
        $initiativeBonusRet = parent::getInitiativebonus($gamedata);
        
        if($gamedata->turn > 0 && $gamedata->phase >= 0 ){
            // If within 5 hexes of a Raptor,
            // each Viper gets +1 initiative.
            
            $ships = $gamedata->getShipsInDistance($this, 5);

            foreach($ships as $ship){
                if(!$ship->isDestroyed()
                        && ($this->userid == $ship->userid)
                        && ($ship instanceof ColonialRaptor)){
                    $initiativeBonusRet+=5;
                    break;
                }
            }
        }
        
        return $initiativeBonusRet;
    }	
	
}
?>
