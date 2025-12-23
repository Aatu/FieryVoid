<?php
class CraytanPoltenRefit2 extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	    $this->pointCost = 20*6;
	    $this->faction = "Nexus Craytan Union";
        $this->phpclass = "CraytanPoltenRefit2";
        $this->shipClass = "Polten Assault Shuttles (2123)";
	    $this->imagePath = "img/ships/Nexus/craytan_polten.png";

	    $this->isd = 2123;
		$this->unofficial = true;
        
        $this->forwardDefense = 11;
        $this->sideDefense = 11;
        $this->freethrust = 8;
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
			$fighter->imagePath = "img/ships/Nexus/craytan_polten.png";
			$fighter->iconPath = "img/ships/Nexus/craytan_polten_large.png";
			
            $fighter->addFrontSystem(new RogolonLtPlasmaGun(300, 60, 5, 1));

			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			$this->addSystem($fighter);
			
		}
		
		
    }
}
?>
