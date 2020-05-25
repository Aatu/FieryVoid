<?php
class Adder extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 156;
	$this->faction = "Raiders";
      $this->phpclass = "Adder";
       $this->shipClass = "Adder Light Fighters";
	$this->imagePath = "img/ships/deltaV.png"; //Need to change this
        
        $this->forwardDefense = 8;
        $this->sideDefense = 8;
        $this->freethrust = 10;
        $this->offensivebonus = 2;
        $this->jinkinglimit = 10;
        $this->turncost = 0.33;
        
	$this->iniativebonus = 100;
        $this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
		$armour = array(1, 0, 1, 1);
		$fighter = new Fighter("deltaV", $armour, 5, $this->id);
		$fighter->displayName = "Adder Light Fighter";
		$fighter->imagePath = "img/ships/deltaV.png"; //Need to Change this
		$fighter->iconPath = "img/ships/deltaV_large.png"; //Need to Change this
			
		$fighter->addFrontSystem(new PairedParticleGun(330, 30, 2));
			
		$this->addSystem($fighter);
	}
    }
}


?>
