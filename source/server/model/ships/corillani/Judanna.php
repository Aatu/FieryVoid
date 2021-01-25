<?php
class Judanna extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 490;
        $this->faction = "Corillani";
        $this->phpclass = "Judanna";
        $this->imagePath = "img/ships/CorillaniJudanna.png";
        $this->shipClass = "Judanna Escort Frigate";
	    $this->isd = 2229;
		$this->notes = 'Defenders of Corillan (DoC)';
		$this->canvasSize= 200;
        $this->fighters = array("normal"=>6);			    
        
        
        $this->forwardDefense = 13;
        $this->sideDefense = 14;
        
        $this->turncost = 0.75;
        $this->turndelaycost = 0.75;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 30;
        
         
        $this->addPrimarySystem(new Reactor(4, 10, 0, 0));
        $this->addPrimarySystem(new CnC(4, 9, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 4, 7));
        $this->addPrimarySystem(new Engine(4, 11, 0, 8, 2));
        $this->addPrimarySystem(new Hangar(4, 7));
        $this->addPrimarySystem(new Thruster(3, 6, 0, 2, 3));
        $this->addPrimarySystem(new Thruster(3, 6, 0, 2, 3));        
        $this->addPrimarySystem(new Thruster(3, 6, 0, 2, 4));
        $this->addPrimarySystem(new Thruster(3, 6, 0, 2, 4));        
            
        $this->addFrontSystem(new Thruster(3, 6, 0, 2, 1));
        $this->addFrontSystem(new Thruster(3, 6, 0, 2, 1));
        $this->addFrontSystem(new PlasmaAccelerator(3, 10, 5, 300, 60));
        $this->addFrontSystem(new TwinArray(2, 6, 2, 240, 60));
        $this->addFrontSystem(new TwinArray(2, 6, 2, 240, 60));
        $this->addFrontSystem(new TwinArray(2, 6, 2, 300, 120));
        $this->addFrontSystem(new TwinArray(2, 6, 2, 300, 120));        

        $this->addAftSystem(new Thruster(3, 6, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 6, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 6, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 6, 0, 2, 2));               
        $this->addAftSystem(new TwinArray(2, 6, 2, 90, 270));     
        
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 40));
        $this->addAftSystem(new Structure( 4, 40));
        $this->addPrimarySystem(new Structure( 4, 52));
		
			
		
		$this->hitChart = array(
			0=> array(
				8 => "Structure",
				12 => "Thruster",
				14 => "Scanner",
				16 => "Engine",
				17 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				3 => "Thruster",
				5 => "Particle Cannon",
				9 => "Twin Array",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				6 => "Thruster",
				8 => "Twin Array",
				16 => "Structure",
				20 => "Primary",
			),
		); 
    }
}



?>
