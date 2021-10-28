<?php
class VulcanShuttle extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 25 *6; //for 6
        $this->faction = "ZTrek Playtest Federation";
        $this->phpclass = "VulcanShuttle";
        $this->shipClass = "Vulcan Shuttle flight";
        $this->imagePath = "img/ships/StarTrek/VulcanShuttle.png";
		$this->unofficial = true;
		
        $this->isd = 2151;

        $this->forwardDefense = 6;
        $this->sideDefense = 8;
        $this->freethrust = 8;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 4;
        $this->turncost = 0.33;
		
		
		$this->hangarRequired = "Shuttlecraft"; //I took category name from ST wikis
		$this->unitSize = 1; //counted as singles
        
       	$this->iniativebonus = 15 *5;
        $this->populate();        
    }
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){   
            $armour = array(2, 2, 2, 2);
            $fighter = new Fighter("VulcanShuttle", $armour, 11, $this->id);
            $fighter->displayName = "Shuttle";
			
            $fighter->imagePath = "img/ships/StarTrek/VulcanShuttle.png";
            $fighter->iconPath = "img/ships/StarTrek/VulcanShuttle_Large.png";
			
            $frontGun = new LightParticleBeam(300, 60, 2, 1);
            $frontGun->displayName = "Ultralight Phase Cannon";
            $fighter->addFrontSystem($frontGun);
			
		$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
            $this->addSystem($fighter);
        }
    }
}
?>
