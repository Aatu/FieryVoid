<?php
class VorlonAssaultFighterFlight extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 295*6;
		$this->faction = "Vorlons";
		$this->phpclass = "VorlonAssaultFighterFlight";
		$this->shipClass = "Assault Fighters";
		$this->imagePath = "img/ships/VorlonFighter.png";
		$this->variantOf = 'Heavy Fighters'; 
        $this->occurence = "rare"; //official: Uncommon, but counted as Assault craft per Heavy flights
	    
		$this->isd = 'Primordial';
		$this->factionAge = 4; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
        
		/*Vorlons use their own enhancement set */		
		Enhancements::nonstandardEnhancementSet($this, 'VorlonFighter');
		
		$this->notes = "In Primordial timeframe treated as base hull instead of variant."; 
		
		$this->forwardDefense = 10;
		$this->sideDefense = 12;
		$this->freethrust = 13;
		$this->offensivebonus = 10;
		$this->jinkinglimit = 4; //superheavy fighter
		$this->turncost = 0.33;
		$this->turndelay = 0.25;
        
		
	    $this->superheavy = true; //this is superheavy fighter, grouped into flight for convenience in large battles!
	    $this->advancedArmor = true; 
        $this->gravitic = true;
		$this->critRollMod = -4; //dropout roll bonus 
        $this->maxFlightSize = 3;//this is a superheavy fighter originally intended as single unit, limit flight size to 3
		
		
		$this->hangarRequired = "heavy"; //Vorlon Assault Fighters are housed in regular hangars, as heavy fighters
		$this->unitSize = 0.5; //one craft requires 2 hangar slots
		
		
		$this->iniativebonus = 17 *5;
		$this->populate();
    }


    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){			
			$armour = array(4, 3, 4, 4);
			$fighter = new Fighter("VorlonAssaultFighterFlight", $armour, 30, $this->id);
			$fighter->displayName = "Assault Fighter";
			$fighter->imagePath = "img/ships/VorlonFighter.png";
			$fighter->iconPath = "img/ships/VorlonFighter_Large.png";
						
			//main weapon
			$fighter->addFrontSystem(new VorlonLtDischargeGun(330, 30, true));//arcfrom, arcto, dual mount true/false
			
			//Adaptive Armor
			$AAC = $this->createAdaptiveArmorController(3, 1, 0); //$AAtotal, $AApertype, $AApreallocated
			$fighter->addFrontSystem( $AAC );

			
			//ramming attack 			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
       	    //EM Shield Level 2
            $fighter->addAftSystem(new FtrShield(2, 0, 360));
			//Advanced Sensors
            $fighter->addAftSystem(new Fighteradvsensors(0, 1, 0));			
			
			
			$this->addSystem($fighter);			
		}	
    }//endof function populate



}



?>
