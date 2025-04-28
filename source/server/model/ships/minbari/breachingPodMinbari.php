<?php
class breachingPodMinbari  extends FighterFlight{
	/*Minbari Assault Flyers*/
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
    	$this->pointCost = 60*6;
    	$this->faction = "Minbari Federation";
        $this->phpclass = "breachingPodMinbari";
        $this->shipClass = "Ronati Breaching Pods";
    	$this->imagePath = "img/ships/MinbariFlyer.png"; //need Minbari Flyer image!
        $this->isd = 1750;     
				
        $this->forwardDefense = 8;
        $this->sideDefense = 8;
        $this->freethrust = 8;
        $this->offensivebonus = 0;
        $this->jinkinglimit = 0;
		$this->pivotcost = 2; //shuttles have pivot cost higher
        $this->turncost = 0.33;

        $this->maxFlightSize = 3;//this is an unusual type of 'fighter', limit flight size. 		
		$this->hangarRequired = 'shuttles'; //for fleet check
        $this->iniativebonus = 10*5;
        
        $this->gravitic = true;
        $this->populate();
    
    	$this->enhancementOptionsEnabled[] = 'ELT_MAR'; //To enable Elite Marines enhancement
		$this->enhancementOptionsEnabled[] = 'EXT_MAR'; //To enable extra Marines enhancement
		
    }


    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(4, 4, 4, 4);
            $fighter = new Fighter("breachingPodMinbari", $armour, 21, $this->id);
            $fighter->displayName = "Ronati";
            $fighter->imagePath = "img/ships/MinbariFlyer.png";
            $fighter->iconPath = "img/ships/MinbariFlyer_Large.png";
			
			$fighter->addFrontSystem(new Marines(0, 360, 0, false)); //startarc, endarc, damagebonus, elite.
            $fighter->addAftSystem(new Jammer(0, 1, 0));	
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
            $this->addSystem($fighter);
        }
    }
}
?>
