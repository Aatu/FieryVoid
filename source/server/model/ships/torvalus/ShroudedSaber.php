<?php
class ShroudedSaber extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 2325;
		$this->faction = "Torvalus Speculators";
        $this->phpclass = "ShroudedSaber";
        $this->shipClass = "Shrouded Saber";
        $this->imagePath = "img/ships/TorvalusShroudedSaber.png";
        $this->canvasSize = 128;
	    $this->isd = 'Ancient';

		$this->factionAge = 3; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
        $this->agile = true;
        $this->jinkinglimit = 2;
				
        $this->gravitic = true;
		$this->advancedArmor = true;   
        
        $this->forwardDefense = 8;
        $this->sideDefense = 9;
        
        $this->turncost = 0.25;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 1;
		$this->iniativebonus = 16 *5;

		$this->trueStealth = true; //For ships that can actually be hidden, not just jammer from range.  Important for Front End.		
		
		//$this->fighters = array("normal"=>12); //Can optionally control 12
		$this->notes = "Can control 12 fighters";
        
		/*Torvalus use their own enhancement set */		
		Enhancements::nonstandardEnhancementSet($this, 'TorvalusShip');
		
         
		$this->addPrimarySystem(new Reactor( 5, 20, 0, 0));//armor, structure, power req, output
        $this->addPrimarySystem(new CnC(5, 16, 0, 0));
		$scanner = new Scanner(5, 18, 0, 8);
		$scanner->markAdvanced();
		$this->addPrimarySystem($scanner);			
		$this->addPrimarySystem(new Engine(5, 15, 0, 12, 2));
        $this->addPrimarySystem(new SelfRepair(5, 6, 4)); //armor, structure, output
		$this->addPrimarySystem(new ShadingField(5, 16, 5, 4, 0, 360)); //Not installed 'til Tuesday 
		$this->addPrimarySystem(new TransverseDrive(5, 16, 5, 0, 360)); //Not installed 'til Tuesday
		$this->addPrimarySystem(new GraviticThruster(5, 15, 0, 6, 3));
		$this->addPrimarySystem(new GraviticThruster(5, 15, 0, 6, 4));			

        $this->addFrontSystem(new VolleyLaser(5, 0, 0, 240, 120));         
        $this->addFrontSystem(new VolleyLaser(5, 0, 0, 240, 120));         

		
        $this->addFrontSystem(new GraviticThruster(5, 10, 0, 4, 1));		
        $this->addFrontSystem(new PowerLaser(5, 0, 0, 270, 90));          
        $this->addFrontSystem(new GraviticThruster(5, 10, 0, 4, 1));


		$this->addAftSystem(new GraviticThruster(5, 12, 0, 4, 2));
		$this->addAftSystem(new GraviticThruster(5, 12, 0, 4, 2));
		$this->addAftSystem(new GraviticThruster(5, 12, 0, 4, 2));        
		$this->addAftSystem(new JumpEngine(5, 15, 4, 10));
				

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure( 5, 100 ));
		
	
		$this->hitChart = array(
			0=> array( //PRIMARY
				8 => "TAG:Thruster",
				9 => "Shading Field",
				10 => "Transverse Drive",
				11 => "Self Repair",
				13 => "Scanner",                
				16 => "Engine",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array( //Fwd
				4 => "TAG:Thruster",
				7 => "Power Laser", 
				9 => "Volley Laser",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array( //Aft
				5 => "TAG:Thruster",
				8 => "Jump Engine",
				18 => "Structure",                                
				20 => "Primary",
			),
		);
		
    }
}



?>
