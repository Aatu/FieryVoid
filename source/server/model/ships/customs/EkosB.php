<?php
class EkosB extends MineClass{
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 45*6;
        $this->faction = "Custom Ships";
        $this->phpclass = "EkosB";
        $this->shipClass = "Ekos-B Mine";
        $this->imagePath = "img/ships/mine.png";
        
		$this->isd = 1950;
       
        $this->forwardDefense = 10;
        $this->sideDefense = 10;
        $this->freethrust = 0;
        $this->offensivebonus = 6; 
        $this->turncost = 0.33; //actually not all that relevant...
        
		$this->hangarRequired = ""; //they don't require any hangars... although of course cannot be used in pickup battle either!
		$this->unitSize = 6; //number of craft in squadron
		
    	$this->iniativebonus = -20; 
//    	$this->superheavy = true;
        $this->maxFlightSize = 6;//this is a superheavy fighter originally intended as single unit, limit flight size to 3
		
        $this->populate();
		
    }
    
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(2, 2, 2, 2);
            $fighter = new Fighter("Ekos-B", $armour, 9, $this->id);
            $fighter->displayName = "Ekos-B";
            $fighter->imagePath = "img/ships/mine.png";
            $fighter->iconPath = "img/ships/mine_large.png"; 

			$TA1 = new TAFtr8(0, 360, 1);
			$fighter->addFrontSystem($TA1);
			$TA2 = new TAFtr8(0, 360, 1);
			$fighter->addFrontSystem($TA2);
			
        	$this->addSystem($fighter);
       }
    }
    
    
}
?>
