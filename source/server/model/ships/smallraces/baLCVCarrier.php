<?php
class BALCVCarrier extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 400;
        $this->faction = "Small Races";
        $this->phpclass = "BALCVCarrier";
        $this->imagePath = "img/ships/BASurveyShip.png";
        $this->shipClass = "BA LCV Carrier";
        $this->shipSizeClass = 3;
        $this->fighters = array("medium"=>6, "LCVs"=>4); //part of cargo bays dropped for LCV rails, fighter hangar reduced, and also no ElInt Sensors
	    
		$this->variantOf = 'BA Survey Ship';
		$this->occurence = "common";
        $this->isd = 2234;
        $this->limited = 33;
		
		
		$this->notes = "This ship is intended to allow Belters to field LCVs in pickup battle."; //by the fluff such a power projection ship is not needed for them!
		$this->unofficial = true;    
        
        $this->forwardDefense = 15;
        $this->sideDefense = 18;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 3;
		$this->iniativebonus = -20;

        $this->addPrimarySystem(new Reactor(5, 15, 0, 0));
        $this->addPrimarySystem(new CnC(5, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 16, 4, 6));
        $this->addPrimarySystem(new Engine(5, 16, 0, 8, 4));
        $this->addPrimarySystem(new JumpEngine(4, 16, 5, 38));

        $this->addFrontSystem(new Hangar(4, 10));
        $this->addFrontSystem(new Thruster(4, 20, 0, 6, 1));
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 180, 60));
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 300, 180));
        $this->addFrontSystem(new CargoBay(4, 20));
        $this->addFrontSystem(new CargoBay(4, 20));
        $LCVRail = new Catapult(3, 8);
        $LCVRail->displayName = "LCV Rail";
        $this->addFrontSystem($LCVRail);
        $LCVRail = new Catapult(3, 8);
        $LCVRail->displayName = "LCV Rail";
        $this->addFrontSystem($LCVRail);

        $this->addAftSystem(new Thruster(4, 12, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 12, 0, 4, 2));
        $this->addAftSystem(new StdParticleBeam(2, 4, 1, 120, 360));
        $this->addAftSystem(new StdParticleBeam(2, 4, 1, 0, 240));

        $this->addLeftSystem(new Thruster(3, 13, 0, 5, 3));
        $this->addLeftSystem(new CargoBay(3, 10));
        $LCVRail = new Catapult(3, 8);
        $LCVRail->displayName = "LCV Rail";
        $this->addLeftSystem($LCVRail);

        $this->addRightSystem(new Thruster(3, 13, 0, 5, 4));
        $this->addRightSystem(new CargoBay(3, 10));
        $LCVRail = new Catapult(3, 8);
        $LCVRail->displayName = "LCV Rail";
        $this->addRightSystem($LCVRail);

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 32));
        $this->addAftSystem(new Structure( 4, 32));
        $this->addLeftSystem(new Structure( 4, 40));
        $this->addRightSystem(new Structure( 4, 40));
        $this->addPrimarySystem(new Structure( 4, 32));

        $this->hitChart = array(
                0=> array(
                        7 => "Structure",
                        9 => "Jump Engine",
                        12 => "ELINT Scanner",
                        15 => "Engine",
                        18 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        4 => "Thruster",
                        6 => "Cargo Bay",
                        9 => "LCV Rail",
                        11 => "Hangar",
                        12 => "Standard Particle Beam",
                        17 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        6 => "Thruster",
                        8 => "Standard Particle Beam",
                        17 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        5 => "Thruster",
                        7 => "Cargo Bay",
                        9 => "LCV Rail",
                        17 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                        5 => "Thruster",
                        7 => "Cargo Bay",
                        9 => "LCV Rail",
                        17 => "Structure",
                        20 => "Primary",
                ),
        );
    }
}
?>