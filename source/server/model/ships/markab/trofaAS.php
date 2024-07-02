<?php

class trofaAS extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 30*6;
        $this->faction = "Markab Theocracy";
        $this->phpclass = "trofaAS";
        $this->shipClass = "Trofa Assault Shuttles";
        $this->imagePath = "img/ships/MarkabDrofta.png";
		$this->isd = 2005;
        
        $this->forwardDefense = 10;
        $this->sideDefense = 10;
        $this->freethrust = 5;
        $this->offensivebonus = 3;
        $this->jinkinglimit = 0;
        $this->turncost = 0.33;
        
    	$this->iniativebonus = 9 *5;
  	
    	
        $this->populate();        

		$this->enhancementOptionsEnabled[] = 'FTR_FERV'; //To activate Religious Fervor attributes.  
    }



    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){        
            $armour = array(3, 3, 3, 3);
            $fighter = new Fighter("trofaAS", $armour, 13, $this->id);
            $fighter->displayName = "Trofa";
            $fighter->imagePath = "img/ships/MarkabDrofta.png";
            $fighter->iconPath = "img/ships/MarkabDroftaLARGE.png";

		$gun = new LightParticleBeam(330, 30, 1, 1);
		$gun->displayName = "Ultralight Particle Beam";
		$fighter->addFrontSystem($gun);
		
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
            $this->addSystem($fighter);
	   }
    }
}

?>
