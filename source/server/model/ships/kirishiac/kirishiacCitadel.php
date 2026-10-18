<?php
class kirishiacCitadel extends StarBaseSixSections{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 30000;
		$this->faction = "Kirishiac Lords";     
        $this->phpclass = "kirishiacCitadel";
        $this->imagePath = "img/ships/kirishiacCitadel2.png";
        $this->canvasSize = 280;
        $this->shipClass = "Citadel Starbase";
        $this->isd = 'Ancient';
		$this->factionAge = 3; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial	

	    $this->notes = 'Enormous Unit';
	
        $this->Enormous = true;
        
        $this->forwardDefense = 23;
        $this->sideDefense = 23;

		$this->advancedArmor = true;   
		$this->hardAdvancedArmor = true;

        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
		$this->iniativebonus = -200;        

        $this->fighters = array("normal"=>108);

		Enhancements::nonstandardEnhancementSet($this, 'KirshiacShip');

        $orbitalHitChart = array( //Orbital Hits sub-chart (d20): 1-6 whatever is mounted (weapon, ELINT module, or both shield generators sharing 1-3/4-6), 7-20 the orbital itself
            6 => "Weapon",
            20 => "Orbital"
            );

        $heavyOrbitalHitChart = array( //Heavy Orbital Hits sub-chart (d20): 1-6 the mounted weapon, 7-8 the attached Self Repair, 9-20 the orbital itself
            6 => "Weapon",
            8 => "Self Repair",
            20 => "Orbital"
            );

		$this->locations = array(41, 42, 2, 32, 31, 1);
		$this->hitChart = array(			
			0=> array(
				12 => "Structure",
				15 => "Self Repair",
				17 => "Scanner",
				19 => "Reactor",
				20 => "TAG:C&C", //TAG to allow secondary C&C!
			)
		);

		
		/* let's replace this with appropriate two C&Cs!
		$this->addPrimarySystem(new ProtectedCnC(8, 60, 0, 0)); //originally 2 systems with sructure 30, armor 7 each
		*/
		$cnc = new CnC(8, 36, 0, 0);
		$cnc->startArc = 0;
		$cnc->endArc = 360;
        $this->addPrimarySystem($cnc);
		$cnc = new SecondaryCnC(8, 36, 0, 0);//all-around by default
        $this->addPrimarySystem($cnc);
		
		$this->addPrimarySystem(new Reactor(8, 25, 0, 0));
		$scanner1 = new Scanner(8, 36, 4, 10);
		$scanner1->markAdvanced();
		$this->addPrimarySystem($scanner1);			
		$scanner2 = new Scanner(8, 36, 4, 10);
		$scanner2->markAdvanced();
		$this->addPrimarySystem($scanner2);		
		$this->addPrimarySystem(new SelfRepair(8, 42, 20));		

		$this->addPrimarySystem(new Structure( 8, 260));


