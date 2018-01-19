<?php
class Sitara extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 270;
		$this->faction = "Centauri";
        $this->phpclass = "Sitara";
        $this->shipClass = "Sitara Strike Fighters";
	    $this->occurence = "uncommon";
	    
	    $this->isd = 2257;
        $this->variantOf = "Sentri Interceptors";
		$this->imagePath = "img/ships/sentri.png";
        
        $this->forwardDefense = 7;
        $this->sideDefense = 5;
        $this->freethrust = 10;
        $this->offensivebonus = 7;
        $this->jinkinglimit = 8;
        $this->turncost = 0.33;
        
		$this->iniativebonus = 90;
        
        $this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
			$armour = array(3, 2, 3, 3);
			$fighter = new Fighter("sitara", $armour, 10, $this->id);
			$fighter->displayName = "Sitara";
			$fighter->imagePath = "img/ships/sitara.png";
			$fighter->iconPath = "img/ships/sitara_large.png";


			$fighter->addFrontSystem(new IonBolt(330, 30));


			$this->addSystem($fighter);

		}


    }

}
