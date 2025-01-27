<?php
class VelraxSivrinRefit2 extends HeavyCombatVesselLeftRight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 425;
	$this->faction = "ZNexus Velrax Republic";
        $this->phpclass = "VelraxSivrinRefit2";
        $this->imagePath = "img/ships/Nexus/velraxSivrin.png";
        $this->shipClass = "Sivrin Gunship (2106)";
		$this->isd = 2106;
        $this->canvasSize = 125;

		$this->unofficial = true;

        $this->forwardDefense = 14;
        $this->sideDefense = 14;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 30;

        $this->addPrimarySystem(new Reactor(4, 20, 0, 0));
        $this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 7, 7));
        $this->addPrimarySystem(new Engine(4, 13, 0, 8, 4));
        $this->addPrimarySystem(new Hangar(1, 2));
		$this->addFrontSystem(new MediumPlasma(3, 5, 3, 300, 60));
        $this->addAftSystem(new Thruster(3, 15, 0, 4, 1));
        $this->addAftSystem(new Thruster(4, 20, 0, 8, 2));

        $this->addLeftSystem(new LaserLance(3, 6, 4, 240, 360));
        $this->addLeftSystem(new LaserLance(3, 6, 4, 240, 360));
        $this->addLeftSystem(new HeavyPlasma(3, 8, 5, 180, 360));
        $this->addLeftSystem(new DualIonBolter(2, 4, 4, 180, 360));
        $this->addLeftSystem(new Thruster(3, 12, 0, 4, 3));

        $this->addRightSystem(new LaserLance(3, 6, 4, 0, 120));
        $this->addRightSystem(new LaserLance(3, 6, 4, 0, 120));
        $this->addRightSystem(new HeavyPlasma(3, 8, 5, 0, 180));
        $this->addRightSystem(new DualIonBolter(2, 4, 4, 0, 180));
        $this->addRightSystem(new Thruster(3, 12, 0, 4, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(4, 50));
        $this->addLeftSystem(new Structure(4, 50));
        $this->addRightSystem(new Structure(4, 50));
    
            $this->hitChart = array(
        		0=> array(
        				6 => "Structure",
						7 => "1:Medium Plasma Cannon",
        				12 => "2:Thruster",
						13 => "Hangar",
        				15 => "Scanner",
        				17 => "Engine",
        				19 => "Reactor",
        				20 => "C&C",
        		),
        		3=> array(
        				4 => "Thruster",
						5 => "1: Medium Plasma Cannon",
        				7 => "Dual Ion Bolter",
						10 => "Laser Lance",
						12 => "Heavy Plasma Cannon",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		4=> array(
        				4 => "Thruster",
						5 => "1: Medium Plasma Cannon",
        				7 => "Dual Ion Bolter",
						10 => "Laser Lance",
						12 => "Heavy Plasma Cannon",
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
    
    }
}
?>
