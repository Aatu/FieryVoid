<?php
class ProtectorateFlyerCombat extends FighterFlight{
	/*Minbari Combat Flyers*/
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
    	$this->pointCost = 63*6;
    	$this->faction = "Minbari Protectorate";
        $this->phpclass = "ProtectorateFlyerCombat";
        $this->shipClass = "Protectorate Combat Flyers";
        $this->variantOf = "Protectorate Assault Flyers";
    	$this->imagePath = "img/ships/nial.png"; //need Minbari Flyer image!
        
		$this->notes = "Usually housed in common shuttle bays (not mentioned in FV). Most ships can take a pair.";
		
        $this->forwardDefense = 9;
        $this->sideDefense = 7;
        $this->freethrust = 8;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 0;
		$this->pivotcost = 2; //shuttles have pivot cost higher
        $this->turncost = 0.33;
		
		$this->hangarRequired = 'assault shuttles'; //for fleet check 
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
            $fighter->imagePath = "img/ships/nial.png";
            $fighter->iconPath = "img/ships/nial-large.png";
			
            $fighter->addFrontSystem(new LightFusionCannon(300, 0, 4, 1));
            $fighter->addFrontSystem(new LightFusionCannon(0, 60, 4, 1));
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
            $this->addSystem($fighter);
        }
    }
}
?>
