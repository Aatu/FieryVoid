<?php
class systemDrone extends FighterFlight{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 160*6;
		$this->faction = "The System";
		$this->phpclass = "systemDrone";
		$this->shipClass = "Drone";
		$this->imagePath = "img/ships/systemDrone.png";
		$this->unofficial = true;
	    
		$this->isd = 'Ancient';
		$this->factionAge = 3; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial

		$this->notes = "Has Jump Engine with 30 turn delay.";
		$this->notes .= '<br>Does not require Hangar. System ships can control a certain number of Drones.';		
		
		$this->forwardDefense = 8;
		$this->sideDefense = 10;
		$this->freethrust = 15;
		$this->offensivebonus = 8;
		$this->jinkinglimit = 6;
		$this->turncost = 0.33;
        $this->pivotcost = 0;                  
		
	    $this->advancedArmor = true; 
        $this->gravitic = true;
		$this->hangarRequired = '';
		$this->critRollMod = 0; //Normal dropout rules.
		
		$this->iniativebonus = 19 *5;
        $this->dropOutBonus = -2;
		$this->populate();
    }


    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){			
			$armour = array(4, 3, 4, 4);
			$fighter = new Fighter("systemDrone", $armour, 10, $this->id);
			$fighter->displayName = "Drone";
			$fighter->imagePath = "img/ships/systemDrone.png";
			$fighter->iconPath = "img/ships/systemDrone_large.png";
						
			//main weapon
			$fighter->addFrontSystem(new NeutronBlasterFtr(330, 30, 1));//arcfrom, arcto, shots
			
			//ramming attack 			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
			//shield - derived from the Mindrider Thoughtshield
			$shield = new ThoughtShield(0, 9, 9, 0, 360, 'C');
			$shield->displayName = 'Shield Projection';
			$fighter->addAftSystem($shield);

			//Advanced Sensors
            $fighter->addAftSystem(new Fighteradvsensors(0, 1, 0));			
			
			
			$this->addSystem($fighter);			
		}	
    }//endof function populate


}



?>
