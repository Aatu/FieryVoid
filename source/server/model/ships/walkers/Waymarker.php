<?php
class Waymarker extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 2575;
		$this->faction = "Walkers of Sigma-957";
        $this->phpclass = "Waymarker";
        $this->shipClass = "Waymarker";
        $this->imagePath = "img/ships/WalkerWaymarker.png";
        $this->canvasSize = 180;
	    $this->isd = 'Ancient';
		$this->factionAge = 3; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
		$this->variantOf = "NONE";
				
        $this->gravitic = true;
		$this->advancedArmor = true;  
        
        $this->forwardDefense = 16;
        $this->sideDefense = 13;
        
        $this->turncost = 1.33;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 1;
        $this->pivotcost = 5;
		$this->iniativebonus = 10 *5;

		$this->fighters = array("Mapmaker Probes"=>18);

		/*Walkers will use their own enhancement set */		
		Enhancements::nonstandardEnhancementSet($this, 'WalkerShip');
		
         
		$this->addPrimarySystem(new Reactor(7, 20, 0, 0));//armor, structure, power req, output
        $this->addPrimarySystem(new CnC(7, 16, 0, 0));
		$scanner = new Scanner(7, 20, 0, 13);
		$scanner->markAdvanced();
		$this->addPrimarySystem($scanner);			
		$this->addPrimarySystem(new Engine(7, 20, 0, 12, 4));
        $this->addPrimarySystem(new SelfRepair(6, 15, 5)); //armor, structure, output
		$this->addPrimarySystem(new JumpEngine(7, 15, 8, 6));
		//STAGE 7: Energy Draining Net. Args are (armour, maxhealth, powerReq);
		//0 for health/power takes the CONTROL SHEET values in baseSystems.php - health 12, power 4.
		//⚠️ ONE Net alone can only field its own hex: linking needs a SECOND Net within 3 hexes,
		//which for a test means two Travelers on the same team (or a second Net on this hull).
		$this->addPrimarySystem(new EnergyDrainingNet(6, 0, 0));
		$this->addPrimarySystem(new GraviticThruster(6, 20, 0, 6, 3));
		$this->addPrimarySystem(new GraviticThruster(6, 20, 0, 6, 4));		
		
		
        $this->addFrontSystem(new GraviticThruster(6, 15, 0, 4, 1));
        $this->addFrontSystem(new GraviticThruster(6, 15, 0, 4, 1));			       
		//STAGE 2: Lightning Array. Args are (armour, maxhealth, powerReq, startArc, endArc);
		//0 for health/power takes the class defaults, which are PLACEHOLDER values in specialWeapons.php.
		$this->addFrontSystem(new MediumLightningArray(6, 0, 0, 300, 60));
		//STAGE 3: Chromatic Pulse Driver. Args are (armour, maxhealth, powerReq, startArc, endArc);
		//0 for health/power takes the class defaults, which are PLACEHOLDER values in pulse.php.
		//STAGE 6: Energy Draining Mine. Args are (armour, maxhealth, powerReq, startArc, endArc);
		//0 for health/power takes the class defaults, which are PLACEHOLDER values in AoE.php.
		//⚠️ 0..360 rather than 0..0 on purpose - a system with both arcs at zero has its SECTION's
		//arc stamped onto it by addSystem() (arch_addsystem_section_arc_trap), and the probe is
		//launched in any direction.
		$this->addFrontSystem(new EnergyDrainingMine(6, 0, 0, 240, 360));	
		$this->addFrontSystem(new EnergyDrainingMine(6, 0, 0, 0, 120));				
		$this->addFrontSystem(new ChromaticPulseDriver(6, 0, 0, 240, 360));
		$this->addFrontSystem(new ChromaticPulseDriver(6, 0, 0, 0, 120));		


		$this->addAftSystem(new MediumLightningArray(6, 0, 0, 120, 240));
		$this->addAftSystem(new ChromaticPulseDriver(6, 0, 0, 120, 240));			
		$this->addAftSystem(new GraviticThruster(6, 15, 0, 6, 2));
        $this->addAftSystem(new Hangar(6, 18, 6));
		$this->addAftSystem(new GraviticThruster(6, 15, 0, 6, 2));

				

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 7, 60));
        $this->addAftSystem(new Structure( 7, 56));
        $this->addPrimarySystem(new Structure( 7, 60 ));
		
	
		$this->hitChart = array(
			0=> array( //PRIMARY
				7 => "Structure",
				9 => "Thruster",				
				11 => "Jump Engine",
				12 => "Energy Draining Net",			
				13 => "Self Repair",
				15 => "Scanner",                
				17 => "Engine",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array( //Fwd
				3 => "Thruster",
				6 => "Medium Lightning Array",
				8 => "Energy Draining Mine",
				//10 => "EW Detector",								
				12=> "Chromatic Pulse Driver",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array( //Aft
				4 => "Thruster",
				7 => "Medium Lightning Array",
				9=> "Chromatic Pulse Driver",				
				11 => "Hangar",
				18 => "Structure",
				20 => "Primary",
			),
		);
		
    }
}



?>
