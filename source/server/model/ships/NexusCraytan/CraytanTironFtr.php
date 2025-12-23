<?php
class CraytanTironFtr extends FighterFlight{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 38*6;
        $this->faction = "Nexus Craytan Union";
        $this->phpclass = "CraytanTironFtr";
        $this->shipClass = "Tiron Medium Fighters";
        $this->imagePath = "img/ships/Nexus/craytan_tiron.png";
        
        $this->isd = 2114;
        
        $this->forwardDefense = 8;
        $this->sideDefense = 9;
        $this->freethrust = 9;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 8;
        $this->turncost = 0.33;
        
    	$this->iniativebonus = 18 *5; 
        
        $this->populate();
    }
    
    
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(2, 2, 1, 1);
            $fighter = new Fighter("RogolonChelekFtr", $armour, 10, $this->id);
            $fighter->displayName = "Tiron";
            $fighter->imagePath = "img/ships/Nexus/craytan_tiron.png";
            $fighter->iconPath = "img/ships/Nexus/craytan_tiron_large.png"; 

            $fighter->addFrontSystem(new RogolonLtPlasmaGun(330, 30, 5, 2));
			
			$torpedoLauncher = new NexusFighterTorpedoLauncher(1, 330, 30);
            $torpedoLauncher->firingModes = array( 1 => "LPT" );
            $torpedoLauncher->iconPath = "EWLightPlasmaMine.png";
           $torpedoLauncher->displayName = "Light Plasma Torpedo"; //needed
            $torpedoLauncher->missileArray = array(1 => new NexusLtPlasmaTorpedo(330, 30));            	
            $fighter->addFrontSystem($torpedoLauncher);
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
            $this->addSystem($fighter);
       }
    }
    
    
}
?>
