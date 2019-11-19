<?php
class zzftrSkipray extends FighterFlight{
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 100*6;
        $this->faction = "ZStarWars";
        $this->phpclass = "zzftrSkipray";
        $this->shipClass = "Skipray Blastboats";
        $this->imagePath = "img/starwars/skipray.png";
        
		$this->isd = "Galactic Civil War";
		$this->notes = "Primary users: Galactic Empire, New Republic.";
		$this->notes .= "Hyperdrive";
	    
        $this->unofficial = true;
        
        $this->forwardDefense = 9;
        $this->sideDefense = 10;
        $this->freethrust = 8;
        $this->offensivebonus = 6;
        $this->jinkinglimit = 4;
        $this->turncost = 0.33;
    	$this->hasNavigator = true;
        
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
            $armour = array(3, 3, 3, 2);
            $fighter = new Fighter("zzftrSkipray", $armour, 25, $this->id);
            $fighter->displayName = "Skipray Blastboat";
            $fighter->imagePath = "img/starwars/skipray.png";
            $fighter->iconPath = "img/starwars/skipray_large.png"; 
		            
       		
            $frontGun = new SWFighterIon(300, 60, 2, 3); //fwd triple Ion Cannons
            $fighter->addFrontSystem($frontGun);
            
            $roundGun = new SWFighterLaser(0, 360, 2, 2); //all-around dual Laser Cannons
            $fighter->addFrontSystem($roundGun);
           
            //1 forward Proton Torpedo Launcher, 5 shots
            $torpedoLauncher = new SWFtrProtonTorpedoLauncher(5, 300, 60, 2);//single launcher! like for direct fire
            $fighter->addFrontSystem($torpedoLauncher);


			//Ray Shield, 2 points
			$fighter->addAftSystem(new SWRayShield(0, 1, 0, 2, 0, 360));
			
        	$this->addSystem($fighter);
       }
    }
    
    
}
?>
