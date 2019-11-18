<?php
class zzftrtiedef extends FighterFlight{
    /*StarWars Tie Defender...*/
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 65*6;
        $this->faction = "ZStarWars";
        $this->phpclass = "zzftrtiedef";
        $this->shipClass = "TIE Defender Superiority Fighters";
        $this->imagePath = "img/starwars/tieDefender.png";
        
        //$this->isd = 2214;
        $this->unofficial = true;
        
        $this->forwardDefense = 8;
        $this->sideDefense = 7;
        $this->freethrust = 12;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 6;
        $this->turncost = 0.25;
        
    	$this->iniativebonus = 16 *5; 
        
		
		$this->hangarRequired = "Fighter Squadrons"; //SW small craft are handled on squadron basis
		$this->unitSize = 9; //number of craft in squadron
		
        $this->populate();
    }
    
    
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(2, 2, 2, 2);
            $fighter = new Fighter("zzftrtiedef", $armour, 12, $this->id);
            $fighter->displayName = "TIE Defender";
            $fighter->imagePath = "img/starwars/tieDefender.png";
            $fighter->iconPath = "img/starwars/tieDefender_large.png"; 
            
            $frontGun = new SWFighterLaser(330, 30, 2, 4); //front Lasers
            $fighter->addFrontSystem($frontGun);

            $frontGun = new SWFighterIon(330, 30, 1, 2); //front ion cannons
            $frontGun->exclusive = true; //either this or other weapons! no separate gunner here!
            $fighter->addFrontSystem($frontGun);
            
           
            //2 forward Proton Torpedo Launchers, 3 shots each
            $torpedoLauncher = new SWFtrProtonTorpedoLauncher(3, 330, 30, 2);//single dual launcher! like for direct fire
            $fighter->addFrontSystem($torpedoLauncher);
            /*$torpedoLauncher = new SWFtrProtonTorpedoLauncher(3, 330, 30);
            $fighter->addFrontSystem($torpedoLauncher);*/
            
            
            
            //Ray Shield, 1 points
            $fighter->addAftSystem(new SWRayShield(0, 1, 0, 1, 0, 360));
            
            
            
            $this->addSystem($fighter);
       }
    }
    
    
}
?>
