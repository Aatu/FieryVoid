<?php
class lakritAS extends FighterFlight{
    /*Centauri Larisi Assault Shuttles*/
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 20*6;
        $this->faction = "Centauri Republic (WotCR)";
        $this->phpclass = "lakritAS";
        $this->shipClass = "Lakrit Assault Shuttles";
		$this->imagePath = "img/ships/phalan.png";
        $this->isd = 1971;
        
        $this->forwardDefense = 9;
        $this->sideDefense = 9;
        $this->freethrust = 6;
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
			
			$armour = array(1, 1, 1, 1);
			$fighter = new Fighter("Lakrit", $armour, 8, $this->id);
			$fighter->displayName = "Lakrit";
			$fighter->imagePath = "img/ships/phalan.png";
			$fighter->iconPath = "img/ships/phalan_large.png";
			
			
			$fighter->addFrontSystem(new PairedPlasmaBlaster(330, 30, 1)); //1 gun d3+2
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack								
			$this->addSystem($fighter);
			
		}
		
		
    }
}
?>
