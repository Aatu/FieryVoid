<?php
class Jumpgate  extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 1000;//well, it IS a very important and costly structure, even if with zero actual combat value!
		$this->faction = "Civilians";
        $this->phpclass = "jumpgate";
        $this->imagePath = "img/ships/JumpGate.png";
        $this->canvasSize = 200;
        $this->shipClass = "Fixed Jump Gate";
        $this->shipSizeClass = 3;
        $this->Enormous = true;
	            
		$this->base = true;
		$this->smallBase = true;
		$this->nonRotating = true;  //completely immobile, doesn't even rotate
		
        $this->forwardDefense = 20;
        $this->sideDefense = 20;
        
        $this->turncost = 99;
        $this->turndelaycost = 99;
        $this->accelcost = 99;
        $this->rollcost = 99;
        $this->pivotcost = 99;	
        
         
        $this->addPrimarySystem(new Reactor(6, 50, 0, 0));
        $this->addPrimarySystem(new CnC(6, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 6, 2, 2));
        $this->addPrimarySystem(new Hangar(3, 1, 1));
        $this->addPrimarySystem(new JumpEngine(6, 50, 20, 20));
		
		//Structures are not displayed properly if there are no systems - so I add generic "Support System" :)
		$supportSystem = new CargoBay(2,10);
		$supportSystem->displayName = 'Support System';
        $this->addFrontSystem($supportSystem);
		$supportSystem = new CargoBay(2,10);
		$supportSystem->displayName = 'Support System';
        $this->addAftSystem($supportSystem);
		$supportSystem = new CargoBay(2,10);
		$supportSystem->displayName = 'Support System';
        $this->addLeftSystem($supportSystem);
		$supportSystem = new CargoBay(2,10);
		$supportSystem->displayName = 'Support System';
        $this->addRightSystem($supportSystem);
				
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(2, 220));
        $this->addAftSystem(new Structure(2, 220));
        $this->addLeftSystem(new Structure(2, 220));
        $this->addRightSystem(new Structure(2, 220));
        $this->addPrimarySystem(new Structure(3, 160));

        $this->hitChart = array(
                0=> array(
                        9 => "Structure",
                        10 => "Scanner",
                        11 => "Hangar",
                        15 => "Jump Engine",
                		19 => "Reactor",
                		20 => "C&C",
                ),
                1=> array(
                        3 => "Support System",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        3 => "Support System",
                        18 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        3 => "Support System",
                        18 => "Structure",
                        20 => "Structure",
                ),
                4=> array(
                        3 => "Support System",
                        18 => "Structure",
                        20 => "Structure",
                ),
        );
    }
}
?>
