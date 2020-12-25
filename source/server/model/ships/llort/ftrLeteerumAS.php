<?php
class ftrLeteerumAS extends FighterFlight{
    /*Leteerum Llort Assault Shuttle*/
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		    $this->pointCost = 30*6;
		    $this->faction = "Llort";
        $this->phpclass = "ftrLeteerumAS";
        $this->shipClass = "Leteerum Assault Shuttles";
		    $this->imagePath = "img/ships/LlortLeteerum.png";
        
        $this->forwardDefense = 9;
        $this->sideDefense = 9;
        $this->freethrust = 8;
        $this->offensivebonus = 3;
        $this->jinkinglimit = 0;
        $this->pivotcost = 2; //shuttles have pivot cost higher
        $this->turncost = 0.33;
        
		$this->hangarRequired = 'assault shuttles'; //for fleet check
		$this->iniativebonus = 9*5;
      
        $this->populate();
    }
    
    
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
			
			$armour = array(2, 2, 2, 2);
			$fighter = new Fighter("ftrLeteerumAS", $armour, 10, $this->id);
			$fighter->displayName = "Leteerum";
			$fighter->imagePath = "img/ships/LlortLeteerum.png";
			$fighter->iconPath = "img/ships/LlortLeteerum_Large.png";
			
			
			$fighter->addFrontSystem(new PairedParticleGun(330, 30, 2, 1)); //1 gun d6+2
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
						
			$this->addSystem($fighter);
			
		}
		
		
    }
}
?>
