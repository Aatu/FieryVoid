<?php
class BulkFreighter extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 280;
        $this->faction = "Civilians";
        $this->phpclass = "bulkfreighter";
        $this->imagePath = "img/ships/galleon.png";
        $this->shipClass = "Bulk Freighter";
        $this->isd = 2193;        
        
        $this->forwardDefense = 14;
        $this->sideDefense = 15;
        
        $this->turncost = 1.5;
        $this->turndelaycost = 1.33;
        $this->accelcost = 5;
        $this->rollcost = 2;
        $this->pivotcost = 99;
        $this->iniativebonus = -30;        
        
        $cA = new CargoBay(2, 100);
        $cB = new CargoBay(2, 100);
        
        $cA->displayName = "Cargo Bay A";
        $cB->displayName = "Cargo Bay B";
         
        $this->addPrimarySystem(new Reactor(3, 11, 0, 0));
        $this->addPrimarySystem(new CnC(3, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 12, 3, 4));
        $this->addPrimarySystem(new Hangar(2, 2, 1));
        $this->addPrimarySystem(new Hangar(2, 2, 1));
        $this->addPrimarySystem(new Engine(3, 14, 0, 6, 3));
        $this->addPrimarySystem(new Thruster(3, 13, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(3, 13, 0, 4, 4));        
		$this->addPrimarySystem($cA);
		$this->addPrimarySystem($cB);
		$this->addPrimarySystem(new StdParticleBeam(2, 4, 1, 180, 0));
		$this->addPrimarySystem(new StdParticleBeam(2, 4, 1, 180, 0));
		$this->addPrimarySystem(new StdParticleBeam(2, 4, 1, 0, 180));
		$this->addPrimarySystem(new StdParticleBeam(2, 4, 1, 0, 180));
		
        $this->addFrontSystem(new Thruster(3, 12, 0, 5, 1));
        $this->addFrontSystem(new MediumPlasma(2, 5, 3, 240, 360));
        $this->addFrontSystem(new MediumPlasma(2, 5, 3, 0, 120));
        
        $this->addAftSystem(new Thruster(3, 15, 0, 6, 2));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(3, 40));
        $this->addAftSystem(new Structure(3, 40));
        $this->addPrimarySystem(new Structure(3, 40));
        $this->hitChart = array(
            0=> array(
                    6 => "Structure",
                    9 => "Cargo Bay A",
            		12 => "Cargo Bay B",
                    14 => "Scanner",
            		16 => "Engine",
            		17 => "Hangar",
                    18 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    4 => "Thruster",
            		6 => "0:Standard Particle Beam",
            		8 => "Medium Plasma Cannon",
            		12 => "0:Cargo Bay A",
                    18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    4 => "Thruster",
            		6 => "0:Standard Particle Beam",
            		11 => "0:Cargo Bay B",
                    18 => "Structure",
                    20 => "Primary",
            ),
      );
    }
}


?>
