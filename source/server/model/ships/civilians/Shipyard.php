<?php
class Shipyard extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 500;//Nominal value as important and costly structure, even if with zero actual combat value!
		$this->faction = "Civilians";
        $this->phpclass = "Shipyard";
        $this->imagePath = "img/ships/Shipyard.png";
        $this->canvasSize = 200;
        $this->shipClass = "Shipyard";
        $this->shipSizeClass = 3;
        $this->Enormous = false; //classify it as a Capital just so it doesn't auto-ram passing units!
		$this->iniativebonus = -200; //no voluntary movement anyway
        $this->isd = 'Variable';
	            
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
	    
         
        $this->addPrimarySystem(new Reactor(6, 25, 0, 0));
        $this->addPrimarySystem(new CnC(6, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 6, 2, 1));
        $this->addPrimarySystem(new Hangar(3, 1, 1));
		
		//Structures are not displayed properly if there are no systems - using Vree Technical Structure system :)

        $this->addFrontSystem(new StructureTechnical(0, 0, 0, 0));	

        $this->addAftSystem(new StructureTechnical(0, 0, 0, 0));

        $this->addLeftSystem(new StructureTechnical(0, 0, 0, 0));
        
        $this->addRightSystem(new StructureTechnical(0, 0, 0, 0));
				
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(1, 150));
        $this->addAftSystem(new Structure(1, 150));
        $this->addLeftSystem(new Structure(1, 150));
        $this->addRightSystem(new Structure(1, 150));
        $this->addPrimarySystem(new Structure(2, 100));

        $this->hitChart = array(
                0=> array(
                        13 => "Structure",
                        15 => "Scanner",
                        17 => "Hangar",
                        19 => "Reactor",
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
                        20 => "Primary",
                ),
                4=> array(
                        18 => "Structure",
                        20 => "Primary",
                ),
        );
    }
}
?>
