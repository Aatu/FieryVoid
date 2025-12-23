<?php
class ftrVulture extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
    	$this->pointCost = 16*6;
        $this->faction = "Star Wars Clone Wars";
        $this->phpclass = "ftrVulture";
        $this->shipClass = "Separatist Vulture Droid Fighter";
    	$this->imagePath = "img/starwars/CloneWars/vulture.png";

        $this->forwardDefense = 6;
        $this->sideDefense = 6;
        $this->freethrust = 12;
        $this->offensivebonus = 3;
        $this->jinkinglimit = 10; 
        $this->turncost = 0.33;
        $this->iniativebonus = 100;
        $this->populate();
    }
    
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(1, 1, 1, 1); 
            $fighter = new Fighter("Vulture", $armour, 5, $this->id);
            $fighter->displayName = "Vulture";
            $fighter->imagePath = "img/starwars/CloneWars/vulture.png";
            $fighter->iconPath = "img/starwars/CloneWars/vulture_large.png";

//            $fighter->addFrontSystem(new CWFtrConcussion(3, 330, 30));
            $fighter->addFrontSystem(new CWLaserCannonsFtr(330, 30, 2, 1));
//            $fighter->addFrontSystem(new CWFtrConcussion(3, 330, 30));

			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
            $this->addSystem($fighter);
        }
    }
}
?>
