<?php
class OrestesBeta extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 500;
		$this->faction = 'EA (early)';//"EA defenses";
		$this->phpclass = "OrestesBeta";
		$this->imagePath = "img/ships/orestes.png";
		$this->shipClass = "Orestes System Monitor (Beta)";
	        $this->variantOf = "Orestes System Monitor (Alpha)";
			$this->occurence = "common";
 		$this->unofficial = true;
        $this->shipSizeClass = 3;
        $this->limited = 33;

		$this->fighters = array("normal"=>12);
	    
	    $this->isd = 2168;

		$this->forwardDefense = 16;
		$this->sideDefense = 16;

		$this->turncost = 1;
		$this->turndelaycost = 1;
		$this->accelcost = 5;
		$this->rollcost = 3;
		$this->pivotcost = 4;

		$this->iniativebonus = -10;

		$this->addPrimarySystem(new Reactor(4, 16, 0, 0));
		$this->addPrimarySystem(new CnC(5, 16, 0, 0));
		$this->addPrimarySystem(new Scanner(5, 14, 3, 5));
		$this->addPrimarySystem(new Engine(4, 11, 0, 5, 4));
		$this->addPrimarySystem(new Hangar(4, 14));
		$this->addPrimarySystem(new LightParticleBeamShip(3, 2, 1, 0, 360));
		$this->addPrimarySystem(new LightParticleBeamShip(3, 2, 1, 0, 360));

		$this->addFrontSystem(new Thruster(3, 20, 0, 5, 1));
		$this->addFrontSystem(new SoMissileRack(3, 6, 0, 240, 60));
		$this->addFrontSystem(new HeavyPlasma(3, 8, 5, 300, 60));
		$this->addFrontSystem(new InterceptorPrototype(2, 4, 1, 270, 90));
		$this->addFrontSystem(new HeavyPlasma(3, 8, 5, 300, 60));
		$this->addFrontSystem(new SoMissileRack(3, 6, 0, 300, 120));

		$this->addAftSystem(new Thruster(4, 20, 0, 5, 2));
		$this->addAftSystem(new SoMissileRack(3, 6, 0, 120, 300));
		$this->addAftSystem(new InterceptorPrototype(2, 4, 1, 90, 270));
		$this->addAftSystem(new SoMissileRack(3, 6, 0, 60, 240));

		$this->addLeftSystem(new Thruster(3, 15, 0, 3, 3));
		$this->addLeftSystem(new MediumPlasma(3, 5, 3, 180, 360));
		$this->addLeftSystem(new MediumPlasma(3, 5, 3, 180, 360));

		$this->addRightSystem(new Thruster(3, 15, 0, 3, 4));
		$this->addRightSystem(new MediumPlasma(3, 5, 3, 0, 180));
		$this->addRightSystem(new MediumPlasma(3, 5, 3, 0, 180));
        
        $this->addFrontSystem(new Structure( 4, 42));
        $this->addAftSystem(new Structure( 4, 40));
        $this->addLeftSystem(new Structure( 4, 60));
        $this->addRightSystem(new Structure( 4, 60));
        $this->addPrimarySystem(new Structure( 5, 60));
		
		
		$this->hitChart = array(
                0=> array(
                        9 => "Structure",
                        11 => "Light Particle Beam",
                        13 => "Scanner",
                        15 => "Engine",
                        17 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        4 => "Thruster",
                        6 => "Heavy Plasma Cannon",
                        9 => "Class-SO Missile Rack",
                        11 => "Interceptor Prototype",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        6 => "Thruster",
                        8 => "Class-SO Missile Rack",
                        10 => "Interceptor Prototype",
                        18 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        4 => "Thruster",
                        9 => "Medium Plasma Cannon",
                        18 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                        4 => "Thruster",
                        9 => "Medium Plasma Cannon",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );

    }
}
?>
