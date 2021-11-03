<?php
class ColonialViperMk7 extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 48*6;
        $this->faction = "ZBSG Colonials";
        $this->phpclass = "ColonialViperMk7";
        $this->shipClass = "Viper Mk7 Medium Flight";
        $this->imagePath = "img/ships/BSG/viperMk7.png";
		$this->unofficial = true;

        $this->isd = 1948;

	    $this->notes = 'Atmospheric.';
	    $this->notes .= '<br>Gains +5 initiative when within 5 hexes of a standard Raptor.';
        
        $this->forwardDefense = 5;
        $this->sideDefense = 7;
        $this->freethrust = 12;
        $this->offensivebonus = 5;
        $this->jinkinglimit = 8;
        $this->turncost = 0.33;
		$this->turndelay = 0;
		
        
        $this->iniativebonus = 18*5;
        //$this->hasNavigator = true; //that's too much, Navigator OPTION instead
		$this->enhancementOptionsEnabled[] = 'NAVIGATOR'; //this flight can have Navigator enhancement option	
		
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

		
            $frontGun = new BSGLtKineticEnergyWeaponVA(345, 15, 15, 3, 4);//narower arc (from/to), difference between narrow and wide arc, damage bonus, number of shots
            $fighter->addFrontSystem($frontGun);
		/*
			//should be single gun with variable arc, but that's not possible ATM - so 2 exclusive weapons; narrow arc gets bonus FC, wide arc gets penalty
            $frontGun = new BSGLtKineticEnergyWeapon(340, 20, 3, 4);
            $frontGun->displayName = "Kinetic Energy Cannon (narrow)";
            $frontGun->exclusive = true;
			$frontGun->fireControl[0] += 1;
            $fighter->addFrontSystem($frontGun);
			
            $frontGun = new BSGLtKineticEnergyWeapon(330, 30, 3, 4);
            $frontGun->displayName = "Kinetic Energy Cannon (wide)";
            $frontGun->exclusive = true;
			$frontGun->fireControl[0] += -1;
            $fighter->addFrontSystem($frontGun);
		*/
            $fighter->addFrontSystem(new FighterMissileRack(4, 330, 30));

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
