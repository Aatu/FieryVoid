<?php
class ZShadowXonn extends VreeCapital{
  /*custom ship, put into FV as a prize for tournament winner (LordBolton/MrMordensGhost) */

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 2750;
        $this->faction = "Custom Ships";
		$this->phpclass = "ZShadowXonn";
		$this->shipClass = "Shadow Xonn Dreadnought";
		$this->isd = 'not known';
        $this->limited = 10; //Restricted Deployment
	    $this->unofficial = true;
      $this->isd = 2261;
		
        $this->enhancementOptionsDisabled[] = 'SHAD_DIFF'; //no diffuser upgrades for Young ships - they don't have know how to tamper with Shadow systems to that extent!
		$this->advancedArmor = true;   

		$this->shipSizeClass = 3;
		$this->iniativebonus = 0;
		
        $this->turncost = 1.5;
        $this->turndelaycost = 1;
        $this->accelcost = 5;
        $this->rollcost = 6;
        $this->pivotcost = 0;	
        $this->gravitic = true;        	

		$this->forwardDefense = 16;
		$this->sideDefense = 16;

		$this->imagePath = "img/ships/XonnShadow.png";
		$this->canvasSize = 240;

		$this->addPrimarySystem(new Reactor(6, 25, 0, 0));
		$this->addPrimarySystem(new Hangar(6, 3, 3));
		$this->addPrimarySystem(new CnC(7, 16, 0, 0));
		$scanner = new Scanner(6, 18, 9, 10);   
		$scanner->markImproved();
        $this->addPrimarySystem($scanner);
        $this->addPrimarySystem(new Engine(6, 18, 0, 10, 3));
		$this->addPrimarySystem(new JumpEngine(7, 16, 6, 24));
		//Turret mounts: the two weapons sharing a turret have their fire linked, so their targets must
		//be within 60 degrees of each other (they are 360-degree mounts, so weapon arcs can't express
		//this). The group tag doubles as the turret's display name shown in the system window.
		//Turret 1 = shredder + cannon, Turret 2 = shredder + cannon; the third cannon is unturreted.
		//setArcRestriction: every primary-mounted Vree weapon can JAM. Whenever it is damaged it rolls
		//a separate d20 (17+) and locks to the forward 330..30 - and because a turret is one mount,
		//a jam on either linked weapon restricts both. See Weapon::testArcRestriction.
		$turretShredderA = new AntimatterShredder(4, 0, 0, 0, 360);
		$turretShredderA->linkedFiringGroup = 'Turret 1';
		$turretShredderA->linkedFiringSpread = 60;
		$turretShredderA->setArcRestriction(330, 30);
		$this->addPrimarySystem($turretShredderA);

		$turretShredderB = new AntimatterShredder(4, 0, 0, 0, 360);
		$turretShredderB->linkedFiringGroup = 'Turret 2';
		$turretShredderB->linkedFiringSpread = 60;
		$turretShredderB->setArcRestriction(330, 30);

		$turretCannonA = new MolecularSlicerBeamL(4, 0, 0, 0, 360);
		$turretCannonA->repairPriority = 6; //pool with heavy guns, let light ones take damage first
		$turretCannonA->linkedFiringGroup = 'Turret 1';
		$turretCannonA->linkedFiringSpread = 60;
		$turretCannonA->setArcRestriction(330, 30);		
        $this->addPrimarySystem($turretCannonA);

		$cannon = new MolecularSlicerBeamL(4, 0, 0, 0, 360);
		$cannon->repairPriority = 6; //pool with heavy guns, let light ones take damage first
		$cannon->setArcRestriction(330, 30);
		$this->addPrimarySystem($cannon);		

		$turretCannonB = new MultiphasedCutter(4, 0, 0, 0, 360);
		$turretCannonB->repairPriority = 6; //pool with heavy guns, let light ones take damage first
		$turretCannonB->linkedFiringGroup = 'Turret 2';
		$turretCannonB->linkedFiringSpread = 60;
		$turretCannonB->setArcRestriction(330, 30);
		$this->addPrimarySystem($turretCannonB);		
    						

     
        $diffuser = new EnergyDiffuser(4, 13, 3, 300, 60);//($armour, $maxhealth, $dissipation, $startArc, $endArc)
          $tendril=new DiffuserTendril(12,'L');//absorbtion capacity,side
          $diffuser->addTendril($tendril);
          $this->addFrontSystem($tendril);
          $tendril=new DiffuserTendril(12,'R');//absorbtion capacity,side
          $diffuser->addTendril($tendril);
          $this->addFrontSystem($tendril);
        $this->addFrontSystem($diffuser);	

