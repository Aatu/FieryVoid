<?php
class DrakhRaiderSHF extends SuperHeavyFighter{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 150;
        $this->faction = "Drakh";
        $this->phpclass = "DrakhRaiderSHF";
        $this->shipClass = "Raider Assault Fighter";
        $this->imagePath = "img/ships/DrakhRaider.png";
	
	$this->isd = 2210;
        $this->unofficial = true;
        $this->gravitic = true;
	$this->advancedArmor = true;   

        $this->forwardDefense = 9;
        $this->sideDefense = 10;
        $this->freethrust = 10;
        $this->offensivebonus = 7;
        $this->jinkinglimit = 4;
        $this->turncost = 0.33;
        $this->iniativebonus = 70;
        
        $armour = array(4, 3, 4, 4);
        $fighter = new Fighter("DrakhRaider", $armour, 25, $this->id);
        $fighter->displayName = "Raider Assault Fighter";
        $fighter->imagePath = "img/ships/DrakhRaider.png";
        $fighter->iconPath = "img/ships/DrakhRaider_large.png";

        $fighter->addFrontSystem(new LightPhaseDisruptor(330, 30, 5));

	$CombPhaseDisruptor = new LightGravitonBeam(330, 30, 0);
	$CombPhaseDisruptor->displayName = "Combined Phase Disruptor";
 	$CombPhaseDisruptor->exclusive = true;
        $fighter->addFrontSystem($CombPhaseDisruptor);
            
        //Ray Shield, 1 points
        $fighter->addAftSystem(new SWRayShield(0, 1, 0, 1, 0, 360));
        
        $this->addSystem($fighter);
    }
    public function populate(){
        return;
    }
}
?>
