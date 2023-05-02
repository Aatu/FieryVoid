<?php
class Vasy extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 290;
		$this->faction = "Raiders";
        $this->phpclass = "Vasy";
        $this->imagePath = "img/ships/RaiderVSGVasy.png";
        $this->shipClass = "Vasy Light Corvette";
  		$this->canvasSize = 100;      
 		$this->notes = 'Used only by the Vree Salvage Guild<br>'; 		  
	    
	    $this->isd = 2175;

        $this->forwardDefense = 12;
        $this->sideDefense = 12;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 0;
        
        $this->iniativebonus = 12 *5;
        $this->gravitic = true;           

        
        $this->addPrimarySystem(new Reactor(3, 10, 0, 0));
        $this->addPrimarySystem(new CnC(3, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 8, 6, 5));
        $this->addPrimarySystem(new Engine(3, 9, 0, 6, 2));
		$this->addPrimarySystem(new Hangar(3, 2));
		$this->addPrimarySystem(new HeavyPlasma(2, 8, 5, 0, 360));			
		$this->addPrimarySystem(new GraviticThruster(3, 12, 0, 6, 3));
        $this->addPrimarySystem(new GraviticThruster(3, 12, 0, 6, 4));        
        $this->addPrimarySystem(new GraviticThruster(3, 12, 0, 6, 1));
        $this->addPrimarySystem(new GraviticThruster(3, 12, 0, 6, 2)); 
        		        
        $this->addFrontSystem(new AntiprotonDefender(2, 0, 0, 240, 0));
        $this->addFrontSystem(new AntiprotonDefender(2, 0, 0, 0, 120));

        $this->addAftSystem(new AntiprotonDefender(2, 0, 0, 180, 300));
        $this->addAftSystem(new AntiprotonDefender(2, 0, 0, 60, 180));        
       

		//structures
        $this->addPrimarySystem(new Structure(3, 60));
		
		$this->hitChart = array(
			0=> array(
				5 => "Scanner",
				10 => "Engine",
				13 => "Hangar",
				18 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				6 => "0:Thruster",				
				8 => "Antiproton Defender",
				9 => "0:Heavy Plasma Cannon",		
				17 => "Structure",
				20 => "Primary",
			),
			2=> array(
				6 => "0:Thruster",				
				8 => "Antiproton Defender",
				9 => "0:Heavy Plasma Cannon",		
				17 => "Structure",
				20 => "Primary",
			),
		); 
    }
}
