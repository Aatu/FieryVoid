<?php
class TrekFederationType6Shuttles extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 20 *6; //for 6
        $this->faction = "ZStarTrek Federation (TOS)";
        $this->phpclass = "TrekFederationType6Shuttles";
        $this->shipClass = "Type 6 Shuttle Flight";
        $this->imagePath = "img/ships/StarTrek/Type6Shuttle.png";
		$this->unofficial = true;
		
        $this->isd = 2151;

        $this->forwardDefense = 7;
        $this->sideDefense = 8;
        $this->freethrust = 7;
        $this->offensivebonus = 3;
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
            $armour = array(1, 1, 1, 1);
            $fighter = new Fighter("TrekFederationType6Shuttles", $armour, 6, $this->id);
            $fighter->displayName = "Type 6 Shuttle";
			
            $fighter->imagePath = "img/ships/StarTrek/Type6Shuttle.png";
            $fighter->iconPath = "img/ships/StarTrek/Type6Shuttle_Large.png";
			
			/*
            $frontGun = new LightParticleBeam(330, 30, 1, 2);
            $frontGun->displayName = "Dual Phaser Beams";
			*/
			$frontGun = new TrekFtrPhaser(330, 30, 1, 2);
            $fighter->addFrontSystem($frontGun);
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
            $this->addSystem($fighter);
        }
    }
}
?>