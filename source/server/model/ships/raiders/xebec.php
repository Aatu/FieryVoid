<?php
class Xebec extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
    	$this->pointCost = 300;
    	$this->faction = "Raiders";
        $this->phpclass = "Xebec";
        $this->imagePath = "img/ships/xebec.png";
        $this->shipClass = "Xebec";
        $this->canvasSize = 100;
        
		$this->notes = "Generic raider unit.";
		$this->notes .= "<br> ";
		$this->notes .= "<br>More detailed deployment restrictions are in the Faction List document.";
		$this->notes .= "<br> ";

		$this->isd = 2195;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 15;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 2;
        $this->pivotcost = 2;
    	$this->iniativebonus = 30;
         
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 180, 360));
        $this->addFrontSystem(new MediumLaser(3, 6, 5, 240, 60));
        $this->addFrontSystem(new MediumLaser(3, 6, 5, 300, 120));
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 0, 180));

        $this->addPrimarySystem(new Reactor(3, 9, 0, 0));
        $this->addPrimarySystem(new Engine(3, 18, 0, 12, 2));
        $this->addPrimarySystem(new CnC(4, 5, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 12, 3, 5));
        $this->addPrimarySystem(new Hangar(3, 2));
    	$this->addPrimarySystem(new Thruster(3, 15, 0, 5, 3));
        $this->addPrimarySystem(new Thruster(3, 15, 0, 5, 4));
	$this->addPrimarySystem(new StdParticleBeam(2, 4, 1, 0, 360));
		
        $temp1 = new CargoBay(2, 18);
        $temp2 = new CargoBay(2, 18);
        $temp1->displayName = "Cargo Bay A";
        $temp2->displayName = "Cargo Bay B";
        
        $this->addFrontSystem(new Thruster(3, 8, 0, 4, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 4, 1));

        $this->addAftSystem(new Thruster(3, 12, 0, 4, 2));
        $this->addAftSystem(new Thruster(2, 12, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 4, 2));
        $this->addAftSystem($temp1);
        $this->addAftSystem($temp2);
	
        $this->addPrimarySystem(new Structure( 4, 48));
        
        $this->hitChart = array(
        		0=> array( //Possible issue, Primary hit chart specifically calls for Port/Stb Thrust, this will select any thruster, if this is consistant among medium ships we might be able to do a check when thruster is damaged.
        				7 => "Thruster",
        				9 => "Standard Particle Beam",
        				12 => "Scanner",
        				15 => "Engine",
        				17 => "Hangar",
        				19 => "Reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				5 => "Thruster",
        				8 => "Medium Laser",
        				11 => "Standard Particle Beam",
        				17 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				5 => "Thruster",
        				8 => "Cargo Bay A",
        				11 => "Cargo Bay B",
        				17 => "Structure",
        				20 => "Primary",
        		),
        );
        
    }

}



?>
