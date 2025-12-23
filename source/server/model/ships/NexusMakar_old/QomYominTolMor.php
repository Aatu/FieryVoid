<?php
class QomYominTolMor extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 20*6;
        $this->faction = "Nexus Makar Federation (early)";
        $this->phpclass = "QomYominTolMor";
        $this->shipClass = "Tol Mor Armed Drone";
        $this->imagePath = "img/ships/Nexus/makar_tolmor2.png";
		$this->unofficial = true;

        $this->isd = 1925;
        
        $this->forwardDefense = 6;
        $this->sideDefense = 6;
        $this->freethrust = 10;
        $this->offensivebonus = 3;
        $this->jinkinglimit = 4;
        $this->turncost = 0.33;
		$this->turndelay = 0;

        $this->iniativebonus = 90;

        $this->dropOutBonus = -2;
        $this->populate();       

        HkControlNode::addHKFlight($this);
        $this->enhancementOptionsEnabled[] = 'EXT_AMMO'; //To enable extra Ammo for main gun.

    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){            
            $armour = array(1, 1, 0, 0);
            $fighter = new Fighter("QomYominTolMor", $armour, 5, $this->id);
            $fighter->displayName = "Tol Mor";
            $fighter->imagePath = "img/ships/Nexus/makar_tolmor2.png";
            $fighter->iconPath = "img/ships/Nexus/makar_tolmor_large2.png";

	        $light = new NexusLightDefenseGun(300, 60, 1); //$startArc, $endArc, $nrOfShots
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
