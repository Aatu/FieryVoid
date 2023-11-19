<?php
class DalithornMicrosatRefit extends MicroSAT{
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 125*6;
        $this->faction = "ZNexus Dalithorn Commonwealth";
        $this->phpclass = "DalithornMicrosatRefit";
        $this->shipClass = "Laser MicroSAT Cluster (2132 refit)";
			$this->variantOf = "Laser MicroSAT Cluster";
			$this->occurence = "common";
        $this->imagePath = "img/ships/Nexus/DalithornMicrosat.png";
		$this->unofficial = true;
        
		$this->isd = 2132;

        $this->canvasSize = 100;
        
        $this->forwardDefense = 8;
        $this->sideDefense = 9;
        $this->freethrust = 4;
        $this->offensivebonus = 4; 
        $this->turncost = 0.33; //actually not all that relevant...
        
		$this->hangarRequired = ""; //they don't require any hangars... although of course cannot be used in pickup battle either!
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
            $armour = array(3, 3, 3, 3);
            $fighter = new Fighter("Microsat", $armour, 26, $this->id);
            $fighter->displayName = "Microsat";
            $fighter->imagePath = "img/ships/Nexus/DalithornMicrosat.png";
            $fighter->iconPath = "img/ships/Nexus/DalithornMicrosat_large.png"; 
		            
			$leftgun = new NexusMinigunFtr(180, 360, 1);
			$fighter->addFrontSystem($leftgun);
			$hvyGun = new NexusMediumChemicalLaser(0, 1, 0, 330, 30); 
			$hvyGun->fireControl = array(-4, 0, 0); // fighters, <mediums, <capitals	
			$fighter->addFrontSystem($hvyGun);
			$rightgun = new NexusMinigunFtr(0, 180, 1);
			$fighter->addFrontSystem($rightgun);
            
			
        	$this->addSystem($fighter);
       }
    }
    
    
}
?>
