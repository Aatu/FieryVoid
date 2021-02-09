<?php
class ChoukaTeuton extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 24*6;
        $this->faction = "ZEscalation Chouka Theocracy";
        $this->phpclass = "ChoukaTeuton";
        $this->shipClass = "Teuton Assault Shuttles";
        $this->imagePath = "img/ships/EscalationWars/ChoukaTeuton.png";
		$this->unofficial = true;
		
        $this->isd = 1944;
        
        $this->forwardDefense = 10;
        $this->sideDefense = 10;
        $this->freethrust = 6;
        $this->offensivebonus = 2;
        $this->jinkinglimit = 0;
        $this->turncost = 0.33;
		$this->turndelay = 0;
        
        $this->pivotcost = 2; //shuttles have pivot cost higher
		$this->hangarRequired = 'assault shuttles'; //for fleet check
		
        $this->iniativebonus = 45;
        $this->populate();       

    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){            
            $armour = array(2, 2, 2, 2);
            $fighter = new Fighter("ChoukaTeuton", $armour, 8, $this->id);
            $fighter->displayName = "Teuton";
            $fighter->imagePath = "img/ships/EscalationWars/ChoukaTeuton.png";
            $fighter->iconPath = "img/ships/EscalationWars/ChoukaTeuton_Large.png";

            $fighter->addFrontSystem(new EWLightLaserBeam(330, 30, 2, 1));
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            
            $this->addSystem($fighter);
        }
    }
}
?>
