<?php
class QomYominQolAta extends OSAT{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 120;
	$this->faction = "Nexus Makar Federation (early)";
        $this->phpclass = "QomYominQolAta";
        $this->imagePath = "img/ships/Nexus/makar_qolat2.png";
        $this->canvasSize = 80;
        $this->shipClass = 'Qol At Support OSAT';
        
        $this->isd = 1932;
        $this->unofficial = true;
        
        $this->forwardDefense = 10;
        $this->sideDefense = 9;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60;
		
        $this->addPrimarySystem(new Reactor(4, 7, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 7, 2, 5)); 

        $this->addFrontSystem(new SensorSpear(2, 6, 3, 270, 90)); 
        $this->addFrontSystem(new SensorSpear(2, 6, 3, 270, 90)); 
        $this->addFrontSystem(new NexusLightChargeCannon(2, 4, 1, 0, 360));

        $this->addAftSystem(new Thruster(3, 8, 0, 0, 2)); 
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        
        $this->addPrimarySystem(new Structure(3, 25));
        
		
		$this->hitChart = array(
			0=> array(
					9 => "Structure",
					11 => "2:Thruster",
					14 => "1:Sensor Spear",
					16 => "1:Light Charge Cannon",
					18 => "0:Scanner",
					20 => "0:Reactor",
			),
			1=> array(
					9 => "Structure",
					11 => "2:Thruster",
					14 => "1:Sensor Spear",
					16 => "1:Light Charge Cannon",
					18 => "0:Scanner",
					20 => "0:Reactor",
			),
			2=> array(
					9 => "Structure",
					11 => "2:Thruster",
					14 => "1:Sensor Spear",
					16 => "1:Light Charge Cannon",
					18 => "0:Scanner",
					20 => "0:Reactor",
			),
		);
    
    
        
    }
}
?>
