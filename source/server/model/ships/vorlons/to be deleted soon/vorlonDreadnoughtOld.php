<?php
class VorlonDreadnoughtOld extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 5000;
		$this->faction = "Vorlons";
        $this->phpclass = "VorlonDreadnoughtOld";
        $this->shipClass = "Dreadnought";
        $this->variantOf = "TO BE DELETED";		
		$this->limited = 33;
		
        $this->imagePath = "img/ships/VorlonDreadnought.png";
        $this->canvasSize = 300;
	    $this->isd = 'Ancient';
        $this->shipSizeClass = 3; 
		$this->factionAge = 3; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
				
				
				
        $this->gravitic = true;
		$this->advancedArmor = true;   
        
        $this->forwardDefense = 18;
        $this->sideDefense = 21;
        
        $this->turncost = 1.5;
        $this->turndelaycost = 1.5;
        $this->accelcost = 6;
        $this->rollcost = 2;
        $this->pivotcost = 5;
		$this->iniativebonus = 2 *5;
		
		$this->fighters = array("heavy"=>12);
        
		/*Vorlons use their own enhancement set */		
		Enhancements::nonstandardEnhancementSet($this, 'VorlonShip');
		
         
        $this->addPrimarySystem(new MagGravReactorTechnical(99, 99, 0, 0));
		$this->addPrimarySystem(new PowerCapacitor( 8, 64, 0, 22, false));//armor, structure, power req, output, has petals 
        $this->addPrimarySystem(new CnC(8, 16, 0, 0));
		$scanner = new Scanner(7, 24, 0, 14);//Vorlon Scanners do not need power - base systems are included in zero hull running costs
		$scanner->markAdvanced();
		$this->addPrimarySystem($scanner);			
		$this->addPrimarySystem(new Hangar(6, 14, 12));
		$this->addPrimarySystem(new Engine(7, 36, 0, 24, 6));
        $this->addPrimarySystem(new SelfRepair(7, 20, 12)); //armor, structure, output
		$AAC = $this->createAdaptiveArmorController(6, 3, 3); //$AAtotal, $AApertype, $AApreallocated
		$this->addPrimarySystem( $AAC );
		
		
        $this->addFrontSystem(new VorlonDischargeGun(6, 0, 0, 270, 90));
        $this->addFrontSystem(new VorlonLightningCannon(6, 0, 0, 240, 60, 'L'));
        $this->addFrontSystem(new VorlonLightningCannon(6, 0, 0, 300, 120, 'R'));
        $this->addFrontSystem(new EMShield(4, 6, 0, 5, 240, 60));
        $this->addFrontSystem(new EMShield(4, 6, 0, 5, 270, 90));
        $this->addFrontSystem(new EMShield(4, 6, 0, 5, 300, 120));
        $this->addFrontSystem(new GraviticThruster(6, 20, 0, 8, 1));
        $this->addFrontSystem(new GraviticThruster(6, 20, 0, 8, 1));


        $this->addAftSystem(new VorlonDischargeGun(6, 0, 0, 90, 270));
        $this->addAftSystem(new EMShield(4, 6, 0, 5, 120, 300));
        $this->addAftSystem(new EMShield(4, 6, 0, 5, 90, 270));
        $this->addAftSystem(new EMShield(4, 6, 0, 5, 60, 240));
		$this->addAftSystem(new JumpEngine(6, 25, 0, 6));//Vorlon Jump Engines normally do use power (the only system onboard that does so), but still are counted as base running costs - in FV I simplify to 0 power requirement
        $this->addAftSystem(new GraviticThruster(6, 20, 0, 6, 2));
        $this->addAftSystem(new GraviticThruster(6, 20, 0, 6, 2));
        $this->addAftSystem(new GraviticThruster(6, 20, 0, 6, 2));
        $this->addAftSystem(new GraviticThruster(6, 20, 0, 6, 2));
		
		
        $this->addLeftSystem(new VorlonLightningCannon(6, 0, 0, 240, 60, 'L'));
        $this->addLeftSystem(new VorlonLightningCannon(6, 0, 0, 240, 60, 'L'));
        $this->addLeftSystem(new VorlonLightningCannon(6, 0, 0, 240, 60, 'L'));
        $this->addLeftSystem(new GraviticThruster(6, 35, 0, 9, 3));
		
        $this->addRightSystem(new VorlonLightningCannon(6, 0, 0, 300, 120, 'R'));
        $this->addRightSystem(new VorlonLightningCannon(6, 0, 0, 300, 120, 'R'));
        $this->addRightSystem(new VorlonLightningCannon(6, 0, 0, 300, 120, 'R'));
        $this->addRightSystem(new GraviticThruster(6, 35, 0, 9, 4));
		

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 6, 96));
        $this->addAftSystem(new Structure( 6, 106));
        $this->addLeftSystem(new Structure( 6, 108));
        $this->addRightSystem(new Structure( 6, 108));
        $this->addPrimarySystem(new Structure( 8, 96 ));
		
		
	
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
				3 => "Thruster",
				9 => "Lightning Cannon", 
				10 => "Discharge Gun",
				13 => "EM Shield",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array( //Aft
				6 => "Thruster",
				8 => "Jump Drive",
				9 => "Discharge Gun",
				12 => "EM Shield",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array( //Fwd
				4 => "Thruster",
				10 => "Lightning Cannon",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array( //Fwd
				4 => "Thruster",
				10 => "Lightning Cannon",
				18 => "Structure",
				20 => "Primary",
			),
		);
		
    }
}



?>
