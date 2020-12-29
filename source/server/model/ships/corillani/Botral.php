<?php
class Botral extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 425;
        $this->faction = "Corillani";
        $this->phpclass = "Botral";
        $this->imagePath = "img/ships/CorillaniBotral.png";
        $this->shipClass = "Botral Light Cruiser (OSF)";
	    $this->isd = 2241;
		$this->notes = 'Orillani Space Forces (OSF)';
		$this->canvasSize= 200;
        $this->fighters = array("normal"=>6);			    
        
        
        $this->forwardDefense = 12;
        $this->sideDefense = 15;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 30;
        
         
        $this->addPrimarySystem(new Reactor(4, 12, 0, 0));
        $this->addPrimarySystem(new CnC(4, 10, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 10, 3, 6));
        $this->addPrimarySystem(new Engine(4, 13, 0, 9, 3));
        $this->addPrimarySystem(new Hangar(3, 8));
        $this->addPrimarySystem(new Thruster(3, 5, 0, 2, 3));
        $this->addPrimarySystem(new Thruster(3, 5, 0, 2, 3));        
        $this->addPrimarySystem(new Thruster(3, 5, 0, 2, 4));
        $this->addPrimarySystem(new Thruster(3, 5, 0, 2, 4));        
              
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new PlasmaProjector(4, 8, 5, 240, 0));
        $this->addFrontSystem(new PlasmaProjector(4, 8, 5, 0, 120));
        $this->addFrontSystem(new HeavyPlasma(3, 8, 5, 300, 60));        
        $this->addFrontSystem(new TwinArray(2, 6, 2, 240, 60));
        $this->addFrontSystem(new TwinArray(2, 6, 2, 300, 120));

        $this->addAftSystem(new Thruster(3, 8, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 8, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 8, 0, 3, 2));        
        $this->addAftSystem(new TwinArray(2, 6, 2, 120, 300));
        $this->addAftSystem(new TwinArray(2, 6, 2, 60, 240));       
        
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 54));
        $this->addAftSystem(new Structure( 4, 50));
        $this->addPrimarySystem(new Structure( 4, 40));
		
			
		
		$this->hitChart = array(
			0=> array(
				7 => "Structure",
				11 => "Thruster",
				13 => "Scanner",
				15 => "Engine",
				17 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				6 => "Thruster",
				8 => "Plasma Projector",
				9 => "Heavy Plasma Cannon",
				11 => "Twin Array",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				8 => "Thruster",
				10 => "Twin Array",
				18 => "Structure",
				20 => "Primary",
			),
		); 
    }
}



?>
