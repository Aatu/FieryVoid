<?php
class VorlonPlanetKiller extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 8000;
		$this->faction = "Vorlon Empire";
        $this->phpclass = "VorlonPlanetKiller";
        $this->shipClass = "Planet Killer";
        $this->imagePath = "img/ships/VorlonPlanetKiller.png";
        $this->canvasSize = 550;
	    $this->isd = 'Ancient';
        $this->shipSizeClass = 3; 
		$this->factionAge = 3; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
		$this->Enormous = true;
		//$this->variantOf = "NONE";
				
        $this->gravitic = true;
		$this->advancedArmor = true;   
        
        $this->forwardDefense = 26;
        $this->sideDefense = 26;
        
        $this->turncost = 3;
        $this->turndelaycost = 3;
        $this->accelcost = 18;
        $this->rollcost = 99;
        $this->pivotcost = 99;
		$this->iniativebonus = -10 *5;
		$this->notes = "Only to be used in specific scenarios, not for normal battles";
        
		/*Vorlons use their own enhancement set */		
		Enhancements::nonstandardEnhancementSet($this, 'VorlonShip');
		
         
        $this->addPrimarySystem(new MagGravReactorTechnical(99, 99, 0, 0));
		$this->addPrimarySystem(new PowerCapacitor( 8, 105, 0, 10, false));//armor, structure, power req, output, has petals 
        $this->addPrimarySystem(new CnC(8, 28, 0, 0));
		$scanner = new Scanner(7, 24, 0, 14);//Vorlon Scanners do not need power - base systems are included in zero hull running costs
		$scanner->markAdvanced();
		$this->addPrimarySystem($scanner);			
		$this->addPrimarySystem(new Hangar(6, 6, 2));
		$this->addPrimarySystem(new Engine(7, 41, 0, 36, 10));
        $this->addPrimarySystem(new SelfRepair(7, 20, 12)); //armor, structure, output
		$AAC = $this->createAdaptiveArmorController(6, 3, 4); //$AAtotal, $AApertype, $AApreallocated
		$this->addPrimarySystem( $AAC );
		$this->addPrimarySystem(new JumpEngine(8, 25, 0, 8, 12));//Vorlon Jump Engines normally do use power (the only system onboard that does so), but still are counted as base running costs - in FV I simplify to 0 power requirement. 5th argument = jump point projection range: Vorlon Empire hulls reach 12 hexes, not the standard 4 (JUMP_POINTS_PLAN.md section 2.1)		
		
		
        $this->addFrontSystem(new PlanetCrackerBeam(8, 110, 0, 0, 0));
        $this->addFrontSystem(new EMShield(6, 6, 0, 6, 240, 60));
        $this->addFrontSystem(new EMShield(6, 6, 0, 6, 300, 120));
        $this->addFrontSystem(new GraviticThruster(7, 30, 0, 9, 1));
        $this->addFrontSystem(new GraviticThruster(7, 30, 0, 9, 1));


        $this->addAftSystem(new EMShield(6, 6, 0, 6, 120, 300));
        $this->addAftSystem(new EMShield(6, 6, 0, 6, 60, 240));
        $this->addAftSystem(new GraviticThruster(7, 20, 0, 6, 2));
        $this->addAftSystem(new GraviticThruster(7, 20, 0, 6, 2));
        $this->addAftSystem(new GraviticThruster(7, 20, 0, 6, 2));
        $this->addAftSystem(new GraviticThruster(7, 20, 0, 6, 2));
        $this->addAftSystem(new GraviticThruster(7, 20, 0, 6, 2));
        $this->addAftSystem(new GraviticThruster(7, 20, 0, 6, 2));								
		
		
        $this->addLeftSystem(new EMShield(6, 6, 0, 6, 180, 360));
        $this->addLeftSystem(new GraviticThruster(7, 40, 0, 9, 3));
		
        $this->addRightSystem(new EMShield(6, 6, 0, 6, 0, 180));
        $this->addRightSystem(new GraviticThruster(7, 40, 0, 9, 4));
		

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 7, 216));
        $this->addAftSystem(new Structure( 7, 216));
        $this->addLeftSystem(new Structure( 7, 216));
        $this->addRightSystem(new Structure( 7, 216));
        $this->addPrimarySystem(new Structure( 7, 216 ));
		
		
	
		$this->hitChart = array(
			0=> array( //PRIMARY
				9 => "Structure",
				10 => "Jump Engine",
				12 => "Self Repair",
				14 => "Scanner",
				16 => "Engine",
				17 => "Hangar",
				19 => "Power Capacitor",
				20 => "C&C",
			),
			1=> array( //Fwd
				5 => "Thruster",
				9 => "Planet-Cracker Beam", 
				11 => "EM Shield",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array( //Aft
				6 => "Thruster",
				8 => "EM Shield",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array( //Fwd
				6 => "Thruster",
				8 => "EM Shield",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array( //Fwd
				6 => "Thruster",
				8 => "EM Shield",
				18 => "Structure",
				20 => "Primary",
			),
		);
		
    }
}



?>
