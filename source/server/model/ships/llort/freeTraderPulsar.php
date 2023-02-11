<?php
class FreeTraderPulsar extends MediumShip{
	/*approximated as MCV, no EW restrictions*/
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 65;
    $this->faction = "Civilians";
	$this->phpclass = "freetraderpulsar";
	$this->shipClass = "Llort Free Trader (Pulsar)";
	$this->imagePath = "img/ships/LlortDaggaden.png";
	$this->canvasSize = 100;
	$this->agile = true;
	$this->forwardDefense = 11;
	$this->sideDefense = 12;
	$this->isd = 2219;
	$this->turncost = 0.33;
	$this->turndelaycost = 0.33;
	$this->accelcost = 4;
	$this->rollcost = 1;
	$this->pivotcost = 1;
	$this->iniativebonus = 0;
	$this->variantOf = "Llort Free Trader";
	$this->occurence = "common";
	    $this->isCombatUnit = false; //not a combat unit, it will never be present in a regular battlegroup
	
	$this->addFrontSystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
	$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
	$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
	$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance
	
	$cA = new CargoBay(2, 20);
	$cB = new CargoBay(2, 10);
	
	$cA->displayName = "Cargo Bay A";
	$cB->displayName = "Cargo Bay B";
	
	$this->addPrimarySystem(new Reactor(4, 10, 0, 0));
	$this->addPrimarySystem(new CnC(99, 99, 0, 0)); //C&C should be unhittable anyway
	$this->addPrimarySystem(new Scanner(3, 8, 2, 2));
	$this->addPrimarySystem(new Engine(3, 10, 0, 4, 1));
	$this->addPrimarySystem($cA);
	$this->addPrimarySystem($cB);

	$this->addPrimarySystem(new ScatterPulsar(2, 4, 2, 180, 360));
	$this->addPrimarySystem(new PlasmaTorch(2, 4, 2, 300, 120));
	
	$this->addPrimarySystem(new Structure(4, 32));
        $this->hitChart = array(
        		0=> array( //should never happen
        				20 => "Structure",
        		),
        		1=> array( //PRIMARY hit table, effectively
        				6 => "Structure",
        				10 => "0:Cargo Bay A",
        				12 => "0:Cargo Bay B",
        				13 => "0:Scatter Pulsar",
        				15 => "0:Plasma Torch",
        				17 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		2=> array( //same as Fwd
        				6 => "Structure",
        				10 => "0:Cargo Bay A",
        				12 => "0:Cargo Bay B",
        				13 => "0:Scatter Pulsar",
        				15 => "0:Plasma Torch",
        				17 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        ); //end of hit chart
    }
}
?>
