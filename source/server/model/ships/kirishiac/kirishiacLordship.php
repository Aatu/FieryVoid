<?php
class kirishiacLordship extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 3400;
	$this->faction = "Kirishiac Lords";
        $this->phpclass = "kirishiacLordship";
        $this->imagePath = "img/ships/kirishiacLordship2.png";
        $this->shipClass = "Lordship";
        $this->shipSizeClass = 3;
	    $this->isd = "Ancient";
		$this->factionAge = 3; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
	    $this->notes = 'Atmospheric capable.';

        $this->gravitic = true;
		$this->advancedArmor = true;   
		$this->hardAdvancedArmor = true;
		
        $this->forwardDefense = 15;
        $this->sideDefense = 15;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 3;
        $this->rollcost = 4;
        $this->pivotcost = 2;
		$this->iniativebonus = 10;

        $orbitalHitChart = array( 
            6 => "Antigravity Beam",
            20 => "Structure"
            );
	
        $this->addPrimarySystem(new Reactor(7, 35, 0, 0));
        $this->addPrimarySystem(new CnC(8, 24, 0, 0));
		$scanner = new Scanner(7, 24, 0, 14);
		$scanner->markAdvanced();
		$this->addPrimarySystem($scanner);			
        $this->addPrimarySystem(new Engine(7, 25, 0, 12, 4));
        $this->addPrimarySystem(new JumpEngine(8, 25, 6, 8));
        $this->addPrimarySystem(new SelfRepair(7, 12, 6)); //armor, structure, output

        $this->addFrontSystem(new KirishiacOrbital(6, 18, 'R', 'B', $orbitalHitChart)); //Armor, stucture, orientation, pairing, hitchart
        $this->addFrontSystem(new GraviticThruster(7, 15, 0, 4, 1));		
        $this->addFrontSystem(new KirishiacOrbital(6, 18, 'L', 'A', $orbitalHitChart));
        $this->addFrontSystem(new GraviticThruster(7, 15, 0, 4, 1));       
		$this->addFrontSystem(new HypergravitonBlaster(7, 30, 15, 300, 60));
        $this->addFrontSystem(new AntigravityBeam(6, 6, 3, 270, 90, false, 'B'));	    
        $this->addFrontSystem(new AntigravityBeam(6, 6, 3, 270, 90, false, 'A'));	
		
        
        $this->addAftSystem(new AntigravityBeam(6, 6, 3, 90, 270, false, 'F'));
        $this->addAftSystem(new GraviticThruster(7, 15, 0, 4, 2));	
        $this->addAftSystem(new AntigravityBeam(6, 6, 3, 90, 270, false, 'E'));          
        $this->addAftSystem(new GraviticThruster(7, 15, 0, 4, 2));
        $this->addAftSystem(new GraviticThruster(7, 15, 0, 4, 2));
        $this->addAftSystem(new KirishiacOrbital(6, 18, 'R','F', $orbitalHitChart));
        $this->addAftSystem(new KirishiacOrbital(6, 18, 'L', 'E', $orbitalHitChart));  
       

        $this->addLeftSystem(new AntigravityBeam(6, 6, 3, 120, 300, false, 'G'));
        $this->addLeftSystem(new KirishiacOrbital(6, 18,'R', 'G', $orbitalHitChart));              
        $this->addLeftSystem(new AntigravityBeam(6, 6, 3, 240, 60, false, 'H'));
         $this->addLeftSystem(new KirishiacOrbital(6, 18, 'L', 'H', $orbitalHitChart)); 
        $this->addLeftSystem(new GraviticThruster(7, 25, 0, 7, 3));

		$this->addRightSystem(new AntigravityBeam(6, 6, 3, 60, 240, false, 'C'));
        $this->addRightSystem(new KirishiacOrbital(6, 18, 'R','C', $orbitalHitChart));   
        $this->addRightSystem(new KirishiacOrbital(6, 18, 'L', 'D', $orbitalHitChart));              
        $this->addRightSystem(new AntigravityBeam(6, 6, 3, 300, 120, false, 'D'));	        
        $this->addRightSystem(new GraviticThruster(7, 25, 0, 7, 4));


        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 8, 72));  
        $this->addAftSystem(new Structure( 8, 72));   
        $this->addLeftSystem(new Structure( 8, 90));  
        $this->addRightSystem(new Structure( 8, 90));  
        $this->addPrimarySystem(new Structure( 8, 72));
        

        $this->hitChart = array(
            0=> array(
                    10 => "Structure",
                    12 => "Self Repair",
                    14 => "Scanner",
                    16 => "Engine",
                    17 => "Jump Engine",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    4 => "Thruster",
                    8 => "Hypergraviton Blaster",
                    10 => "Kirishiac Orbital",
                    18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    8 => "Thruster",
                    10 => "Kirishiac Orbital",
                    18 => "Structure",
                    20 => "Primary",
            ),
            3=> array(
                    6 => "Thruster",
                    8 => "Kirishiac Orbital",
                    18 => "Structure",
                    20 => "Primary",
            ),
            4=> array(
                    6 => "Thruster",
                    8 => "Kirishiac Orbital",
                    18 => "Structure",
                    20 => "Primary",
            ),
            
        );
    }
}
