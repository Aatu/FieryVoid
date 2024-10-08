<?php
class Tiqincc extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 270;
        $this->faction = "Cascor Commonwealth";
        $this->phpclass = "Tiqincc";
        $this->shipClass = "Tiqincc Medium Fighters";
        $this->imagePath = "img/ships/CascorTiqincc.png";
        $this->isd = 2210;
	    //$this->notes = 'Non-atmospheric.';
        
        $this->forwardDefense = 8;
        $this->sideDefense = 7;
        $this->freethrust = 14;
        $this->offensivebonus = 5;
        $this->jinkinglimit = 8;
        $this->accelcost = 2;
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
            $fighter = new Fighter("Tiqincc", $armour, 9, $this->id);
            $fighter->displayName = "Tiqincc";
            $fighter->imagePath = "img/ships/CascorTiqincc.png";
            $fighter->iconPath = "img/ships/CascorTiqincc_Large.png";

            $frontGun = new Ionizer(330, 30, 2);            
            $fighter->addFrontSystem($frontGun);
			
            $rearGun = new Ionizer(150, 210, 1);
            $fighter->addAftSystem($rearGun);
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			
			
            $this->addSystem($fighter);
       }
    }
}
?>
