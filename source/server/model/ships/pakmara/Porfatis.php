<?php
class Porfatis extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 50*6;
        $this->faction = "Pak'ma'ra Confederacy";
        $this->phpclass = "Porfatis"; 
        $this->shipClass = "Por'fa'tis Medium flight";
        $this->imagePath = "img/ships/PakmaraPorfatis.png";
		
        $this->isd = 2195;
        
        $this->forwardDefense = 9;
        $this->sideDefense = 7;
        $this->freethrust = 15;
        $this->offensivebonus = 7;
        $this->jinkinglimit = 8;
        $this->turncost = 0.5;
		$this->turndelaycost = 0.33;
		$this->hangarRequired = "medium"; //their Ini classifies them as heavies!
        
        $this->iniativebonus = 17*5;
        $this->populate();       

    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){            
            $armour = array(2, 2, 2, 2);
            $fighter = new Fighter("Porfatis", $armour, 12, $this->id);
            $fighter->displayName = "Por'fa'tis";
            $fighter->imagePath = "img/ships/PakmaraPorfatis.png";
            $fighter->iconPath = "img/ships/PakmaraPorfatis_Large.png";

			$gun = new RogolonLtPlasmaGun(300, 60, 5);
			$gun->displayName = "Light Plasma Gun";
			$fighter->addFrontSystem($gun);
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            
            $this->addSystem($fighter);
        }
    }
}
?>
