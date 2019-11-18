<?php
class DaggadenPod extends LCV{
	/*Llort Daggaden LCV*/
	/*approximated as MCV, no EW restrictions*/
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 275;
        $this->faction = "Llort";
	$this->phpclass = "DaggadenPod";
	$this->shipClass = "Daggaden Penetrator Pod";
	$this->imagePath = "img/ships/LlortDaggadenPod.png";
	$this->canvasSize = 100;

        $this->occurence = "common";
        $this->variantOf = 'Daggaden Penetrator';

	$this->forwardDefense = 9;
	$this->sideDefense = 12;
	$this->isd = 2216;
	$this->turncost = 0.25;
	$this->turndelaycost = 0.25;
	$this->accelcost = 2;
	$this->rollcost = 1;
	$this->pivotcost = 1;
	$this->iniativebonus = 14 *5;
	$this->addFrontSystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
	$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
	$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
	$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance
	$this->addPrimarySystem(new Reactor(3, 10, 0, 0));
	$this->addPrimarySystem(new CnC(99, 99, 0, 0)); //C&C should be unhittable anyway
	$this->addPrimarySystem(new Scanner(3, 10, 3, 3));
	$this->addPrimarySystem(new Engine(4, 9, 0, 4, 1));
	$this->addPrimarySystem(new CargoBay(2, 20));
	$this->addPrimarySystem(new TwinArray(2, 6, 2, 180, 270));
	$this->addPrimarySystem(new MediumPlasma(3, 5, 3, 300, 90));
	$this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
	$this->addPrimarySystem(new Structure( 4, 36));
        $this->hitChart = array(
        		0=> array( //should never happen
        				20 => "Structure",
        		),
        		1=> array( //PRIMARY hit table, effectively
        				9 => "Structure",
        				11 => "0:Cargo Bay",
        				13 => "0:Medium Plasma Cannon",
        				15 => "0:Twin Array",
        				16 => "0:Light Particle Beam",
        				18 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		2=> array( //same as Fwd
        				9 => "Structure",
        				11 => "0:Cargo Bay",
        				13 => "0:Medium Plasma Cannon",
        				15 => "0:Twin Array",
        				16 => "0:Light Particle Beam",
        				18 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		
        ); //end of hit chart
    }
}
?>
