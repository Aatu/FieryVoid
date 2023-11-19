<?php
class wlcChlonasVesTek extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 70*6;
        $this->phpclass = "wlcChlonasVesTek";
        $this->shipClass = "Ves'Tek Assault flight";
        $this->imagePath = "img/ships/ChlonasVesTek.png";
        
        $this->faction = "Ch'Lonas Cooperative";
	    $this->unofficial = true;
	    $this->isd = 2247;

        $this->forwardDefense = 8;
        $this->sideDefense = 10;
        $this->freethrust = 10;
        $this->offensivebonus = 5;
        $this->jinkinglimit = 6;
        $this->turncost = 0.33;
        $this->iniativebonus = 85;
        $this->limited = 33;

        
        $this->populate();
    }

    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(3, 3, 3, 3);
            $fighter = new Fighter("wlcChlonasVesTek", $armour, 13, $this->id);
            $fighter->displayName = "Ves'Tek";
            $fighter->imagePath = "img/ships/ChlonasVesTek.png";
            $fighter->iconPath = "img/ships/ChlonasVesTekLARGE.png";
            $fighter->addFrontSystem(new PairedParticleGun(330, 30, 3));
            $fighter->addFrontSystem(new CustomLightMatterCannonF(345, 15));
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
            $this->addSystem($fighter);
        }
    }
}
?>
