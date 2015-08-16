<?php
class Auk extends HeavyCombatVesselLeftRight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
    	$this->pointCost = 500;
        $this->faction = "Drazi (WotCR)";
        $this->phpclass = "Auk";
        $this->imagePath = "img/ships/shrike.png";
        $this->shipClass = "Auk Gunship";
        
        $this->forwardDefense = 12;
        $this->sideDefense = 12;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 3;
        $this->iniativebonus = 40;

        $this->addPrimarySystem(new Reactor(5, 12, 0, 0));
        $this->addPrimarySystem(new CnC(5, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 3, 6));
        $this->addPrimarySystem(new Engine(4, 12, 0, 7, 3));
        $this->addPrimarySystem(new Hangar(3, 1));
        $this->addPrimarySystem(new ParticleCannon(3, 8, 7, 300, 60));
        $this->addPrimarySystem(new Thruster(3, 10, 0, 4, 1));
        $this->addPrimarySystem(new Thruster(4, 16, 0, 7, 2));

        $this->addLeftSystem(new ParticleCannon(3, 8, 7, 300, 0));
        $this->addLeftSystem(new LightParticleCannon(2, 6, 5, 240, 0));
        $this->addLeftSystem(new Thruster(4, 11, 0, 3, 3));

        $this->addRightSystem(new ParticleCannon(3, 8, 7, 0, 60));
        $this->addRightSystem(new LightParticleCannon(2, 6, 5, 0, 120));
        $this->addRightSystem(new Thruster(4, 11, 0, 3, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(4, 24));
        $this->addLeftSystem(new Structure(4, 36));
        $this->addRightSystem(new Structure(4, 36));
    }
}
?>