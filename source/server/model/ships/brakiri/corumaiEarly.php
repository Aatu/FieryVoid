<?php
class CorumaiEarly extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 1000;
	$this->faction = "Brakiri";
        $this->phpclass = "CorumaiEarly";
        $this->imagePath = "img/ships/corumai.png";
        $this->shipClass = "Corumai Dreadnought (early)";
			$this->occurence = "common";
			$this->variantOf = 'Corumai Dreadnought';
        $this->shipSizeClass = 3;
        $this->limited = 10;
        
		$this->notes = 'Ak-Habil Conglomerate';//Corporation producing the design
		$this->isd = 2230;
		
        $this->forwardDefense = 18;
        $this->sideDefense = 20;
        
        $this->turncost = 1.25;
        $this->turndelaycost = 1.25;
        $this->accelcost = 5;
        $this->rollcost = 3;
        $this->pivotcost = 5;
        $this->iniativebonus = -10;
        
        $this->gravitic = true;

        $this->addPrimarySystem(new Reactor(5, 27, 0, -16));
        $this->addPrimarySystem(new CnC(6, 20, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 16, 8, 8));
        $this->addPrimarySystem(new Engine(5, 21, 0, 15, 5));
        $this->addPrimarySystem(new JumpEngine(5, 12, 4, 28));
		$this->addPrimarySystem(new Hangar(5, 4));
   
        $this->addFrontSystem(new GraviticBolt(3, 5, 2, 240, 60));
        $this->addFrontSystem(new GravitonBeam(5, 8, 8, 300, 60));
        $this->addFrontSystem(new GraviticBolt(3, 5, 2, 270, 90));
        $this->addFrontSystem(new GravitonBeam(5, 8, 8, 300, 60));
        $this->addFrontSystem(new GraviticBolt(3, 5, 2, 300, 120));
        $this->addFrontSystem(new GraviticThruster(5, 15, 0, 4, 1));
        $this->addFrontSystem(new GraviticThruster(5, 15, 0, 4, 1));

        $this->addAftSystem(new GravitonBeam(5, 8, 8, 120, 240));
        $this->addAftSystem(new GraviticThruster(5, 10, 0, 4, 2));
        $this->addAftSystem(new GraviticThruster(5, 10, 0, 4, 2));
        $this->addAftSystem(new GraviticThruster(5, 10, 0, 4, 2));
        $this->addAftSystem(new GraviticThruster(5, 10, 0, 4, 2));

        $this->addLeftSystem(new GraviticBolt(3, 5, 2, 180, 0));
        $this->addLeftSystem(new GravitonBeam(5, 8, 8, 300, 0));
        $this->addLeftSystem(new GravitonBeam(5, 8, 8, 300, 0));
        $this->addLeftSystem(new GraviticThruster(5, 20, 0, 6, 3));
        $this->addLeftSystem(new GravitonBeam(5, 8, 8, 180, 240));

        $this->addRightSystem(new GraviticBolt(3, 5, 2, 0, 180));
        $this->addRightSystem(new GravitonBeam(5, 8, 8, 0, 60));
        $this->addRightSystem(new GravitonBeam(5, 8, 8, 0, 60));
        $this->addRightSystem(new GraviticThruster(5, 20, 0, 6, 4));
        $this->addRightSystem(new GravitonBeam(5, 8, 8, 120, 180));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(5, 54));
        $this->addAftSystem(new Structure(5, 54));
        $this->addLeftSystem(new Structure(5, 68));
        $this->addRightSystem(new Structure(5, 68));
        $this->addPrimarySystem(new Structure(5, 65));
		
		$this->hitChart = array(
			0=> array(
					9 => "Structure",
					11 => "Jump Engine",
					13 => "Scanner",
					15 => "Engine",
					16 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					5 => "Thruster",
					8 => "Gravitic Bolt",
					10 => "Graviton Beam",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					7 => "Thruster",
					9 => "Graviton Beam",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					4 => "Thruster",
					7 => "Graviton Beam",
					9 => "Gravitic Bolt",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					4 => "Thruster",
					7 => "Graviton Beam",
					9 => "Gravitic Bolt",
					18 => "Structure",
					20 => "Primary",
			),
		);
				
    }
}
