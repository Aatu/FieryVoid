<?php
class Nial extends FighterFlight{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 636;
		$this->faction = "Minbari";
		$this->phpclass = "Nial";
		$this->shipClass = "Nial flight";
		$this->imagePath = "img/ships/nial.png";

        $this->forwardDefense = 6;
        $this->sideDefense = 8;
        $this->freethrust = 14;
        $this->offensivebonus = 6;
        $this->jinkinglimit = 6;
        $this->turncost = 0.33;
        $this->iniativebonus = 85;
        $this->gravitic = true;
        $this->populate();
        $this->isd = 2050;
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(4, 4, 4, 4);
            $nial = new Fighter("Nial", $armour, 14, $this->id);
            $nial->displayName = "Nial";
        // need picture
            $nial->imagePath = "img/ships/nial.png";
        // need picture
            $nial->iconPath = "img/ships/nial-large.png";
            $nial->addFrontSystem(new LightFusionCannon(330, 30, 4, 3));
            $nial->addAftSystem(new Jammer(0, 1, 0));
		
		
		$nial->addAftSystem(new RammingAttack(0, 0, 360, $nial->getRammingFactor(), 0)); //ramming attack
			
		
            $this->addSystem($nial);
        }
    }
}



?>
