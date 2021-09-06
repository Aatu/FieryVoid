<?php
class KastanCudgelAS extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
    	$this->pointCost = 28*6;
    	$this->faction = "ZEscalation Kastan Monarchy";
        $this->phpclass = "KastanCudgelAS";
        $this->shipClass = "Cudgel Assault Shuttles";
    	$this->imagePath = "img/ships/EscalationWars/KastanKatana.png";
        
		$this->isd = 1908;
		
        $this->forwardDefense = 10;
        $this->sideDefense = 10;
        $this->freethrust = 7;
        $this->offensivebonus = 2;
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
            $armour = array(3, 3, 3, 3);
            $fighter = new Fighter("KastanCudgelAS", $armour, 8, $this->id);
            $fighter->displayName = "Cudgel";
            $fighter->imagePath = "img/ships/EscalationWars/KastanKatana.png";
            $fighter->iconPath = "img/ships/EscalationWars/KastanKatana_large.png";
			
			$gun = new EWLaserBoltFtr(330, 30, 2, 1);
			$gun->displayName = "Light Laser Bolt";
			$fighter->addFrontSystem($gun);
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
            $this->addSystem($fighter);
        }
    }
}
?>
