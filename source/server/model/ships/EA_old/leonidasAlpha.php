<?php
class LeonidasAlpha extends OSAT{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 100;
		$this->faction = 'EA (early)';//"EA defenses";
        $this->phpclass = "LeonidasAlpha";
        $this->imagePath = "img/ships/hector.png";
        $this->shipClass = 'Leonidas Satellite (Alpha)';
 		$this->unofficial = true;

	    $this->isd = 2128;
        
        $this->forwardDefense = 10;
        $this->sideDefense = 10;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60;

        $this->addPrimarySystem(new EWOMissileRack(3, 6, 0, 270, 90, true));
        $this->addPrimarySystem(new EWOMissileRack(3, 6, 0, 270, 90, true));
        $this->addPrimarySystem(new LtBlastCannon(2, 4, 1, 180, 360));
        $this->addPrimarySystem(new LtBlastCannon(2, 4, 1, 0, 360));
        $this->addPrimarySystem(new LtBlastCannon(2, 4, 1, 0, 180));
        //$this->addPrimarySystem(new InterceptorMkI(2, 4, 1, 0, 360));

        $this->addPrimarySystem(new Reactor(4, 6, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 14, 2, 3));   

        $this->addPrimarySystem(new Thruster(2, 6, 0, 0, 2));
                
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(3, 30));
		
		$this->hitChart = array(
			0=> array(
				9 => "Structure",
				11 => "Thruster",
				14 => "Class-O Missile Rack",
				17 => "Light Blast Cannon",
				19 => "Scanner",
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