		for ($i = 0; $i < sizeof($this->locations); $i++){

			$min = (0 + ($i*60)) % 360;
			$max = (120 + ($i*60)) % 360;

			$min2 = (270 + ($i * 60)) % 360; // $min - 90
			$max2 = (210 + ($i * 60)) % 360; // $max + 90			
			
			$struct = Structure::createAsOuter(7, 220, $min, $max);
			$hangar = new Hangar(7, 18, 12);
			$hangar->startArc = $min;
			$hangar->endArc = $max;
			$subReactor = new SubReactorUniversal(7, 30);
			$subReactor->startArc = $min;
			$subReactor->endArc = $max;

		$weapA = new PhasedGraviticTorpedo(6, 16, 6, $min2, $max2, 'A');
		$weapA->setStowedArcs($min, $max); //undeployed (docked) firing arc
        $selfRepairA = new SelfRepair(7, 4, 2); //armor, structure, output
		$hOrbitalA = new KirishiacHeavyOrbital(6, 54, 'C', 'A', 0, $heavyOrbitalHitChart);
		$hOrbitalA->addOrbitalWeapon($weapA);
        $hOrbitalA->addOrbitalSystem($selfRepairA);
		$hOrbitalA->addTag('Heavy Orbital');
		
		$weapB = new HypergravitonBlaster(6, 30, 6, $min2, $max2, 'B');
		$weapB->setStowedArcs($min, $max); //undeployed (docked) firing arc
        $selfRepairB = new SelfRepair(7, 4, 2); //armor, structure, output
		$hOrbitalB = new KirishiacHeavyOrbital(6, 54, 'C', 'B', 0, $heavyOrbitalHitChart);
		$hOrbitalB->addOrbitalWeapon($weapB);
        $hOrbitalB->addOrbitalSystem($selfRepairB);
		$hOrbitalB->addTag('Heavy Orbital');	
		
		$weapC = new HypergravitonBlaster(6, 30, 6, $min2, $max2, 'C');
		$weapC->setStowedArcs($min, $max); //undeployed (docked) firing arc
        $selfRepairC = new SelfRepair(7, 4, 2); //armor, structure, output
		$hOrbitalC = new KirishiacHeavyOrbital(6, 54, 'C', 'C', 0, $heavyOrbitalHitChart);
		$hOrbitalC->addOrbitalWeapon($weapC);
        $hOrbitalC->addOrbitalSystem($selfRepairC);
		$hOrbitalC->addTag('Heavy Orbital');	
		
		
		$beamOrbitalA = new KirishiacOrbital(6, 18, 'C', 'A', 0, $orbitalHitChart);
		$beamA = new AntigravityBeam(6, 6, 3, $min2, $max2, 'A');
		$beamOrbitalA->addOrbitalWeapon($beamA);
		$beamOrbitalA->addTag('Orbital'); //section chart 'TAG:Orbital' (displayName is 'Orbital A' - the tag lookup needs an exact name or a tag)

		$beamOrbitalB = new KirishiacOrbital(6, 18, 'C', 'B', 0, $orbitalHitChart);
		$beamB = new AntigravityBeam(6, 6, 3, $min2, $max2, 'B');
		$beamOrbitalB->addOrbitalWeapon($beamB);
		$beamOrbitalB->addTag('Orbital');

		$beamOrbitalC = new KirishiacOrbital(6, 18, 'C', 'C', 0, $orbitalHitChart);
		$beamC = new AntigravityBeam(6, 6, 3, $min2, $max2, 'C');
		$beamOrbitalC->addOrbitalWeapon($beamC);
		$beamOrbitalC->addTag('Orbital');

		$beamOrbitalD = new KirishiacOrbital(6, 18, 'C', 'D', 0, $orbitalHitChart);
		$beamD = new AntigravityBeam(6, 6, 3, $min2, $max2, 'D');
		$beamOrbitalD->addOrbitalWeapon($beamD);
		$beamOrbitalD->addTag('Orbital');

		$shieldOrbitalE = new KirishiacOrbital(6, 18, 'C', 'E', 0, $orbitalHitChart);
		$shield1 = new KirishiacAdvGravShield(2, 3, 0, 4, $min2, $max2, 'E');
		$shield2 = new KirishiacAdvGravShield(2, 3, 0, 4, $min2, $max2, 'E2'); //second generator named as the refit names it
		$shieldOrbitalE->addOrbitalWeapon($shield1);
		$shieldOrbitalE->addMountedSystem($shield2);
		$shieldOrbitalE->addTag('Orbital');

		$augOrbitalF = new KirishiacOrbital(6, 18, 'C', 'F', 0, $orbitalHitChart);
		$augF = new GraviticAugmenter(7, 12, 7, $min2, $max2, 'F');
		$augOrbitalF->addOrbitalWeapon($augF);
		$augOrbitalF->addTag('Orbital');

		$elintOrbitalG = new KirishiacOrbital(6, 18, 'C', 'G', 0, $orbitalHitChart);
		$elintG = new KirishiacElintModule(6, 12, 4, 4, $min2, $max2, 'G');
		$elintOrbitalG->addOrbitalWeapon($elintG);
		$elintOrbitalG->addTag('Orbital');
		$this->registerElintModule($elintG); //as the SYS_ELINT refit does - without it the module's ELINT is not limited to its arc

			$systems = array(
				$hangar, 
				$subReactor, 
				$weapA,
				$selfRepairA,
				$hOrbitalA,	
				$weapB,
				$selfRepairB,
				$hOrbitalB,	
				$weapC,
				$selfRepairC,
				$hOrbitalC,	
				$beamOrbitalA,
				$beamA,
				$beamOrbitalB,
				$beamB,
				$beamOrbitalC,
				$beamC,
				$beamOrbitalD,
				$beamD,												
				$shieldOrbitalE,
				$shield1,
				$shield2,
				$augOrbitalF,
				$augF,		
				$elintOrbitalG,
				$elintG,		
				$struct 
			);

			$loc = $this->locations[$i];

		
			$this->hitChart[$loc] = array(
				5 => "TAG:Heavy Orbital",
				10 => "TAG:Orbital",
				11 => "TAG:Hangar",
				12 => "TAG:Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			);
			
			foreach ($systems as $system){
				$this->addSystem($system, $loc);
			}
		}
    }
}
?>
