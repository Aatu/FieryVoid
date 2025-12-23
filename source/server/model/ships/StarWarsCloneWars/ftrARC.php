<?php
class ftrARC extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 75*6;
        $this->faction = "Star Wars Clone Wars";
        $this->phpclass = "ftrARC";
        $this->shipClass = "Republic ARC-170 Recon Fighter";
        $this->imagePath = "img/starwars/CloneWars/ARC170.png";

		$this->limited = 33;
	    
//	    $this->occurence = 'special';
//	    $this->notes = 'Special deployment: 1 in 4 fighers.';//let's try this way...

        $this->forwardDefense = 8;
        $this->sideDefense = 7;
        $this->freethrust = 9;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 6;
        $this->turncost = 0.33;

        $this->iniativebonus = 80;
        $this->populate();

		$this->enhancementOptionsEnabled[] = 'NAVIGATOR'; //this flight can have Navigator enhancement option	

    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(2, 2, 2, 2);
            $fighter = new Fighter("ARC-170", $armour, 15, $this->id);
            $fighter->displayName = "ARC-170";
            $fighter->imagePath = "img/starwars/CloneWars/ARC170.png";
            $fighter->iconPath = "img/starwars/CloneWars/ARC170_large.png";

            $frontGun = new CWLaserCannonsFtr(330, 30, 5);
            $frontGun->displayName = "Medium Lasers";
            $aftGun = new CWLaserCannonsFtr(150, 210, 2, 1);
            $aftGun->displayName = "Blaster";

            $fighter->addFrontSystem(new CWFighterProtonLauncher(3, 330, 30));
            $fighter->addFrontSystem($frontGun);
            $fighter->addFrontSystem(new CWFighterProtonLauncher(3, 330, 30));
            
       	    //Shield Level 1
            $fighter->addAftSystem(new FtrShield(1, 0, 360));

            $fighter->addAftSystem($aftGun);
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack	
            
            $this->addSystem($fighter);
	   }
    }
}

?>
