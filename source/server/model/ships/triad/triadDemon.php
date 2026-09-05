<?php
class triadDemon extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 4550;
		$this->faction = "The Triad";
        $this->phpclass = "triadDemon";
        $this->shipClass = "Chaos: Demon";
        $this->imagePath = "img/ships/triadDemon.png";
        $this->canvasSize = 200;
	    $this->isd = 'Primordial';
        $this->shipSizeClass = 3; 
		$this->factionAge = 4; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
        $this->agile = true;
				
        $this->gravitic = true;
		$this->advancedArmor = true;  
		$this->skinDancer = true; 	
        
        $this->forwardDefense = 16;
        $this->sideDefense = 17;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 4;
		$this->iniativebonus = 3 *5;

		$this->notes = 'Triad Capital Ship'; 
		$this->notes .= '<br>Atmospheric Capable'; 

		$this->fighters = array("Triad Fighter"=>12);

		$this->addPrimarySystem(new Reactor(8, 30, 0, 0));//armor, structure, power req, output
        $this->addPrimarySystem(new CnC(8, 24, 0, 0));
//		$scanner = new Scanner(8, 24, 0, 15);
//			$scanner->markAdvanced();
//			$this->addPrimarySystem($scanner);			
        $scanner = new ElintScanner(8, 24, 0, 14);
			$scanner->markMindrider();
			$this->addPrimarySystem($scanner);	        
		$this->addPrimarySystem(new Engine(8, 25, 0, 15, 3));
//        $this->addPrimarySystem(new StructureSelfRepair(8, 24, 24)); //armor, structure, output
        $this->addPrimarySystem(new CoopStructureSelfRepair(8, 20, 21)); //armor, structure, output

		/*Triad use their own enhancement set */		
		Enhancements::nonstandardEnhancementSet($this, 'TriadShip');
 		
        $this->addFrontSystem(new GraviticThruster(7, 13, 0, 5, 1));		
        $this->addFrontSystem(new GraviticThruster(7, 13, 0, 5, 1));		
        $this->addFrontSystem(new GraviticThruster(7, 13, 0, 5, 1));		
        $this->addFrontSystem(new PlasmaDriver(7, 6, 6, 300, 60));	
        $this->addFrontSystem(new AntimatterSlicer(6, 12, 4, 270, 90));	
        $this->addFrontSystem(new SingularityMine(4, 28, 16, 300, 60));	
        $this->addFrontSystem(new AntimatterWave(4, 20, 8, 300, 60));	
        $this->addFrontSystem(new AntimatterSlicer(6, 12, 4, 270, 90));	
        $this->addFrontSystem(new PlasmaDriver(7, 6, 6, 300, 60));	

		$this->addAftSystem(new GraviticThruster(7, 18, 0, 5, 2));
		$this->addAftSystem(new GraviticThruster(7, 18, 0, 5, 2));
		$this->addAftSystem(new GraviticThruster(7, 18, 0, 5, 2));
		$this->addAftSystem(new JumpEngine(8, 25, 5, 8));        
        $this->addAftSystem(new SelfRepair(8, 15, 7)); //armor, structure, output

        $this->addLeftSystem(new GraviticThruster(7, 25, 0, 8, 3)); 		
        $this->addLeftSystem(new PlasmaDriver(7, 6, 6, 240, 360));	
        $this->addLeftSystem(new PlasmaDriver(7, 6, 6, 240, 360));	

        $this->addRightSystem(new GraviticThruster(7, 25, 0, 8, 4)); 				
        $this->addRightSystem(new PlasmaDriver(7, 6, 6, 0, 120));	
        $this->addRightSystem(new PlasmaDriver(7, 6, 6, 0, 120));	

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 7, 120));
        $this->addAftSystem(new Structure( 8, 90));
        $this->addLeftSystem(new Structure( 7, 64));
        $this->addRightSystem(new Structure( 7, 64));
        $this->addPrimarySystem(new Structure( 8, 60));
	
		$this->hitChart = array(
			0=> array( //PRIMARY
				12 => "Structure",
				13 => "Cooperative Structure Self Repair",
				15 => "ELINT Scanner",                
				17 => "Engine",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array( //Fwd
				4 => "Thruster",
				6 => "Singularity Mine", 
				8 => "Antimatter Wave",
				10 => "Antimatter Slicer",
				11 => "Plasma Driver",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array( //Aft
				6 => "Thruster",
				8 => "Self Repair",
				10 => "Jump Engine",
				18 => "Structure",  				
				20 => "Primary",
			),
			3=> array( //Port
				6 => "Thruster",
				9 => "Plasma Driver", 
				18 => "Structure",
				20 => "Primary",
			),
			4=> array( //Stbd
				6 => "Thruster",
				9 => "Plasma Driver", 
				18 => "Structure",
				20 => "Primary",
			),
		);
		
    }
}



?>
