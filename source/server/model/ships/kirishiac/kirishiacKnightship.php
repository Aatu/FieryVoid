<?php
class KirishiacKnightship extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 2700;
		$this->faction = "Kirishiac Lords";
        $this->phpclass = "kirishiacKnightship";
        $this->shipClass = "Knightship";
        $this->imagePath = "img/ships/kirishiacKnightship.png";
        $this->canvasSize = 200;
	    $this->isd = 'Ancient';
        $this->shipSizeClass = 2; 
		$this->factionAge = 3; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
	    //$this->notes = 'Atmospheric capable.';
		$this->variantOf = 'Conqueror';
		$this->occurence = 'rare';		
				
		$this->agile = true;
        $this->gravitic = true;
		$this->advancedArmor = true; 
		$this->hardAdvancedArmor = true;		  
        
        $this->forwardDefense = 14;
        $this->sideDefense = 13;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 2;
		$this->iniativebonus = 8 *5;


        $orbitalHitChart = array( //Orbital Hits sub-chart (d20): 1-6 the mounted weapon, 7-20 the orbital itself
            6 => "Weapon",
            20 => "Orbital"
            );

        $heavyOrbitalHitChart = array( //Heavy Orbital Hits sub-chart (d20): 1-6 the mounted weapon, 7-8 the attached Self Repair, 9-20 the orbital itself
            6 => "Weapon",
            8 => "Self Repair",
            20 => "Orbital"
            );			

        $this->addPrimarySystem(new CnC(7, 12, 0, 0));
		$scanner = new Scanner(6, 20, 0, 12);
		$scanner->markAdvanced();
		$this->addPrimarySystem($scanner);			
		$this->addPrimarySystem(new Reactor(6, 16, 0, 0));
		$this->addPrimarySystem(new Engine(7, 18, 0, 12, 4));
        $this->addPrimarySystem(new SelfRepair(7, 8, 4)); //armor, structure, output
        $this->addPrimarySystem(new GraviticThruster(6, 15, 0, 6, 3));
        $this->addPrimarySystem(new GraviticThruster(6, 15, 0, 6, 4));
		$this->addPrimarySystem(new JumpEngine(6, 20, 8, 9));

		$orbitalA = new KirishiacOrbitalLight(5, 15, 'L', 'A', -7, $orbitalHitChart);
		$beamA = new MedAntigravityBeam(5, 6, 2, 180, 360, 'A');
		$orbitalA->addOrbitalWeapon($beamA);
		$this->addFrontSystem($orbitalA);
		$this->addFrontSystem($beamA);

		$orbitalB = new KirishiacOrbitalLight(5, 15, 'R', 'B', -7, $orbitalHitChart);
		$beamB = new MedAntigravityBeam(5, 6, 2, 0, 180, 'B');
		$orbitalB->addOrbitalWeapon($beamB);
		$this->addFrontSystem($orbitalB);
		$this->addFrontSystem($beamB);

        $this->addFrontSystem(new GraviticThruster(6, 13, 0, 4, 1));
        $this->addFrontSystem(new GraviticThruster(6, 13, 0, 4, 1));

		$orbitalC = new KirishiacOrbitalLight(5, 15, 'R', 'C', -7, $orbitalHitChart);
		$beamC = new MedAntigravityBeam(5, 6, 2, 180, 360, 'C');
		$orbitalC->addOrbitalWeapon($beamC);
		//$orbitalC->addTag('ORBITALAFT');
		$this->addAftSystem($orbitalC);
		$this->addAftSystem($beamC);

		$orbitalD = new KirishiacOrbitalLight(5, 15, 'L', 'D', -7, $orbitalHitChart);
		$beamD = new MedAntigravityBeam(5, 6, 2, 0, 180, 'D');
		$orbitalD->addOrbitalWeapon($beamD);
		//$orbitalC->addTag('ORBITALAFT');
		$this->addAftSystem($orbitalD);
		$this->addAftSystem($beamD);		

        $this->addAftSystem(new GraviticThruster(6, 13, 0, 4, 2));
        $this->addAftSystem(new GraviticThruster(6, 13, 0, 4, 2));
        $this->addAftSystem(new GraviticThruster(6, 13, 0, 4, 2));

		$torpA = new PhasedGraviticTorpedo(6, 16, 6, 180, 360, 'A');
		$torpA->setStowedArcs(300, 360); //undeployed (docked) firing arc
        $selfRepairA = new SelfRepair(7, 4, 2); //armor, structure, output
		$hOrbitalA = new KirishiacHeavyOrbital(6, 42, 'L', 'A', 0, $heavyOrbitalHitChart);
		$hOrbitalA->addOrbitalWeapon($torpA);
        $hOrbitalA->addOrbitalSystem($selfRepairA);
		$hOrbitalA->setStructureHome(0); //primary block, shown on the left section	
		$hOrbitalA->addTag('HVYORBITAL');			
		$this->addLeftSystem($torpA);
		$this->addLeftSystem($hOrbitalA);
		$this->addLeftSystem($selfRepairA);

		$torpB = new PhasedGraviticTorpedo(6, 16, 6, 0, 180, 'B');
		$torpB->setStowedArcs(0, 60); //undeployed (docked) firing arc
        $selfRepairB = new SelfRepair(7, 4, 2); //armor, structure, output
		$hOrbitalB = new KirishiacHeavyOrbital(6, 42, 'R', 'B', 0, $heavyOrbitalHitChart);
		$hOrbitalB->addOrbitalWeapon($torpB);
        $hOrbitalB->addOrbitalSystem($selfRepairB);
		$hOrbitalB->setStructureHome(0); //primary block, shown on the right section
		$hOrbitalB->addTag('HVYORBITAL');						
		$this->addRightSystem($torpB);
		$this->addRightSystem($hOrbitalB);
		$this->addRightSystem($selfRepairB);


        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(7, 63));  
        $this->addAftSystem(new Structure(7, 60));
        $this->addPrimarySystem(new Structure(7, 88)); 
	
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
                7 => "Orbital",
				12 => "TAG:HVYORBITAL", //tag search is ship-wide: finds orbitals A-C on the left/front/right display sections; beams are only hit through the orbital sub-chart
				18 => "Structure",
				20 => "Primary",
			),
			2=> array( //Aft
				4 => "Thruster",
                7 => "Orbital",
				12 => "TAG:HVYORBITAL", //tag search is ship-wide: finds orbitals A-C on the left/front/right display sections; beams are only hit through the orbital sub-chart
				18 => "Structure",
				20 => "Primary",
			),
		);
		
    }
}



?>
