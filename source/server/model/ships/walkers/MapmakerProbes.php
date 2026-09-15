<?php
class MapmakerProbes extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 210*6;
		$this->faction = "Walkers of Sigma-957";
		$this->phpclass = "MapmakerProbes";
		$this->shipClass = "Mapmaker Sensor Probes";
		$this->imagePath = "img/ships/WalkerMapmaker.png";
	    
		$this->isd = 'Ancient';
		$this->factionAge = 3; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
		$this->variantOf = "NONE";
        
		/*Vorlons use their own enhancement set */		
		Enhancements::nonstandardEnhancementSet($this, 'WalkerFighter');

		$this->notes = "Does not require hangar space.";		
		$this->notes .= "Can use EW.";
		
		$this->forwardDefense = 5;
		$this->sideDefense = 8;
		$this->freethrust = 15;
		$this->offensivebonus = 8;
		$this->jinkinglimit = 8; //heavy fighter
		$this->turncost = 0.33;
        
		
	    $this->advancedArmor = true; 
        $this->gravitic = true;
        $this->maxFlightSize = 6;//this is very powerful craft, let's not overdo on its durability, limit flight size to 6
		
		$this->iniativebonus = 20 *5;
		$this->populate();
    }


    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){			
			$armour = array(3, 5, 3, 3);
			$fighter = new Fighter("MapmakerProbes", $armour, 13, $this->id);
			$fighter->displayName = "Mapmaker";
			$fighter->imagePath = "img/ships/WalkerMapmaker.png";
			$fighter->iconPath = "img/ships/WalkerMapmaker_large.png";
						
			//main weapon
			$fighter->addFrontSystem(new LightChromaticPulsar(330, 30));//arcfrom, arcto, dual mount true/false
			//$fighter->addFrontSystem(new MedLightnIngArrayFighter(330, 30, false));//arcfrom, arcto, dual mount true/false
			
			//ramming attack 			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
       	    //Jump Engine
            //$fighter->addAftSystem(new JumpEngine(0, 1, 0, 10)); //Placeholder line for Jump Engine system
			//Advanced Sensors w/ 3 EW
            $fighter->addAftSystem(new Fighteradvsensors(0, 1, 0));	//Need to modify this so it also provide the 3 EW Mapmakers have		
			
			
			$this->addSystem($fighter);			
		}	
    }//endof function populate



}



?>
