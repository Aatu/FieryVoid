<?php
class TrekMazeriteShuttles extends FighterFlight{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 33 *6; //for 6
        $this->faction = "ZTrek Playtest Other Factions";
        $this->phpclass = "TrekMazeriteShuttles";
        $this->imagePath = "img/ships/StarTrek/MazeriteShuttle.png";
        $this->shipClass = "Mazerite Shuttles";
        $this->customFtrName = "Mazerite small craft"; //requires hangar space on Vulcan ships

	$this->unofficial = true;
		
        $this->isd = 2142;

        $this->forwardDefense = 5;
        $this->sideDefense = 8;
        $this->freethrust = 8;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 4;
        $this->turncost = 0.33;
		
		
	$this->hangarRequired = "Shuttlecraft"; //I took category name from ST wikis
	$this->unitSize = 1; //counted as singles
        
       	$this->iniativebonus = 16 *5;
        $this->populate();        
    }
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){   
            $armour = array(2, 1, 2, 2);
            $fighter = new Fighter("TrekMazeriteShuttles", $armour, 10, $this->id);
            $fighter->displayName = "Mazerite Shuttle";
			
            $fighter->imagePath = "img/ships/StarTrek/MazeriteShuttle.png";
            $fighter->iconPath = "img/ships/StarTrek/MazeriteShuttle_Large.png";
			
            $frontGun = new LightParticleBeam(330, 30, 1, 3);
            $frontGun->displayName = "Triple Beams";
            $fighter->addFrontSystem($frontGun);
			
		$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
            $this->addSystem($fighter);
        }
    }
}
?>