<?php
class Jumpgate  extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 0;
		$this->faction = "Civilians";
        $this->phpclass = "jumpgate";
        $this->imagePath = "img/ships/orion.png";
        $this->shipClass = "Jumpgate";
        $this->shipSizeClass = 3;
        $this->canvasSize = 280;
	            
        $this->forwardDefense = 20;
        $this->sideDefense = 20;
        
        $this->turncost = 99;
        $this->turndelaycost = 99;
        $this->accelcost = 99;
        $this->rollcost = 99;
        $this->pivotcost = 99;	
        
         
        $this->addPrimarySystem(new Reactor(4, 50, 0, 0));
        $this->addPrimarySystem(new CnC(6, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 6, 2, 2));
        $this->addPrimarySystem(new Hangar(3, 1, 1));
        $this->addPrimarySystem(new JumpEngine(8, 10, 3, 20));
				
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(2, 200));
        $this->addAftSystem(new Structure(2, 200));
        $this->addLeftSystem(new Structure(2, 240));
        $this->addRightSystem(new Structure(2, 240));
        $this->addPrimarySystem(new Structure(3, 160));

        $this->hitChart = array(
                0=> array(
                        10 => "Structure",
                        13 => "Scanner",
                		17 => "Reactor",
                        18 => "Hangar",
                        19 => "Jump Engine",
                		20 => "C&C",
                ),
                1=> array(
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        18 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        18 => "Structure",
                        20 => "Structure",
                ),
                4=> array(
                        18 => "Structure",
                        20 => "Structure",
                ),
        );
    }
}
