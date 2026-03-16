<?php
class PolarenWyrithRefit extends HeavyCombatVesselLeftRight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 320;
        $this->faction = "Nexus Polaren Confederacy (early)";
        $this->phpclass = "PolarenWyrithRefit";
        $this->imagePath = "img/ships/Nexus/polarenGratherin.png";
        $this->shipClass = "Wyrith Tender (refit)";
			$this->variantOf = "Gratherin Destroyer";
			$this->occurence = "uncommon";
	    $this->isd = 2121;
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
        $this->addPrimarySystem(new NexusPolarenLCVController(5, 10, 5, 1));
        $this->addFrontSystem(new NexusSandCaster(1, 4, 2, 0, 360));
        $this->addAftSystem(new Thruster(3, 12, 0, 6, 1));
        $this->addAftSystem(new Thruster(3, 15, 0, 8, 2));

        $this->addLeftSystem(new DockingCollar(2, 10));
//        $this->addLeftSystem(new Maser(2, 6, 3, 240, 60));
        $this->addLeftSystem(new Thruster(4, 12, 0, 4, 3));
        $this->addLeftSystem(new Maser(2, 6, 3, 180, 360));

        $this->addRightSystem(new DockingCollar(2, 10));
//        $this->addRightSystem(new Maser(2, 6, 3, 300, 120));
        $this->addRightSystem(new Thruster(4, 12, 0, 4, 4));
        $this->addRightSystem(new Maser(2, 6, 3, 0, 180));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(3, 32));
        $this->addLeftSystem(new Structure(3, 36));
        $this->addRightSystem(new Structure(3, 36));
        
        $this->hitChart = array(
        		0=> array(
        				8 => "Structure",
						9 => "Polaren LCV Controller",
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
        				8 => "Docking Collar",
						10 => "Maser",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		4=> array(
        				5 => "Thruster",
        				8 => "Docking Collar",
						10 => "Maser",
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
    }
}
?>
