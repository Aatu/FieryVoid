<?php
class VorlonTransport extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 600;
		$this->faction = "Vorlons";
        $this->phpclass = "VorlonTransport";
        $this->shipClass = "Transport";
        //$this->variantOf = "Battle Destroyer";
        $this->imagePath = "img/ships/VorlonTransport.png";
        $this->canvasSize = 100;
	    $this->isd = 'Ancient';
		$this->factionAge = 3; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
				
        $this->agile = true;
        $this->gravitic = true;
		$this->advancedArmor = true;   
        
        $this->forwardDefense = 12;
        $this->sideDefense = 14;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 1;
        $this->rollcost = 1;
        $this->pivotcost = 1;
		$this->iniativebonus = 14 *5;
		
		//$this->fighters = array("heavy"=>12);
        
		/*Vorlons use their own enhancement set */		
		Enhancements::nonstandardEnhancementSet($this, 'VorlonShip');
		
         
        $this->addPrimarySystem(new MagGravReactorTechnical(99, 99, 0, 0));
		$this->addPrimarySystem(new PowerCapacitor( 5, 16, 0, 8, true));//armor, structure, power req, output, has petals 
        $this->addPrimarySystem(new CnC(5, 8, 0, 0));
		$scanner = new Scanner(5, 14, 0, 10);//Vorlon Scanners do not need power - base systems are included in zero hull running costs
		$scanner->markAdvanced();
		$this->addPrimarySystem($scanner);			
		//$this->addPrimarySystem(new Hangar(4, 2, 0)); //no Hangar, ship itself docks to bases and such
		$this->addPrimarySystem(new Engine(5, 12, 0, 8, 2));
        $this->addPrimarySystem(new SelfRepair(5, 3, 2)); //armor, structure, output
		$AAC = $this->createAdaptiveArmorController(4, 2, 2); //$AAtotal, $AApertype, $AApreallocated
		$this->addPrimarySystem( $AAC );
        $this->addPrimarySystem(new GraviticThruster(5, 13, 0, 5, 3));
        $this->addPrimarySystem(new GraviticThruster(5, 13, 0, 5, 4));
		
		
        $this->addFrontSystem(new VorlonDischargeGun(4, 0, 0, 240, 120));
        $this->addFrontSystem(new EMShield(4, 6, 0, 3, 180, 0));
        $this->addFrontSystem(new EMShield(4, 6, 0, 3, 0, 180));
        $this->addFrontSystem(new GraviticThruster(5, 13, 0, 4, 1));

        $this->addAftSystem(new GraviticThruster(5, 5, 0, 2, 2));
        $this->addAftSystem(new GraviticThruster(5, 5, 0, 2, 2));
        $this->addAftSystem(new GraviticThruster(5, 5, 0, 2, 2));
        $this->addAftSystem(new GraviticThruster(5, 5, 0, 2, 2));
		

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure( 5, 80 ));
		
		
	
		$this->hitChart = array(
			0=> array( //PRIMARY
				4 => "Thruster",
				7 => "Self Repair",
				11 => "Scanner",
				15 => "Engine",
				18 => "Power Capacitor",
				20 => "C&C",
			),
			1=> array( //Fwd
				5 => "Thruster",
				8 => "Discharge Gun",
				10 => "EM Shield",
				17 => "Structure",
				20 => "Primary",
			),
			2=> array( //Aft
				8 => "Thruster",
				17 => "Structure",
				20 => "Primary",
			),
		);
		
    }
}



?>
