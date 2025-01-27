<?php
class VorlonStrikeCruiser extends VorlonCapitalShip{
    //NOTE: Still in development
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 2950;
		$this->faction = "Vorlon Empire";
        $this->phpclass = "VorlonStrikeCruiser";
        $this->shipClass = "Strike Cruiser";
//				$this->variantOf = "UNDER CONSTRUCTION";
        $this->imagePath = "img/ships/VorlonStrikeCruiser.png";
        $this->canvasSize = 250;
	    $this->isd = 'Primordial';
        $this->shipSizeClass = 3; 
		$this->factionAge = 4; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
				
        $this->gravitic = true;
		$this->advancedArmor = true;   
        
        $this->forwardDefense = 16;
        $this->sideDefense = 19;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 1;
        $this->pivotcost = 3;
		$this->iniativebonus = 2*5;
		
		/*Vorlons use their own enhancement set */		
		Enhancements::nonstandardEnhancementSet($this, 'VorlonShip');
         
        $this->addPrimarySystem(new MagGravReactorTechnical(99, 99, 0, 0));
		$this->addPrimarySystem(new PowerCapacitor( 7, 40, 0, 10, true));//armor, structure, power req, output, has petals 
        $this->addPrimarySystem(new CnC(7, 16, 0, 0));
		$scanner = new Scanner(7, 20, 0, 12);//Vorlon Scanners do not need power - base systems are included in zero hull running costs
		$scanner->markAdvanced();
		$this->addPrimarySystem($scanner);			
		$this->addPrimarySystem(new Engine(6, 20, 0, 14, 4));
        $this->addPrimarySystem(new SelfRepair(6, 16, 10)); //armor, structure, output
		$AAC = $this->createAdaptiveArmorController(5, 2, 2); //$AAtotal, $AApertype, $AApreallocated
		$this->addPrimarySystem( $AAC );
		
        $this->addFrontSystem(new VorlonDischargePulsar(5, 0, 0, 240, 120));
        $this->addFrontSystem(new EMShield(4, 6, 0, 3, 240, 60));
        $this->addFrontSystem(new EMShield(4, 6, 0, 3, 300, 120));
        $this->addFrontSystem(new GraviticThruster(5, 13, 0, 5, 1));
        $this->addFrontSystem(new GraviticThruster(5, 13, 0, 5, 1));

        $this->addAftSystem(new EMShield(4, 6, 0, 3, 120, 300));
        $this->addAftSystem(new EMShield(4, 6, 0, 3, 60, 240));
		$this->addAftSystem(new JumpEngine(5, 20, 0, 12));//Vorlon Jump Engines normally do use power (the only system onboard that does so), but still are counted as base running costs - in FV I simplify to 0 power requirement
        $this->addAftSystem(new GraviticThruster(5, 20, 0, 7, 2));
		$this->addAftSystem(new GraviticThruster(5, 20, 0, 7, 2));
		
		$GunA = new VorlonLightningGun(5, 0, 0, 240, 60, 'L', 'A');
		$GunA2 = new VorlonLightningGun2(5, 0, 0, 240, 60, 'L', 'A'); 
		$GunA->addMirror($GunA2);
		$this->addLeftFrontSystem($GunA);
		$this->addLeftFrontSystem($GunA2);
		$GunB = new VorlonLightningGun(5, 0, 0, 240, 60, 'L', 'B');
		$GunB2 = new VorlonLightningGun2(5, 0, 0, 240, 60, 'L', 'B'); 
		$GunB->addMirror($GunB2);
		$this->addLeftFrontSystem($GunB);
		$this->addLeftFrontSystem($GunB2);
//        $this->addLeftFrontSystem(new VorlonLightningGun(5, 0, 0, 240, 60, 'L'));
//        $this->addLeftFrontSystem(new VorlonLightningGun(5, 0, 0, 240, 60, 'L'));
        $this->addLeftSystem(new GraviticThruster(5, 20, 0, 6, 3));
		
		$GunC = new VorlonLightningGun(5, 0, 0, 300, 120, 'R', 'C');
		$GunC2 = new VorlonLightningGun2(5, 0, 0, 300, 120, 'R', 'C'); 
		$GunC->addMirror($GunC2);
		$this->addRightFrontSystem($GunC);
		$this->addRightFrontSystem($GunC2);
		$GunD = new VorlonLightningGun(5, 0, 0, 300, 120, 'R', 'D');
		$GunD2 = new VorlonLightningGun2(5, 0, 0, 300, 120, 'R', 'D'); 
		$GunD->addMirror($GunD2);
		$this->addRightFrontSystem($GunD);
		$this->addRightFrontSystem($GunD2);
//        $this->addRightFrontSystem(new VorlonLightningGun(5, 0, 0, 300, 120, 'R'));
//        $this->addRightFrontSystem(new VorlonLightningGun(5, 0, 0, 300, 120, 'R'));
        $this->addRightSystem(new GraviticThruster(5, 20, 0, 6, 4));
		
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 6, 72));
        $this->addAftSystem(new Structure( 6, 66));
        $this->addLeftSystem(new Structure( 6, 72));
        $this->addRightSystem(new Structure( 6, 72));
        $this->addPrimarySystem(new Structure( 6, 72 ));
	
		$this->hitChart = array(
			0=> array( //PRIMARY
				10 => "Structure",
				12 => "Self Repair",
				14 => "Scanner",
				16 => "Engine",
				19 => "Power Capacitor",
				20 => "C&C",
			),
			1=> array( //Fwd
				4 => "Thruster",
				8 => "TAG:Lightning Gun", 
				10 => "Discharge Pulsar",
				12 => "EM Shield",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array( //Aft
				6 => "Thruster",
				8 => "Jump Engine",
				10 => "EM Shield",
				18 => "Structure",
				20 => "Primary",
			),
			32=> array( //Fwd
				6 => "Thruster",
				10 => "TAG:Lightning Gun",
				18 => "Structure",
				20 => "Primary",
			),
			42=> array( //Fwd
				6 => "Thruster",
				10 => "TAG:Lightning Gun",
				18 => "Structure",
				20 => "Primary",
			),
			31=> array( //Fwd
				6 => "32:Thruster",
				10 => "TAG:Lightning Gun",
				18 => "32:Structure",
				20 => "Primary",
			),
			41=> array( //Fwd
				6 => "42:Thruster",
				10 => "TAG:Lightning Gun",
				18 => "42:Structure",
				20 => "Primary",
			),
		);
		
    }
}



?>
