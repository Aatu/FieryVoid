<?php
class ShlassanDeltaV extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 192;
	$this->faction = "Small Races";
        $this->phpclass = "ShlassanDeltaV";
        $this->shipClass = "Sh'lassan Delta-V Light Fighters";
	$this->imagePath = "img/ships/shlassanDeltaV.png";
		$this->unofficial = true;
        
        $this->forwardDefense = 5;
        $this->sideDefense = 5;
        $this->freethrust = 9;
        $this->offensivebonus = 3;
        $this->jinkinglimit = 10;
        $this->turncost = 0.33;
        
		$this->isd = 2250;
        
	$this->iniativebonus = 100;
        $this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
		$armour = array(2, 0, 1, 1);
		$fighter = new Fighter("ShlassanDeltaV", $armour, 7, $this->id);
		$fighter->displayName = "Sh'lassan Delta-V";
		$fighter->imagePath = "img/ships/shlassanDeltaV.png";
		$fighter->iconPath = "img/ships/shlassanDeltaV_Large.png";
			
		$fighter->addFrontSystem(new PairedParticleGun(330, 30, 2));
			
		$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
		
		$this->addSystem($fighter);
	}
    }
}


?>
