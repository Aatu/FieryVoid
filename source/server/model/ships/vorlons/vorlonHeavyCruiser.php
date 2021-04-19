<?php
class VorlonHeavyCruiser extends VorlonCapitalShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 3250;
		$this->faction = "Vorlons";
        $this->phpclass = "VorlonHeavyCruiser";
        $this->shipClass = "Heavy Cruiser";
        //$this->variantOf = "Battle Destroyer";
        $this->imagePath = "img/ships/VorlonHeavyCruiser.png";
        $this->canvasSize = 200;
	    $this->isd = 'Ancient';
        $this->shipSizeClass = 3; 
		$this->factionAge = 3; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
				
        $this->gravitic = true;
		$this->advancedArmor = true;   
        
        $this->forwardDefense = 16;
        $this->sideDefense = 19;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 1;
        $this->pivotcost = 3;
		$this->iniativebonus = 2 *5;
		
		$this->fighters = array("heavy"=>6);
        
		/*Vorlons use their own enhancement set */		
		Enhancements::nonstandardEnhancementSet($this, 'VorlonShip');
		
         
        $this->addPrimarySystem(new MagGravReactorTechnical(99, 99, 0, 0));
		$this->addPrimarySystem(new PowerCapacitor( 7, 32, 0, 14, false));//armor, structure, power req, output, has petals 
        $this->addPrimarySystem(new CnC(7, 16, 0, 0));
		$scanner = new Scanner(7, 20, 0, 14);//Vorlon Scanners do not need power - base systems are included in zero hull running costs
		$scanner->markAdvanced();
		$this->addPrimarySystem($scanner);			
		$this->addPrimarySystem(new Hangar(5, 8, 6));
		$this->addPrimarySystem(new Engine(6, 25, 0, 16, 4));
        $this->addPrimarySystem(new SelfRepair(6, 12, 8)); //armor, structure, output
		$AAC = $this->createAdaptiveArmorController(6, 3, 3); //$AAtotal, $AApertype, $AApreallocated
		$this->addPrimarySystem( $AAC );
		
		
        $this->addFrontSystem(new VorlonDischargeGun(5, 0, 0, 240, 120));
        $this->addFrontSystem(new EMShield(4, 6, 0, 4, 240, 60));
        $this->addFrontSystem(new EMShield(4, 6, 0, 4, 300, 120));
        $this->addFrontSystem(new GraviticThruster(5, 15, 0, 5, 1));
        $this->addFrontSystem(new GraviticThruster(5, 15, 0, 5, 1));


        $this->addAftSystem(new EMShield(4, 6, 0, 4, 120, 300));
        $this->addAftSystem(new EMShield(4, 6, 0, 4, 60, 240));
		$this->addAftSystem(new JumpEngine(5, 20, 0, 8));//Vorlon Jump Engines normally do use power (the only system onboard that does so), but still are counted as base running costs - in FV I simplify to 0 power requirement
        $this->addAftSystem(new GraviticThruster(5, 15, 0, 4, 2));
		$this->addAftSystem(new GraviticThruster(5, 15, 0, 4, 2));
		$this->addAftSystem(new GraviticThruster(5, 15, 0, 4, 2));
		$this->addAftSystem(new GraviticThruster(5, 15, 0, 4, 2));
		
		
        $this->addLeftFrontSystem(new VorlonLightningCannon(5, 0, 0, 240, 60, 'L'));
        $this->addLeftFrontSystem(new VorlonLightningCannon(5, 0, 0, 240, 60, 'L'));
        $this->addLeftSystem(new GraviticThruster(5, 25, 0, 7, 3));
		
        $this->addRightFrontSystem(new VorlonLightningCannon(5, 0, 0, 300, 120, 'R'));
        $this->addRightFrontSystem(new VorlonLightningCannon(5, 0, 0, 300, 120, 'R'));
        $this->addRightSystem(new GraviticThruster(5, 25, 0, 7, 4));
		

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 6, 72));
        $this->addAftSystem(new Structure( 6, 66));
        $this->addLeftSystem(new Structure( 6, 90));
        $this->addRightSystem(new Structure( 6, 90));
        $this->addPrimarySystem(new Structure( 7, 72 ));
		
		
	
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
				6 => "31:Lightning Cannon", 
				8 => "41:Lightning Cannon", 
				10 => "Discharge Gun",
				12 => "EM Shield",
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
			32=> array( //Fwd
				6 => "Thruster",
				10 => "31:Lightning Cannon",
				18 => "Structure",
				20 => "Primary",
			),
			42=> array( //Fwd
				6 => "Thruster",
				10 => "41:Lightning Cannon",
				18 => "Structure",
				20 => "Primary",
			),
			31=> array( //Fwd
				6 => "32:Thruster",
				10 => "31:Lightning Cannon",
				18 => "32:Structure",
				20 => "Primary",
			),
			41=> array( //Fwd
				6 => "42:Thruster",
				10 => "41:Lightning Cannon",
				18 => "42:Structure",
				20 => "Primary",
			),
		);
		
    }
}



?>
