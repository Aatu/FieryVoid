<?php
class ftrLAAT extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
    	$this->pointCost = 26*6;
        $this->faction = "Star Wars Clone Wars";
        $this->phpclass = "ftrLAAT";
        $this->shipClass = "Republic LAAT Gunship";
    	$this->imagePath = "img/starwars/CloneWars/laat.png";

//        $this->isd = 2230; 
       
        $this->forwardDefense = 8;
        $this->sideDefense = 8;
        $this->freethrust = 6;
        $this->offensivebonus = 3;
        $this->jinkinglimit = 0;
        $this->turncost = 0.33;

		$this->hangarRequired = 'assault shuttles'; //for fleet check
        $this->iniativebonus = 45;

        $this->populate();
    }
    
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(1, 1, 1, 1); //armor 2 _including shield_!
            $fighter = new Fighter("LAAT", $armour, 9, $this->id);
            $fighter->displayName = "LAAT";
            $fighter->imagePath = "img/starwars/CloneWars/laat.png";
            $fighter->iconPath = "img/starwars/CloneWars/laat_large.png";

//            $fighter->addFrontSystem(new CWFtrConcussion(3, 330, 30));
            $fighter->addFrontSystem(new CWLaserCannonsFtr(330, 30, 2, 1));
//            $fighter->addFrontSystem(new CWFtrConcussion(3, 330, 30));

       	    //Shield Level 1
            $fighter->addAftSystem(new FtrShield(1, 0, 360));
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
            $this->addSystem($fighter);
        }
    }
}
?>
