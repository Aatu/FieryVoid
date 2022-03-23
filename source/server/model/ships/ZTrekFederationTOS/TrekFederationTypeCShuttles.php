<?php
class TrekFederationTypeCShuttles extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 30 *6; //for 6
        $this->faction = "ZTrek Playtest Federation (TOS)";
        $this->phpclass = "TrekFederationTypeCShuttles";
        $this->shipClass = "Type C Shuttle Flight";
        $this->imagePath = "img/ships/StarTrek/TypeCShuttle.png";
		$this->unofficial = true;
		
        $this->isd = 2151;

        $this->forwardDefense = 7;
        $this->sideDefense = 9;
        $this->freethrust = 9;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 4;
        $this->turncost = 0.33;
		
		
		$this->hangarRequired = "Shuttlecraft"; //I took category name from ST wikis
        $this->customFtrName = "Human small craft"; //requires hangar space on Vulcan ships
		$this->unitSize = 1; //counted as singles
        
       	$this->iniativebonus = 14 *5;
        $this->populate();        
    }
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){   
            $armour = array(2, 2, 2, 1);
            $fighter = new Fighter("TrekFederationTypeCShuttles", $armour, 10, $this->id);
            $fighter->displayName = "Type C Shuttle";
			
            $fighter->imagePath = "img/ships/StarTrek/TypeCShuttle.png";
            $fighter->iconPath = "img/ships/StarTrek/TypeCShuttle_Large.png";
			
            $frontGun = new LightParticleBeam(330, 30, 3, 2);
            $frontGun->displayName = "Dual Phaser Beams";
            $fighter->addFrontSystem($frontGun);
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
            $this->addSystem($fighter);
        }
    }
}
?>