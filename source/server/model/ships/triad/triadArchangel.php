<?php
class triadArchangel extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 6400;
		$this->faction = "The Triad";
        $this->phpclass = "triadArchangel";
        $this->shipClass = "Order: Archangel";
        $this->imagePath = "img/ships/triadArchangel.png";
        $this->canvasSize = 200;
	    $this->isd = 'Primordial';
        $this->shipSizeClass = 3; 
		$this->factionAge = 4; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial

		$this->triadOrder = true;	//Important to ensure immunity from Flare Generator!	

        $this->gravitic = true;
		$this->advancedArmor = true;  
        
        $this->forwardDefense = 16;
        $this->sideDefense = 18;
        
        $this->turncost = 1.33;
        $this->turndelaycost = 1;
        $this->accelcost = 2;
        $this->rollcost = 3;
        $this->pivotcost = 4;
		$this->iniativebonus = 3 *5;

		$this->notes = 'Triad Capital Ship'; 

		$this->fighters = array("Triad Fighter"=>12);

		/*Triad use their own enhancement set */		
		Enhancements::nonstandardEnhancementSet($this, 'TriadShip');

		$this->addPrimarySystem(new Reactor(8, 25, 0, 0));//armor, structure, power req, output
        $this->addPrimarySystem(new CnC(8, 24, 0, 0));
//		$scanner = new Scanner(8, 24, 0, 15);
//			$scanner->markAdvanced();
//			$this->addPrimarySystem($scanner);			
        $scanner = new ElintScanner(8, 24, 0, 15);  // Using this for now as Jealous ELINT not implemented
			$scanner->markMindrider();
			$this->addPrimarySystem($scanner);	        
		$this->addPrimarySystem(new Engine(8, 25, 0, 18, 3));
//        $this->addPrimarySystem(new StructureSelfRepair(8, 24, 24)); //armor, structure, output
        $this->addPrimarySystem(new CoopStructureSelfRepair(8, 24, 24)); //armor, structure, output
        $this->addPrimarySystem(new SelfRepair(8, 18, 8)); //armor, structure, output
        $this->addPrimarySystem(new FlareGenerator(8, 16, 8, 0, 360));	
		
        $this->addFrontSystem(new GraviticThruster(8, 13, 0, 5, 1));		
        $this->addFrontSystem(new GraviticThruster(8, 13, 0, 5, 1));		
        $this->addFrontSystem(new GraviticThruster(8, 13, 0, 5, 1));		
        $this->addFrontSystem(new PhotonicPrismBeam(8, 24, 8, 270, 90));	
        $this->addFrontSystem(new PhotonicPrismBeam(8, 24, 8, 270, 90));	
        $this->addFrontSystem(new NeutronBurst(6, 12, 4, 240, 360));	
        $this->addFrontSystem(new NeutronBurst(6, 12, 4, 0, 120));	

		$this->addAftSystem(new GraviticThruster(8, 20, 0, 6, 2));
		$this->addAftSystem(new GraviticThruster(8, 20, 0, 6, 2));
		$this->addAftSystem(new GraviticThruster(8, 20, 0, 6, 2));
        $this->addAftSystem(new PhotonicPrismBeam(8, 24, 8, 90, 270));	
		$this->addAftSystem(new JumpEngine(8, 25, 5, 8));        

        $this->addLeftSystem(new GraviticThruster(8, 25, 0, 8, 3)); 		
        $this->addLeftSystem(new PhotonicPrismBeam(8, 24, 8, 210, 30));	

        $this->addRightSystem(new GraviticThruster(8, 25, 0, 8, 4)); 				
        $this->addRightSystem(new PhotonicPrismBeam(8, 24, 8, 330, 150));	

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 8, 90));
        $this->addAftSystem(new Structure( 8, 90));
        $this->addLeftSystem(new Structure( 8, 80));
        $this->addRightSystem(new Structure( 8, 80));
        $this->addPrimarySystem(new Structure( 9, 75 ));
	
		$this->hitChart = array(
			0=> array( //PRIMARY
				10 => "Structure",
				11 => "Flare Generator",
				12 => "Self Repair",
				13 => "Cooperative Structure Self Repair",
				15 => "ELINT Scanner",                
				17 => "Engine",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array( //Fwd
				4 => "Thruster",
				7 => "Photonic Prism Beam", 
				9 => "Neutron Burst",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array( //Aft
				6 => "Thruster",
				8 => "Photonic Prism Beam",
				10 => "Jump Engine",
				18 => "Structure",  				
				20 => "Primary",
			),
			3=> array( //Port
				6 => "Thruster",
				9 => "Photonic Prism Beam", 
				18 => "Structure",
				20 => "Primary",
			),
			4=> array( //Stbd
				6 => "Thruster",
				9 => "Photonic Prism Beam", 
				18 => "Structure",
				20 => "Primary",
			),
		);
		
    }
}



?>
