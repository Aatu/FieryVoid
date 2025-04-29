<?php
class FlyerCombat extends FighterFlight{
	/*Minbari Combat Flyers*/
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
    	$this->pointCost = 70*6;
    	$this->faction = "Minbari Federation";
        $this->phpclass = "FlyerCombat";
        $this->shipClass = "Combat Flyers";
        $this->variantOf = "Assault Flyers";
    	$this->imagePath = "img/ships/MinbariFlyer.png"; //need Minbari Flyer image!
        $this->isd = 1750;
        
		
        $this->forwardDefense = 9;
        $this->sideDefense = 7;
        $this->freethrust = 8;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 0;
		$this->pivotcost = 2; //shuttles have pivot cost higher
        $this->turncost = 0.33;
		
		$this->hangarRequired = 'shuttles'; //for fleet check; they replace regular shuttles (rather than assault shuttles!) - which means a ship needs shuttle hangar tracked for it to work!
        $this->iniativebonus = 10*5;
        
        $this->gravitic = true;
        $this->populate();
    }


    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(1, 1, 1, 1);
            $fighter = new Fighter("FlyerCombat", $armour, 16, $this->id);
            $fighter->displayName = "Combat Flyer";
            $fighter->imagePath = "img/ships/MinbariFlyer.png";
            $fighter->iconPath = "img/ships/MinbariFlyer_Large.png";
			
            $fighter->addFrontSystem(new LightFusionCannon(300, 0, 4, 1));
            $fighter->addFrontSystem(new LightFusionCannon(0, 60, 4, 1));
            $fighter->addAftSystem(new Jammer(0, 1, 0));	
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
            $this->addSystem($fighter);
        }
    }
}
?>
