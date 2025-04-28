<?php
class QShip extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 350;
		$this->faction = "Civilians";
        $this->phpclass = "QShip";
        $this->imagePath = "img/ships/civilianFreighter.png";
        $this->shipClass = "Commercial Freighter (Q-Ship)";
        $this->canvasSize = 100;
	    $this->isCombatUnit = false; //not a combat unit, it will never be present in a regular battlegroup
		$this->isd = 2196;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 15;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 999;
        $this->pivotcost = 999;
		$this->iniativebonus = 0;
		$this->fighters = array("normal"=>6);
		         
        $this->addPrimarySystem(new Reactor(3, 12, 0, 0));
        $this->addPrimarySystem(new CnC(3, 5, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 12, 3, 4));
        $this->addPrimarySystem(new Engine(3, 6, 0, 6, 3));
		$this->addPrimarySystem(new Hangar(3, 8));
		$this->addPrimarySystem(new StdParticleBeam(2, 4, 2, 0, 360));
		$this->addPrimarySystem(new Thruster(2, 10, 0, 3, 3));
		$this->addPrimarySystem(new Thruster(2, 10, 0, 3, 4));
		$this->addPrimarySystem(new ReloadRack(3, 9));
		
        $this->addFrontSystem(new Thruster(2, 8, 0, 5, 1));
        $this->addFrontSystem(new HeavyPlasma(2, 8, 5, 180, 360));
        $this->addFrontSystem(new HeavyPlasma(2, 8, 5, 0, 180));
        $this->addFrontSystem(new SMissileRack(2, 6, 0, 180, 360));
        $this->addFrontSystem(new SMissileRack(2, 6, 0, 0, 180));
        
        $this->addAftSystem(new Thruster(2, 12, 0, 4, 2));
        $this->addAftSystem(new HeavyPlasma(2, 8, 5, 180, 360));
        $this->addAftSystem(new HeavyPlasma(2, 8, 5, 0, 180));
        $this->addAftSystem(new SMissileRack(2, 6, 0, 180, 360));
        $this->addAftSystem(new SMissileRack(2, 6, 0, 0, 180));
        
        $this->addPrimarySystem(new Structure( 3, 56));
        
        $this->hitChart = array(
        		0=> array(
        				6 => "Thruster",
        				7 => "Reload Rack",
        				9 => "Standard Particle Beam",
        				12 => "Scanner",
        				15 => "Engine",
        				17 => "Hangar",
        				19 => "Reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				6 => "Thruster",
        				8 => "Heavy Plasma Cannon",
        				10 => "Class-S Missile Rack",
        				17 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				6 => "Thruster",
        				8 => "Heavy Plasma Cannon",
        				10 => "Class-S Missile Rack",
        				17 => "Structure",
        				20 => "Primary",
        		),
        );
    }
}
?>