        $thrust = new GraviticThruster(5, 20, 0, 10, 1);
		$thrust->startArc = 300;
		$thrust->endArc = 60;
		$this->addFrontSystem($thrust);
		$weapon = new MultiphasedCutterL(3, 0, 0, 300, 60);
		$weapon->addTag("Weapon");
		$this->addFrontSystem($weapon);	
		$weapon = new MultiphasedCutterL(3, 0, 0, 300, 60);
		$weapon->addTag("Weapon");
		$this->addFrontSystem($weapon);	
		 
    
        $diffuser = new EnergyDiffuser(4, 13, 3, 120, 240);//($armour, $maxhealth, $dissipation, $startArc, $endArc)
          $tendril=new DiffuserTendril(12,'L');//absorbtion capacity,side
          $diffuser->addTendril($tendril);
          $this->addAftSystem($tendril);
          $tendril=new DiffuserTendril(12,'R');//absorbtion capacity,side
          $diffuser->addTendril($tendril);
          $this->addAftSystem($tendril);
        $this->addAftSystem($diffuser);		
        $thrust = new GraviticThruster(5, 20, 0, 10, 2);
		$thrust->startArc = 120;
		$thrust->endArc = 240;
		$this->addAftSystem($thrust);   
		$weapon = new MultiphasedCutterL(3, 0, 0, 120, 240);
		$weapon->addTag("Weapon");
		$this->addAftSystem($weapon);	
		$weapon = new MultiphasedCutterL(3, 0, 0, 120, 240);
		$weapon->addTag("Weapon");
		$this->addAftSystem($weapon);	
    
    
        $diffuser = new EnergyDiffuser(4, 13, 3, 240, 360);//($armour, $maxhealth, $dissipation, $startArc, $endArc)
          $tendril=new DiffuserTendril(12,'L');//absorbtion capacity,side
          $diffuser->addTendril($tendril);
          $this->addLeftFrontSystem($tendril);
          $tendril=new DiffuserTendril(12,'L');//absorbtion capacity,side
          $diffuser->addTendril($tendril);
          $this->addLeftFrontSystem($tendril);
        $this->addLeftFrontSystem($diffuser);	
		$weapon = new MultiphasedCutterL(3, 0, 0, 240, 360);
		$weapon->addTag("Weapon");
		$this->addLeftFrontSystem($weapon);	
		$weapon = new MultiphasedCutterL(3, 0, 0, 240, 360);
		$weapon->addTag("Weapon");
		$this->addLeftFrontSystem($weapon);	
		
				
				
        $diffuser = new EnergyDiffuser(4, 13, 3, 180, 300);//($armour, $maxhealth, $dissipation, $startArc, $endArc)
          $tendril=new DiffuserTendril(12,'L');//absorbtion capacity,side
          $diffuser->addTendril($tendril);
          $this->addLeftAftSystem($tendril);
          $tendril=new DiffuserTendril(12,'L');//absorbtion capacity,side
          $diffuser->addTendril($tendril);
          $this->addLeftAftSystem($tendril);
        $this->addLeftAftSystem($diffuser);	

		$thrust = new GraviticThruster(5, 20, 0, 10, 3);
		$thrust->startArc = 240;
		$thrust->endArc = 300;
		$thrust->overkillArcStructures = array(31, 32); //overkill spills to whichever Port quarter is in arc
		$this->addLeftSystem($thrust);
		$weapon = new MultiphasedCutterL(3, 0, 0, 180, 300);
		//$weapon->addTag("Weapon");
		$this->addLeftAftSystem($weapon);
		$weapon = new MultiphasedCutterL(3, 0, 0, 180, 300);
		//$weapon->addTag("Weapon");
		$this->addLeftAftSystem($weapon);	
    
        $diffuser = new EnergyDiffuser(4, 13, 3, 0, 120);//($armour, $maxhealth, $dissipation, $startArc, $endArc)
          $tendril=new DiffuserTendril(12,'R');//absorbtion capacity,side
          $diffuser->addTendril($tendril);
          $this->addRightFrontSystem($tendril);
          $tendril=new DiffuserTendril(12,'R');//absorbtion capacity,side
          $diffuser->addTendril($tendril);
          $this->addRightFrontSystem($tendril);
        $this->addRightFrontSystem($diffuser);	
		
		$weapon = new MultiphasedCutterL(3, 0, 0, 0, 120);
		//$weapon->addTag("Weapon");
		$this->addRightFrontSystem($weapon);
		$weapon = new MultiphasedCutterL(3, 0, 0, 0, 120);
		//$weapon->addTag("Weapon");
		$this->addRightFrontSystem($weapon);
				
				
        $diffuser = new EnergyDiffuser(4, 13, 3, 60, 180);//($armour, $maxhealth, $dissipation, $startArc, $endArc)
          $tendril=new DiffuserTendril(12,'R');//absorbtion capacity,side
          $diffuser->addTendril($tendril);
          $this->addRightAftSystem($tendril);
          $tendril=new DiffuserTendril(12,'R');//absorbtion capacity,side
          $diffuser->addTendril($tendril);
          $this->addRightAftSystem($tendril);
        $this->addRightAftSystem($diffuser);	
		$thrust = new GraviticThruster(5, 20, 0, 10, 4);
		$thrust->startArc = 60;
		$thrust->endArc = 120;
		$thrust->overkillArcStructures = array(41, 42); //overkill spills to whichever Stbd quarter is in arc
		$this->addRightSystem($thrust);		
		$weapon = new MultiphasedCutterL(3, 0, 0, 60, 180);
		//$weapon->addTag("Weapon");
		$this->addRightAftSystem($weapon);
		$weapon = new MultiphasedCutterL(3, 0, 0, 60, 180);
		//$weapon->addTag("Weapon");
		$this->addRightAftSystem($weapon);
		
	
		$structArmor = 5;
		$structHP = 36;
		
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
	
