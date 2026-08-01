<?php
class BlackDagger extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 2300;
		$this->faction = "Torvalus Speculators";
        $this->phpclass = "BlackDagger";
        $this->shipClass = "Black Dagger";
        $this->imagePath = "img/ships/TorvalusBlackRapier.png";
        $this->canvasSize = 280;
	    $this->isd = 'Ancient';
        $this->shipSizeClass = 3; 
		$this->factionAge = 4; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
        $this->agile = true;
				
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

		$this->trueStealth = false; //Alpha Shading Field does not grant true stealth		
		$this->canPreOrder = false;		
		$this->fighters = array("Stilettos"=>6);
		//$this->notes = "Can control 6 fighters";		
		$this->notes .= '<br>Can skin dance'; 

		/*Torvalus use their own enhancement set */		
		Enhancements::nonstandardEnhancementSet($this, 'TorvalusShip');
		
         
		$this->addPrimarySystem(new Reactor(6, 20, 0, 0));//armor, structure, power req, output
        $this->addPrimarySystem(new CnC(6, 16, 0, 0));
		$scanner = new Scanner(6, 18, 0, 10);
		$scanner->markAdvanced();
		$this->addPrimarySystem($scanner);			
		$this->addPrimarySystem(new Engine(6, 18, 0, 12, 4));
        $this->addPrimarySystem(new SelfRepair(6, 9, 4)); //armor, structure, output
		$this->addPrimarySystem(new AlphaShadingField(6, 20, 2, 4, 0, 360));       
		$this->addPrimarySystem(new JumpEngine(6, 20, 6, 14));        
		
        $this->addFrontSystem(new VolleyLaser(5, 0, 0, 240, 120));        

		$this->addAftSystem(new GraviticThruster(5, 15, 0, 4, 2));
        $this->addAftSystem(new MedPowerLaser(5, 0, 0, 90, 270));          

        $this->addLeftSystem(new MedPowerLaser(5, 0, 0, 270, 90));    		
        $this->addLeftSystem(new GraviticThruster(5, 13, 0, 4, 1));       
		$this->addLeftSystem(new GraviticThruster(5, 15, 0, 4, 2));
        $this->addLeftSystem(new GraviticThruster(5, 20, 0, 7, 3)); 		

        $this->addRightSystem(new MedPowerLaser(5, 0, 0, 270, 90));     
        $this->addRightSystem(new GraviticThruster(5, 13, 0, 4, 1));       
		$this->addRightSystem(new GraviticThruster(5, 15, 0, 4, 2));
        $this->addRightSystem(new GraviticThruster(5, 20, 0, 7, 4)); 				

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 6, 45));
        $this->addAftSystem(new Structure( 6, 45));
        $this->addLeftSystem(new Structure( 6, 64));
        $this->addRightSystem(new Structure( 6, 64));
        $this->addPrimarySystem(new Structure( 6, 60 ));
		
	
		$this->hitChart = array(
			0=> array( //PRIMARY
				8 => "Structure",
				10 => "Alpha Shading Field",
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
				10 => "Medium Power Laser", 
				18 => "Structure",
				20 => "Primary",
			),
			4=> array( //Fwd
				7 => "TAG:Thruster",
				10 => "Medium Power Laser", 
				18 => "Structure",
				20 => "Primary",
			),
		);
		
    }
}



?>
