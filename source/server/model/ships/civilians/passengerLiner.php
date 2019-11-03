<?php
class PassengerLiner extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 175;
        $this->faction = "Civilians";
        $this->phpclass = "passengerliner";
        $this->imagePath = "img/ships/civilianFreighter.png";
        $this->shipClass = "Passenger Liner";
        $this->isd = 2198;       
        
        $this->forwardDefense = 13;
        $this->sideDefense = 16;
        
        $this->turncost = 1.5;
        $this->turndelaycost = 1.5;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 99;
        $this->iniativebonus = -10;        
        
        $cA = new CargoBay(3, 40);
        $cB = new CargoBay(3, 40);
        $cC = new CargoBay(3, 40);
        $cD = new CargoBay(3, 40);
        
        $cA->displayName = "Cargo Bay A";
        $cB->displayName = "Cargo Bay B";
        $cC->displayName = "Cargo Bay C";
        $cD->displayName = "Cargo Bay D";
        
        $this->addPrimarySystem(new Reactor(3, 15, 0, 0));
        $this->addPrimarySystem(new CnC(3, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 10, 3, 3));
        $this->addPrimarySystem(new Engine(3, 16, 0, 8, 3));
        $this->addPrimarySystem(new Hangar(2, 8, 2));
        $this->addPrimarySystem(new Thruster(3, 13, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(3, 13, 0, 4, 4));        
		$this->addPrimarySystem($cA);
		$this->addPrimarySystem($cB);
		$this->addPrimarySystem($cC);
		$this->addPrimarySystem($cD);
		
        $this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
		$this->addFrontSystem(new StdParticleBeam(2, 4, 1, 240, 60));
		$this->addFrontSystem(new StdParticleBeam(2, 4, 1, 300, 120));
		
        $this->addAftSystem(new Thruster(3, 12, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 4, 2));
		$this->addAftSystem(new StdParticleBeam(2, 4, 1, 60, 240));
		$this->addAftSystem(new StdParticleBeam(2, 4, 1, 120, 300));
		
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(3, 48));
        $this->addAftSystem(new Structure(3, 44));
        $this->addPrimarySystem(new Structure(3, 60));
        $this->hitChart = array(
            0=> array(
                    6 => "Structure",
                    8 => "Thruster",
            		9 => "Cargo Bay A",
            		10 => "Cargo Bay B",
            		11 => "Cargo Bay C",
            		12 => "Cargo Bay D",
                    14 => "Scanner",
            		16 => "Engine",
            		17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    4 => "Thruster",
            		6 => "Standard Particle Beam",
            		9 => "0:Cargo Bay A",
            		12 => "0:Cargo Bay B",
                    18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    4 => "Thruster",
            		6 => "Standard Particle Beam",
            		9 => "0:Cargo Bay C",
            		12 => "0:Cargo Bay D",
                    18 => "Structure",
                    20 => "Primary",
            ),
      );
    }
}


?>
