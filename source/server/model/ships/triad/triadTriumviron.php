<?php
class triadTriumviron extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 5175;
		$this->faction = "The Triad";
        $this->phpclass = "triadTriumviron";
        $this->shipClass = "The Triumviron";
        $this->imagePath = "img/ships/triadTriumviron.png";
        $this->canvasSize = 200;
	    $this->isd = 'Ancient';
        $this->shipSizeClass = 3; 
		$this->factionAge = 3; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
		$this->occurence = "unique";

        $this->gravitic = true;
		$this->advancedArmor = true;  
        
        $this->forwardDefense = 14;
        $this->sideDefense = 16;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 1;
        $this->accelcost = 2;
        $this->rollcost = 3;
        $this->pivotcost = 4;
		$this->iniativebonus = 2 *5;

		$this->notes = 'Unified Triad'; 
		$this->notes .= '<br>Triad Capital Ship'; 
		$this->notes .= '<br>Atmospheric capable'; 

		$this->fighters = array("Triad Fighter"=>24);

		$t1l = new GraviticThruster(7, 13, 0, 5, 1);
		$t3l = new GraviticThruster(7, 25, 0, 8, 3);

		$t1r = new GraviticThruster(7, 13, 0, 5, 1);
		$t4r = new GraviticThruster(7, 25, 0, 8, 4);

		$this->addLeftSystem($t1l);
		$this->addLeftSystem($t3l);
		$this->addRightSystem($t1r);
		$this->addRightSystem($t4r);

		/*Triad use their own enhancement set */		
		Enhancements::nonstandardEnhancementSet($this, 'TriadShip');
		
		$this->addPrimarySystem(new Reactor(8, 30, 0, 0));//armor, structure, power req, output
        $this->addPrimarySystem(new CnC(8, 24, 0, 0));
		$scanner = new ElintScanner(8, 24, 0, 15);
		$scanner->markAdvanced();
       		$this->addPrimarySystem($scanner);
		$this->addPrimarySystem(new Engine(8, 23, 0, 14, 3));
//        $this->addPrimarySystem(new StructureSelfRepair(8, 24, 24)); //armor, structure, output
        $this->addPrimarySystem(new CoopStructureSelfRepair(8, 24, 24)); //armor, structure, output
		
		$hyperplasma = new HyperplasmaCutter(7, 16, 9, 270, 90);
			$hyperplasma->displayName = 'Hyperplasma Cutter A';
			$this->addFrontSystem($hyperplasma);
        $this->addFrontSystem(new GraviticThruster(7, 13, 0, 5, 1));		

		$this->addAftSystem(new GraviticThruster(7, 10, 0, 4, 2));
		$this->addAftSystem(new GraviticThruster(7, 20, 0, 6, 2));
		$this->addAftSystem(new GraviticThruster(7, 10, 0, 4, 2));
        $this->addAftSystem(new SelfRepair(6, 12, 6)); //armor, structure, output
		$this->addAftSystem(new JumpEngine(6, 25, 6, 8));        

		$hyperplasma = new HyperplasmaCutter(7, 16, 9, 270, 90);
			$hyperplasma->displayName = 'Hyperplasma Cutter B';
			$this->addLeftSystem($hyperplasma);
//        $this->addLeftSystem(new GraviticThruster(7, 13, 0, 5, 1));       
//        $this->addLeftSystem(new GraviticThruster(7, 25, 0, 8, 3)); 		

		$hyperplasma = new HyperplasmaCutter(7, 16, 9, 270, 90);
			$hyperplasma->displayName = 'Hyperplasma Cutter C';
			$this->addRightSystem($hyperplasma);
//        $this->addRightSystem(new GraviticThruster(7, 13, 0, 5, 1));       
//        $this->addRightSystem(new GraviticThruster(7, 25, 0, 8, 4)); 				

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 7, 60));
        $this->addAftSystem(new Structure( 8, 90));
        $this->addLeftSystem(new Structure( 7, 80));
        $this->addRightSystem(new Structure( 7, 80));
        $this->addPrimarySystem(new Structure( 8, 75 ));
	
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
				6 => "Hyperplasma Cutter A", 
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
				5 => "Thruster",
				7 => "2:Thruster",
				9 => "Hyperplasma Cutter B", 
				18 => "Structure",
				20 => "Primary",
			),
			4=> array( //Stbd
				5 => "Thruster",
				7 => "2:Thruster",
				9 => "Hyperplasma Cutter C", 
				18 => "Structure",
				20 => "Primary",
			),
		);
		
    }
}



?>
