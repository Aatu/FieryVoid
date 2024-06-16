<?php
class breachingpodcentwotcr extends FighterFlight{
    /*Centauri Larisi Assault Shuttles*/
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 30*6;
        $this->faction = "Centauri Republic (WotCR)";
        $this->phpclass = "breachingpodcentwotcr";
        $this->shipClass = "Sciona Breaching Pod";
		$this->imagePath = "img/ships/phalan.png";
        
        $this->forwardDefense = 9;
        $this->sideDefense = 9;
        $this->freethrust = 5;
        $this->offensivebonus = 1;
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
			$fighter = new Fighter("breachingpodcentwotcr", $armour, 8, $this->id);
			$fighter->displayName = "Sciona";
			$fighter->imagePath = "img/ships/phalan.png";
			$fighter->iconPath = "img/ships/phalan_large.png";
			
			
			$fighter->addFrontSystem(new Marines(330, 30, 0, false)); //startarc, endarc, damagebonus, elite.
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack								
			$this->addSystem($fighter);
			
		}
		
		
    }
}
?>
