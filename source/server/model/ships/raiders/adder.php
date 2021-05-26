<?php
class Adder extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 156;
	$this->faction = "Raiders";
      $this->phpclass = "Adder";
       $this->shipClass = "Adder Light Fighters";
	$this->imagePath = "img/ships/dragon.png"; //much earlier than Dragon, but still Drazi - closer to that silhouette than Delta-V

		$this->notes = "Generic raider unit.";
		$this->notes .= "<br> ";

		$this->isd = 1730;
        
        $this->forwardDefense = 8;
        $this->sideDefense = 8;
        $this->freethrust = 10;
        $this->offensivebonus = 2;
        $this->jinkinglimit = 10;
        $this->turncost = 0.33;
        
	$this->iniativebonus = 100; //Drazi design, but not Drazi pilots - no Drazi racial bonus
        $this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
		$armour = array(1, 0, 1, 1);
		$fighter = new Fighter("deltaV", $armour, 5, $this->id);
		$fighter->displayName = "Adder";
		$fighter->imagePath = "img/ships/dragon.png"; 
		$fighter->iconPath = "img/ships/dragon_large.png"; 
			
		$fighter->addFrontSystem(new PairedParticleGun(330, 30, 2));
	    	$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
		$this->addSystem($fighter);
	}
    }
}


?>
