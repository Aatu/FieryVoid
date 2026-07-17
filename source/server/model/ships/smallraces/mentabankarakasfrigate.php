<?php
class mentabankarakasfrigate extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 440;
        $this->faction = "Small Races";
        $this->phpclass = "mentabankarakasfrigate";
        $this->imagePath = "img/ships/mentabanKarakas.png";
        $this->shipClass = "Mentaban Karakas Frigate";
        $this->isd = 2254;
 		$this->unofficial = true;
       
        $this->forwardDefense = 14;
        $this->sideDefense = 15;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 2;
        $this->rollcost = 3;
        $this->pivotcost = 2;
        $this->iniativebonus = 30;
        
         
        $this->addPrimarySystem(new Reactor(4, 10, 0, 3));
        $this->addPrimarySystem(new CnC(5, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 4, 5));
        $this->addPrimarySystem(new Engine(4, 11, 0, 6, 2));
        $this->addPrimarySystem(new Hangar(4, 2, 1));
        $this->addPrimarySystem(new Thruster(4, 11, 0, 3, 3));
        $this->addPrimarySystem(new Thruster(4, 11, 0, 3, 4));       
        $this->addPrimarySystem(new MediumPlasma(4, 5, 3, 240, 0));
        $this->addPrimarySystem(new MediumPlasma(4, 5, 3, 0, 120));
     
        $this->addFrontSystem(new Thruster(5, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(5, 8, 0, 3, 1));       
        $this->addFrontSystem(new MedBlastCannon(3, 5, 2, 240, 0));
        $this->addFrontSystem(new MedBlastCannon(3, 5, 2, 0, 120));
        $this->addFrontSystem(new LtBlastCannon(3, 4, 1, 270, 90));      
        
        $this->addAftSystem(new Thruster(5, 12, 0, 4, 2));
        $this->addAftSystem(new Thruster(5, 12, 0, 4, 2));
        $this->addAftSystem(new MedBlastCannon(3, 5, 2, 180, 300));
        $this->addAftSystem(new MedBlastCannon(3, 5, 2, 60, 180));
        $this->addAftSystem(new LtBlastCannon(3, 4, 1, 90, 270));        
        $this->addAftSystem(new LtBlastCannon(3, 4, 1, 180, 360));
        $this->addAftSystem(new LtBlastCannon(3, 4, 1, 180, 360));
        $this->addAftSystem(new LtBlastCannon(3, 4, 1, 0, 180));
        $this->addAftSystem(new LtBlastCannon(3, 4, 1, 0, 180));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 40));
        $this->addAftSystem(new Structure( 4, 40));
        $this->addPrimarySystem(new Structure( 4, 45));
        
        $this->hitChart = array(
        		0=> array(
        				7=> "Structure",
        				9=> "Medium Plasma Cannon",
        				11=> "Thruster",
        				13=> "Scanner",
        				15=> "Engine",
        				16=> "Hangar",
        				19=> "Reactor",
        				20=> "C&C",
        		),
        		1=> array(
        				3=> "Thruster",
                        9=> "Medium Blast Cannon",
        				8=> "Light Blast Cannon",
        				18=> "Structure",
        				20=> "Primary",
        		),
        		2=> array(
        				5=> "Thruster",
                        10=> "Medium Blast Cannon",
                        12=> "Light Blast Cannon",
        				18=> "Structure",
        				20=> "Primary",
        		),
        );
    }
}



?>

