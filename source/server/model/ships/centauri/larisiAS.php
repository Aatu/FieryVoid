<?php
class larisiAS extends FighterFlight{
    /*Centauri Larisi Assault Shuttles*/
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		    $this->pointCost = 30*6;
		    $this->faction = "Centauri";
        $this->phpclass = "larisias";
        $this->shipClass = "Larisi Assault Shuttles";
		    $this->imagePath = "img/ships/sentri.png";
        
        $this->forwardDefense = 8;
        $this->sideDefense = 8;
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
			
			$armour = array(1, 1, 1, 1);
			$fighter = new Fighter("larisias", $armour, 9, $this->id);
			$fighter->displayName = "Larisi Assault Shuttle";
			$fighter->imagePath = "img/ships/sentri.png";
			$fighter->iconPath = "img/ships/sentri_large.png";
			
			
			$fighter->addFrontSystem(new PairedParticleGun(330, 30, 2, 1)); //1 gun d6+2
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
						
			
			$this->addSystem($fighter);
			
		}
		
		
    }
}
?>
