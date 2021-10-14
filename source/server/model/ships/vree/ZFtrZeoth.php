<?php
class ZFtrZeoth extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 30*6;
		$this->faction = "Vree";
		$this->phpclass = "ZFtrZeoth";
        $this->shipClass = "Zeoth Assault Shuttles";
		$this->imagePath = "img/ships/VreeZeoth.png";
        
        $this->forwardDefense = 8;
        $this->sideDefense = 8;
        $this->freethrust = 5;
        $this->offensivebonus = 3;
        $this->jinkinglimit = 0;
        $this->pivotcost = 0; //shuttles have pivot cost higher
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
			$fighter = new Fighter("Zeoth", $armour, 10, $this->id);
			$fighter->displayName = "Zeoth";
			$fighter->imagePath = "img/ships/VreeZeoth.png";
			$fighter->iconPath = "img/ships/VreeZeoth_Large.png";
			
			
            $fighter->addFrontSystem(new LightAntiprotonGun(330, 30, 1));
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			
			$this->addSystem($fighter);
			
		}
		
		
    }
}
?>
