<?php
class Stiletto extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 260*6;
		$this->faction = "Torvalus Speculators";
		$this->phpclass = "Stiletto";
		$this->shipClass = "Stiletto Drones";
		$this->imagePath = "img/ships/TorvalusStiletto.png";
	    
		$this->isd = 'Ancient';
		$this->factionAge = 3; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
        
		/*Vorlons use their own enhancement set */		
		Enhancements::nonstandardEnhancementSet($this, 'TorvalusFighter');
		
		$this->notes = "Has Jump Engine with 20 turn delay.";
		
		$this->forwardDefense = 5;
		$this->sideDefense = 6;
		$this->freethrust = 14;
		$this->offensivebonus = 8;
		$this->jinkinglimit = 10;
		$this->turncost = 0.25;
		$this->trueStealth = true; //For ships that can actually be hidden, not just jammer from range.  Important for Front End.	        
		
	    $this->advancedArmor = true; 
        $this->gravitic = true;
		//$this->critRollMod = -4; //dropout roll bonus 
        $this->maxFlightSize = 6;//this is very powerful craft, let's not overdo on its durability, limit flight size to 6
		$this->specialDropout = true; //Has special rules for dropout.  		
		$this->iniativebonus = 24*5;
		$this->populate();
    }


    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){			
			$armour = array(2, 1, 1, 1);
			$fighter = new Fighter("Stiletto", $armour, 8, $this->id);
			$fighter->displayName = "Stiletto Drone";
			$fighter->imagePath = "img/ships/TorvalusStiletto.png";
			$fighter->iconPath = "img/ships/TorvalusStiletto_Large.png";
						
			//main weapon
			$fighter->addFrontSystem(new UltralightLaser(330, 30, false));//arcfrom, arcto, dual mount true/false
			
			//ramming attack 			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
       	    //EM Shield Level 1
            $fighter->addAftSystem(new ShadingField(0, 1, 0, 3, 0, 360));
			//Advanced Sensors
            $fighter->addAftSystem(new Fighteradvsensors(0, 1, 0));			
			
			$this->addSystem($fighter);			
		}	
    }//endof function populate



}



?>
