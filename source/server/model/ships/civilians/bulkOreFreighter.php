<?php
class BulkOreFreighter extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 90;
        $this->faction = "Civilians";
        $this->phpclass = "bulkOreFreighter";
        $this->imagePath = "img/ships/galleon.png";
        $this->shipClass = "Bulk Ore Freighter";
	    
        $this->isd = 2129;        
        $this->unofficial = true;
        
        $this->forwardDefense = 15;
        $this->sideDefense = 16;
        
        $this->turncost = 2;
        $this->turndelaycost = 2;
        $this->accelcost = 6;
        $this->rollcost = 99;
        $this->pivotcost = 99;
        $this->iniativebonus = -30;        
        
        $cA = new CargoBay(2, 108);
        $cB = new CargoBay(2, 108);
        $cC = new CargoBay(2, 108);
        
        $cA->displayName = "Cargo Bay A";
        $cB->displayName = "Cargo Bay B";
        $cC->displayName = "Cargo Bay C";
         
        $this->addPrimarySystem(new Reactor(2, 5, 0, 0));
        $this->addPrimarySystem(new CnC(2, 4, 0, 0));
        $this->addPrimarySystem(new Scanner(2, 7, 2, 2));
        $this->addPrimarySystem(new Hangar(2, 2, 1));
        $this->addPrimarySystem(new Thruster(1, 11, 0, 3, 3));
        $this->addPrimarySystem(new Thruster(1, 11, 0, 3, 4));        
		$this->addPrimarySystem($cA);
		$this->addPrimarySystem($cB);
		$this->addPrimarySystem($cC);
		
        $this->addFrontSystem(new Thruster(1, 7, 0, 3, 1));
        $this->addFrontSystem(new Thruster(1, 7, 0, 3, 1));       
        
        $this->addAftSystem(new Thruster(1, 9, 0, 3, 2));
        $this->addAftSystem(new Thruster(1, 9, 0, 3, 2));
		$this->addAftSystem(new Engine(2, 5, 0, 3, 4));
		$this->addAftSystem(new Engine(2, 5, 0, 3, 4));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 2, 20));
        $this->addAftSystem(new Structure( 2, 30));
        $this->addPrimarySystem(new Structure( 2, 40));
        $this->hitChart = array(
            0=> array(
                    7 => "Structure",
                    12 => "Thruster",
            		13 => "Hangar",
                    15 => "Scanner",
                    18 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    5 => "Thruster",
            		7 => "0:Cargo Bay A",
            		9 => "0:Cargo Bay B",
            		11 => "0:Cargo Bay C",
                    18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    4 => "Thruster",
            		6 => "0:Cargo Bay A",
            		8 => "0:Cargo Bay B",
            		10 => "0:Cargo Bay C",
            		12 => "Engine",
                    18 => "Structure",
                    20 => "Primary",
            ),
      );
    }
}
?>
