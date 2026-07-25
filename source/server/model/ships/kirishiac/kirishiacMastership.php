<?php
class kirishiacMastership extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 2400;
	$this->faction = "Kirishiac Lords";
        $this->phpclass = "kirishiacMastership";
        $this->imagePath = "img/ships/kirishiacMastership.png";
        $this->shipClass = "Mastership";
        $this->shipSizeClass = 3;
        $this->fighters = array("normal"=>54);
        //$this->limited = 33;
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
		$this->iniativebonus = 2*5;

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


		$orbitalB = new KirishiacOrbital(6, 18, 'L', 'B', -7, $orbitalHitChart);
		$beamB = new AntigravityBeam(6, 6, 3, 270, 90, 'B');
		$orbitalB->addOrbitalWeapon($beamB);
		$this->addFrontSystem($orbitalB);
		$this->addFrontSystem($beamB);

		$orbitalA = new KirishiacOrbital(6, 18, 'C', 'A', -7, $orbitalHitChart);
		$augmenterA = new GraviticAugmenter(7, 0, 0, 270, 90, 'A');
		$orbitalA->addOrbitalWeapon($augmenterA);
		$this->addFrontSystem($orbitalA);
		$this->addFrontSystem($augmenterA);        

		$orbitalC = new KirishiacOrbital(6, 18, 'R', 'C', -7, $orbitalHitChart);
		$beamC = new AntigravityBeam(6, 6, 3, 270, 90, 'C');
		$orbitalC->addOrbitalWeapon($beamC);
		$this->addFrontSystem($orbitalC);
		$this->addFrontSystem($beamC);

		$this->addFrontSystem(new Hangar(5, 18, 12));
        $this->addFrontSystem(new GraviticThruster(7, 15, 0, 4, 1));
        $this->addFrontSystem(new GraviticThruster(7, 15, 0, 4, 1));
		

        $this->addAftSystem(new GraviticThruster(7, 15, 0, 4, 2));
        $this->addAftSystem(new GraviticThruster(7, 15, 0, 4, 2));
        $this->addAftSystem(new GraviticThruster(7, 15, 0, 4, 2));



		$orbitalK = new KirishiacOrbital(6, 18, 'R', 'K', -7, $orbitalHitChart);
		$beamK = new AntigravityBeam(6, 6, 3, 90, 270, 'K');
		$orbitalK->addOrbitalWeapon($beamK);
		$this->addAftSystem($orbitalK);
		$this->addAftSystem($beamK);

		$orbitalJ = new KirishiacOrbital(6, 18, 'C', 'J', -7, $orbitalHitChart);
		$augmenterJ = new GraviticAugmenter(7, 0, 0, 90, 270, 'J');
		$orbitalJ->addOrbitalWeapon($augmenterJ);
		$this->addAftSystem($orbitalJ);
		$this->addAftSystem($augmenterJ);

		$orbitalL = new KirishiacOrbital(6, 18, 'L', 'L', -7, $orbitalHitChart);
		$beamL = new AntigravityBeam(6, 6, 3, 90, 270, 'L');
		$orbitalL->addOrbitalWeapon($beamL);
		$this->addAftSystem($orbitalL);
		$this->addAftSystem($beamL);



		$orbitalD = new KirishiacOrbital(6, 18, 'L', 'D', -7, $orbitalHitChart);
		$augmenterD = new GraviticAugmenter(7, 0, 0, 180, 360, 'D');
		$orbitalD->addOrbitalWeapon($augmenterD);
		$this->addLeftSystem($orbitalD);
		$this->addLeftSystem($augmenterD);

		$orbitalE = new KirishiacOrbital(6, 18, 'L', 'E', -7, $orbitalHitChart);
		$beamE = new AntigravityBeam(6, 6, 3, 240, 60, 'E');
		$orbitalE->addOrbitalWeapon($beamE);
		$this->addLeftSystem($orbitalE);
		$this->addLeftSystem($beamE);

		$orbitalF = new KirishiacOrbital(6, 18, 'R', 'F', -7, $orbitalHitChart);
		$beamF = new AntigravityBeam(6, 6, 3, 120, 300, 'F');
		$orbitalF->addOrbitalWeapon($beamF);
		$this->addLeftSystem($orbitalF);
		$this->addLeftSystem($beamF);

		$this->addLeftSystem(new Hangar(5, 18, 12, 5));
        $this->addLeftSystem(new GraviticThruster(7, 25, 0, 7, 3));


		$orbitalG = new KirishiacOrbital(6, 18, 'R', 'G', -7, $orbitalHitChart);
		$augmenterG = new GraviticAugmenter(7, 0, 0, 0, 180, 'G');
		$orbitalG->addOrbitalWeapon($augmenterG);
		$this->addRightSystem($orbitalG);
		$this->addRightSystem($augmenterG);

		$orbitalH = new KirishiacOrbital(6, 18, 'R', 'H', -7, $orbitalHitChart);
		$beamH = new AntigravityBeam(6, 6, 3, 300, 120, 'H');
		$orbitalH->addOrbitalWeapon($beamH);
		$this->addRightSystem($orbitalH);
		$this->addRightSystem($beamH);

		$orbitalI = new KirishiacOrbital(6, 18, 'L', 'I', -7, $orbitalHitChart);
		$beamI = new AntigravityBeam(6, 6, 3, 60, 240, 'I');
		$orbitalI->addOrbitalWeapon($beamI);
		$this->addRightSystem($orbitalI);
		$this->addRightSystem($beamI);

		$this->addRightSystem(new Hangar(5, 18, 12, 1));
        $this->addRightSystem(new GraviticThruster(7, 25, 0, 7, 4));        


        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 8, 72));  
        $this->addAftSystem(new Structure( 8, 72));   
        $this->addLeftSystem(new Structure( 8, 80));  
        $this->addRightSystem(new Structure( 8, 80));  
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
                    9 => "Orbital",
                    11 => "Hangar",
                    18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    7 => "Thruster",
                    11 => "Orbital",
                    18 => "Structure",
                    20 => "Primary",
            ),
            3=> array(
                    4 => "Thruster",
                    9 => "Orbital",
                    11 => "Hangar",
                    18 => "Structure",
                    20 => "Primary",
            ),
            4=> array(
                    4 => "Thruster",
                    9 => "Orbital",
                    11 => "Hangar",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }

}
