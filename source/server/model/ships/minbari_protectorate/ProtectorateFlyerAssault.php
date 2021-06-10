<?php
class ProtectorateFlyerAssault extends FighterFlight{
	/*Minbari Assault Flyers*/
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
    	$this->pointCost = 35*6;
    	$this->faction = "Minbari Protectorate";
        $this->phpclass = "ProtectorateFlyerAssault";
        $this->shipClass = "Protectorate Assault Flyers";
    	$this->imagePath = "img/ships/nial.png"; //need Minbari Flyer image!
		
        $this->forwardDefense = 9;
        $this->sideDefense = 7;
        $this->freethrust = 8;
        $this->offensivebonus = 3;
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
            $fighter = new Fighter("FlyerAssault", $armour, 16, $this->id);
            $fighter->displayName = "Assault Flyer";
            $fighter->imagePath = "img/ships/nial.png";
            $fighter->iconPath = "img/ships/nial-large.png";
			
            $fighter->addFrontSystem(new LightFusionCannon(330, 30, 4, 1));
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
            $this->addSystem($fighter);
        }
    }
}
?>
