<?php
class breachingpodgaim extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 40*6;
        $this->faction = "Gaim Intelligence";
        $this->phpclass = "breachingpodgaim";
        $this->shipClass = "Ech'Akki Breaching Pod";
        $this->imagePath = "img/ships/GaimIttaka.png";
		
        $this->isd = 2251;
        
        $this->forwardDefense = 8;
        $this->sideDefense = 8;
        $this->freethrust = 9;
        $this->offensivebonus = 0;
        $this->jinkinglimit = 0;
        $this->turncost = 0.33;
		$this->turndelay = 0;
        
        $this->pivotcost = 2; //shuttles have pivot cost higher
		$this->hangarRequired = 'assault shuttles'; //for fleet check
		
        $this->iniativebonus = 9 * 5;
        $this->populate();       

    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){            
            $armour = array(2, 2, 2, 2);
            $fighter = new Fighter("breachingpodgaim", $armour, 15, $this->id);
            $fighter->displayName = "Ech'Akki";
            $fighter->imagePath = "img/ships/GaimIttaka.png";
            $fighter->iconPath = "img/ships/GaimIttaka_large.png";

			$fighter->addFrontSystem(new Marines(330, 30, 0, true)); //startarc, endarc, damagebonus, elite.
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            
            $this->addSystem($fighter);
        }
    }
}
?>
