<?php
class VorlonDestroyerEscort extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 1300;
		$this->faction = "Vorlons";
        $this->phpclass = "VorlonDestroyerEscort";
        $this->shipClass = "Destroyer Escort";
        $this->variantOf = "Battle Destroyer";
        $this->occurence = "uncommon";		
		
        $this->imagePath = "img/ships/VorlonBattleDestroyer.png";
        $this->canvasSize = 200;
	    $this->isd = 'Ancient';
        $this->shipSizeClass = 2; 
		$this->factionAge = 3; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
				
        $this->gravitic = true;
		$this->advancedArmor = true;   
        
        $this->forwardDefense = 14;
        $this->sideDefense = 17;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 1;
        $this->pivotcost = 2;
		$this->iniativebonus = 8 *5;
		
		//$this->fighters = array("heavy"=>12);
        
		/*Vorlons use their own enhancement set */		
		Enhancements::nonstandardEnhancementSet($this, 'VorlonShip');
		
         
        $this->addPrimarySystem(new MagGravReactorTechnical(99, 99, 0, 0));
		$this->addPrimarySystem(new PowerCapacitor( 6, 24, 0, 12, true));//armor, structure, power req, output, has petals 
        $this->addPrimarySystem(new CnC(6, 16, 0, 0));
		$scanner = new Scanner(6, 16, 0, 12);//Vorlon Scanners do not need power - base systems are included in zero hull running costs
		$scanner->markAdvanced();
		$this->addPrimarySystem($scanner);			
		$this->addPrimarySystem(new Hangar(4, 2, 0));
		$this->addPrimarySystem(new Engine(6, 17, 0, 12, 3));
        $this->addPrimarySystem(new SelfRepair(5, 6, 4)); //armor, structure, output
		$AAC = $this->createAdaptiveArmorController(5, 2, 2); //$AAtotal, $AApertype, $AApreallocated
		$this->addPrimarySystem( $AAC );
        $this->addPrimarySystem(new GraviticThruster(5, 20, 0, 6, 3));
        $this->addPrimarySystem(new GraviticThruster(5, 20, 0, 6, 4));
		
		
        $this->addFrontSystem(new VorlonDischargeGun(4, 0, 0, 120, 360));
        $this->addFrontSystem(new VorlonDischargeGun(4, 0, 0, 240, 120));
        $this->addFrontSystem(new VorlonDischargeGun(4, 0, 0, 0, 240));
        $this->addFrontSystem(new EMShield(4, 6, 0, 4, 240, 60));
        $this->addFrontSystem(new EMShield(4, 6, 0, 4, 300, 120));
        $this->addFrontSystem(new GraviticThruster(5, 11, 0, 5, 1));
        $this->addFrontSystem(new GraviticThruster(5, 11, 0, 5, 1));

        $this->addAftSystem(new EMShield(4, 6, 0, 4, 120, 300));
        $this->addAftSystem(new EMShield(4, 6, 0, 4, 60, 240));
		$this->addAftSystem(new JumpEngine(5, 16, 0, 12));//Vorlon Jump Engines normally do use power (the only system onboard that does so), but still are counted as base running costs - in FV I simplify to 0 power requirement
        $this->addAftSystem(new GraviticThruster(5, 11, 0, 3, 2));
        $this->addAftSystem(new GraviticThruster(5, 11, 0, 3, 2));
        $this->addAftSystem(new GraviticThruster(5, 11, 0, 3, 2));
        $this->addAftSystem(new GraviticThruster(5, 11, 0, 3, 2));
		

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 80));
        $this->addAftSystem(new Structure( 5, 66));
        $this->addPrimarySystem(new Structure( 6, 60 ));
		
		
	
		$this->hitChart = array(
			0=> array( //PRIMARY
				8 => "Structure",
				11 => "Thruster",
				12 => "Self Repair",
				14 => "Scanner",
				16 => "Engine",
				17 => "Hangar",
				19 => "Power Capacitor",
				20 => "C&C",
			),
			1=> array( //Fwd
				5 => "Thruster",
				8 => "Discharge Gun",
				10 => "EM Shield",
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
		);
		
    }
}



?>
