<?php

class uplift extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 120;
        $this->faction = "Orieni Imperium";
        $this->phpclass = "uplift";
        $this->shipClass = "Uplift Assault Shuttles";
        $this->imagePath = "img/ships/uplift2.png";
        
        $this->isd = 1710;

        $this->forwardDefense = 9;
        $this->sideDefense = 10;
        $this->freethrust = 6;
        $this->offensivebonus = 2;
        $this->jinkinglimit = 0;
        $this->pivotcost = 2; //shuttles have pivot cost higher        
        $this->turncost = 0.33;
        
		$this->hangarRequired = 'assault shuttles'; //for fleet check
    	$this->iniativebonus = 45;
        $this->populate();        
    
     	//$this->enhancementOptionsEnabled[] = 'EXT_AMMO'; //To enable extra Ammo for main gun.
   
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){   
            $armour = array(1, 1, 1, 1);
            $fighter = new Fighter("uplift", $armour, 8, $this->id);
            $fighter->displayName = "Uplift";
            $fighter->imagePath = "img/ships/uplift2.png";
            $fighter->iconPath = "img/ships/uplift_large2.png";

            $fighter->addFrontSystem(new LtGatlingGun(330, 30));
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack

            $this->addSystem($fighter);
        }
    }
}

?>
