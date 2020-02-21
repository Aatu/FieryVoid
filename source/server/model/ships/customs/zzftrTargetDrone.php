<?php
class zzftrTargetDrone extends FighterFlight{
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 10*6;
        $this->faction = "Custom Ships";
        $this->phpclass = "zzftrTargetDrone";
		$this->shipClass = "Fighter Target Drone - DO NOT USE";
        $this->imagePath = "img/starwars/skipray.png";
        
		$this->notes = "DO NOT USE, prone to change!";
	    
        $this->unofficial = true;
        
        $this->forwardDefense = 10;
        $this->sideDefense = 10;
        $this->freethrust = 15;
        $this->offensivebonus = 8;
        $this->jinkinglimit = 4;
        $this->turncost = 0.33;
        
		$this->hangarRequired = "Fighter Squadrons"; //SW small craft are handled on squadron basis
		$this->unitSize = 3; //number of craft in squadron
		
    	$this->iniativebonus = 14 *5; 
    	$this->superheavy = true;
        $this->maxFlightSize = 3;//this is a superheavy fighter originally intended as single unit, limit flight size to 3
		
		
        $this->populate();
		
    }
    
    
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(4, 3, 3, 2);
            $fighter = new Fighter("zzftrTargetDrone", $armour, 25, $this->id);
            $fighter->displayName = "Target Drone";
            $fighter->imagePath = "img/starwars/skipray.png";
            $fighter->iconPath = "img/starwars/skipray_large.png"; 
		            
       		
            $frontGun = new SWFighterIon(300, 60, 2, 3); //fwd triple Ion Cannons
            $fighter->addFrontSystem($frontGun);
            
            $roundGun = new SWFighterLaser(0, 360, 2, 2); //all-around dual Laser Cannons
            $fighter->addFrontSystem($roundGun);
           



		$AAC = $this->createAdaptiveArmorController(4, 3, 1); //$AAtotal, $AApertype, $AApreallocated
		$fighter->addAftSystem( $AAC );

			
        	$this->addSystem($fighter);
       }
    }
    
    
}
?>
