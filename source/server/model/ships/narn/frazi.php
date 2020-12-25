<?php
class Frazi extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 348;
		$this->faction = "Narn";
        $this->phpclass = "Frazi";
        $this->shipClass = "Frazi Heavy Fighters";
		$this->imagePath = "img/ships/frazi.png";
        
        $this->forwardDefense = 6;
        $this->sideDefense = 8;
        $this->freethrust = 10;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 6;
        $this->turncost = 0.33;
        
		$this->iniativebonus = 80;
        $this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
			
			$armour = array(2, 2, 3, 3);
			$frazi = new Fighter("frazi", $armour, 12, $this->id);
			$frazi->displayName = "Frazi";
			$frazi->imagePath = "img/ships/frazi.png";
			$frazi->iconPath = "img/ships/frazi_large.png";
			
			
			$frazi->addFrontSystem(new PairedParticleGun(330, 30, 5));
			
			
			$frazi->addAftSystem(new RammingAttack(0, 0, 360, $frazi->getRammingFactor(), 0)); //ramming attack
			
			$this->addSystem($frazi);
			
		}
		
		
    }

}



?>
