<?php
class ArmedShuttleBrakiri extends FighterFlight{
    /*generic armed shuttles*/
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 26*6;
        $this->faction = "Brakiri Syndicracy";
        $this->phpclass = "ArmedShuttleBrakiri";
        $this->shipClass = "Armed Shuttles";
		$this->imagePath = "img/ships/shuttleBrakiri.png"; //more appropriate image needed
        $this->isd = 2200;
        
		Enhancements::nonstandardEnhancementSet($this, 'Shuttles');
        
		
        $this->forwardDefense = 8;
        $this->sideDefense = 8;
        $this->freethrust = 3;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 0;
        $this->pivotcost = 2; //shuttles have pivot cost higher
        $this->turncost = 0.33;
        
		$this->hangarRequired = 'shuttles'; //for fleet check - draws from the default-shuttle pool
		$this->iniativebonus = 9*5;
        $this->gravitic = true;                    
      
        $this->populate();
    }
    
    
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
			
			$armour = array(0, 0, 0, 0);
			$fighter = new Fighter("ArmedShuttleBrakiri", $armour, 9, $this->id);
			$fighter->displayName = "Armed Shuttle";
			$fighter->imagePath = "img/ships/shuttleBrakiri.png";
			$fighter->iconPath = "img/ships/shuttleBrakiri_large.png";
			
			
			$fighter->addFrontSystem(new LightGraviticBolt(330, 30, 0, 1)); //1 gun 7 damage
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			
			$this->addSystem($fighter);
			
		}
		
		
    }
}
?>
