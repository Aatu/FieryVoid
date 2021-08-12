<?php
class ColonialAlliance extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
         $this->pointCost = 800;
        $this->faction = "ZPlaytest BSG Colonials";
        $this->phpclass = "ColonialAlliance";
        $this->imagePath = "img/ships/BSG/ColonialAlliance.png";
        $this->shipClass = "Alliance Scout Cruiser";
        $this->fighters = array("superheavy" => 4);
 //       $this->isd = 2160;
        $this->canvasSize = 130;

		$this->notes = "Primary users: Colonial Fleet";
        
        $this->forwardDefense = 13;
        $this->sideDefense = 14;

        $this->turncost = 0.50;
        $this->turndelaycost = 1;
        $this->accelcost = 2;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        
        $this->iniativebonus = 35;

        $this->addPrimarySystem(new Reactor(5, 15, 0, 0));
        $this->addPrimarySystem(new CnC(5, 10, 0, 0));
        $this->addPrimarySystem(new ElintScanner(4, 12, 6, 9));
        $this->addPrimarySystem(new Engine(5, 12, 0, 10, 3));
        $this->addPrimarySystem(new Hangar(4, 8));
        $this->addPrimarySystem(new Thruster(4, 13, 0, 5, 3));
        $this->addPrimarySystem(new Thruster(4, 13, 0, 5, 4));
        $this->addPrimarySystem(new BSGFlakBattery(4, 6, 2, 0, 360));
        $hyperdrive = new JumpEngine(4, 12, 6, 20);
			$hyperdrive->displayName = 'Hyperdrive';
			$this->addPrimarySystem($hyperdrive);
        
        $this->addFrontSystem(new Thruster(5, 8, 0, 4, 1));
        $this->addFrontSystem(new Thruster(5, 8, 0, 4, 1));
		$this->addFrontSystem(new BSGMedBattery(5, 7, 4, 330, 30)); 
		$this->addFrontSystem(new Bulkhead(0, 5));
		$this->addFrontSystem(new Bulkhead(0, 5));

        $this->addAftSystem(new Thruster(5, 12, 0, 5, 2));
        $this->addAftSystem(new Thruster(5, 12, 0, 5, 2));
        $this->addAftSystem(new Bulkhead(0, 5));
		$this->addAftSystem(new RapidGatling(4, 4, 1, 180, 360)); 
		$this->addAftSystem(new RapidGatling(4, 4, 1, 180, 360)); 
		$this->addAftSystem(new RapidGatling(4, 4, 1, 0, 180)); 
		$this->addAftSystem(new RapidGatling(4, 4, 1, 0, 180)); 

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(5, 40));
        $this->addAftSystem(new Structure(5, 40));
        $this->addPrimarySystem(new Structure(5, 50));
		
		$this->hitChart = array(
			0=> array(
					8 => "Structure",
					9 => "Flak Battery",					
					11 => "Thruster",
					13 => "ELINT Scanner",
					15 => "Engine",
					16 => "Hangar",
					17 => "Hyperdrive",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					6 => "Thruster",
					8 => "Battery",
                    18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
                    10 => "RapidGatling",
					18 => "Structure",
					20 => "Primary",
			),
		);
		

    }
}
?>
