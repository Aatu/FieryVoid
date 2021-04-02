<?php
class VorlonHeavyFighterFlight extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 175*6;
		$this->faction = "Custom Ships";
		$this->phpclass = "VorlonHeavyFighterFlight";
		$this->shipClass = "Heavy Fighters";
		$this->imagePath = "img/ships/VorlonFighter.png";
	    
		$this->isd = 'Ancient';
		$this->factionAge = 3; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
        
		/*Vorlons use their own enhancement set */		
		Enhancements::nonstandardEnhancementSet($this, 'VorlonFighter');
		
		//$this->notes = "Shadow fighters are integral part of their carriers. For every Shadow fighter included in fleet, appropriate carrier should take a level of Fighter Launched enhancement OR fighter should take Uncontrolled enhancement (the latter for scenarios only).";
		
		$this->forwardDefense = 7;
		$this->sideDefense = 9;
		$this->freethrust = 14;
		$this->offensivebonus = 9;
		$this->jinkinglimit = 6; //heavy fighter
		$this->turncost = 0.33;
        
		
	    $this->advancedArmor = true; 
        $this->gravitic = true;
		$this->critRollMod = -4; //dropout roll bonus 
        $this->maxFlightSize = 6;//this is very powerful craft, let's not overdo on its durability, limit flight size to 6
		
		$this->iniativebonus = 18 *5;
		$this->populate();
    }


    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){			
			$armour = array(4, 2, 3, 3);
			$fighter = new Fighter("VorlonHeavyFighterFlight", $armour, 15, $this->id);
			$fighter->displayName = "Heavy Fighter";
			$fighter->imagePath = "img/ships/VorlonFighter.png";
			$fighter->iconPath = "img/ships/VorlonFighter_Large.png";
						
			//main weapon
			$fighter->addFrontSystem(new VorlonLtDischargeGun(330, 30, false));//arcfrom, arcto, dual mount true/false
			
			//Adaptive Armor
			$AAC = $this->createAdaptiveArmorController(2, 1, 0); //$AAtotal, $AApertype, $AApreallocated
			$fighter->addFrontSystem( $AAC );

			
			//ramming attack 			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
       	    //EM Shield Level 1
            $fighter->addAftSystem(new FtrShield(1, 0, 360));
			//Advanced Sensors
            $fighter->addAftSystem(new Fighteradvsensors(0, 1, 0));			
			
			
			$this->addSystem($fighter);			
		}	
    }//endof function populate



}



?>
