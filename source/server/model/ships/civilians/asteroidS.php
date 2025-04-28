<?php
class asteroidS  extends Terrain{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 1;
		$this->faction = "Civilians";
		$this->factionAge = 1;        
        $this->phpclass = "asteroidS";
        $this->imagePath = "img/ships/AsteroidS1.png";
        $this->canvasSize = 200;
        $this->shipClass = "Asteroid (Small)";
        $this->Enormous = true; 
		$this->iniativebonus = -200; //no voluntary movement anyway
        $this->isd = 0;     
     
        $this->notes .= "<br>Ships (but not fighters) entering this hex take collision damage";          
	            
		$this->base = true;
		$this->smallBase = true;
		$this->nonRotating = true;  //completely immobile, doesn't even rotate
		
        $this->forwardDefense = 20;
        $this->sideDefense = 20;
        
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

        $this->addPrimarySystem(new Structure(8,  300));

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
