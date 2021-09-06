<?php
class penitent extends SmallStarBaseFourSections{
		function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 2270;
		$this->base = true;
		$this->smallBase = true;
		$this->faction = "Orieni";
		$this->phpclass = "penitent";
		$this->shipClass = "Penitent Station";
		$this->imagePath = "img/ships/MarkabShadrakShrine.png";
		$this->canvasSize = 280; 
		$this->fighters = array("light"=>24, "medium"=>24); 
		$this->shipSizeClass = 3; 
		$this->Enormous = true; 
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;
		$this->forwardDefense = 22;
		$this->sideDefense = 22;
		$this->isd = 2007;
		
		$this->addFrontSystem(new Structure(5, 132));
		$this->addAftSystem(new Structure(5, 132));
		$this->addLeftSystem(new Structure(5, 132));
		$this->addRightSystem(new Structure(5, 132));
		$this->addPrimarySystem(new Structure(5, 150));
		
		$this->addPrimarySystem(new Reactor(5, 44, 0, 0));
        $this->addPrimarySystem(new CnC(5, 21, 0, 0));
        $this->addPrimarySystem(new CnC(5, 21, 0, 0));
		$this->addPrimarySystem(new Scanner(5, 28, 4, 8));
		$this->addPrimarySystem(new Scanner(5, 28, 4, 8));
		$this->addPrimarySystem(new Hangar(5, 14));
		$this->addPrimarySystem(new Hangar(5, 14));
        $this->addPrimarySystem(new HKControlNode(5, 30, 4, 4));
    	$this->addPrimarySystem(new HeavyLaserLance(5, 6, 4, 0, 360));
       	$this->addPrimarySystem(new HeavyLaserLance(5, 6, 4, 0, 360));
       	$this->addPrimarySystem(new HeavyLaserLance(5, 6, 4, 0, 360));
       	$this->addPrimarySystem(new HeavyLaserLance(5, 6, 4, 0, 360));

   		$this->addFrontSystem(new HeavyGausscannon(5, 10, 4, 270, 90));
   		$this->addFrontSystem(new HeavyGausscannon(5, 10, 4, 270, 90));
        $this->addFrontSystem(new SMissileRack(5, 6, 0, 270, 90, true));
        $this->addFrontSystem(new SMissileRack(5, 6, 0, 270, 90, true));
		$this->addFrontSystem(new RapidGatling(5, 4, 1, 270, 90));
		$this->addFrontSystem(new RapidGatling(5, 4, 1, 270, 90));
		$this->addFrontSystem(new RapidGatling(5, 4, 1, 270, 90));
		$this->addFrontSystem(new RapidGatling(5, 4, 1, 270, 90));
		$this->addFrontSystem(new RapidGatling(5, 4, 1, 270, 90));
		$this->addFrontSystem(new RapidGatling(5, 4, 1, 270, 90));
		$this->addFrontSystem(new Hangar(5, 6));
		$this->addFrontSystem(new CargoBay(5, 25));
		$this->addFrontSystem(new SubReactorUniversal(5, 30, 0, 0));

        $this->addAftSystem(new HeavyGausscannon(5, 10, 4, 90, 270));
        $this->addAftSystem(new HeavyGausscannon(5, 10, 4, 90, 270));
        $this->addAftSystem(new SMissileRack(5, 6, 0, 90, 270, true));
        $this->addAftSystem(new SMissileRack(5, 6, 0, 90, 270, true));
		$this->addAftSystem(new RapidGatling(5, 4, 1, 90, 270));
		$this->addAftSystem(new RapidGatling(5, 4, 1, 90, 270));
		$this->addAftSystem(new RapidGatling(5, 4, 1, 90, 270));
		$this->addAftSystem(new RapidGatling(5, 4, 1, 90, 270));
		$this->addAftSystem(new RapidGatling(5, 4, 1, 90, 270));
		$this->addAftSystem(new RapidGatling(5, 4, 1, 90, 270));
		$this->addAftSystem(new Hangar(5, 6));
		$this->addAftSystem(new CargoBay(5, 25));
		$this->addAftSystem(new SubReactorUniversal(5, 30, 0, 0));

        $this->addLeftSystem(new HeavyGausscannon(5, 10, 4, 180, 360));
        $this->addLeftSystem(new HeavyGausscannon(5, 10, 4, 180, 360));
        $this->addLeftSystem(new SMissileRack(5, 6, 0, 180, 360, true));
        $this->addLeftSystem(new SMissileRack(5, 6, 0, 180, 360, true));
		$this->addLeftSystem(new RapidGatling(5, 4, 1, 180, 360));
		$this->addLeftSystem(new RapidGatling(5, 4, 1, 180, 360));
		$this->addLeftSystem(new RapidGatling(5, 4, 1, 180, 360));
		$this->addLeftSystem(new RapidGatling(5, 4, 1, 180, 360));
		$this->addLeftSystem(new RapidGatling(5, 4, 1, 180, 360));
		$this->addLeftSystem(new RapidGatling(5, 4, 1, 180, 360));
		$this->addLeftSystem(new Hangar(5, 6));
		$this->addLeftSystem(new CargoBay(5, 25));
		$this->addLeftSystem(new SubReactorUniversal(5, 30, 0, 0));

        $this->addRightSystem(new HeavyGausscannon(5, 10, 4, 0, 180));
        $this->addRightSystem(new HeavyGausscannon(5, 10, 4, 0, 180));
        $this->addRightSystem(new SMissileRack(5, 6, 0, 0, 180, true));
        $this->addRightSystem(new SMissileRack(5, 6, 0, 0, 180, true));
		$this->addRightSystem(new RapidGatling(5, 4, 1, 0, 180));
		$this->addRightSystem(new RapidGatling(5, 4, 1, 0, 180));
		$this->addRightSystem(new RapidGatling(5, 4, 1, 0, 180));
		$this->addRightSystem(new RapidGatling(5, 4, 1, 0, 180));
		$this->addRightSystem(new RapidGatling(5, 4, 1, 0, 180));
		$this->addRightSystem(new RapidGatling(5, 4, 1, 0, 180));
		$this->addRightSystem(new Hangar(5, 6));
		$this->addRightSystem(new CargoBay(5, 25));
		$this->addRightSystem(new SubReactorUniversal(5, 30, 0, 0));

		$this->hitChart = array(			
			0=> array(
				9 => "Structure",
				11 => "Heavy Laser Lance",
				13 => "HK Control Node",
				16 => "Scanner",
				17 => "Hangar",
				18 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				2 => "Class-S Missile Rack",
				4 => "Heavy Gauss Cannon",
				7 => "Rapid Gatling Railgun",
				9 => "Cargo Bay",
				10 => "Sub Reactor",
				11 => "Hangar",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				2 => "Class-S Missile Rack",
				4 => "Heavy Gauss Cannon",
				7 => "Rapid Gatling Railgun",
				9 => "Cargo Bay",
				10 => "Sub Reactor",
				11 => "Hangar",
				18 => "Structure",
				20 => "Primary",
			),	
			3=> array(
				2 => "Class-S Missile Rack",
				4 => "Heavy Gauss Cannon",
				7 => "Rapid Gatling Railgun",
				9 => "Cargo Bay",
				10 => "Sub Reactor",
				11 => "Hangar",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				2 => "Class-S Missile Rack",
				4 => "Heavy Gauss Cannon",
				7 => "Rapid Gatling Railgun",
				9 => "Cargo Bay",
				10 => "Sub Reactor",
				11 => "Hangar",
				18 => "Structure",
				20 => "Primary",
			),
		);
		
    }
}
?>