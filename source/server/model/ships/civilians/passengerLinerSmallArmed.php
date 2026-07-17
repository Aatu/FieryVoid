<?php
class passengerLinerSmallArmed extends LCV{
	/*approximated as MCV, no EW restrictions*/
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 70;
    $this->faction = "Civilians";
	$this->phpclass = "passengerLinerSmallArmed";
	$this->shipClass = "Civilian Armed Passenger Liner";
        $this->variantOf = "Civilian Passenger Liner";
        $this->occurence = "common";
	$this->imagePath = "img/ships/CivilianPassengerLinerLCV.png";
	$this->canvasSize = 100;
	$this->isCombatUnit = false; //not a combat unit, it will never be present in a regular battlegroup

	$this->notes = 'Atmospheric Capable.';
	$this->forwardDefense = 10;
	$this->sideDefense = 12;
	$this->isd = 2193;
	
	$this->turncost = 0.33;
	$this->turndelaycost = 0.33;
	$this->accelcost = 2;
	$this->rollcost = 1;
	$this->pivotcost = 1;
	$this->iniativebonus = 0;
	
	$this->addFrontSystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
	$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
	$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
	$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance
	
	$this->addPrimarySystem(new Reactor(3, 3, 0, 0));
	$this->addPrimarySystem(new CnC(99, 99, 0, 0)); //C&C should be unhittable anyway
	$this->addPrimarySystem(new Scanner(3, 8, 2, 2));
	$this->addPrimarySystem(new Engine(3, 9, 0, 4, 3));
    $this->addPrimarySystem(new Quarters(2, 9));
	$this->addFrontSystem(new LightParticleBeamShip(1, 2, 1, 240, 120));
	$this->addPrimarySystem(new Structure(3, 30));
        $this->hitChart = array(
        		0=> array( //should never happen
        				20 => "Structure",
        		),
        		1=> array( //PRIMARY hit table, effectively
        				10 => "Structure",
						11 => "Light Particle Beam",
        				14 => "0:Quarters",
        				17 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		2=> array( //same as Fwd
        				10 => "Structure",
						11 => "Light Particle Beam",
        				14 => "0:Quarters",
        				17 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		
        ); //end of hit chart
    }
}
?>
