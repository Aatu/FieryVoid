<?php
class wlcChlonasEsKashiDD2198 extends HeavyCombatVesselLeftRight{
    /*Ch'Lonas Es'Kashi destroyer, variant ISD 2198*/
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 400;
        $this->faction = "Custom Ships";
        $this->phpclass = "wlcChlonasEsKashiDD2198";
        $this->imagePath = "img/ships/sunhawk.png";
        $this->shipClass = "Ch'Lonas Es'Kashi Destroyer (2198)";
        //$this->fighters = array("medium"=>6);
        
        $this->forwardDefense = 13;
        $this->sideDefense = 14;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.50;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 40;
        
         

        $this->addPrimarySystem(new Reactor(5, 14, 0, 0));
        $this->addPrimarySystem(new CnC(4, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 13, 5, 4));
        $this->addPrimarySystem(new Engine(4, 11, 0, 8, 2));
        $this->addPrimarySystem(new Hangar(4, 2));
        $this->addPrimarySystem(new Thruster(4, 15, 0, 4, 1));
        $this->addPrimarySystem(new Thruster(3, 21, 0, 8, 2));
        $this->addPrimarySystem(new ImperialLaser(3, 8, 5, 300, 60));
        
        $this->addLeftSystem(new Thruster(4, 15, 0, 4, 3));
        $this->addLeftSystem(new TacLaser(3, 5, 4, 240, 60));
        $this->addLeftSystem(new MatterCannon(3, 7, 4, 240, 360));
        $this->addLeftSystem(new TwinArray(2, 6, 2, 240, 60));

        $this->addRightSystem(new Thruster(4, 15, 0, 4, 4));
        $this->addRightSystem(new TacLaser(3, 5, 4, 300, 120));
        $this->addRightSystem(new MatterCannon(3, 7, 4, 0, 120));
        $this->addRightSystem(new TwinArray(2, 6, 2, 300, 120));
        
        
        
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addLeftSystem(new Structure( 4, 44));
        $this->addRightSystem(new Structure( 4, 44));
        $this->addPrimarySystem(new Structure( 5, 36 ));
        
        
	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			5 => "Structure",
			7 => "Imperial Laser",
			11 => "Thruster",
			13 => "Scanner",
			15 => "Engine",
			17 => "Hangar",
			19 => "Reactor",
			20 => "CnC",
		),

		3=> array(
			4 => "Thruster",
			7 => "Tactical Laser",
			10 => "Matter Cannon",
			12 => "Twin Array",
			18 => "Structure",
			20 => "Primary",
		),

		4=> array(
			4 => "Thruster",
			7 => "Tactical Laser",
			10 => "Matter Cannon",
			12 => "Twin Array",
			18 => "Structure",
			20 => "Primary",
		),


	);

    }
}
?>