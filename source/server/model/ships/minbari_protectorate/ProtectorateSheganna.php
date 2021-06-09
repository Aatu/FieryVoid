<?php
class ProtectorateSheganna extends OSAT{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 270;
		$this->faction = "Minbari Protectorate";
        $this->phpclass = "ProtectorateSheganna";
        $this->imagePath = "img/ships/sheganna.png";
        $this->shipClass = 'Protectorate Sheganna OSAT';        
        $this->forwardDefense = 9;
        $this->sideDefense = 9;
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 65; 
        $this->isd = 2070;

        $this->addPrimarySystem(new Reactor(4, 9, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 7, 2, 8)); 
        $this->addPrimarySystem(new Thruster(4, 7, 0, 0, 2));
        
        $this->addPrimarySystem(new NeutronLaser(2, 5, 4, 270, 90));
        $this->addPrimarySystem(new FusionCannon(3, 8, 4, 180, 360));
        $this->addPrimarySystem(new FusionCannon(3, 8, 4, 270, 90));
        $this->addPrimarySystem(new FusionCannon(3, 8, 4, 270, 90));
        $this->addPrimarySystem(new FusionCannon(3, 8, 4, 0, 180));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        
        $this->addPrimarySystem(new Structure(4, 35));
		
		$this->hitChart = array(
                0=> array(
                        10 => "Structure",
                        13 => "Thruster",
                        14 => "Neutron Laser",
                        16 => "Fusion Cannon",
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
