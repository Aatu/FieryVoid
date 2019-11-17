<?php
class SorithianZolorII extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 222;
        $this->faction = "Small Races";
        $this->phpclass = "SorithianZolorII";
        $this->shipClass = "Sorithian Zolor II Fighters";
        $this->imagePath = "img/ships/BAStarFox.png";
        $this->forwardDefense = 7;
        $this->sideDefense = 7;
        $this->freethrust = 9;
        $this->offensivebonus = 2;
        $this->jinkinglimit = 8;
        $this->turncost = 0.33;
        
        $this->unofficial = true;
        
    	$this->iniativebonus = 90;
        $this->populate();        
    }
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){   
            $armour = array(1, 0, 1, 1);
            $fighter = new Fighter("SorithianZolorII", $armour, 7, $this->id);
            $fighter->displayName = "Zolor II";
            $fighter->imagePath = "img/ships/BAStarFox.png";
            $fighter->iconPath = "img/ships/BAStarFox_large.png";

            $missileRack = new FighterMissileRack(4, 330, 30);
            $missileRack->firingModes = array(
                1 => "FY"
            );
            $missileRack->missileArray = array(
                1 => new MissileFY(330, 30)
            );
            $frontGun = new LightParticleBeam(330, 30, 1, 2);
            $frontGun->displayName = "Ultralight Particle Beam";
            $fighter->addFrontSystem($missileRack);
            $fighter->addFrontSystem($frontGun);
            $this->addSystem($fighter);
        }
    }
}
?>
