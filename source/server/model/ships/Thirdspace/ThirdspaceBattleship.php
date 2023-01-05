<?php
class ThirdspaceBattleship extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 6000;
	$this->faction = "Thirdspace";
        $this->phpclass = "ThirdspaceBattleship";
        $this->imagePath = "img/ships/ThirdspaceBattleship.png";
        $this->shipClass = "Harbringer Battleship";
        $this->shipSizeClass = 3;
	    $this->isd = 'Ancient';
		$this->factionAge = 4;
		$this->limited = 33;			    
	    
		$this->fighters = array("LCVs" => 6);	
		
        $this->gravitic = true;
		$this->advancedArmor = true; 		    
		
        $this->forwardDefense = 18;
        $this->sideDefense = 19;
        
        $this->turncost = 1.33;
        $this->turndelaycost = 1.33;
        $this->accelcost = 5;
        $this->rollcost = 6;
        $this->pivotcost = 4;
       
		$this->iniativebonus = 2 *5;        

        
        $this->addPrimarySystem(new AdvancedSingularityDrive(8, 60, 0, 100+8+5));
        $this->addPrimarySystem(new CnC(8, 24, 0, 0));
        $this->addPrimarySystem(new Scanner(7, 28, 8, 15));
		$scanner->markAdvanced();
		$this->addPrimarySystem($scanner);	        
        $this->addPrimarySystem(new Engine(7, 36, 0, 20, 3));
		$this->addPrimarySystem(new Hangar(7, 20));
	//	$this->addPrimarySystem(new PsychicField(7, 0, 0, 0, 360));		
        $this->addPrimarySystem(new SelfRepair(7, 10, 5)); //armor, structure, output
        $this->addPrimarySystem(new SelfRepair(7, 10, 5)); //armor, structure, output        		
        
 //       $this->addFrontSystem(new HeavyPsionicLance(6, 0, 12, 330, 30));
 //       $this->addFrontSystem(new PsionicLance(6, 0, 8, 300, 60));
  //      $this->addFrontSystem(new PsionicLance(6, 0, 0, 300, 60));
 //       $this->addFrontSystem(new PsionicConcentrator(5, 0, 0, 300, 60));
 //       $this->addFrontSystem(new PsionicConcentrator(5, 0, 0, 300, 60));        
//		$projection = new ThirdspaceShieldProjection(0, 160, 160, 270, 90, 'F');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R
//		$projector = new ThirdspaceShieldProjector(6, 24, 10, 6, 270, 90, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R 
//		$projection->addProjector($projector);
//		$this->addFrontSystem($projector);
//		$projector = new ThirdspaceShieldProjector(6, 24, 10, 6, 270, 90, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R 
//		$projection->addProjector($projector);
//		$this->addFrontSystem($projector);
//		$this->addFrontSystem($projection);
        $this->addFrontSystem(new GraviticThruster(6, 20, 0, 8, 1));
        $this->addFrontSystem(new GraviticThruster(6, 20, 0, 8, 1));   
			
		
 //       $this->addAftSystem(new PsionicConcentrator(5, 0, 4, 120, 240)); 
 //       $this->addAftSystem(new PsionicConcentrator(5, 0, 0, 120, 240)); 
  //      $this->addAftSystem(new PsionicConcentrator(5, 0, 0, 120, 240)); 
 //       $this->addAftSystem(new PsionicConcentrator(5, 0, 0, 120, 240)); 
//		$projection = new ThirdspaceShieldProjection(0, 160, 160, 270, 90, 'F');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R
//		$projector = new ThirdspaceShieldProjector(6, 24, 10, 6, 90, 270, 'A'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R 
//		$projection->addProjector($projector);
//		$this->addAftSystem($projector);
//		$projector = new ThirdspaceShieldProjector(6, 24, 10, 6, 90, 270, 'A'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R 
//		$projection->addProjector($projector);
//		$this->addAftSystem($projector);
//		$this->addAftSystem($projection);
		$this->addAftSystem(new JumpEngine(6, 28, 5, 6));        
        $this->addAftSystem(new GraviticThruster(6, 20, 0, 8, 2));
        $this->addAftSystem(new GraviticThruster(6, 20, 0, 8, 2));
        $this->addAftSystem(new GraviticThruster(6, 20, 0, 8, 2));
        $this->addAftSystem(new GraviticThruster(6, 20, 0, 8, 2));       
       
//        $this->addLeftSystem(new PsionicLance(6, 0, 0, 240, 360));
//        $this->addLeftSystem(new PsionicConcentrator(5, 0, 0, 180, 60)); 
//        $this->addLeftSystem(new PsionicConcentrator(5, 0, 0, 180, 60)); 
//        $this->addLeftSystem(new PsionicConcentrator(5, 0, 0, 180, 60));                        
        $this->addLeftSystem(new GraviticThruster(6, 36, 0, 10, 3));   
            
 //       $this->addRightSystem(new PsionicLance(6, 0, 0, 0, 120));
//        $this->addRightSystem(new PsionicConcentrator(5, 0, 0, 300, 180)); 
//        $this->addRightSystem(new PsionicConcentrator(5, 0, 0, 300, 180)); 
//        $this->addRightSystem(new PsionicConcentrator(5, 0, 0, 300, 180));                         
        $this->addRightSystem(new GraviticThruster(6, 36, 0, 10, 4)); 
              			          
		
		//structures
        $this->addFrontSystem(new Structure(6, 96));
        $this->addAftSystem(new Structure(6, 88));
        $this->addLeftSystem(new Structure(6, 120));
        $this->addRightSystem(new Structure(6, 120));
        $this->addPrimarySystem(new Structure(7, 108));
		
		
		$this->hitChart = array(
			0=> array( //PRIMARY
				9 => "Structure",
				11 => "Psychic Field"
				12 => "Self Repair",
				14 => "Scanner",
				16 => "Engine",
				17 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array( //Fwd
				4 => "Thruster",
				6 => "Shield Projector",
				8 => "Heavy Psionic Lance", 
				10 => "Psionic Lance",
				12 => "Psionic Concentrator",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array( //Aft
				6 => "Thruster",
				8 => "Shield Projector",
				10 => "Jump Engine",
				12 => "Psionic Concentrator",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array(
				4 => "Thruster",
				6 => "Psionic Lance",
				10 => "Psionic Concentrator",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				4 => "Thruster",
				6 => "Psionic Lance",
				10 => "Psionic Concentrator",
				18 => "Structure",
				20 => "Primary",
			),

		);
		
    }
}


?>
