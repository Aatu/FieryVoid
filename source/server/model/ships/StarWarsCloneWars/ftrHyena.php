<?php
class ftrHyena extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
    	$this->pointCost = 40*6;
        $this->faction = "Star Wars Clone Wars";
        $this->phpclass = "ftrHyena";
        $this->shipClass = "Separatist Hyena Droid Bomber";
    	$this->imagePath = "img/starwars/CloneWars/hyena.png";

        $this->forwardDefense = 7;
        $this->sideDefense = 8;
        $this->freethrust = 10;
        $this->offensivebonus = 3;
        $this->jinkinglimit = 8; 
        $this->turncost = 0.33;
        $this->iniativebonus = 90;
        $this->populate();
    }
    
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(2, 1, 1, 1); 
            $fighter = new Fighter("Hyena", $armour, 9, $this->id);
            $fighter->displayName = "Hyena";
            $fighter->imagePath = "img/starwars/CloneWars/hyena.png";
            $fighter->iconPath = "img/starwars/CloneWars/hyena_large.png";

            $fighter->addFrontSystem(new CWFighterProtonLauncher(3, 330, 30));
            $fighter->addFrontSystem(new CWLaserCannonsFtr(330, 30, 2));
            $fighter->addFrontSystem(new CWFighterProtonLauncher(3, 330, 30));

       	    //Shield Level 1
            $fighter->addAftSystem(new FtrShield(1, 0, 360));

			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
            $this->addSystem($fighter);
        }
    }
}
?>
