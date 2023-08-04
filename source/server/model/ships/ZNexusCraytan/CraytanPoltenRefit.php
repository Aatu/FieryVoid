<?php
class CraytanPoltenRefit extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	    $this->pointCost = 18*6;
	    $this->faction = "ZNexus Craytan";
        $this->phpclass = "CraytanPoltenRefit";
        $this->shipClass = "Polten Assault Shuttles (2090 refit)";
			$this->variantOf = "Polten Assault Shuttles";
			$this->occurence = "common";
	    $this->imagePath = "img/ships/Nexus/CraytanPolten.png";

	    $this->isd = 2090;
		$this->unofficial = true;
        
        $this->forwardDefense = 11;
        $this->sideDefense = 11;
        $this->freethrust = 7;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 0;
        $this->pivotcost = 2; //shuttles have pivot cost higher
        $this->turncost = 0.33;
        
		$this->hangarRequired = 'assault shuttles'; //for fleet check
	    $this->iniativebonus = 12*5;
      
        $this->populate();
    }
    
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
			
			$armour = array(2, 2, 2, 2);
			$fighter = new Fighter("Polten", $armour, 12, $this->id);
			$fighter->displayName = "Polten";
			$fighter->imagePath = "img/ships/Nexus/CraytanPolten.png";
			$fighter->iconPath = "img/ships/Nexus/CraytanPolten_large.png";
			
	        $autogun = new NexusAutogun(300, 60, 1); //$startArc, $endArc, $nrOfShots
	        $fighter->addFrontSystem($autogun);
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			$this->addSystem($fighter);
			
		}
		
		
    }
}
?>
