<?php
class PolarenGratherin extends HeavyCombatVesselLeftRight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 450;
        $this->faction = "Nexus Polaren Confederacy (early)";
        $this->phpclass = "PolarenGratherin";
        $this->imagePath = "img/ships/Nexus/polarenGratherin.png";
        $this->shipClass = "Gratherin Destroyer";
	    $this->isd = 1770;
        $this->canvasSize = 125;

        $this->fighters = array("assault shuttles"=>1); //1 breaching pod    

        $this->forwardDefense = 15;
        $this->sideDefense = 15;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 30;

        $this->addPrimarySystem(new Reactor(4, 16, 0, 0));
        $this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 4, 5));
        $this->addPrimarySystem(new Engine(4, 12, 0, 8, 3));
        $this->addPrimarySystem(new Hangar(2, 3));
        $this->addFrontSystem(new StunBeam(2, 6, 5, 300, 60));
        $this->addFrontSystem(new NexusSandCaster(1, 4, 2, 0, 360));
        $this->addAftSystem(new Thruster(3, 12, 0, 6, 1));
        $this->addAftSystem(new Thruster(3, 15, 0, 8, 2));

        $this->addLeftSystem(new NexusLightRadCannon(3, 6, 4, 240, 360));
        $this->addLeftSystem(new Maser(2, 6, 3, 240, 60));
        $this->addLeftSystem(new Thruster(4, 12, 0, 4, 3));
        $this->addLeftSystem(new NexusLightMaser(2, 4, 2, 120, 300));

        $this->addRightSystem(new NexusLightRadCannon(3, 6, 4, 0, 120));
        $this->addRightSystem(new Maser(2, 6, 3, 300, 120));
        $this->addRightSystem(new Thruster(4, 12, 0, 4, 4));
        $this->addRightSystem(new NexusLightMaser(2, 4, 2, 60, 240));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(3, 32));
        $this->addLeftSystem(new Structure(3, 36));
        $this->addRightSystem(new Structure(3, 36));
        
        $this->hitChart = array(
        		0=> array(
        				8 => "Structure",
						9 => "1:Stun Beam",
        				11 => "2:Thruster",
						12 => "1: Sand Caster",
        				14 => "Scanner",
        				16 => "Engine",
        				17 => "Hangar",
        				19 => "Reactor",
        				20 => "C&C",
        		),
        		3=> array(
        				5 => "Thruster",
        				7 => "Light Rad Cannon",
        				9 => "Maser",
						10 => "Light Maser",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		4=> array(
        				5 => "Thruster",
        				7 => "Light Rad Cannon",
        				9 => "Maser",
						10 => "Light Maser",
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
    }
}
?>
