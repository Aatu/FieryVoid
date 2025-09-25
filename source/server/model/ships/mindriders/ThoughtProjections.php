<?php
class ThoughtProjections extends FighterFlight{
	public $mindrider = true;    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 160*6;
		$this->faction = "Mindriders";
		$this->phpclass = "ThoughtProjections";
		$this->shipClass = "Thought Projections";
		$this->imagePath = "img/ships/MindriderProjection.png";
	    
		$this->isd = 'Ancient';
		$this->factionAge = 3; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial

		Enhancements::nonstandardEnhancementSet($this, 'MindriderFighter');
		
		$this->notes = "Automatically drops out if no Mindrider ship with available hangar space on battlefield!";
		$this->notes .= '<br>Ignores Manoeuvre Hit Modifiers';		
		
		$this->forwardDefense = 4;
		$this->sideDefense = 4;
		$this->freethrust = 16;
		$this->offensivebonus = 6;
		$this->jinkinglimit = 10;
		$this->turncost = 0.33;
        $this->maxFlightSize = 6;          
        $this->pivotcost = 0;                  
		
	    $this->advancedArmor = true; 
        $this->gravitic = true;
		$this->ignoreManoeuvreMods = true;         
		$this->critRollMod = 0; //Normal dropout rules.
		
		$this->iniativebonus = 22 *5;
		$this->populate();
		
        MindriderHangar::addProjections($this);		
    }


    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){			
			$armour = array(2, 2, 2, 2);
			$fighter = new Fighter("ThoughtProjections", $armour, 6, $this->id);
			$fighter->displayName = "Thought Projection";
			$fighter->imagePath = "img/ships/MindriderProjection.png";
			$fighter->iconPath = "img/ships/MindriderProjection_Large.png";
						
			//main weapon
			$fighter->addFrontSystem(new MinorThoughtPulsar(0, 360, 1));//arcfrom, arcto, shots
			
			//ramming attack 			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
			//ThoughtShield
            $fighter->addAftSystem(new ThoughtShield(0, 9, 9, 0, 360, 'C'));
			//Advanced Sensors
            $fighter->addAftSystem(new Fighteradvsensors(0, 1, 0));			
			
			
			$this->addSystem($fighter);			
		}	
    }//endof function populate


}



?>
