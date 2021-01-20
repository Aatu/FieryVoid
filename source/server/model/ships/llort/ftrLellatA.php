<?php
class ftrLellatA extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 240;
	$this->faction = "Llort";
        $this->phpclass = "ftrLellatA";
        $this->shipClass = "Lellat-A Medium Fighters";
	$this->imagePath = "img/ships/LlortLellat.png";
        
        $this->isd = 2207;

        $this->forwardDefense = 7;
        $this->sideDefense = 7;
        $this->freethrust = 12;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 8;
        $this->turncost = 0.33;
        
	$this->iniativebonus = 90;
        
        $this->dropOutBonus = +1;
        $this->populate();
    }
	
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
		$armour = array(2, 1, 2, 2);
		$fighter = new Fighter("ftrLellatA", $armour, 10, $this->id);
		$fighter->displayName = "Lellat-A";
		$fighter->imagePath = "img/ships/LlortLellat.png";
		$fighter->iconPath = "img/ships/LlortLellat_Large.png";
			
		$fighter->addFrontSystem(new PairedParticleGun(330, 30, 2));
	
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
		$this->addSystem($fighter);
	}
    }
}

?>
