<?php
class QomYominTevShurRefit extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 15*6;
        $this->faction = "Nexus Makar Federation";
        $this->phpclass = "QomYominTevShurRefit";
        $this->shipClass = "Tev Shur Armed Minesweeper (2115)";
        $this->imagePath = "img/ships/Nexus/makar_tolmor2.png";
		$this->unofficial = true;
        $this->customFtrName = "TevShurRefit";

        $this->notes = 'Needs updated hangars to handle.';
        $this->notes .= 'Minesweeper.';        

        $this->isd = 2115;
        
        $this->forwardDefense = 11;
        $this->sideDefense = 11;
        $this->freethrust = 4;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 4;
        $this->turncost = 0.33;
		$this->turndelay = 0;

        $this->iniativebonus = 45;

		$this->minesweeper = true;
		
        $this->dropOutBonus = -2;
        $this->populate();       

        HkControlNode::addHKFlight($this);

    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){            
            $armour = array(1, 1, 1, 1);
            $fighter = new Fighter("QomYominTevShurRefit", $armour, 11, $this->id);
            $fighter->displayName = "Tev Shur";
            $fighter->imagePath = "img/ships/Nexus/makar_tolmor2.png";
            $fighter->iconPath = "img/ships/Nexus/makar_tolmor_large2.png";

	        $light = new NexusSmallXrayLaser(300, 60, 3, 1); //$startArc, $endArc, $nrOfShots
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
