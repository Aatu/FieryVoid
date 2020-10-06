<?php
class DefenderFtr extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 30*6;
        $this->faction = "Deneth";
        $this->phpclass = "defenderftr";
        $this->shipClass = "Defender Medium Fighters";
        $this->imagePath = "img/ships/DenethDefender.png";
        $this->isd = 2222;
        
        $this->forwardDefense = 6;
        $this->sideDefense = 7;
        $this->freethrust = 10;
        $this->offensivebonus = 3;
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
            $armour = array(2, 1, 2, 2);
            $fighter = new Fighter("defenderftr", $armour, 9, $this->id);
            $fighter->displayName = "Defender";
            $fighter->imagePath = "img/ships/DenethDefender.png";
            $fighter->iconPath = "img/ships/DenethDefenderLARGE.png";
            
            $frontGun = new PairedParticleGun(330, 30, 1);
            $fighter->addFrontSystem($frontGun);
            
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
            $this->addSystem($fighter);
       }
    }
}
?>
