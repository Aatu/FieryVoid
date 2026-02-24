<?php
class KlingonKTochShuttle extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 35 *6; //for 6
        $this->faction = "ZStarTrek Klingon";
        $this->phpclass = "KlingonKTochShuttle";
        $this->shipClass = "K'Toch Shuttle Flight";
        $this->imagePath = "img/ships/StarTrek/KlingonKTochShuttle.png";
		$this->unofficial = true;
		
        $this->isd = 2145;

        $this->forwardDefense = 7;
        $this->sideDefense = 8;
        $this->freethrust = 8;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 4;
        $this->turncost = 0.33;
		
		$this->hangarRequired = "Shuttlecraft"; //I took category name from ST wikis
        $this->customFtrName = "Klingon small craft"; //requires hangar space on Klingon ships
		$this->unitSize = 1; //counted as singles
        
       	$this->iniativebonus = 14 *5;
        $this->populate();        
    }

    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){   
            $armour = array(2, 1, 2, 2);
            $fighter = new Fighter("KlingonKTochShuttle", $armour, 11, $this->id);
            $fighter->displayName = "K'Toch Shuttle";
			
            $fighter->imagePath = "img/ships/StarTrek/KlingonKTochShuttle.png";
            $fighter->iconPath = "img/ships/StarTrek/KlingonKTochShuttle_Large.png";
			
            $frontGun = new LightFusionCannon(330, 30, 3, 2);
            $frontGun->displayName = "Dual Light Disruptors";
            $fighter->addFrontSystem($frontGun);
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
            $this->addSystem($fighter);
        }
    }
}

?>