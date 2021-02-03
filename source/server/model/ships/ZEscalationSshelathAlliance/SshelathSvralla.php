<?php
class SshelathSvralla extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 15*6;
        $this->faction = "ZEscalation Sshel'ath Alliance";
        $this->phpclass = "SshelathSvralla";
        $this->shipClass = "Svralla Assault Shuttles";
        $this->imagePath = "img/ships/EscalationWars/SshelathSvralla.png";
		$this->unofficial = true;
		
        $this->isd = 1930;
        
        $this->forwardDefense = 8;
        $this->sideDefense = 11;
        $this->freethrust = 8;
        $this->offensivebonus = 0;
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
            $fighter = new Fighter("SshelathSvralla", $armour, 8, $this->id);
            $fighter->displayName = "Svralla";
            $fighter->imagePath = "img/ships/EscalationWars/SshelathSvralla.png";
            $fighter->iconPath = "img/ships/EscalationWars/SshelathSvralla_Large.png";

			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            
            $this->addSystem($fighter);
        }
    }
}
?>
