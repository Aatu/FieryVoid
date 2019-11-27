<?php
class zzftrYT2400 extends FighterFlight{
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 75*6;
        $this->faction = "ZStarWars";
        $this->phpclass = "zzftrYT2400";
        $this->shipClass = "YT-2400 Light Freighters";
        $this->imagePath = "img/starwars/YT2400.png";
        
		$this->isd = "late Galactic Republic";
		$this->notes = "Primary users: common (civilian).";
		$this->notes .= "Hyperdrive";
	    
        $this->unofficial = true;
        
        $this->forwardDefense = 9;
        $this->sideDefense = 9;
        $this->freethrust = 7;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 4;
        $this->turncost = 0.33;
        
        
		$this->unitSize = 3; //number of craft in squadron
		
    	$this->iniativebonus = 8 *5; //essentially a civilian unit, Ini lowered
    	$this->superheavy = true;
        $this->maxFlightSize = 3;//this is a superheavy fighter originally intended as single unit, limit flight size to 3
		
        $this->hangarRequired = ''; //StarWars unit independence is much larger than B5, this SHF-sized unit is a cargo ship and has great endurance
  
		
        $this->populate();
		
    }
    
    
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(3, 3, 3, 2);
            $fighter = new Fighter("zzftrYT2400", $armour, 30, $this->id);
            $fighter->displayName = "YT-2400";
            $fighter->imagePath = "img/starwars/YT2400.png";
            $fighter->iconPath = "img/starwars/YT2400_Large.png"; 
            
            $roundGun = new SWFighterLaser(0, 360, 2, 2); //all-around dual Laser Cannons
            $fighter->addFrontSystem($roundGun);
            
            $roundGun = new SWFighterLaser(0, 360, 2, 2); //all-around dual Laser Cannons
            $fighter->addFrontSystem($roundGun);

			//Ray Shield, 1 points
			$fighter->addAftSystem(new SWRayShield(0, 1, 0, 1, 0, 360));


        	$this->addSystem($fighter);
       }
    }
    
    
}
?>
