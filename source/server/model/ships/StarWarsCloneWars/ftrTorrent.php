<?php
class ftrTorrent extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
    	$this->pointCost = 150;
        $this->faction = "Star Wars Clone Wars";
        $this->phpclass = "ftrTorrent";
        $this->shipClass = "Republic V-19 Torrent Starfighter";
    	$this->imagePath = "img/starwars/torrent.png";

//        $this->isd = 2230; 
       
        $this->forwardDefense = 7;
        $this->sideDefense = 5;
        $this->freethrust = 11;
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
            $armour = array(1, 1, 1, 1); //armor 2 _including shield_!
            $fighter = new Fighter("Torrent", $armour, 8, $this->id);
            $fighter->displayName = "Torrent";
            $fighter->imagePath = "img/starwars/torrent.png";
            $fighter->iconPath = "img/starwars/torrent_large.png";

            $fighter->addFrontSystem(new CWFighterTorpedoLauncher(3, 330, 30));
			$fighter->addFrontSystem(new CWLaserCannonsFtr(330, 30, 2));
            $fighter->addFrontSystem(new CWFighterTorpedoLauncher(3, 330, 30));

       	    //Shield Level 1
            $fighter->addAftSystem(new FtrShield(1, 0, 360));
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
            $this->addSystem($fighter);
        }
    }
}
?>
