<?php
class FolshotB extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 186;
	$this->faction = "Brakiri";
        $this->phpclass = "FolshotB";
        $this->shipClass = "Folshot B Light Fighters";
	$this->imagePath = "img/ships/falkosi.png";
        
        $this->forwardDefense = 6;
        $this->sideDefense = 7;
        $this->freethrust = 10;
        $this->offensivebonus = 4;
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
            $armour = array(1, 1, 1, 1);
            $fighter = new Fighter("folshotB", $armour, 7, $this->id);
            $fighter->displayName = "Folshot-B";
            $fighter->imagePath = "img/ships/falkosi.png";
            $fighter->iconPath = "img/ships/falkosi_large.png";


            $fighter->addFrontSystem(new UltraLightGraviticBolt(330, 30, 0));


            $this->addSystem($fighter);

        }
    }
}

?>
