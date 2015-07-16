<?php
class TarzaNav extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 330;
	$this->faction = "Narn";
        $this->phpclass = "TarzaNav";
        $this->shipClass = "Tarza Torpedo Fighters (with Navigator)";
	$this->imagePath = "img/ships/gorith.png";
        
        $this->forwardDefense = 7;
        $this->sideDefense = 9;
        $this->freethrust = 9;
        $this->offensivebonus = 5;
        $this->jinkinglimit = 8;
        $this->turncost = 0.33;
        
	$this->iniativebonus = 90;
        $this->hasNavigator = true;
        
        $this->occurence = 'rare';
        $this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(2, 2, 2, 2);
            $fighter = new Fighter("tarzaNav", $armour, 10, $this->id);
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