<?php
/* Replaces VorlonStrikeCruiser (2026-09-19), which is now hidden with variantOf = "NONE" but stays
   loadable for games already in progress. Identical hull; the only change is that each Lightning Gun is
   ONE VorlonLightningGunSplit system with two guns, instead of a Lightning Gun + Mirror Lightning Gun
   pair. A new class rather than an edit because system ids are positional - dropping the four mirror
   systems would renumber every later system in games already using the old hull. */
class VorlonStrikeCruiserNew extends VorlonCapitalShip{
    //NOTE: Still in development
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 2950;
		$this->faction = "Vorlon Empire";
        $this->phpclass = "VorlonStrikeCruiserNew";
        $this->shipClass = "Strike Cruiser";
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
		$this->addAftSystem((new JumpEngine(5, 20, 6, 12, 12))->markCapacitorFed());//HYPERSPACE_IMPROVEMENTS_PLAN.md Stage H3 (user, 2026-09-17): the real Vorlon power requirement, and markCapacitorFed() is what makes it an UPKEEP - the drive pays it again out of the Power Capacitor for every turn it holds its jump point open, and in exchange is exempt from the all-systems-dark Maintain rule and from the four-turn cap (JumpEngine::$vortexUpkeep). 5th argument = jump point projection range: Vorlon Empire hulls reach 12 hexes, not the standard 4 (JUMP_POINTS_PLAN.md section 2.1)
        $this->addAftSystem(new GraviticThruster(5, 20, 0, 7, 2));
		$this->addAftSystem(new GraviticThruster(5, 20, 0, 7, 2));

		$GunA = new VorlonLightningGunSplit(5, 0, 0, 240, 60, 'L');
		$GunA->overkillArcStructures = array(1, 32);
		$GunA->setStructureHome(array(1, 32));
		$this->addLeftFrontSystem($GunA);
		$GunB = new VorlonLightningGunSplit(5, 0, 0, 240, 60, 'L');
		$GunB->overkillArcStructures = array(1, 32);
		$GunB->setStructureHome(array(1, 32));
		$this->addLeftFrontSystem($GunB);
        $this->addLeftSystem(new GraviticThruster(5, 20, 0, 6, 3));

		$GunC = new VorlonLightningGunSplit(5, 0, 0, 300, 120, 'R');
		$GunC->overkillArcStructures = array(1, 42);
		$GunC->setStructureHome(array(1, 42));
		$this->addRightFrontSystem($GunC);
		$GunD = new VorlonLightningGunSplit(5, 0, 0, 300, 120, 'R');
		$GunD->overkillArcStructures = array(1, 42);
		$GunD->setStructureHome(array(1, 42));
		$this->addRightFrontSystem($GunD);
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
				10 => "31:Lightning Gun",
				18 => "Structure",
				20 => "Primary",
			),
			42=> array( //Fwd
				6 => "Thruster",
				10 => "41:Lightning Gun",
				18 => "Structure",
				20 => "Primary",
			),
		);

    }
}



?>
