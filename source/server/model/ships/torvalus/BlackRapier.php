<?php
class BlackRapier extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 3825;
		$this->faction = "Torvalus Speculators";
        $this->phpclass = "BlackRapier";
        $this->shipClass = "Black Rapier";
        $this->imagePath = "img/ships/TorvalusBlackRapier.png";
        $this->canvasSize = 256;
	    $this->isd = 'Ancient';
        $this->shipSizeClass = 3; 
		$this->factionAge = 3; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
        $this->agile = true;
		$this->notes .= "Can skin dance"; 
				
        $this->gravitic = true;
		$this->advancedArmor = true;   
		$this->skinDancer = true;         

        $this->forwardDefense = 10;
        $this->sideDefense = 12;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 1;
		$this->iniativebonus = 4 *5;

		$this->trueStealth = true; //For ships that can actually be hidden, not just jammer from range.  Important for Front End.		
		$this->canPreOrder = true;		
		//$this->fighters = array("normal"=>6);
		$this->notes = "Can control 6 fighters";		
        
		/*Torvalus use their own enhancement set */		
		Enhancements::nonstandardEnhancementSet($this, 'TorvalusShip');
		
         
		$this->addPrimarySystem(new Reactor( 6, 30, 0, 0));//armor, structure, power req, output
        $this->addPrimarySystem(new CnC(6, 16, 0, 0));
		$scanner = new Scanner(6, 18, 0, 10);
		$scanner->markAdvanced();
		$this->addPrimarySystem($scanner);			
		$this->addPrimarySystem(new Engine(6, 18, 0, 12, 4));
        $this->addPrimarySystem(new SelfRepair(6, 10, 5)); //armor, structure, output
		$this->addPrimarySystem(new ShadingField(6, 20, 6, 4, 0, 360));       
		$this->addPrimarySystem(new TransverseDrive(5, 16, 5, 0, 360));
		$this->addPrimarySystem(new JumpEngine(6, 25, 6, 8));        
		
        $this->addFrontSystem(new VolleyLaser(5, 0, 0, 240, 120));        

		$this->addAftSystem(new GraviticThruster(5, 15, 0, 4, 2));
        $this->addAftSystem(new MedPowerLaser(5, 0, 0, 90, 270));          

        $this->addLeftSystem(new PowerLaser(5, 0, 0, 270, 90));    		
        $this->addLeftSystem(new GraviticThruster(5, 13, 0, 3, 1));       
		$this->addLeftSystem(new GraviticThruster(5, 15, 0, 4, 2));
        $this->addLeftSystem(new GraviticThruster(5, 20, 0, 7, 3)); 		

        $this->addRightSystem(new PowerLaser(5, 0, 0, 270, 90));     
        $this->addRightSystem(new GraviticThruster(5, 13, 0, 3, 1));       
		$this->addRightSystem(new GraviticThruster(5, 15, 0, 4, 2));
        $this->addRightSystem(new GraviticThruster(5, 20, 0, 7, 4)); 				

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 6, 45));
        $this->addAftSystem(new Structure( 6, 45));
        $this->addLeftSystem(new Structure( 6, 74));
        $this->addRightSystem(new Structure( 6, 74));
        $this->addPrimarySystem(new Structure( 6, 60 ));
		
	
		$this->hitChart = array(
			0=> array( //PRIMARY
				7 => "Structure",
				9 => "Shading Field",
				10 => "Transverse Drive",
				12 => "Self Repair",
				14 => "Scanner",                
				16 => "Engine",
				17 => "Jump Engine",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array( //Fwd
				5 => "TAG:Thruster",
				9 => "Volley Laser",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array( //Aft
				5 => "TAG:Thruster",
				7 => "Medium Power Laser",                 
				18 => "Structure",  				
				20 => "Primary",
			),
			3=> array( //Fwd
				7 => "TAG:Thruster",
				10 => "Power Laser", 
				18 => "Structure",
				20 => "Primary",
			),
			4=> array( //Fwd
				7 => "TAG:Thruster",
				10 => "Power Laser", 
				18 => "Structure",
				20 => "Primary",
			),
		);
		
    }
}



?>
