<?php
class PolarenGrantir extends MediumShipLeftRight{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 335;
        $this->faction = "Nexus Polaren Confederacy (early)";
        $this->phpclass = "PolarenGrantir";
        $this->imagePath = "img/ships/Nexus/polarenNorevet.png";
        $this->shipClass = "Grantir Police Frigate";
			$this->variantOf = "Norevet Heavy Frigate";
			$this->occurence = "common";
		$this->unofficial = true;
        $this->canvasSize = 110;
	    $this->isd = 1717;

        $this->fighters = array("assault shuttles"=>1); //1 breaching pod    
	    $this->notes = 'Atmospheric capable';
        
        $this->forwardDefense = 12;
        $this->sideDefense = 12;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 60;
         
        $this->addPrimarySystem(new Reactor(3, 12, 0, 0));
        $this->addPrimarySystem(new CnC(3, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 9, 3, 5));
        $this->addPrimarySystem(new Engine(3, 12, 0, 8, 3));
        $this->addAftSystem(new Thruster(2, 8, 0, 4, 1));
        $this->addAftSystem(new Thruster(2, 13, 0, 8, 2));        
		$this->addFrontSystem(new NexusSandCaster(1, 4, 2, 0, 360));
        
		$this->addLeftSystem(new StunBeam(2, 6, 5, 240, 360));
		$this->addLeftSystem(new NexusLightMaser(2, 4, 2, 180, 360));
		$this->addLeftSystem(new NexusLightMaser(2, 4, 2, 180, 360));
        $this->addLeftSystem(new Thruster(2, 10, 0, 4, 3));
	    
		$this->addRightSystem(new StunBeam(2, 6, 5, 0, 120));
		$this->addRightSystem(new NexusLightMaser(2, 4, 2, 0, 180));
		$this->addRightSystem(new NexusLightMaser(2, 4, 2, 0, 180));
        $this->addRightSystem(new Thruster(2, 10, 0, 4, 4));
        
        $this->addPrimarySystem(new Structure(3, 33));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			9 => "Structure",
			11 => "2:Thruster",
			12 => "1:Sand Caster",
			14 => "Scanner",
			16 => "Engine",
			17 => "Hangar",
			19 => "Reactor",
			20 => "C&C",
		),

		3=> array(
			5 => "Thruster",
			7 => "Stun Beam",
			9 => "Light Maser",
			17 => "Structure",
			20 => "Primary",
		),

		4=> array(
			5 => "Thruster",
			7 => "Stun Beam",
			9 => "Light Maser",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>
