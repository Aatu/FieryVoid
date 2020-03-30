<?php
class Lahas extends BaseShip{
    
    function __construct($id, $userid, $name,  $movement){
        parent::__construct($id, $userid, $name,  $movement);
        
		$this->pointCost = 765;
		$this->faction = "Balosian";
        $this->phpclass = "Lahas";
        $this->imagePath = "img/ships/brahassa.png";
        $this->shipClass = "Lahas Command Cruiser";
        $this->shipSizeClass = 3;
        $this->variantOf = "Brahassa Cruiser";
        $this->occurence = "uncommon";
        $this->limited = 33;
        $this->fighters = array("normal"=>12);
		$this->isd = 2250;
        
        $this->forwardDefense = 15;
        $this->sideDefense = 17;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 3;
         
        $this->addPrimarySystem(new Reactor(5, 25, 0, 0));
        $this->addPrimarySystem(new CnC(5, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 12, 4, 7));
		$this->addPrimarySystem(new Engine (5, 15, 0, 10, 3));
		$this->addPrimarySystem(new Hangar(5, 14));
		//$this->addPrimarySystem(new Hangar(5, 14)); SCS shows two hangars, but capacity is listed as for one - it seems that Jump Drive was intended to take place of the second hangar
        $this->addPrimarySystem(new JumpEngine(5, 10, 3, 36));

       	$this->addFrontSystem(new StdParticleBeam(3, 4, 1, 240, 60));
       	$this->addFrontSystem(new StdParticleBeam(3, 4, 1, 300, 120));
       	$this->addFrontSystem(new IonCannon(4, 6, 4, 300, 60));
       	$this->addFrontSystem(new IonCannon(4, 6, 4, 300, 60));
		$this->addFrontSystem(new Thruster(4, 8, 0, 4, 1));
		$this->addFrontSystem(new Thruster(4, 8, 0, 4, 1));
		
		$this->addAftSystem(new StdParticleBeam(3, 4, 1, 120, 0));
       	$this->addAftSystem(new StdParticleBeam(3, 4, 1, 0, 240));
       	$this->addAftSystem(new IonCannon(4, 6, 4, 120, 240));
		$this->addAftSystem(new Thruster(3, 15, 0, 5, 2));
		$this->addAftSystem(new Thruster(3, 15, 0, 5, 2));
	
		$this->addLeftSystem(new StdParticleBeam(3, 4, 1, 240, 0));
		$this->addLeftSystem(new StdParticleBeam(3, 4, 1, 240, 0));
		$this->addLeftSystem(new IonCannon(4, 6, 4, 240, 0));
		$this->addLeftSystem(new IonCannon(4, 6, 4, 240, 0));
		$this->addLeftSystem(new Thruster(3, 10, 0, 4, 3));

		$this->addRightSystem(new StdParticleBeam(3, 4, 1, 0, 120));
		$this->addRightSystem(new StdParticleBeam(3, 4, 1, 0, 120));
		$this->addRightSystem(new IonCannon(4, 6, 4, 0, 120));
		$this->addRightSystem(new IonCannon(4, 6, 4, 0, 120));
		$this->addRightSystem(new Thruster(3, 10, 0, 4, 4));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(5, 60));
        $this->addAftSystem(new Structure(4, 52));
        $this->addLeftSystem(new Structure(4, 39));
        $this->addRightSystem(new Structure(4, 39));
        $this->addPrimarySystem(new Structure(5, 60));
		
		$this->hitChart = array(
            0=> array(
                    8 => "Structure",
                    10 => "Scanner",
                    13 => "Engine",
					15 => "Jump Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    4 => "Thruster",
                    7 => "Standard Particle Beam",
					10 => "Ion Cannon",
                    18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    6 => "Thruster",
					9 => "Standard Particle Beam",
					11 => "Ion Cannon",
                    18 => "Structure",
                    20 => "Primary",
            ),
            3=> array(
                    4 => "Thruster",
					7 => "Standard Particle Beam",
					10 => "Ion Cannon",
                    18 => "Structure",
                    20 => "Primary",
            ),
            4=> array(
                    4 => "Thruster",
					7 => "Standard Particle Beam",
					10 => "Ion Cannon",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>
