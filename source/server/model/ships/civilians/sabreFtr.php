<?php
class ValkyrieFtr extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 240;
        $this->faction = "EA";
        $this->phpclass = "ValkyrieFtr";
        $this->shipClass = "EA Valkyrie Aerospace Medium flight";
        $this->imagePath = "img/ships/BAStarFox.png";
	    $this->isd = 2237;
 		$this->unofficial = true;
	    
        $this->forwardDefense = 7;
        $this->sideDefense = 7;
        $this->freethrust = 9;
        $this->offensivebonus = 4;
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
            $armour = array(2, 1, 1, 1);
            $fighter = new Fighter("ValkyrieFtr", $armour, 9, $this->id);
            $fighter->displayName = "Valkyrie";
            $fighter->imagePath = "img/ships/BAStarFox.png";
            $fighter->iconPath = "img/ships/BAStarFox_large.png";

            $frontGun = new PairedParticleGun(330, 30, 4);
            $frontGun->displayName = "Uni-Pulse Cannon";

            $fighter->addFrontSystem($frontGun);
            $fighter->addFrontSystem(new FighterMissileRack(2, 330, 30));

			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack	
            $this->addSystem($fighter);
	}
    }
}
?>
