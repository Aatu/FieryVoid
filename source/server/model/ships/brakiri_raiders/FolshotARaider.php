<?php
class FolshotARaider extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 162;
		$this->faction = "Raiders";
	    $this->phpclass = "FolshotARaider";
	    $this->shipClass = "Folshot A Light Fighters";
		$this->imagePath = "img/ships/RaiderShokanFolshot.png";

		$this->isd = 2115;        
		$this->notes = 'Used only by Brakiri Shokan Privateers';
        
        $this->forwardDefense = 6;
        $this->sideDefense = 7;
        $this->freethrust = 8;
        $this->offensivebonus = 3;
        $this->jinkinglimit = 10;
        $this->turncost = 0.33;
        $this->iniativebonus = 100;
        
        $this->gravitic = true;
        
        $this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(1, 1, 1, 1);
            $fighter = new Fighter("folshotA", $armour, 7, $this->id);
            $fighter->displayName = "Folshot-A";
            $fighter->imagePath = "img/ships/RaiderShokanFolshot.png";
            $fighter->iconPath = "img/ships/RaiderShokanFolshotA_Large.png";


		$gun = new LightParticleBeam(330, 30, 1);
		$gun->displayName = "Ultralight Particle Beam";
		$fighter->addFrontSystem($gun);

			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			

            $this->addSystem($fighter);

        }
    }
}

?>
