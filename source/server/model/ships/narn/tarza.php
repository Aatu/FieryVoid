<?php
class Tarza extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 270;
	$this->faction = "Narn";
        $this->phpclass = "Tarza";
        $this->shipClass = "Tarza Torpedo Fighters";
	$this->imagePath = "img/ships/gorith.png";
        
        $this->forwardDefense = 7;
        $this->sideDefense = 9;
        $this->freethrust = 9;
        $this->offensivebonus = 5;
        $this->jinkinglimit = 8;
        $this->turncost = 0.33;
        
	$this->iniativebonus = 90;
        
        $this->occurence = 'rare';
        
        for ($i = 0; $i<6; $i++){
            $armour = array(2, 2, 2, 2);
            $fighter = new Fighter("tarza", $armour, 10, $this->id);
            $fighter->displayName = "Tarza Torpedo Fighter";
            $fighter->imagePath = "img/ships/gorith.png";
            $fighter->iconPath = "img/ships/gorith_large.png";
            
            $torpedoLauncher = new FighterTorpedoLauncher(4, 330, 30);
            $torpedoLauncher->firingModes = array( 1 => "LIT" );
            $torpedoLauncher->iconPath = "lightIonTorpedo";
            $torpedoLauncher->missileArray = array(1 => new LightIonTorpedo(330, 30));
            
            $fighter->addFrontSystem($torpedoLauncher);
            $fighter->addFrontSystem(new PairedParticleGun(330, 30, 3));
            
            $this->addSystem($fighter);
        }
    }
}
?>