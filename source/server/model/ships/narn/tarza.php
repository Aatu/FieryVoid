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
	$this->variantOf = "Gorith Medium Fighters";
	    
		$this->enhancementOptionsEnabled[] = 'NAVIGATOR'; //this flight can take Navigator enhancement option	
		
        $this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(2, 2, 2, 2);
            $fighter = new Fighter("tarza", $armour, 10, $this->id);
            $fighter->displayName = "Tarza";
            $fighter->imagePath = "img/ships/gorith.png";
            $fighter->iconPath = "img/ships/gorith_large.png";
            
            $torpedoLauncher = new FighterTorpedoLauncher(4, 330, 30);
            $torpedoLauncher->firingModes = array( 1 => "LIT" );
            $torpedoLauncher->iconPath = "lightIonTorpedo.png";
            $torpedoLauncher->missileArray = array(1 => new LightIonTorpedo(330, 30));
            $torpedoLauncher->displayName = "Light Ion Torpedo"; //needed
            
            $fighter->addFrontSystem($torpedoLauncher);
            $fighter->addFrontSystem(new PairedParticleGun(330, 30, 3));
            
            $this->addSystem($fighter);
        }
    }
}
?>
