<?php
class technicalTargetDrone extends VreeCapital
{
/* WARNING: prone to change!*/

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);
		$this->pointCost = 5;
		$this->faction = "Custom Ships";
		$this->phpclass = "technicalTargetDrone";
		$this->imagePath = "img/ships/kirishiacLordship2.png";
		$this->canvasSize = 200;
		$this->shipClass = "Target Drone - DO NOT USE";
		$this->shipSizeClass = 3;
		$this->forwardDefense = 20;
		$this->sideDefense = 20;
        $this->isd = 0;
		
		$this->fighters = array("light"=>12);
		$this->turncost = 0.5;
		$this->turndelaycost = 0.5;
		$this->accelcost = 1;
		$this->rollcost = 1;
		$this->pivotcost = 1;
		
        $this->gravitic = true;  

		$this->notes = "DO NOT USE, prone to change!";

		$this->VreeHitLocations = false;

		$this->addPrimarySystem(new Reactor(6, 18, 0, 0));
		$this->addPrimarySystem(new Hangar(6, 1));
		$this->addPrimarySystem(new CnC(6, 12, 0, 0));
		$this->addPrimarySystem(new Scanner(6, 14, 9, 10));
        $this->addPrimarySystem(new Engine(6, 16, 0, 16, 2));
		$this->addPrimarySystem(new JumpEngine(6, 10, 5, 24));	

		$this->addPrimarySystem(new GraviticThruster(5, 16, 0, 9, 3));	
        $this->addPrimarySystem(new GraviticThruster(5, 16, 0, 9, 1)); 
        $this->addPrimarySystem(new GraviticThruster(5, 16, 0, 9, 2));  
		$this->addPrimarySystem(new GraviticThruster(5, 16, 0, 9, 4));	

//        $this->addFrontSystem(new AncientMatterGun(5, 10, 5, 300, 60));	
//        $this->addFrontSystem(new AncientPlasmaGun(5, 10, 5, 300, 60));	
//        $this->addFrontSystem(new AncientParticleGun(5, 10, 5, 300, 60));	
//        $this->addFrontSystem(new AncientParticleCannon(5, 10, 5, 300, 60));	
//        $this->addFrontSystem(new AncientAntimatter(5, 10, 5, 300, 60));	
//        $this->addFrontSystem(new AncientIonTorpedo(5, 10, 5, 300, 60));	
//        $this->addFrontSystem(new AncientBurstBeam(5, 10, 5, 300, 60));	
//        $this->addFrontSystem(new AncientMolecularDisruptor(5, 10, 5, 300, 60));	
//        $this->addFrontSystem(new AncientShockCannon(5, 10, 5, 300, 60));	
//        $this->addFrontSystem(new AncientParticleCutter(5, 10, 5, 300, 60));	
//        $this->addFrontSystem(new AncientPlasmaArc(5, 10, 5, 300, 60));	
        $this->addFrontSystem(new PlasmaWaveTorpedo(4, 7, 4, 300, 60));
        $this->addFrontSystem(new MolecularSlicerBeamL(5, 0, 0, 300, 60));	

		$structArmor = 0;
		$structHP = 50;
		
		$struct = new Structure( $structArmor, $structHP, true);
		$struct->addTag("Outer Structure");
		$struct->startArc = 300;
		$struct->endArc = 60;
        $this->addFrontSystem($struct);
		
		$struct = new Structure( $structArmor, $structHP, true);
		$struct->addTag("Outer Structure");
		$struct->startArc = 120;
		$struct->endArc = 240;
        $this->addAftSystem($struct);
		
		$struct = new Structure( $structArmor, $structHP, true);
		$struct->addTag("Outer Structure");
		$struct->startArc = 240;
		$struct->endArc = 0;
        $this->addLeftFrontSystem($struct);
		
		$struct = new Structure( $structArmor, $structHP, true);
		$struct->addTag("Outer Structure");
		$struct->startArc = 180;
		$struct->endArc = 300;
        $this->addLeftAftSystem($struct);
		
		$struct = new Structure( $structArmor, $structHP, true);
		$struct->addTag("Outer Structure");
		$struct->startArc = 0;
		$struct->endArc = 120;
        $this->addRightFrontSystem($struct);
		
		$struct = new Structure( $structArmor, $structHP, true);
		$struct->addTag("Outer Structure");
		$struct->startArc = 60;
		$struct->endArc = 180;
        $this->addRightAftSystem($struct);  
		
        $this->addPrimarySystem(new Structure( 6, 44));
	    
		$this->hitChart = array(
            0=> array(
                    9 => "Structure",
                    10 => "Jump Engine",
                    12 => "Scanner",
                    15 => "Engine",
                    16 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
           		 ),
            1=> array(
                    15 => "TAG:Weapon",
                    20 => "Structure",
           		 ),
            2=> array(
                    15 => "TAG:Weapon",
                    20 => "Structure",
           		 ),
            31=> array(
                    15 => "TAG:Weapon",
                    20 => "Structure",
           		 ),
            32=> array(
                    15 => "TAG:Weapon",
                    20 => "Structure",
           		 ),
            41=> array(
                    15 => "TAG:Weapon",
                    20 => "Structure",
           		 ),
       		42=> array(
                    15 => "TAG:Weapon",
                    20 => "Structure",
           		 ),
           	);
		}
		
		/*
	//d20 hit chart
        $this->hitChart = array(
            0=> array(
                    9 => "Structure",
                    10 => "Jump Engine",
                    12 => "Scanner",
                    15 => "Engine",
                    16 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
           		 ),
            1=> array(
                    4 => "TAG:Thruster", 
                    8 => "TAG:Weapon",                    
                    17 => "Structure",
                    20 => "Primary",
           		 ),
            2=> array(
                    4 => "TAG:Thruster", 
                    8 => "TAG:Weapon",                    
                    17 => "Structure",
                    20 => "Primary",
           		 ),
            31=> array(
                    4 => "TAG:Thruster", 
                    8 => "TAG:Weapon",                    
                    17 => "Structure",
                    20 => "Primary",
           		 ),
            32=> array(
                    4 => "TAG:Thruster", 
                    8 => "TAG:Weapon",                    
                    17 => "Structure",
                    20 => "Primary",
           		 ),
            41=> array(
                    4 => "TAG:Thruster", 
                    8 => "TAG:Weapon",                    
                    17 => "Structure",
                    20 => "Primary",
           		 ),
       		42=> array(
                    4 => "TAG:Thruster", 
                    8 => "TAG:Weapon",                    
                    17 => "Structure",
                    20 => "Primary",
           		 ),
           	);
		}
*/		
	}
		
?>		
