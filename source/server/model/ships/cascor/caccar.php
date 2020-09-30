<?php
class Caccar extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 33 *6;
        $this->faction = "Cascor";
        $this->phpclass = "Caccar";
        $this->shipClass = "Caccar Ultralight Fighters";
        $this->imagePath = "img/ships/CascorCaccar.png";
        $this->isd = 2209;
	    //$this->notes = 'Non-atmospheric.';
        
        $this->forwardDefense = 7;
        $this->sideDefense = 5;
        $this->freethrust = 18;
        $this->offensivebonus = 6;
        $this->jinkinglimit = 99; //ultralight fighters no jinking limit technically
        $this->accelcost = 2;
        $this->turncost = 0.33;
        
		//$this->unitSize = 2;//Unlike larger fighters, ultralights are small enough to be packed into hangars at twice the normal rates.
		///...but this is general rule, not Caccar-specific. Fleet checker itself will cover it.

    	$this->iniativebonus = 20 *5;//ultralight fighter base should be 22, but apparently Caccar designer decided otherwise (or forgot ;) )
        $this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(1, 0, 0, 0);
            $fighter = new Fighter("Caccar", $armour, 5, $this->id);
            $fighter->displayName = "Caccar";
            $fighter->imagePath = "img/ships/CascorCaccar.png";
            $fighter->iconPath = "img/ships/CascorCaccar_Large.png";

            $frontGun = new Ionizer(330, 30, 2);
            $frontGun->displayName = "Ionizer";
            $fighter->addFrontSystem($frontGun);
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
            $this->addSystem($fighter);
       }
    }
}
?>
