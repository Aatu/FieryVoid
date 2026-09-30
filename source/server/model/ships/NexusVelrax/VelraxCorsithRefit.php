<?php
class VelraxCorsithRefit extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 24 * 6;
        $this->faction = "Nexus Velrax Republic";
        $this->phpclass = "VelraxCorsithRefit";
        $this->shipClass = "Corsith Assault Shuttles (2114)";
        $this->imagePath = "img/ships/Nexus/velraxAS.png";
        $this->isd = 2114;
        
        $this->forwardDefense = 7;
        $this->sideDefense = 10;
        $this->freethrust = 8;
        $this->offensivebonus = 2;
        $this->jinkinglimit = 0;
        $this->pivotcost = 2; //shuttles have pivot cost higher        
        $this->turncost = 0.33;
        
		$this->hangarRequired = 'assault shuttles'; //for fleet check
    	$this->iniativebonus = 11 * 5;
        $this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(1, 1, 1, 1);
            $fighter = new Fighter("VelraxCorsith", $armour, 10, $this->id);
            $fighter->displayName = "Corsith";
            $fighter->imagePath = "img/ships/Nexus/velraxAS.png";
            $fighter->iconPath = "img/ships/Nexus/velraxAS_large.png";

	        $light = new NexusLightIonBolter(330, 30, 0, 1); //$startArc, $endArc, $nrOfShots
	        $fighter->addFrontSystem($light);
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            
			$this->addSystem($fighter);

       }
    }
}
?>
