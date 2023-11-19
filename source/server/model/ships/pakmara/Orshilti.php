<?php
class Orshilti extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 30*6;
        $this->faction = "Pak'Ma'Ra Confederacy";
        $this->phpclass = "Orshilti";
        $this->shipClass = "Or'shil'ti Assault Shuttles";
        $this->imagePath = "img/ships/PakmaraOrshilti.png";
		
        $this->isd = 2195;
        
        $this->forwardDefense = 10;
        $this->sideDefense = 10;
        $this->freethrust = 8;
        $this->offensivebonus = 0;
        $this->jinkinglimit = 0;
        $this->turncost = 0.33;
		$this->turndelay = 0;
        
        $this->hangarRequired = 'assault shuttles'; //for fleet check
        $this->iniativebonus = 9*5;
        $this->populate();       

    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){            
            $armour = array(4, 4, 4, 4);
            $fighter = new Fighter("Porfatis", $armour, 18, $this->id);
            $fighter->displayName = "Or'shil'ti";
            $fighter->imagePath = "img/ships/PakmaraOrshilti.png";
            $fighter->iconPath = "img/ships/PakmaraOrshilti_Large.png";

			$gun = new RogolonLtPlasmaGun(300, 60, 5, 1);
			$gun->displayName = "Light Plasma Gun";
			$fighter->addFrontSystem($gun);
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            
            $this->addSystem($fighter);
        }
    }
}
?>