        $this->addPrimarySystem(new Structure( 6, 60));
	    
	//d20 hit chart
        $this->hitChart = array(

            0=> array(
                    9 => "Structure",
                    10 => "Jump Engine",
                    13 => "Scanner",
                    15 => "Engine",
                    16 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
           		 ),
            1=> array(
                    4 => "TAG:Thruster",                  
                    9 => "TAG:Weapon",
                    10 => "Energy Diffuser",
                    17 => "Structure",
                    20 => "Primary",
           		 ),
            2=> array(
                    4 => "TAG:Thruster",                  
                    9 => "TAG:Weapon",
                    10 => "Energy Diffuser",
                    17 => "Structure",
                    20 => "Primary",
           		 ),
            31=> array(
                    4 => "TAG:Thruster",                  
                    9 => "TAG:Weapon",
                    10 => "Energy Diffuser",
                    17 => "Structure",
                    20 => "Primary",
           		 ),
            32=> array(
                    4 => "TAG:Thruster",                  
                    9 => "TAG:Weapon",
                    10 => "Energy Diffuser",
                    17 => "Structure",
                    20 => "Primary",
           		 ),
            41=> array(
                    4 => "TAG:Thruster",                  
                    9 => "TAG:Weapon",
                    10 => "Energy Diffuser",
                    17 => "Structure",
                    20 => "Primary",
           		 ),
       		42=> array(
                    4 => "TAG:Thruster",                  
                    9 => "TAG:Weapon",
                    10 => "Energy Diffuser",
                    17 => "Structure",
                    20 => "Primary",
           		 ),
           	);       		
	}
	
	
	
	/* remade for Tags!
        $this->hitChart = array(

            0=> array(
                    9 => "Structure",
                    10 => "Jump Engine",
                    13 => "Scanner",
                    15 => "Engine",
                    16 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
           		 ),
            1=> array(
                    4 => "Thruster",
                    6 => "Light Multiphased Cutter",
                    7 => "0:Antimatter Shredder",                    
                    8 => "0:Light Slicer Beam",                  
                    9 => "0:Multiphased Cutter",
                    11 => "Energy Diffuser",
                    17 => "Structure",
                    20 => "Primary",
           		 ),
            2=> array(
                    4 => "Thruster",
                    6 => "Light Multiphased Cutter",
                    7 => "0:Antimatter Shredder",                    
                    8 => "0:Light Slicer Beam",                  
                    9 => "0:Multiphased Cutter",
                    11 => "Energy Diffuser",
                    17 => "Structure",
                    20 => "Primary",
           		 ),
            31=> array(
                    4 => "32:Thruster",
                    6 => "Light Multiphased Cutter",
                    7 => "0:Antimatter Shredder",                    
                    8 => "0:Light Slicer Beam",                  
                    9 => "0:Multiphased Cutter",
                    11 => "Energy Diffuser",
                    17 => "Structure",
                    20 => "Primary",
           		 ),
            32=> array(
                    4 => "Thruster",
                    6 => "Light Multiphased Cutter",
                    7 => "0:Antimatter Shredder",                    
                    8 => "0:Light Slicer Beam",                  
                    9 => "0:Multiphased Cutter",
                    11 => "Energy Diffuser",
                    17 => "Structure",
                    20 => "Primary",
           		 ),
            41=> array(
                    4 => "42:Thruster",
                    6 => "Light Multiphased Cutter",
                    7 => "0:Antimatter Shredder",                    
                    8 => "0:Light Slicer Beam",                  
                    9 => "0:Multiphased Cutter",
                    11 => "Energy Diffuser",
                    17 => "Structure",
                    20 => "Primary",
           		 ),
       		42=> array(
                    4 => "Thruster",
                    6 => "Light Multiphased Cutter",
                    7 => "0:Antimatter Shredder",                    
                    8 => "0:Light Slicer Beam",                  
                    9 => "0:Multiphased Cutter",
                    11 => "Energy Diffuser",
                    17 => "Structure",
                    20 => "Primary",
           		 ),
           	);
       		
		}
		*/
	}
		
?>		
