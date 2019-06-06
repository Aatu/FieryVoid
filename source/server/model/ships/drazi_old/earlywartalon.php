<?php
class EarlyWartalon extends HeavyCombatVesselLeftRight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
    	$this->pointCost = 450;
        $this->faction = "Drazi (WotCR)";
        $this->phpclass = "EarlyWartalon";
        $this->imagePath = "img/ships/drazi/warbird.png";
        $this->shipClass = "Wartalon Escort Carrier";
        
        $this->fighters = array("light" => 6);
        
        $this->occurence = "uncommon";
        $this->variantOf = "Warbird Cruiser";
        $this->isd = 2003;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 12;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 40;

        $this->addPrimarySystem(new Reactor(5, 12, 0, 4));
        $this->addPrimarySystem(new CnC(5, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 13, 4, 7));
        $this->addPrimarySystem(new Engine(5, 11, 0, 7, 2));
        $this->addPrimarySystem(new Hangar(4, 7));
        $this->addPrimarySystem(new StdParticleBeam(3, 4, 1, 240, 60));
        $this->addPrimarySystem(new StdParticleBeam(3, 4, 1, 300, 120));
        $this->addPrimarySystem(new Thruster(4, 13, 0, 4, 1));
        $this->addPrimarySystem(new Thruster(5, 19, 0, 7, 2));

        $this->addLeftSystem(new ParticleRepeater(4, 6, 4, 240, 0));
        $this->addLeftSystem(new StdParticleBeam(3, 4, 1, 240, 60));
        $this->addLeftSystem(new Thruster(4, 13, 0, 4, 3));

        $this->addRightSystem(new ParticleRepeater(4, 6, 4, 0, 120));
        $this->addRightSystem(new StdParticleBeam(3, 4, 1, 300, 120));
        $this->addRightSystem(new Thruster(4, 13, 0, 4, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(5, 32));
        $this->addLeftSystem(new Structure(4, 40));
        $this->addRightSystem(new Structure(4, 40));
    }
}
?>
