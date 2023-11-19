<?php
class Hades extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 30 * 6;
        $this->faction = "EA";
        $this->phpclass = "Hades";
        $this->shipClass = "Hades Assault Shuttles";
        $this->imagePath = "img/ships/Hades.png";
        $this->isd = 2238;
        
//	    $this->notes = 'Non-atmospheric.';
        
        $this->forwardDefense = 8;
        $this->sideDefense = 8;
        $this->freethrust = 6;
        $this->offensivebonus = 3;
        $this->jinkinglimit = 0;
        $this->pivotcost = 2; //shuttles have pivot cost higher        
        $this->turncost = 0.33;
        
    	$this->iniativebonus = 9 * 5;
        $this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(2, 2, 2, 2);
            $fighter = new Fighter("Hades", $armour, 10, $this->id);
            $fighter->displayName = "Hades";
            $fighter->imagePath = "img/ships/Hades.png";
            $fighter->iconPath = "img/ships/Hades_Large.png";

            $frontGun = new PairedParticleGun(330, 30, 4, 1); //1 gun d6+4
            $frontGun->displayName = "Uni-Pulse Cannon";
            $fighter->addFrontSystem($frontGun);
		$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            $this->addSystem($fighter);
       }
    }
}
?>
