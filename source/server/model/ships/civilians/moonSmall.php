<?php
class moonSmall  extends Terrain{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 5;
		$this->faction = "Civilians";
		$this->factionAge = 1;        
        $this->phpclass = "moonSmall";
        $this->imagePath = "img/ships/moonSmall.png";
        $this->canvasSize = 512;
        $this->shipClass = "Moon (Small)";
        $this->Enormous = true; 
        $this->Huge = 1;        
		$this->iniativebonus = -200; //no voluntary movement anyway
        $this->notes = "Occupies multiple hexes";
        $this->notes .= "<br>Units entering terrain's area will automatically ram"; 
        $this->isd = 0;      
	            
		$this->base = true;
		$this->smallBase = true;
		$this->nonRotating = true;  //completely immobile, doesn't even rotate
		
        $this->forwardDefense = 28;
        $this->sideDefense = 28;
        
        //No engine/thrusters!!
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
	    
        //Block all enhancements for Terrain units when bought
		Enhancements::nonstandardEnhancementSet($this, 'Terrain');

        $this->addPrimarySystem(new OSATCnC(10, 1, 0, 0)); //Required for some checks.
        $this->addPrimarySystem(new MagGravReactorTechnical(10, 1000, 0, 0)); //Required for some checks.

        $this->addPrimarySystem(new Structure(8,  2000));

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
