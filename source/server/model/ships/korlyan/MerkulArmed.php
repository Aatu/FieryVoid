<?php
class MerkulArmed extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 25*6;
		$this->faction = "Kor-Lyan";
        $this->phpclass = "MerkulArmed";
        $this->shipClass = "Merkul Armed Shuttles";
			$this->occurence = "common";
			$this->variantOf = 'Merkul Shuttles';
		$this->imagePath = "img/ships/korlyanArmedMerkul.png"; 

		$this->canvasSize = 40;
		
		$this->notes = "Armed variant of the Merkul shuttle. Basic missiles only.";

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
			$fighter = new Fighter("MerkulArmed", $armour, 9, $this->id);
			$fighter->displayName = "Armed Merkul";
			$fighter->imagePath = "img/ships/korlyanArmedMerkul.png";
			$fighter->iconPath = "img/ships/korlyanArmedMerkul_large.png";
			
			$fighter->addFrontSystem(new LightParticleBeam(330, 30, 3, 1));
			$fighter->addFrontSystem(new FighterMissileRack(2, 330, 30));

			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			
			$this->addSystem($fighter);
			
		}
		
		
    }
}
?>
