<?php
class DrakhShuttle extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 80*6;
	$this->faction = "Drakh";
        $this->phpclass = "DrakhShuttle";
        $this->shipClass = "Armed Shuttles";
	 $this->imagePath = "img/ships/DrakhShuttle.png";
        
        $this->forwardDefense = 9;
        $this->sideDefense = 8;
        $this->freethrust = 10;
        $this->offensivebonus = 5;
        $this->jinkinglimit = 2;
        $this->pivotcost = 2; //shuttles have pivot cost higher
	    $this->gravitic = true;
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->advancedArmor = true;   
        
		$this->hangarRequired = 'Shuttles'; //for fleet check
	$this->iniativebonus = 10*5;
      
        $this->populate();
    }
    
    
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
			
        $armour = array(2, 1, 2, 2);
        $fighter = new Fighter("DrakhShuttle", $armour, 18, $this->id);
        $fighter->displayName = "Armed Shuttle";
        $fighter->imagePath = "img/ships/DrakhShuttle.png";
        $fighter->iconPath = "img/ships/DrakhShuttle_Large.png";

		
			
		$fighter->addFrontSystem(new LightFusionCannon(330, 30, 4, 2));
           
        	//Absorbtion Shield, 1 points
        	$fighter->addAftSystem(new AbsorbtionShield(0, 1, 0, 1, 0, 360));
						
			//Improved Sensors
            $fighter->addAftSystem(new Fighterimprsensors(0, 1, 0));
			
		$this->addSystem($fighter);
			
	}
		
		
    }
}
?>
