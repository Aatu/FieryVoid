<?php
class VorlonLightCruiserOld extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 2500;
		$this->faction = "Vorlons";
        $this->phpclass = "VorlonLightCruiserOld";
        $this->shipClass = "Light Cruiser";
        $this->variantOf = "TO BE DELETED";	
        $this->imagePath = "img/ships/VorlonLightCruiser.png";
        $this->canvasSize = 200;
	    $this->isd = 'Ancient';
        $this->shipSizeClass = 3; 
		$this->factionAge = 3; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
				
        $this->gravitic = true;
		$this->advancedArmor = true;   
        
        $this->forwardDefense = 15;
        $this->sideDefense = 18;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 1;
        $this->pivotcost = 2;
		$this->iniativebonus = 2 *5;
		
		//$this->fighters = array("heavy"=>6);
        
		/*Vorlons use their own enhancement set */		
		Enhancements::nonstandardEnhancementSet($this, 'VorlonShip');
		
         
        $this->addPrimarySystem(new MagGravReactorTechnical(99, 99, 0, 0));
		$this->addPrimarySystem(new PowerCapacitor( 6, 24, 0, 10, false));//armor, structure, power req, output, has petals 
        $this->addPrimarySystem(new CnC(6, 16, 0, 0));
		$scanner = new Scanner(6, 18, 0, 13);//Vorlon Scanners do not need power - base systems are included in zero hull running costs
		$scanner->markAdvanced();
		$this->addPrimarySystem($scanner);			
		$this->addPrimarySystem(new Hangar(5, 2, 0));
		$this->addPrimarySystem(new Engine(6, 21, 0, 12, 4));
        $this->addPrimarySystem(new SelfRepair(6, 9, 6)); //armor, structure, output
		$AAC = $this->createAdaptiveArmorController(5, 3, 3); //$AAtotal, $AApertype, $AApreallocated
		$this->addPrimarySystem( $AAC );
		
		
        $this->addFrontSystem(new VorlonLightningCannon(5, 0, 0, 240, 60, 'L'));
        $this->addFrontSystem(new VorlonLightningCannon(5, 0, 0, 300, 120, 'R'));
        $this->addFrontSystem(new GraviticThruster(5, 25, 0, 8, 1));


        $this->addAftSystem(new EMShield(4, 6, 0, 4, 120, 300));
        $this->addAftSystem(new EMShield(4, 6, 0, 4, 60, 240));
		$this->addAftSystem(new JumpEngine(5, 16, 0, 10));//Vorlon Jump Engines normally do use power (the only system onboard that does so), but still are counted as base running costs - in FV I simplify to 0 power requirement
		$this->addAftSystem(new GraviticThruster(5, 13, 0, 3, 2));
		$this->addAftSystem(new GraviticThruster(5, 13, 0, 3, 2));
		$this->addAftSystem(new GraviticThruster(5, 13, 0, 3, 2));
		$this->addAftSystem(new GraviticThruster(5, 13, 0, 3, 2));
		
		
        $this->addLeftSystem(new VorlonLightningCannon(5, 0, 0, 240, 60, 'L'));
        $this->addLeftSystem(new EMShield(4, 6, 0, 4, 240, 60));
        $this->addLeftSystem(new GraviticThruster(5, 21, 0, 6, 3));
		
        $this->addRightSystem(new VorlonLightningCannon(5, 0, 0, 300, 120, 'R'));
        $this->addRightSystem(new EMShield(4, 6, 0, 4, 300, 120));
        $this->addRightSystem(new GraviticThruster(5, 21, 0, 6, 4));
		

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 6, 48));
        $this->addAftSystem(new Structure( 6, 54));
        $this->addLeftSystem(new Structure( 6, 70));
        $this->addRightSystem(new Structure( 6, 70));
        $this->addPrimarySystem(new Structure( 6, 60 ));
		
		
	
		$this->hitChart = array(
			0=> array( //PRIMARY
				10 => "Structure",
				12 => "Self Repair",
				14 => "Scanner",
				16 => "Engine",
				17 => "Hangar",
				19 => "Power Capacitor",
				20 => "C&C",
			),
			1=> array( //Fwd
				4 => "Thruster",
				10 => "Lightning Cannon", 
				18 => "Structure",
				20 => "Primary",
			),
			2=> array( //Aft
				6 => "Thruster",
				8 => "Jump Drive",
				10 => "EM Shield",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array( //Fwd
				5 => "Thruster",
				7 => "EM Shield",
				11 => "Lightning Cannon",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array( //Fwd
				5 => "Thruster",
				7 => "EM Shield",
				11 => "Lightning Cannon",
				18 => "Structure",
				20 => "Primary",
			),
		);
		
    }
}



?>
