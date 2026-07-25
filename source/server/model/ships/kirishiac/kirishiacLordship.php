<?php
class kirishiacLordship extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 3400;
	$this->faction = "Kirishiac Lords";
        $this->phpclass = "kirishiacLordship";
        $this->imagePath = "img/ships/kirishiacLordship.png";
        $this->shipClass = "Lordship";
        $this->shipSizeClass = 3;
	    $this->isd = "Ancient";
		$this->factionAge = 3; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
	    $this->notes = 'Atmospheric capable.';

        $this->gravitic = true;
		$this->advancedArmor = true;   
		$this->hardAdvancedArmor = true;
		
        $this->forwardDefense = 15;
        $this->sideDefense = 15;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 3;
        $this->rollcost = 4;
        $this->pivotcost = 2;
		$this->iniativebonus = 10;

        $orbitalHitChart = array( //Orbital Hits sub-chart (d20): 1-6 the mounted weapon, 7-20 the orbital itself
            6 => "Weapon",
            20 => "Orbital"
            );

		/*Kirishiac use their own enhancement set */		
		Enhancements::nonstandardEnhancementSet($this, 'KirshiacShip');              

        $this->addPrimarySystem(new Reactor(7, 35, 0, 0));
        $this->addPrimarySystem(new CnC(8, 24, 0, 0));
		$scanner = new Scanner(7, 24, 0, 14);
		$scanner->markAdvanced();
		$this->addPrimarySystem($scanner);			
        $this->addPrimarySystem(new Engine(7, 25, 0, 12, 4));
        $this->addPrimarySystem(new JumpEngine(8, 25, 6, 8));
        $this->addPrimarySystem(new SelfRepair(7, 12, 6)); //armor, structure, output


		$orbitalA = new KirishiacOrbital(6, 18, 'L', 'A', -7, $orbitalHitChart);
		$beamA = new AntigravityBeam(6, 6, 3, 270, 90, 'A');
		$orbitalA->addOrbitalWeapon($beamA);
		$this->addFrontSystem($orbitalA);
		$this->addFrontSystem($beamA);

		$orbitalB = new KirishiacOrbital(6, 18, 'R', 'B', -7, $orbitalHitChart);
		$beamB = new AntigravityBeam(6, 6, 3, 270, 90, 'B');
		$orbitalB->addOrbitalWeapon($beamB);
		$this->addFrontSystem($orbitalB);
		$this->addFrontSystem($beamB);

        $this->addFrontSystem(new GraviticThruster(7, 15, 0, 4, 1));		
        $this->addFrontSystem(new GraviticThruster(7, 15, 0, 4, 1));       
		$this->addFrontSystem(new HypergravitonBlaster(7, 30, 15, 300, 60));

		$orbitalG = new KirishiacOrbital(6, 18, 'R', 'G', -7, $orbitalHitChart);
		$beamG = new AntigravityBeam(6, 6, 3, 90, 270, 'G');
		$orbitalG->addOrbitalWeapon($beamG);
		$this->addAftSystem($orbitalG);
		$this->addAftSystem($beamG);

		$orbitalH = new KirishiacOrbital(6, 18, 'L', 'H', -7, $orbitalHitChart);
		$beamH = new AntigravityBeam(6, 6, 3, 90, 270, 'H');
		$orbitalH->addOrbitalWeapon($beamH);
		$this->addAftSystem($orbitalH);
		$this->addAftSystem($beamH);
        
        $this->addAftSystem(new GraviticThruster(7, 15, 0, 4, 2));	
        $this->addAftSystem(new GraviticThruster(7, 15, 0, 4, 2));
        $this->addAftSystem(new GraviticThruster(7, 15, 0, 4, 2));
       
		$beamC = new AntigravityBeam(6, 6, 3, 120, 300, 'C');
		$orbitalC = new KirishiacOrbital(6, 18, 'R', 'C', -7, $orbitalHitChart);
		$orbitalC->addOrbitalWeapon($beamC);
		$this->addLeftSystem($beamC);
		$this->addLeftSystem($orbitalC);

		$beamD = new AntigravityBeam(6, 6, 3, 240, 60, 'D');
		$orbitalD = new KirishiacOrbital(6, 18, 'L', 'D', -7, $orbitalHitChart);
		$orbitalD->addOrbitalWeapon($beamD);
		$this->addLeftSystem($beamD);
		$this->addLeftSystem($orbitalD);

        $this->addLeftSystem(new GraviticThruster(7, 25, 0, 7, 3));

		$beamE = new AntigravityBeam(6, 6, 3, 60, 240, 'E');
		$orbitalE = new KirishiacOrbital(6, 18, 'L', 'E', -7, $orbitalHitChart);
		$orbitalE->addOrbitalWeapon($beamE);
		$this->addRightSystem($beamE);
		$this->addRightSystem($orbitalE);

        $this->addRightSystem(new GraviticThruster(7, 25, 0, 7, 4));

		$beamF = new AntigravityBeam(6, 6, 3, 300, 120, 'F');
		$orbitalF = new KirishiacOrbital(6, 18, 'R', 'F', -7, $orbitalHitChart);
		$orbitalF->addOrbitalWeapon($beamF);
		$this->addRightSystem($beamF);
		$this->addRightSystem($orbitalF);




        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 8, 72));  
        $this->addAftSystem(new Structure( 8, 72));   
        $this->addLeftSystem(new Structure( 8, 90));  
        $this->addRightSystem(new Structure( 8, 90));  
        $this->addPrimarySystem(new Structure( 8, 72));
        

        $this->hitChart = array(
            0=> array(
                    10 => "Structure",
                    12 => "Self Repair",
                    14 => "Scanner",
                    16 => "Engine",
                    17 => "Jump Engine",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    4 => "Thruster",
                    8 => "Hypergraviton Blaster",
                    10 => "Orbital",
                    18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    8 => "Thruster",
                    10 => "Orbital",
                    18 => "Structure",
                    20 => "Primary",
            ),
            3=> array(
                    6 => "Thruster",
                    8 => "Orbital",
                    18 => "Structure",
                    20 => "Primary",
            ),
            4=> array(
                    6 => "Thruster",
                    8 => "Orbital",
                    18 => "Structure",
                    20 => "Primary",
            ),
            
        );
    }
}
