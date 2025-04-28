<?php
class KobolViperMk7 extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 360;
        $this->faction = "ZPlaytest 12 Colonies of Kobol";
        $this->phpclass = "KobolViperMk7";
        $this->shipClass = "Viper Mk-7 flight (Beta)";
        $this->imagePath = "img/ships/BSG/viperMk7.png";
	    $this->isd = 2212;
 		$this->unofficial = true;

	    $this->notes = 'Gains +10 initiative when within 5 hexes of a standard Raptor.';
	    
        $this->forwardDefense = 6;
        $this->sideDefense = 7;
        $this->freethrust = 13;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 8;
        $this->turncost = 0.25;
        
	$this->iniativebonus = 90;
        $this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(3, 2, 3, 3);
            $fighter = new Fighter("KobolViperMk7", $armour, 10, $this->id);
            $fighter->displayName = "Viper Mk7";
            $fighter->imagePath = "img/ships/BSG/viperMk7.png";
            $fighter->iconPath = "img/ships/BSG/viperMk7_large.png";

            $frontGun = new PairedParticleGun(330, 30, 3);
            $frontGun->displayName = "MEC Cannon Mk2";
            $fighter->addFrontSystem($frontGun);

/*            $missileRack1 = new FighterMissileRack(3, 330, 30);
            $missileRack1->firingModes = array(
                1 => "FY"
            );

            $missileRack1->missileArray = array(
                1 => new MissileFY(330, 30)
            );

            $missileRack2 = new FighterMissileRack(3, 330, 30);
            $missileRack2->firingModes = array(
                1 => "FY"
            );

            $missileRack2->missileArray = array(
                1 => new MissileFY(330, 30)
            );
*/

            $fighter->addFrontSystem(new FighterMissileRack(2, 330, 30));
//            $fighter->addFrontSystem($missileRack1);
//            $fighter->addFrontSystem($missileRack2);
//            $fighter->addFrontSystem(new FighterMissileRack(2, 330, 30));

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
                        && ($ship instanceof KobolRaptor)){
                    $initiativeBonusRet+=10;
                    break;
                }
            }
        }
        
        return $initiativeBonusRet;
    }	
	
}
?>
