<?php
class ArmedShuttleDrazi extends FighterFlight{
    /*generic armed shuttles*/
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 24*6;
        $this->faction = "Drazi Freehold";
        $this->phpclass = "ArmedShuttleDrazi";
        $this->shipClass = "Armed Shuttles";
		$this->imagePath = "img/ships/shuttleDrazi.png"; //more appropriate image needed
        $this->isd = 2200;
        
		Enhancements::nonstandardEnhancementSet($this, 'Shuttles');
        
		
        $this->forwardDefense = 8;
        $this->sideDefense = 9;
        $this->freethrust = 5;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 0;
        $this->pivotcost = 2; //shuttles have pivot cost higher
        $this->turncost = 0.33;
        
		$this->hangarRequired = 'shuttles'; //for fleet check - draws from the default-shuttle pool
		$this->iniativebonus = 11*5;                  
      
        $this->populate();
    }
    
    
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
			
			$armour = array(0, 0, 0, 0);
			$fighter = new Fighter("ArmedShuttleDrazi", $armour, 9, $this->id);
			$fighter->displayName = "Armed Shuttle";
			$fighter->imagePath = "img/ships/shuttleDrazi.png";
			$fighter->iconPath = "img/ships/shuttleDrazi_large.png";
			
			
			$fighter->addFrontSystem(new LightParticleBlaster(330, 30, 5, 1)); //1 gun D3+5 damage
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			
			$this->addSystem($fighter);
			
		}
		
		
    }
}
?>
