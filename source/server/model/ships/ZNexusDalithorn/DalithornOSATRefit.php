<?php
class DalithornOSATRefit extends OSAT{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 225;
		$this->faction = 'ZNexus Dalithorn';
        $this->phpclass = "DalithornOSATRefit";
        $this->imagePath = "img/ships/Nexus/DalithornOSAT.png";
			$this->canvasSize = 80; //img has 100px per side
        $this->shipClass = "Coilgun OSAT (2111 refit)";
			$this->variantOf = "Coilgun OSAT";
			$this->occurence = "common";
		$this->unofficial = true;
		$this->isd = 2111;
        
        $this->forwardDefense = 10;
        $this->sideDefense = 10;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60;

        $this->addPrimarySystem(new NexusHeavyCoilgun(2, 12, 5, 300, 60));
        $this->addPrimarySystem(new NexusAutocannon(2, 4, 1, 180, 60));
        $this->addPrimarySystem(new NexusMinigun(2, 4, 1, 0, 360));
        $this->addPrimarySystem(new NexusAutocannon(2, 4, 1, 300, 180));
        $this->addPrimarySystem(new NexusHeavyCoilgun(2, 12, 5, 300, 60));
        $this->addPrimarySystem(new Reactor(4, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 10, 3, 5));
		$this->addPrimarySystem(new CargoBay(4, 12));
        $this->addPrimarySystem(new Thruster(3, 8, 0, 0, 2));
                
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(4, 30));
		
		$this->hitChart = array(
			0=> array(
				7 => "Structure",
				9 => "Cargo Bay",
				10 => "Thruster",
				13 => "Heavy Coilgun",
				14 => "Minigun",
				16 => "Autocannon",
				18 => "Scanner",
				20 => "Reactor",
			),
			1=> array(
				20 => "Primary",
			),
			2=> array(
				20 => "Primary",
			),
        );
    }
}

?>