<?php
class kirishiacConqueror extends SixSidedHCV{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 2300;
		$this->faction = "Kirishiac Lords";
        $this->phpclass = "kirishiacConqueror";
        $this->shipClass = "Conqueror";
        $this->imagePath = "img/ships/kirishiacConqueror.png";
        $this->canvasSize = 200;
	    $this->isd = 'Primordial';
        $this->shipSizeClass = 2; 
		$this->factionAge = 4; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
	    $this->notes = 'Atmospheric capable.';
				
        $this->gravitic = true;
		$this->advancedArmor = true;   
        
        $this->forwardDefense = 14;
        $this->sideDefense = 14;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.33;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 2;
		$this->iniativebonus = 8 *5;


        $orbitalHitChart = array( //Orbital Hits sub-chart (d20): 1-6 the mounted weapon, 7-20 the orbital itself
            6 => "Weapon",
            20 => "Orbital"
            );
		
		/*Kirishiac use their own enhancement set */		
		Enhancements::nonstandardEnhancementSet($this, 'KirshiacShip');  

        $this->addPrimarySystem(new CnC(7, 16, 0, 0));
		$scanner = new Scanner(6, 24, 0, 10);
		$scanner->markAdvanced();
		$this->addPrimarySystem($scanner);			
		$this->addPrimarySystem(new Reactor(6, 24, 0, 0));
		$this->addPrimarySystem(new Engine(7, 18, 0, 12, 4));
        $this->addPrimarySystem(new SelfRepair(7, 6, 3)); //armor, structure, output
        $this->addPrimarySystem(new GraviticThruster(7, 20, 0, 7, 3));
        $this->addPrimarySystem(new GraviticThruster(7, 20, 0, 7, 4));
		$this->addPrimarySystem(new JumpEngine(6, 16, 6, 12));

		//Orbitals dock to the FRONT/AFT structure blocks but are DISPLAYED on the left/right
		//sections (ship-window declutter): setStructureHome keeps destruction, docked merge,
		//regeneration and SelfRepair coupled to the home block, and the TAG chart rows find
		//them regardless of section. Call order unchanged - system ids are positional!
		$orbitalB = new KirishiacOrbitalLight(5, 15, 'L', 'B', -7, $orbitalHitChart);
		$beamB = new MedAntigravityBeam(5, 6, 2, 210, 30, 'B');
		$orbitalB->addOrbitalWeapon($beamB);
		$orbitalB->setStructureHome(1); //front block, shown on the left section
		$orbitalB->addTag('ORBITALFWD');
		$this->addLeftFrontSystem($orbitalB);
		$this->addLeftFrontSystem($beamB);

		$orbitalA = new KirishiacOrbitalLight(5, 15, 'C', 'A', -7, $orbitalHitChart);
		$beamA = new MedAntigravityBeam(5, 6, 2, 270, 90, 'A');
		$orbitalA->addOrbitalWeapon($beamA);
		$orbitalA->addTag('ORBITALFWD');
		$this->addFrontSystem($orbitalA);
		$this->addFrontSystem($beamA);

		$orbitalC = new KirishiacOrbitalLight(5, 15, 'R', 'C', -7, $orbitalHitChart);
		$beamC = new MedAntigravityBeam(5, 6, 2, 330, 150, 'C');
		$orbitalC->addOrbitalWeapon($beamC);
		$orbitalC->setStructureHome(1); //front block, shown on the right section
		$orbitalC->addTag('ORBITALFWD');
		$this->addRightFrontSystem($orbitalC);
		$this->addRightFrontSystem($beamC);
		
        $this->addFrontSystem(new UltraMatterCannon(5, 13, 7, 240, 360));
        $this->addFrontSystem(new HypergravitonBeam(6, 20, 12, 300, 60));	
        $this->addFrontSystem(new UltraMatterCannon(5, 13, 7, 300, 60));
        $this->addFrontSystem(new UltraMatterCannon(5, 13, 7, 0, 120));
        $this->addFrontSystem(new GraviticThruster(6, 13, 0, 4, 1));
        $this->addFrontSystem(new GraviticThruster(6, 13, 0, 4, 1));

		$orbitalF = new KirishiacOrbitalLight(5, 15, 'R', 'F', -7, $orbitalHitChart);
		$beamF = new MedAntigravityBeam(5, 6, 2, 150, 330, 'F');
		$orbitalF->addOrbitalWeapon($beamF);
		$orbitalF->setStructureHome(2); //aft block, shown on the left section
		$orbitalF->addTag('ORBITALAFT');
		$this->addLeftAftSystem($orbitalF);
		$this->addLeftAftSystem($beamF);

		$orbitalE = new KirishiacOrbitalLight(5, 15, 'C', 'E', -7, $orbitalHitChart);
		$beamE = new MedAntigravityBeam(5, 6, 2, 90, 270, 'E');
		$orbitalE->addOrbitalWeapon($beamE);
		$orbitalE->addTag('ORBITALAFT');
		$this->addAftSystem($orbitalE);
		$this->addAftSystem($beamE);

		$orbitalD = new KirishiacOrbitalLight(5, 15, 'L', 'D', -7, $orbitalHitChart);
		$beamD = new MedAntigravityBeam(5, 6, 2, 30, 210, 'D');
		$orbitalD->addOrbitalWeapon($beamD);
		$orbitalD->setStructureHome(2); //aft block, shown on the right section
		$orbitalD->addTag('ORBITALAFT');
		$this->addRightAftSystem($orbitalD);
		$this->addRightAftSystem($beamD);

        $this->addAftSystem(new GraviticThruster(6, 13, 0, 4, 2));
        $this->addAftSystem(new GraviticThruster(6, 13, 0, 4, 2));
        $this->addAftSystem(new GraviticThruster(6, 13, 0, 4, 2));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(7, 63));  
        $this->addAftSystem(new Structure(7, 60));
        $this->addPrimarySystem(new Structure(7, 60)); 
	
		$this->hitChart = array(
			0=> array( //PRIMARY
				9 => "Structure",
				11 => "Thruster",
				12 => "Self Repair",
				14 => "Scanner",
				16 => "Engine",
				17 => "Jump Engine",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array( //Fwd
				4 => "Thruster",
				8 => "Hypergraviton Beam",
				10 => "Ultra Matter Cannon",
				12 => "TAG:ORBITALFWD", //tag search is ship-wide: finds orbitals A-C on the left/front/right display sections; beams are only hit through the orbital sub-chart
				18 => "Structure",
				20 => "Primary",
			),
			2=> array( //Aft
				6 => "Thruster",
				8 => "TAG:ORBITALAFT", //orbitals D-F
				18 => "Structure",
				20 => "Primary",
			),
		);
		
    }
}



?>
