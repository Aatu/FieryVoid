<?php
class TakalaAS extends FighterFlight{
	/*Brakiri Takala Assault Shuttles, Kabrik Police Ship*/
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
    	$this->pointCost = 50*6;
    	$this->faction = "Brakiri";
        $this->phpclass = "TakalaAS";
        $this->shipClass = "Takala Battle Assault Shuttles";
    	$this->imagePath = "img/ships/takala.png";
        
		$this->notes = 'Pri-Wakat Concepts & Solutions';//Corporation producing the design
		$this->isd = 2241;
		
        $this->forwardDefense = 8;
        $this->sideDefense = 9;
        $this->freethrust = 9;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 0;
		$this->pivotcost = 2; //shuttles have pivot cost higher
        $this->turncost = 0.33;
		
		$this->hangarRequired = 'assault shuttles'; //for fleet check
        $this->iniativebonus = 9*5;
        
        $this->gravitic = true;
        $this->populate();
    }

    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(1, 1, 1, 1);
            $fighter = new Fighter("Takala", $armour, 9, $this->id);
            $fighter->displayName = "Takala";
            $fighter->imagePath = "img/ships/takala.png";
            $fighter->iconPath = "img/ships/takala_large.png";
            $fighter->addFrontSystem(new LightGraviticBolt(330, 30, 0, 2));
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
            $this->addSystem($fighter);
        }
    }
}
?>
