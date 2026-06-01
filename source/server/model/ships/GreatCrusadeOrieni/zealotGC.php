<?php
class zealotGC extends BaseShipNoAft{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 625;
		$this->faction = "Great Crusade Orieni Imperium";
        $this->phpclass = "zealotGC";
        $this->imagePath = "img/ships/GCzealot.png";
		$this->canvasSize = 165; //img has 200px per side
        $this->shipClass = "Zealot Cruiser";
		$this->unofficial = true;
		$this->isd = 2050;
         
        $this->forwardDefense = 15;
        $this->sideDefense = 14;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 4;
        $this->pivotcost = 3;
		$this->iniativebonus = 10;
         
        $this->addPrimarySystem(new Reactor(5, 17, 0, 0));
        $this->addPrimarySystem(new CnC(5, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 15, 5, 8));
        $this->addPrimarySystem(new Engine(5, 15, 0, 12, 3));
		$this->addPrimarySystem(new Hangar(2, 2, 2));
		$this->addPrimarySystem(new JumpEngine(5, 10, 5, 36));
		$this->addPrimarySystem(new Thruster(5, 21, 0, 12, 2));

        $this->addFrontSystem(new Thruster(3, 13, 0, 4, 1));
        $this->addFrontSystem(new Thruster(3, 13, 0, 4, 1));
		$this->addFrontSystem(new GaussCannon(3, 10, 4, 300, 60));
		$this->addFrontSystem(new GaussCannon(3, 10, 4, 300, 60));
		$this->addFrontSystem(new GaussCannon(3, 10, 4, 300, 60));
        $this->addFrontSystem(new ImpRapidGatling(2, 4, 2, 240, 360));
        $this->addFrontSystem(new ImpRapidGatling(2, 4, 2, 0, 120));
        
		$this->addLeftSystem(new Thruster(3, 15, 0, 6, 3));
        $this->addLeftSystem(new LightLaser(2, 4, 3, 120, 300));
        $this->addLeftSystem(new LightLaser(2, 4, 3, 240, 360));
        $this->addLeftSystem(new LightLaser(2, 4, 3, 240, 360));
        $this->addLeftSystem(new ImpRapidGatling(2, 4, 2, 180, 300));
        $this->addLeftSystem(new ImpRapidGatling(2, 4, 2, 300, 60));
		
		$this->addRightSystem(new Thruster(3, 15, 0, 6, 4));
        $this->addRightSystem(new LightLaser(2, 4, 3, 60, 240));
        $this->addRightSystem(new LightLaser(2, 4, 3, 0, 120));
        $this->addRightSystem(new LightLaser(2, 4, 3, 0, 120));
        $this->addRightSystem(new ImpRapidGatling(2, 4, 2, 60, 180));
        $this->addRightSystem(new ImpRapidGatling(2, 4, 2, 300, 60));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 40));
        $this->addLeftSystem(new Structure( 4, 40));
        $this->addRightSystem(new Structure( 4, 40));
        $this->addPrimarySystem(new Structure( 5, 40));
		
        $this->hitChart = array(
            0=> array(
                    8 => "Structure",
                    9 => "Jump Engine",
                    12 => "Thruster",
					14 => "Scanner",
                    16 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    4 => "Thruster",
					7 => "Gauss Cannon",
					9 => "Improved Gatling Railgun",
					18 => "Structure",
                    20 => "Primary",
            ),
            3=> array(
                    4 => "Thruster",
					8 => "Light Laser",
					10 => "Improved Gatling Railgun",
                    18 => "Structure",
                    20 => "Primary",
			),
            4=> array(
                    4 => "Thruster",
					8 => "Light Laser",
					10 => "Improved Gatling Railgun",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );

		
    }
}
?>