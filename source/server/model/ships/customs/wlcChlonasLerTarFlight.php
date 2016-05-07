<?php
class wlcChlonasLerTarFlight extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	      $this->pointCost = 150; //25*6
      	$this->faction = "Custom Ships";
        $this->phpclass = "wlcChlonasLerTarFlight";
        $this->shipClass = "Ch'Lonas Ler'Tar Light Fighters";
	      $this->imagePath = "img/ships/shasi.png";
        
        $this->forwardDefense = 5;
        $this->sideDefense = 7;
        $this->freethrust = 9;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 10;
        $this->turncost = 0.33;
        $this->iniativebonus = 105;


        
        $this->populate();
    }

    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(1, 1, 1, 1);
            $fighter = new Fighter("wlcChlonasLerTarFlight", $armour, 7, $this->id);
            $fighter->displayName = "Ler'Tar Light Fighter";
	        	$fighter->imagePath = "img/ships/shasi.png";
		        $fighter->iconPath = "img/ships/shasi_large.png";
			
	        	$fighter->addFrontSystem(new PairedParticleGun(330, 30, 2));

            $this->addSystem($fighter);
        }
    }
}
?>
