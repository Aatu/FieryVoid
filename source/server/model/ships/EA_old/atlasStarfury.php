<?php
class AtlasStarfury extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 210;
        $this->faction = "Earth Alliance (Early)";
        $this->phpclass = "AtlasStarfury";
        $this->shipClass = "Starfury: Atlas Heavy flight (2212)";
			$this->variantOf = "Starfury: Flying Fox Heavy flight";
			$this->occurence = "uncommon";
        $this->imagePath = "img/ships/aries.png";
	    $this->isd = 2212;
 		$this->unofficial = 'S'; //HRT design released after AoG demise
	    
	    $this->notes = 'Non-atmospheric and 1/2 turn cost.';
        
        $this->forwardDefense = 8;
        $this->sideDefense = 9;
        $this->freethrust = 8;
        $this->offensivebonus = 3;
        $this->jinkinglimit = 6;
        $this->turncost = 0.5;
        
	$this->iniativebonus = 75;
        $this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(1, 1, 1, 1);
            $fighter = new Fighter("AtlasStarfury", $armour, 13, $this->id);
            $fighter->displayName = "Atlas";
            $fighter->imagePath = "img/ships/aries.png";
            $fighter->iconPath = "img/ships/aries_large.png";

            $missileRack1 = new FighterMissileRack(4, 330, 30);
            $missileRack1->firingModes = array(
                1 => "FY"
            );

            $missileRack1->missileArray = array(
                1 => new MissileFY(330, 30)
            );

            $missileRack2 = new FighterMissileRack(4, 330, 30);
            $missileRack2->firingModes = array(
                1 => "FY"
            );

            $missileRack2->missileArray = array(
                1 => new MissileFY(330, 30)
            );
			
            $frontGun = new PairedParticleGun(330, 30, 4);
            $frontGun->displayName = "Uni-Pulse Cannon";

            $fighter->addFrontSystem($missileRack1);
            $fighter->addFrontSystem($frontGun);
            $fighter->addFrontSystem($missileRack2);

			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack	
            $this->addSystem($fighter);
	}
    }
}
?>
