<?php
class DalithornMicrosat extends MicroSAT{
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 100*6;
        $this->faction = "Nexus Dalithorn Commonwealth (early)";
        $this->phpclass = "DalithornMicrosat";
        $this->shipClass = "Laser MicroSAT Cluster";
        $this->imagePath = "img/ships/Nexus/Dalithorn_Microsat2.png";
		$this->unofficial = true;
        
		$this->isd = 2087;

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
		
        $this->enhancementOptionsEnabled[] = 'EXT_AMMO'; //To enable extra Ammo for main gun.

    }
    
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(3, 2, 2, 2);
            $fighter = new Fighter("Microsat", $armour, 26, $this->id);
            $fighter->displayName = "Microsat";
            $fighter->imagePath = "img/ships/Nexus/Dalithorn_Microsat2.png";
            $fighter->iconPath = "img/ships/Nexus/Dalithorn_Microsat_Large2.png"; 
		            
			$leftgun = new NexusShatterGunFtr(180, 360, 1);
			$fighter->addFrontSystem($leftgun);
			$hvyGun = new NexusMedChemicalLaserFtr(330, 30, 1); 
			$hvyGun->fireControl = array(-4, 0, 0); // fighters, <mediums, <capitals	
			$fighter->addFrontSystem($hvyGun);
			$rightgun = new NexusShatterGunFtr(0, 180, 1);
			$fighter->addFrontSystem($rightgun);
			
        	$this->addSystem($fighter);

			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack

       }
    }
    
    
}
?>
