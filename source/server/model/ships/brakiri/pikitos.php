<?php
class Pikitos extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 468;
	$this->faction = "Brakiri";
        $this->phpclass = "Pikitos";
        $this->shipClass = "Pikitos Heavy Fighters";
	$this->imagePath = "img/ships/pikitos.png";
        
        $this->forwardDefense = 9;
        $this->sideDefense = 10;
        $this->freethrust = 10;
        $this->offensivebonus = 5;
        $this->jinkinglimit = 6;
        $this->turncost = 0.33;
        $this->iniativebonus = 80;
        
        $this->gravitic = true;
        
        $this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(3, 3, 4, 4);
            $fighter = new Fighter("pikitos", $armour, 15, $this->id);
            $fighter->displayName = "Pikitos Heavy Fighter";
            $fighter->imagePath = "img/ships/pikitos.png";
            $fighter->iconPath = "img/ships/pikitos_large.png";


            $fighter->addFrontSystem(new LightGraviticBolt(330, 30, 0));
            $fighter->addFrontSystem(new LightGravitonBeam(330, 30, 0));


            $this->addSystem($fighter);

        }
    }
}

?>