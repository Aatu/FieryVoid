


<?php
class ColonialJanusCruiser extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
         $this->pointCost = 900;
        $this->faction = "ZPlaytest BSG Colonials";
        $this->phpclass = "ColonialJanusCruiser";
        $this->imagePath = "img/ships/BSG/ColonialJanus.png";
        $this->shipClass = "Janus Cruiser";
        $this->fighters = array("normal" => 0, "superheavy" => 1);
 //       $this->isd = 2160;
        $this->canvasSize = 160;
        
        $this->forwardDefense = 14;
        $this->sideDefense = 14;

        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 2;
        
        $this->iniativebonus = 30;

        $this->addPrimarySystem(new Reactor(6, 18, 0, 0));
        $this->addPrimarySystem(new CnC(6, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 12, 6, 7));
        $this->addPrimarySystem(new Engine(6, 14, 0, 12, 3));
        $this->addPrimarySystem(new Hangar(5, 2));
        $this->addPrimarySystem(new Thruster(4, 13, 0, 5, 3));
        $this->addPrimarySystem(new Thruster(4, 13, 0, 5, 4));
        $this->addPrimarySystem(new Bulkhead(0, 5));
        $this->addPrimarySystem(new RapidGatling(5, 4, 1, 0, 360));
        $this->addPrimarySystem(new RapidGatling(5, 4, 1, 0, 360));
        
        $this->addFrontSystem(new Thruster(6, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(6, 8, 0, 3, 1));
		$this->addFrontSystem(new BSGMainBattery(6, 9, 6, 315, 45));
		$this->addFrontSystem(new BSGMainBattery(6, 9, 6, 315, 45)); 
		$this->addFrontSystem(new BSGMainBattery(6, 9, 6, 315, 45)); 
		$this->addFrontSystem(new LMissileRack(5, 6, 0, 270, 360));
		$this->addFrontSystem(new LMissileRack(5, 6, 0, 0, 90));
		$this->addFrontSystem(new Bulkhead(0, 5));
		$this->addFrontSystem(new Bulkhead(0, 5));

        $this->addAftSystem(new Thruster(5, 12, 0, 6, 2));
        $this->addAftSystem(new Thruster(5, 12, 0, 6, 2));
		$this->addAftSystem(new BSGMainBattery(5, 9, 6, 135, 225)); 
        $this->addAftSystem(new Bulkhead(0, 5));
		$this->addAftSystem(new LMissileRack(5, 6, 0, 135, 225));
		$this->addAftSystem(new BSGMedBattery(5, 7, 4, 135, 180)); 
		$this->addAftSystem(new BSGMedBattery(5, 7, 4, 180, 225)); 
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 70));
        $this->addAftSystem(new Structure( 5, 50));
        $this->addPrimarySystem(new Structure( 5, 50 ));
		
		$this->hitChart = array(
			0=> array(
					8 => "Structure",
					10 => "Rapid Gatling",					
					12 => "Thruster",
					14 => "Scanner",
					16 => "Engine",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					7 => "Main Battery",
					9 => "LMissileRack",
                                        11 => "Bulkhead",
                                        18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					5 => "Thruster",
					8 => "Main Battery",
                                        9 => "LMissileRack",
                                        10 => "Battery",
                                        12 => "Bulkhead",

					18 => "Structure",
					20 => "Primary",
			),
		);
		

    }
}
?>