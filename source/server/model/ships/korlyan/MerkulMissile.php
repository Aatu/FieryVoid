<?php
class MerkulMissile extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 30*6;
        $this->faction = "Custom Ships";
	        $this->variantOf = 'OBSOLETE'; //awaiting all games it's used in, then is to be removed from active ships list
        $this->phpclass = "MerkulMissile";
        $this->shipClass = "Merkul Missile Shuttles";
			$this->occurence = "uncommon";
//			$this->variantOf = 'Merkul Shuttles';
		$this->imagePath = "img/ships/korlyanArmedMerkul2.png"; 
		
		$this->notes = "Missile variant of the Merkul shuttle. Basic missiles only.";

	    $this->isd = 2194;

        $this->forwardDefense = 9;
        $this->sideDefense = 9;
        $this->freethrust = 5;
        $this->offensivebonus = 3;
        $this->jinkinglimit = 0;
        $this->pivotcost = 2; //shuttles have pivot cost higher
        $this->turncost = 0.33;
        
		$this->hangarRequired = 'assault shuttles'; //for fleet check
		$this->iniativebonus = 9*5;
      
        $this->populate();
    }
    
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
			
			$armour = array(1, 1, 1, 1);
			$fighter = new Fighter("MerkulMissile", $armour, 9, $this->id);
			$fighter->displayName = "Missile Merkul";
			$fighter->imagePath = "img/ships/korlyanArmedMerkul2.png";
			$fighter->iconPath = "img/ships/korlyanArmedMerkul_large2.png";
			
            $fighter->addFrontSystem(new FighterMissileRack(6, 330, 30));
//            $fighter->addFrontSystem(new FighterMissileRack(3, 330, 30));

			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			
			$this->addSystem($fighter);
			
		}
		
		
    }
}
?>
