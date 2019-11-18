<?php
class swSkipray extends SuperHeavyFighter{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 100;
        $this->faction = "ZStarWars";
        $this->phpclass = "swSkipray";
        $this->shipClass = "Skipray Blastboat";
        $this->variantOf = "OBSOLETE";
        $this->imagePath = "img/starwars/skipray.png";
	
	//$this->isd = 2218;
        $this->unofficial = true;

        $this->forwardDefense = 9;
        $this->sideDefense = 10;
        $this->freethrust = 8;
        $this->offensivebonus = 6;
        $this->jinkinglimit = 4;
        $this->turncost = 0.33;
        $this->iniativebonus = 70;
    	$this->hasNavigator = true;
        
        $armour = array(3, 3, 3, 2);
        $fighter = new Fighter("swSkipray", $armour, 25, $this->id);
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
    public function populate(){
        return;
    }
}
?>
