<?php
class Falkosi extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
    	$this->pointCost = 228;
    	$this->faction = "Brakiri";
        $this->phpclass = "Falkosi";
        $this->shipClass = "Falkosi Light Fighters";
    	$this->imagePath = "img/ships/falkosi.png";
        
        $this->forwardDefense = 7;
        $this->sideDefense = 8;
        $this->freethrust = 12;
        $this->offensivebonus = 5;
        $this->jinkinglimit = 10;
        $this->turncost = 0.33;
        $this->iniativebonus = 100;
        
        $this->gravitic = true;
        $this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(2, 1, 1, 1);
            $fighter = new Fighter("falkosi", $armour, 9, $this->id);
            $fighter->displayName = "Falkosi Light Fighter";
            $fighter->imagePath = "img/ships/falkosi.png";
            $fighter->iconPath = "img/ships/falkosi_large.png";


            $fighter->addFrontSystem(new LightGraviticBolt(330, 30, 0));


            $this->addSystem($fighter);

        }
    }
}

?>