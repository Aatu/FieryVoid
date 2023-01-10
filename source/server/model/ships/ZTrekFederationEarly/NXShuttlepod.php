<?php
class NXShuttlepod extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 20 *6; //for 6
        $this->faction = "ZStarTrek Federation (early)";
        $this->phpclass = "NXShuttlepod";
        $this->shipClass = "Shuttlepod Flight";
        $this->imagePath = "img/ships/StarTrek/NXShuttlepod.png";
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
            $fighter = new Fighter("NXShuttlepod", $armour, 6, $this->id);
            $fighter->displayName = "Shuttlepod";
			
            $fighter->imagePath = "img/ships/StarTrek/NXShuttlepod.png";
            $fighter->iconPath = "img/ships/StarTrek/NXShuttlepod_Large.png";
			
            /*$frontGun = new LightParticleBeam(330, 30, 1, 2);
            $frontGun->displayName = "Dual Beams";*/
			$frontGun = new TrekFtrPhaser(330, 30, 1, 2,"Phase Cannon");
            $fighter->addFrontSystem($frontGun);
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
            $this->addSystem($fighter);
        }
    }
}
?>