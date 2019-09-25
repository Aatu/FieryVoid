<?php
class wlcChlonasSaTrevanFlight extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 34 *6;
        $this->phpclass = "wlcChlonasSaTrevanFlight";
        $this->shipClass = "Sa'Trevan Light flight";
	$this->imagePath = "img/ships/shasi.png";
	    
        $this->faction = "Ch'Lonas";
	$this->unofficial = true;
	    $this>isd = 2235;
        
        $this->forwardDefense = 6;
        $this->sideDefense = 7;
        $this->freethrust = 12;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 10;
        $this->turncost = 0.33;
        $this->iniativebonus = 100;
        
        $this->populate();
    }
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
            	$armour = array(2, 1, 1, 1);
            	$fighter = new Fighter("wlcChlonasSaTrevanFlight", $armour, 7, $this->id);
            	$fighter->displayName = "Sa'Trevan";
		$fighter->imagePath = "img/ships/shasi.png";
	        $fighter->iconPath = "img/ships/shasi_large.png";
		
		$fighter->addFrontSystem(new PairedParticleGun(330, 30, 3));
            	$this->addSystem($fighter);
        }
    }
}
?>
