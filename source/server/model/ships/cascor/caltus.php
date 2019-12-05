<?php
class Caltus extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 65*6;
        $this->faction = "Cascor";
        $this->phpclass = "Caltus";
        $this->shipClass = "Caltus Torpedo Fighters";
        $this->imagePath = "img/ships/CascorCalaq.png";
        $this->isd = 2259;
	    //$this->notes = 'Non-atmospheric.';
	    $this->occurence ="uncommon";
	    $this->variantOf ="Calaq Assault Fighters";
        
        $this->forwardDefense = 10;
        $this->sideDefense = 9;
        $this->freethrust = 10;
        $this->offensivebonus = 5;
        $this->jinkinglimit = 6;
        $this->accelcost = 2;
        $this->turncost = 0.33;
        $this->hasNavigator = true;
        
    	$this->iniativebonus = 16*5; //+1 from Navigator, but that's added automatically as a modifier
        $this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(2, 2, 2, 2);
            $fighter = new Fighter("Caltus", $armour, 10, $this->id);
            $fighter->displayName = "Caltus";
            $fighter->imagePath = "img/ships/CascorCalaq.png";
            $fighter->iconPath = "img/ships/CascorCalaq_Large.png";

            $frontGun = new Ionizer(330, 30, 1);
            //$frontGun->displayName = "Ionizer"; //not needed
            $fighter->addFrontSystem($frontGun);	
            
            $torpedoLauncher = new FighterTorpedoLauncher(3, 330, 30);
            $torpedoLauncher->firingModes = array( 1 => "LIT" );
            $torpedoLauncher->iconPath = "lightIonTorpedo.png";
            $torpedoLauncher->displayName = "Light Ion Torpedo"; //needed
            $torpedoLauncher->missileArray = array(1 => new LightIonTorpedo(330, 30));            	
            $fighter->addFrontSystem($torpedoLauncher);
		
            $torpedoLauncher = new FighterTorpedoLauncher(3, 330, 30);
            $torpedoLauncher->firingModes = array( 1 => "LIT" );
            $torpedoLauncher->iconPath = "lightIonTorpedo.png";
            $torpedoLauncher->displayName = "Light Ion Torpedo"; //needed
            $torpedoLauncher->missileArray = array(1 => new LightIonTorpedo(330, 30));
            $fighter->addFrontSystem($torpedoLauncher);
		
            $this->addSystem($fighter);
       }
    }
}
?>
