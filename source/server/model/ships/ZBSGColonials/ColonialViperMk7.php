<?php
class ColonialViperMk7 extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 48*6;
        $this->faction = "ZPlaytest BSG Colonials";
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
        
        $this->iniativebonus = 90;
        $this->hasNavigator = true;
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

            $frontGun = new BSGKineticEnergyWeapon(340, 20, 3, 4);
            $frontGun->displayName = "Kinetic Energy Cannon";

            $fighter->addFrontSystem(new FighterMissileRack(4, 330, 30));
            $fighter->addFrontSystem($frontGun);
//            $fighter->addFrontSystem(new FighterMissileRack(1, 330, 30));

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
