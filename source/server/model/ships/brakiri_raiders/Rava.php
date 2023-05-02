<?php
class Rava extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 325;
	$this->faction = "Raiders";
        $this->phpclass = "Rava";
        $this->imagePath = "img/ships/RaiderShokanRava.png";
        $this->shipClass = "Shokan Rava Privateer";
        $this->canvasSize = 100;
        
		$this->notes = 'Used only by Brakiri Shokan Privateers';
		$this->isd = 2236;
        
        $this->forwardDefense = 14;
        $this->sideDefense = 15;
        
        $this->turncost = 1;
        $this->turndelaycost = 0.66;
        $this->accelcost = 4;
        $this->rollcost = 99;
        $this->pivotcost = 99;
		$this->iniativebonus = 6 * 5;
        
        $this->addPrimarySystem(new Reactor(4, 10, 0, 0));
        $this->addPrimarySystem(new CnC(3, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 9, 3, 4));
    	$this->addPrimarySystem(new Hangar(2, 2));
		$this->addPrimarySystem(new CargoBay(2, 12));     	
        $this->addPrimarySystem(new StdParticleBeam(2, 4, 1, 0, 360));    	
    	$this->addPrimarySystem(new GraviticThruster(4, 10, 0, 5, 3));
    	$this->addPrimarySystem(new GraviticThruster(4, 10, 0, 5, 4));
		
        $this->addFrontSystem(new GraviticThruster(4, 8, 0, 2, 1));
        $this->addFrontSystem(new GraviticThruster(4, 8, 0, 2, 1));
        $this->addFrontSystem(new MediumBolter(3, 8, 4, 240, 360));
        $this->addFrontSystem(new MediumBolter(3, 8, 4, 0, 120));        


        $this->addAftSystem(new Engine(3, 10, 0, 12, 3));        
        $this->addAftSystem(new GraviticThruster(4, 10, 0, 5, 2));
        $this->addAftSystem(new GraviticThruster(4, 10, 0, 5, 2));
        $this->addAftSystem(new StdParticleBeam(2, 4, 1, 180, 360));
        $this->addAftSystem(new StdParticleBeam(2, 4, 1, 0, 180));
		
        $this->addPrimarySystem(new Structure( 4, 44));
		
		$this->hitChart = array(
			0=> array(
					8 => "Thruster",
					9 => "Standard Particle Beam",
					11 => "Scanner",
					14 => "Cargo Bay",					
					15 => "Hangar",
					18 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					8 => "Medium Bolter",
					17 => "Structure",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					8 => "Standard Particle Beam",
					11 => "Engine",					
					17 => "Structure",
					20 => "Primary",
			),
		);

    }
}
?>
