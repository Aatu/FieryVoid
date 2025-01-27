<?php
class tghorAS extends FighterFlight{
    /*T'Ghor Narn Assault Shuttle*/
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 25*6;
		$this->faction = "Narn Regime";
        $this->phpclass = "tghorAS";
        $this->shipClass = "T'Ghor Early Assault Shuttles";
		$this->imagePath = "img/ships/NarnTGhor.png";
	    $this->isd = 2211;		    

		$this->variantOf = "T'Khar Assault Shuttles";
		$this->occurence = "common";
 		$this->unofficial = 'S'; //Showdowns 10	
        
        $this->forwardDefense = 9;
        $this->sideDefense = 9;
        $this->freethrust = 7;
        $this->offensivebonus = 2;
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
			$fighter = new Fighter("tkharas", $armour, 8, $this->id);
			$fighter->displayName = "T'Ghor";
			$fighter->imagePath = "img/ships/NarnTGhor.png";
			$fighter->iconPath = "img/ships/NarnTGhor_Large.png";
			
			
			$fighter->addFrontSystem(new PairedParticleGun(330, 30, 3, 1)); //1 gun d6+5
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			
			$this->addSystem($fighter);
			
		}
		
		
    }
}
?>
