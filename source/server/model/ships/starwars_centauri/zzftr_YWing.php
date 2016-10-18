<?php
class swftrywingx extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 60*6;
	$this->faction = "(custom) Centauri Galactic Empire";
        $this->phpclass = "swftrywingx";
        $this->shipClass = "Y-Wing Demonstrator flight";
	$this->imagePath = "img/starwars/yWing.png";
        
        $this->forwardDefense = 7;
        $this->sideDefense = 9;
        $this->freethrust = 8;
        $this->offensivebonus = 6;
        $this->jinkinglimit = 6;
        $this->turncost = 0.33;
	    
	$this->hasNavigator = true;
        
	$this->iniativebonus = 17 *5; //incl. navigator bonus
        $this->populate();
    }
    
	
	
	
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
          $armour = array(2, 1, 1, 1);
          $fighter = new Fighter("swftrywingx", $armour, 15, $this->id);
          $fighter->displayName = "Y-Wing Technology Demonstrator";
          $fighter->imagePath = "img/starwars/yWing.png";
          $fighter->iconPath = "img/starwars/yWing_large.png";
			
          $fighter->addFrontSystem(new SWFighterLaser(330, 30, 2, 2));
          $fighter->addFrontSystem(new SWFighterIon(0, 360, 1, 2));
          //to be added: torpedo launchers, shields
			
	  $this->addSystem($fighter);
			
	}
    }
}
?>
