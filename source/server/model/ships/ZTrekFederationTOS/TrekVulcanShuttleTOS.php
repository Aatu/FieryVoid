<?php
class TrekVulcanShuttleTOS extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 30 *6; //for 6
        $this->faction = "ZTrek Playtest Federation (TOS)";
        $this->phpclass = "TrekVulcanShuttleTOS";
        $this->shipClass = "Vulcan Shuttle flight";
        $this->imagePath = "img/ships/StarTrek/VulcanShuttle.png";
		$this->unofficial = true;
		
        $this->isd = 2181;

        $this->forwardDefense = 6;
        $this->sideDefense = 8;
        $this->freethrust = 9;
        $this->offensivebonus = 5;
        $this->jinkinglimit = 4;
        $this->turncost = 0.33;
		
		
		$this->hangarRequired = "Shuttlecraft"; //I took category name from ST wikis
        $this->customFtrName = "Vulcan small craft"; //requires hangar space on Vulcan ships
		$this->unitSize = 1; //counted as singles
        
       	$this->iniativebonus = 15 *5;
        $this->populate();        
    }
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){   
            $armour = array(3, 2, 2, 2);
            $fighter = new Fighter("TrekVulcanShuttleTOS", $armour, 11, $this->id);
            $fighter->displayName = "Shuttle";
			
            $fighter->imagePath = "img/ships/StarTrek/VulcanShuttle.png";
            $fighter->iconPath = "img/ships/StarTrek/VulcanShuttle_Large.png";
			
			$frontGun = new TrekFtrPhaser(300, 60, 2, 2,"Point Defense Phaser");
            $fighter->addFrontSystem($frontGun);
			
			
		$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
            $this->addSystem($fighter);
        }
    }
}
?>