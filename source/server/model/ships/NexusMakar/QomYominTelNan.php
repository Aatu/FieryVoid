<?php
class QomYominTelNan extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 33*6;
        $this->faction = "Nexus Makar Federation";
        $this->phpclass = "QomYominTelNan";
        $this->shipClass = "Tel Nan Armed Drone";
        $this->imagePath = "img/ships/Nexus/makar_tolmor2.png";
		$this->unofficial = true;

        $this->isd = 2108;
        
        $this->forwardDefense = 7;
        $this->sideDefense = 7;
        $this->freethrust = 12;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 5;
        $this->turncost = 0.33;
		$this->turndelay = 0;
        
        $this->iniativebonus = 90;

        $this->dropOutBonus = -2;
        $this->populate();       

        HkControlNode::addHKFlight($this);

    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){            
            $armour = array(1, 1, 0, 0);
            $fighter = new Fighter("QomYominTelNan", $armour, 7, $this->id);
            $fighter->displayName = "Tel Nan";
            $fighter->imagePath = "img/ships/Nexus/makar_tolmor2.png";
            $fighter->iconPath = "img/ships/Nexus/makar_tolmor_large2.png";

	        $light = new NexusSmallXrayLaser(300, 60, 3); //$startArc, $endArc, $nrOfShots
	        $fighter->addFrontSystem($light);
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            
            $this->addSystem($fighter);
        }
    }


    public function getInitiativebonus($gamedata){
        $iniBonus = parent::getInitiativebonus($gamedata);
	$iniBonus += HkControlNode::getIniMod($this->userid,$gamedata);
        return $iniBonus;
    }	


}
?>
