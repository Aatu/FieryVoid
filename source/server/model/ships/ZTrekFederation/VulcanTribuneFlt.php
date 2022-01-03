<?php
class VulcanTribuneFlt extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 90 *6; //for 6
        $this->faction = "ZTrek Playtest Federation";
        $this->phpclass = "VulcanTribuneFlt";
        $this->shipClass = "Vulcan Tribune flight";
        $this->imagePath = "img/ships/StarTrek/VulcanTribune.png";
		$this->unofficial = true;
		
        $this->isd = 2151;
		$this->notes = "Warp Engine";

        $this->forwardDefense = 9;
        $this->sideDefense = 10;
        $this->freethrust = 9;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 2;
        $this->turncost = 0.33;
		
        $this->pivotcost = 2;

	//$this->unitSize = 3; //number of craft in squadron
    	$this->superheavy = true;
        $this->maxFlightSize = 3;//this is a superheavy fighter originally intended as single unit, limit flight size to 3
        
       	$this->iniativebonus = 14 *5; //default SHF ini
        $this->populate();
    }
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){   
            $armour = array(3, 2, 3, 3);
            $fighter = new Fighter("VulcanTribuneFlt", $armour, 25, $this->id);
            $fighter->displayName = "Tribune Courier";
			
            $fighter->imagePath = "img/ships/StarTrek/VulcanTribune.png";
            $fighter->iconPath = "img/ships/StarTrek/VulcanTribuneLarge.png";
			
            $frontGun = new LightParticleBeam(180, 360, 2, 1);
            $frontGun->displayName = "Ultralight Phase Cannon";
            $fighter->addFrontSystem($frontGun);

            $frontGun = new TrekFtrPhaseCannon(330, 30, 2, 1);
            //$frontGun->displayName = "Light Phase Cannon"; //no need to rename
            $fighter->addFrontSystem($frontGun);

            $frontGun = new LightParticleBeam(0, 180, 2, 1);
            $frontGun->displayName = "Ultralight Phase Cannon";
            $fighter->addFrontSystem($frontGun);

			$fighter->addAftSystem(new TrekShieldFtr(1, 6, 3, 1) ); //armor, health, rating, recharge
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
		
            $this->addSystem($fighter);
        }
    }
}
?>