<?php
class AnroethAS extends FighterFlight{
    /*Anroeth Hurr Assault Shuttles*/
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 26*6;
		$this->faction = "Hurr";
        $this->phpclass = "AnroethAS";
        $this->shipClass = "Anroeth Assault Shuttles";
		$this->imagePath = "img/ships/doubleV.png";
        
        $this->forwardDefense = 9;
        $this->sideDefense = 10;
        $this->freethrust = 4;
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
			
			$armour = array(1, 1, 1, 1);
			$fighter = new Fighter("AnroethAS", $armour, 15, $this->id);
			$fighter->displayName = "Anroeth Assault Shuttle";
			$fighter->imagePath = "img/ships/Hurranroeth.png";
			$fighter->iconPath = "img/ships/Hurranroeth_large.png";
			
			
			$fighter->addFrontSystem(new PairedParticleGun(330, 30, 1, 1)); //1 gun d6+1
			
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack		
			
			
			$this->addSystem($fighter);
			
		}
		
		
    }
}

?>
