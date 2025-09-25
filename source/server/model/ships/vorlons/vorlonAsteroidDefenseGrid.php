<?php
class vorlonAsteroidDefenseGrid extends OSAT{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 1000;
		$this->faction = "Vorlon Empire";
        $this->phpclass = "vorlonAsteroidDefenseGrid";
        $this->imagePath = "img/ships/AsteroidS1.png";
        $this->shipClass = "Asteroid Defense Grid";
        $this->canvasSize = 200;

	    $this->isd = 'Ancient';
		$this->factionAge = 3; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
				
        $this->gravitic = true;
		$this->advancedArmor = true;   
        
        $this->forwardDefense = 12;
        $this->sideDefense = 12;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 70;

		/*Vorlons use their own enhancement set */		
		Enhancements::nonstandardEnhancementSet($this, 'VorlonShip');

//        $this->addFrontSystem(new HvyParticleCannon(5, 12, 9, 300, 60));
        $this->addFrontSystem(new VorlonLightningCannon(4, 0, 0, 240, 60, 'L'));
        $this->addFrontSystem(new VorlonLightningCannon(4, 0, 0, 240, 60, 'L'));
        $this->addFrontSystem(new VorlonLightningCannon(4, 0, 0, 300, 120, 'R'));
        $this->addFrontSystem(new VorlonLightningCannon(4, 0, 0, 300, 120, 'R'));

        $this->addAftSystem(new EMShield(4, 6, 0, 5, 180, 360));
        $this->addAftSystem(new VorlonDischargeGun(4, 0, 0, 0, 360));
        $this->addAftSystem(new GraviticThruster(4, 24, 0, 0, 2));
        $this->addAftSystem(new VorlonDischargeGun(4, 0, 0, 0, 360));
        $this->addAftSystem(new EMShield(4, 6, 0, 5, 0, 180));
        
        $this->addPrimarySystem(new OSATCnC(0, 1, 0, 0));        
        $this->addPrimarySystem(new MagGravReactorTechnical(99, 99, 0, 0));
		$this->addPrimarySystem(new PowerCapacitor( 4, 26, 0, 12, false));//armor, structure, power req, output, has petals 
		$scanner = new Scanner(4, 24, 0, 12);//Vorlon Scanners do not need power - base systems are included in zero hull running costs
			$scanner->markAdvanced();
			$this->addPrimarySystem($scanner);			
        $this->addPrimarySystem(new SelfRepair(4, 6, 4)); //armor, structure, output
			$AAC = $this->createAdaptiveArmorController(4, 2, 2); //$AAtotal, $AApertype, $AApreallocated
			$this->addPrimarySystem( $AAC );
                
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(4, 150));
		
		$this->hitChart = array(
			0=> array(
				6 => "Structure",
				7 => "2:Thruster",
				11 => "1:Lightning Cannon",
				13 => "2:Discharge Gun",
				15 => "2:EM Shield",
				17 => "0:Scanner",
				19 => "0:Power Capacitor",
				20 => "0:Self Repair",
			),
			1=> array(
				20 => "Primary",
			),
			2=> array(
				20 => "Primary",
			),
        );
    }
}

?>
