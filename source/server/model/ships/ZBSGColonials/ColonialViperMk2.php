<?php
class ColonialViperMk2 extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 25*6;
        $this->faction = "ZPlaytest BSG Colonials";
        $this->phpclass = "ColonialViperMk2";
        $this->shipClass = "Viper Mk2 Light Flight";
        $this->imagePath = "img/ships/BSG/viperMk2.png";
		$this->unofficial = true;

//        $this->isd = 1948;

	    $this->notes = 'Atmospheric.';
	    $this->notes .= '<br>Gains +5 initiative when within 5 hexes of a standard Raptor.';
        
        $this->forwardDefense = 4;
        $this->sideDefense = 6;
        $this->freethrust = 11;
        $this->offensivebonus = 3;
        $this->jinkinglimit = 10;
        $this->turncost = 0.33;
		$this->turndelay = 0;
        
        $this->iniativebonus = 100;
        $this->populate();       

    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){            
            $armour = array(1, 1, 2, 2);
            $fighter = new Fighter("ColonialViperMk2", $armour, 8, $this->id);
            $fighter->displayName = "Viper Mk2";
            $fighter->imagePath = "img/ships/BSG/viperMk2.png";
            $fighter->iconPath = "img/ships/BSG/viperMk2_large.png";

            $missileRack = new FighterMissileRack(2, 330, 30);
            $missileRack->firingModes = array(
                1 => "FY"
            );

            $missileRack->missileArray = array(
                1 => new MissileFY(330, 30)
            );

            $frontGun = new BSGLtKineticEnergyWeapon(340, 20, 1, 2);
            $frontGun->displayName = "Light Kinetic Energy Cannon";

            $fighter->addFrontSystem($frontGun);
            $fighter->addFrontSystem($missileRack);

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
