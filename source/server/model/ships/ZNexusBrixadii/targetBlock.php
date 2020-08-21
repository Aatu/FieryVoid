<?php
class targetBlock extends OSAT{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 100;
		$this->faction = 'ZNexus Brixadii';
        $this->phpclass = "targetBlock";
        $this->imagePath = "img/ships/IpshaBorgCube.png";
			$this->canvasSize = 200; //img has 200px per side
        $this->shipClass = "Target Block";
		$this->unofficial = true;
		$this->isd = 0;

        
        $this->forwardDefense = 20;
        $this->sideDefense = 20;
        
        $this->turncost = 999;
        $this->turndelaycost = 999;
        $this->accelcost = 999;
        $this->rollcost = 999;
        $this->pivotcost = 999;	
        $this->iniativebonus = -20;

		$this->addPrimarySystem(new CargoBay(3, 20));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(0, 200));
		
		$this->hitChart = array(
			0=> array(
				20 => "Structure",
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
