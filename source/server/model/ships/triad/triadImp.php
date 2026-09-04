<?php
class triadImp extends FighterFlight{

	public $hyperplasmaMatrixImmune = false;
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 175*6;
		$this->faction = "The Triad";
		$this->phpclass = "triadImp";
		$this->shipClass = "Chaos: Imp Medium Fighters";
		$this->imagePath = "img/ships/triadImp.png";
	    
		$this->isd = 'Primordial';
		$this->factionAge = 4; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial

		/*Triad use their own enhancement set */		
		Enhancements::nonstandardEnhancementSet($this, 'TriadFighter');
        
		$this->forwardDefense = 7;
		$this->sideDefense = 7;
		$this->freethrust = 15;
		$this->offensivebonus = 6;
		$this->jinkinglimit = 8; //medium fighter
		$this->turncost = 0.33;
		
	    $this->advancedArmor = true; 
        $this->gravitic = true;
        $this->maxFlightSize = 6;//this is very powerful craft, let's not overdo on its durability, limit flight size to 6

		$this->hangarRequired = "Triad Fighter"; 
		
		$this->iniativebonus = 20 *5;
		$this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){			
			$armour = array(4, 4, 4, 4);
			$fighter = new Fighter("triadImp", $armour, 15, $this->id);
			$fighter->displayName = "Imp";
			$fighter->imagePath = "img/ships/triadImp.png";
			$fighter->iconPath = "img/ships/triadImp_large.png";
						
			//main weapon
			$fighter->addFrontSystem(new HyperplasmaMatrix(330, 30, false));//arcfrom, arcto, dual mount true/false
			
			//ramming attack 			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
			//Advanced Sensors
            $fighter->addAftSystem(new Fighteradvsensors(0, 1, 0));			
			
			$this->addSystem($fighter);			
		}	
    }//endof function populate



}



?>
