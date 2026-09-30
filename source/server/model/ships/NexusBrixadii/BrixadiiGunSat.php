<?php
class BrixadiiGunSat extends MicroSAT{
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 60*6;
        $this->faction = "Nexus Brixadii Clans";
        $this->phpclass = "BrixadiiGunSat";
        $this->shipClass = "Rivex Gun Sat Cluster";
        $this->imagePath = "img/ships/Nexus/brixadii_gunsat.png";
		$this->unofficial = true;
        
		$this->isd = 2109;

        $this->canvasSize = 100;
        
        $this->forwardDefense = 5;
        $this->sideDefense = 5;
        $this->freethrust = 4;
        $this->offensivebonus = 6; 
        $this->turncost = 0.33; //actually not all that relevant...
        
		$this->hangarRequired = ""; //they don't require any hangars... although of course cannot be used in pickup battle either!
		$this->unitSize = 3; //number of craft in squadron
		
    	$this->iniativebonus = 15 *5; 
    	$this->superheavy = true;
        $this->maxFlightSize = 3;//this is a superheavy fighter originally intended as single unit, limit flight size to 3
		
        $this->populate();
		
    }
    
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(3, 3, 3, 3);
            $fighter = new Fighter("Rivex", $armour, 24, $this->id);
            $fighter->displayName = "Microsat";
            $fighter->imagePath = "img/ships/Nexus/brixadii_gunsat.png";
            $fighter->iconPath = "img/ships/Nexus/brixadii_gunsat_large.png"; 
		            
			$leftgun = new ScatterPulsarFtr(270, 90, 1);
			$fighter->addFrontSystem($leftgun);
			$rightgun = new ScatterPulsarFtr(270, 90, 1);
			$fighter->addFrontSystem($rightgun);
			
        	$this->addSystem($fighter);

			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack

       }
    }
    
    
}
?>
