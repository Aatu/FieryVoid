<?php
class Fenja extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 300;
		$this->faction = "Kor-Lyan";
        $this->phpclass = "Fenja";
        $this->imagePath = "img/ships/korlyan_axor.png";
        $this->shipClass = "Fenja Assault Leader";
			$this->occurence = "uncommon";
			$this->variantOf = 'Axor Assault Frigate';
        $this->canvasSize = 70;
	    
	    $this->isd = 2243;

	    $this->notes = 'Atmospheric Capable.';
        
        $this->forwardDefense = 10;
        $this->sideDefense = 12;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 1;
        $this->rollcost = 1;
        $this->pivotcost = 1;
		$this->iniativebonus = 65;
         
        $this->addPrimarySystem(new Reactor(3, 10, 0, 0));
        $this->addPrimarySystem(new CnC(3, 9, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 8, 3, 4));
        $this->addPrimarySystem(new Engine(3, 9, 0, 4, 1));
		$this->addPrimarySystem(new Hangar(3, 4));
		$this->addPrimarySystem(new Thruster(3, 10, 0, 3, 3));
		$this->addPrimarySystem(new Thruster(3, 10, 0, 3, 4));
		
        $this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
        $this->addFrontSystem(new StdParticleBeam(1, 4, 1, 240, 60));
		$this->addFrontSystem(new CustomIndustrialGrappler(3, 5, 0, 300, 60));
		$this->addFrontSystem(new LimpetBoreTorp(2, 5, 3, 270, 90));
		$this->addFrontSystem(new CustomIndustrialGrappler(3, 5, 0, 300, 60));
        $this->addFrontSystem(new StdParticleBeam(1, 4, 1, 300, 120));
		
        $this->addAftSystem(new Thruster(3, 6, 0, 2, 2));
		$this->addAftSystem(new LimpetBoreTorp(2, 5, 3, 90, 270));
        $this->addAftSystem(new Thruster(3, 6, 0, 2, 2));
	
        $this->addPrimarySystem(new Structure( 3, 64));
        
		$this->hitChart = array(
                0=> array(
                        8 => "Thruster",
                        11 => "Scanner",
                        14 => "Engine",
						15 => "Hangar",
                        18 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        5 => "Thruster",
						7 => "Standard Particle Beam",
                        9 => "Limpet Bore Torpedo",
						11 => "Industrial Grappler",
                        17 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        6 => "Thruster",
                        8 => "Limpet Bore Torpedo",
                        17 => "Structure",
                        20 => "Primary",
                ),
        );

    }

}



?>