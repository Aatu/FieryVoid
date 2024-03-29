<?php
class ColonialMamba extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 52*6;
        $this->faction = "ZBSG Colonials";
        $this->phpclass = "ColonialMamba";
        $this->shipClass = "Mamba Prototype Medium Flight";
        $this->imagePath = "img/ships/BSG/mamba.png";
		$this->unofficial = true;
        $this->limited = 10;

//        $this->isd = 1948;

	    $this->notes = 'Atmospheric and a 1/3 turn delay.';
	    $this->notes .= '<br>Gains +5 initiative when within 5 hexes of a standard Raptor.';
        
        $this->forwardDefense = 6;
        $this->sideDefense = 8;
        $this->freethrust = 14;
        $this->offensivebonus = 5;
        $this->jinkinglimit = 8;
        $this->turncost = 0.33;
		$this->turndelaycost = 0.33;
        
        $this->iniativebonus = 90;
        $this->populate();       

    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){            
            $armour = array(2, 1, 1, 1);
            $fighter = new Fighter("ColonialMamba", $armour, 9, $this->id);
            $fighter->displayName = "Mamba";
            $fighter->imagePath = "img/ships/BSG/mamba.png";
            $fighter->iconPath = "img/ships/BSG/mamba_large.png";

//            $frontGun = new BSGHypergun(340, 20, 0, 4);
//            $frontGun->displayName = "Hypergun";
//            $fighter->addFrontSystem($frontGun);

            $frontGun = new BSGHypergunVA(345, 15, 15, 0, 5);//narower arc (from/to), difference between narrow and wide arc, damage bonus, number of shots
            $fighter->addFrontSystem($frontGun);

//            $fighter->addFrontSystem(new FighterMissileRack(4, 330, 30));
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
