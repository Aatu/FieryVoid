<?php
class TobrakiAS extends FighterFlight{
	/*Brakiri Tobraki Assault Shuttles, Ships of the Fleet*/
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
    	$this->pointCost = 30*6;
    	$this->faction = "Brakiri";
        $this->phpclass = "tobrakias";
        $this->shipClass = "Tobraki Assault Shuttles";
    	$this->imagePath = "img/ships/falkosi.png";
        
		$this->notes = 'Pri-Wakat Concepts & Solutions';//Corporation producing the design
		$this->isd = 2198;
		
        $this->forwardDefense = 7;
        $this->sideDefense = 10;
        $this->freethrust = 7;
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
            $fighter = new Fighter("tobraki", $armour, 10, $this->id);
            $fighter->displayName = "Tobraki";
            $fighter->imagePath = "img/ships/falkosi.png";
            $fighter->iconPath = "img/ships/falkosi_large.png";
            $fighter->addFrontSystem(new LightGraviticBolt(330, 30, 0, 1));
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
            $this->addSystem($fighter);
        }
    }
}
?>
