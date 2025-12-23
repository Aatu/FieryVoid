<?php
class DalithornOSATEarly extends OSAT{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 165;
		$this->faction = 'Nexus Dalithorn Commonwealth (early)';
        $this->phpclass = "DalithornOSATEarly";
        $this->imagePath = "img/ships/Nexus/Dalithorn_CoilgunOSAT2.png";
			$this->canvasSize = 90; //img has 100px per side
        $this->shipClass = "Early Coilgun OSAT (1908)";
			$this->variantOf = "Coilgun OSAT";
			$this->occurence = "common";
		$this->unofficial = true;
		$this->isd = 1908;
        
        $this->forwardDefense = 10;
        $this->sideDefense = 10;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60;

        $this->addPrimarySystem(new OSATCnC(0, 1, 0, 0));
        $this->addFrontSystem(new NexusCoilgun(2, 10, 4, 300, 60));
        $this->addFrontSystem(new NexusLightGasGun(2, 5, 1, 180, 60));
        $this->addFrontSystem(new NexusShatterGun(1, 2, 1, 0, 360));
        $this->addFrontSystem(new NexusLightGasGun(2, 5, 1, 300, 180));
        $this->addFrontSystem(new NexusCoilgun(2, 10, 4, 300, 60));
        $this->addPrimarySystem(new Reactor(4, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 10, 3, 5));
		$this->addPrimarySystem(new Magazine(4, 12));
        $this->addAftSystem(new Thruster(3, 8, 0, 0, 2));
                
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(3, 30));
		
		$this->hitChart = array(
			0=> array(
				7 => "Structure",
				8 => "0:Magazine",
				10 => "2:Thruster",
				12 => "1:Coilgun",
				14 => "1:Shatter Gun",
				16 => "1:Light Gas Gun",
				18 => "0:Scanner",
				20 => "0:Reactor",
			),
			1=> array(
				7 => "Structure",
				8 => "0:Magazine",
				10 => "2:Thruster",
				12 => "1:Coilgun",
				14 => "1:Shatter Gun",
				16 => "1:Light Gas Gun",
				18 => "0:Scanner",
				20 => "0:Reactor",
			),
			2=> array(
				7 => "Structure",
				8 => "0:Magazine",
				10 => "2:Thruster",
				12 => "1:Coilgun",
				14 => "1:Shatter Gun",
				16 => "1:Light Gas Gun",
				18 => "0:Scanner",
				20 => "0:Reactor",
			),
        );
    }
}

?>
