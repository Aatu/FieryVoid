<?php
class ShipyardNew extends Terrain{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 10;//Nominal value as important and costly structure, even if with zero actual combat value!
		$this->faction = "Terrain";  
        $this->phpclass = "shipyardNew";
        $this->imagePath = "img/ships/Shipyard.png";
        $this->canvasSize = 200;
        $this->shipClass = "Shipyard";
        $this->Enormous = false; //classify it as a Capital just so it doesn't auto-ram passing units!
		$this->iniativebonus = -200; //no voluntary movement anyway
        $this->isd = '2100';
	            
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

        //Block all enhancements for Terrain units when bought
		Enhancements::nonstandardEnhancementSet($this, 'Terrain');        
         
        $this->addPrimarySystem(new Reactor(6, 25, 0, 0));
        $this->addPrimarySystem(new CnC(6, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 6, 2, 1));
        $this->addPrimarySystem(new Hangar(3, 4, 1));
						
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(2, 500));

        $this->hitChart = array(
                0=> array(
                        13 => "Structure",
                        15 => "Scanner",
                        17 => "Hangar",
                        19 => "Reactor",
                		20 => "C&C",
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
