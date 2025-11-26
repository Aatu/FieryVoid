<?php
class kirishiacWarrior extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 160*6;
		$this->faction = "Kirishiac Lords";
        $this->phpclass = "kirishiacWarrior";
        $this->shipClass = "Warrior Projectile";
		$this->imagePath = "img/ships/kirishiacWarrior2.png";
	    $this->isd = 'Ancient';
		$this->factionAge = 3; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
        
        $this->forwardDefense = 8;
        $this->sideDefense = 8;
        $this->freethrust = 14;
        $this->offensivebonus = 7;
        $this->jinkinglimit = 6;
        $this->turncost = 0.33;
		
        $this->gravitic = true;
		$this->advancedArmor = true;   
		$this->hardAdvancedArmor = true;   
        
		$this->iniativebonus = 90;
        $this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
			
			$armour = array(6, 6, 6, 6);
			$fighter = new Fighter("kirishiacWarrior", $armour, 18, $this->id);
			$fighter->displayName = "Warrior";
			$fighter->imagePath = "img/ships/kirishiacWarrior2.png";
			$fighter->iconPath = "img/ships/kirishiacWarrior_large2.png";
			
			
			//$fighter->addFrontSystem(new GlancingRam(0, 360, 1));
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			$this->addSystem($fighter);
			
		}
		
		
    }

}



?>
