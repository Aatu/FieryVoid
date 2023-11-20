<?php
class Delegor extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 250;
	$this->faction = "Dilgar Imperium";
        $this->phpclass = "Delegor";
        $this->imagePath = "img/ships/Delegor.png";
        $this->shipClass = "Delegor Suicide Frigate";
        $this->canvasSize = 100;
                $this->isd = 2232;
				
	    $this->occurence = 'special'; //common, but can only be taken in specific scenario circumstances!
	    $this->notes = 'CANNOT be taken if Dilgar are not allowed to ram. Otherwise common.';
	    $this->notes .= "<br>+20 ramming hit bonus.";
	    
	    
        $this->forwardDefense = 12;
        $this->sideDefense = 12;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 1;
        $this->rollcost = 1;
        $this->pivotcost = 1;
        $this->iniativebonus = 12 *5;

        $this->addPrimarySystem(new Reactor(2, 8, 0, 0));
        $this->addPrimarySystem(new CnC(3, 3, 0, 0));
        $this->addPrimarySystem(new Scanner(2, 4, 2, 3));
        $this->addPrimarySystem(new Engine(2, 5, 0, 2, 2));
        $this->addPrimarySystem(new Hangar(2, 1));
        $this->addPrimarySystem(new Thruster(2, 6, 0, 3, 3));
        $this->addPrimarySystem(new Thruster(2, 6, 0, 3, 4));

	    $hitBonus = 4; //a bonus for ramming (NOT a penalty in this case)
	    $rammingAttack = new RammingAttack(0, 0, 360, 180, $hitBonus, true, 0); //actual damage - NOT calculated, but designed (180)
		$this->addPrimarySystem($rammingAttack);

        $this->addFrontSystem(new Thruster(2, 6, 0, 2, 1));
        $this->addFrontSystem(new ScatterPulsar(1, 4, 2, 240, 0));
        $this->addFrontSystem(new ScatterPulsar(1, 4, 2, 0, 120));

        $this->addAftSystem(new Thruster(2, 8, 0, 4, 2));
        $this->addAftSystem(new Engine(2, 5, 0, 2, 2));

        $this->addPrimarySystem(new Structure( 3, 100));
	
		
	$this->hitChart = array(
		0=> array(
			9 => "Thruster",
			11 => "Scanner",
			14 => "Engine",
			16 => "Hangar",
			19 => "Reactor",
			20 => "C&C",
		),
		1=> array(
			3 => "Thruster",
			6 => "Scatter Pulsar",
			17 => "Structure",
			20 => "Primary",
		),
		2=> array(
			6 => "Thruster",
			8 => "Engine",
			17 => "Structure",
			20 => "Primary",
		),
	 );
	    
    
    }
}
?>
