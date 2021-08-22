<?php
class ColonialJanusCruiser extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 725;
        $this->faction = "ZBSG Colonials";
        $this->phpclass = "ColonialJanusCruiser";
        $this->imagePath = "img/ships/BSG/ColonialJanus.png";
        $this->shipClass = "Janus Cruiser";
        $this->fighters = array("normal" => 0, "superheavy" => 1);
 //       $this->isd = 2160;
        $this->canvasSize = 140;
        $this->limited = 33;

		$this->unofficial = true;
        
        $this->forwardDefense = 14;
        $this->sideDefense = 17;

        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 4;
        $this->pivotcost = 3;
        
        $this->iniativebonus = 20;

        $this->addPrimarySystem(new Reactor(6, 18, 0, 0));
        $this->addPrimarySystem(new CnC(6, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 12, 6, 7));
        $this->addPrimarySystem(new Engine(6, 14, 0, 10, 3));
        $this->addPrimarySystem(new Hangar(5, 2));
        $this->addPrimarySystem(new Thruster(4, 13, 0, 5, 3));
        $this->addPrimarySystem(new Thruster(4, 13, 0, 5, 4));
        $this->addPrimarySystem(new RapidGatling(5, 4, 1, 0, 360));
        $this->addPrimarySystem(new RapidGatling(5, 4, 1, 0, 360));
        $hyperdrive = new JumpEngine(4, 12, 6, 20);
			$hyperdrive->displayName = 'Hyperdrive';
			$this->addPrimarySystem($hyperdrive);
        
        $this->addFrontSystem(new Thruster(6, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(6, 8, 0, 3, 1));
		$this->addFrontSystem(new BSGMainBattery(6, 9, 6, 300, 360));
		$this->addFrontSystem(new BSGMainBattery(6, 9, 6, 0, 60)); 
		$this->addFrontSystem(new BSGMainBattery(6, 9, 6, 330, 30)); 
		$this->addFrontSystem(new SMissileRack(5, 6, 0, 300, 60));
		$this->addFrontSystem(new Bulkhead(0, 5));
		$this->addFrontSystem(new Bulkhead(0, 5));

        $this->addAftSystem(new Thruster(5, 12, 0, 5, 2));
        $this->addAftSystem(new Thruster(5, 12, 0, 5, 2));
		$this->addAftSystem(new BSGMainBattery(5, 9, 6, 150, 210)); 
        $this->addAftSystem(new Bulkhead(0, 5));
		$this->addAftSystem(new SMissileRack(5, 6, 0, 120, 240));
		$this->addAftSystem(new BSGMedBattery(5, 7, 4, 180, 240)); 
		$this->addAftSystem(new BSGMedBattery(5, 7, 4, 120, 180)); 
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 70));
        $this->addAftSystem(new Structure( 4, 50));
        $this->addPrimarySystem(new Structure( 5, 50 ));
		
		$this->hitChart = array(
			0=> array(
					8 => "Structure",
					9 => "Rapid Gatling Railgun",					
					11 => "Thruster",
					13 => "Scanner",
					15 => "Engine",
					16 => "Hangar",
					17 => "Hyperdrive",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					5 => "Thruster",
					9 => "Main Battery",
					11 => "Class-S Missile Rack",
                    18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					5 => "Thruster",
					8 => "Main Battery",
                    9 => "Class-S Missile Rack",
                    11 => "Battery",
					18 => "Structure",
					20 => "Primary",
			),
		);
		

    }
}
?>