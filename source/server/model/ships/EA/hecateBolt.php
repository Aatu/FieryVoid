<?php
class HecateBolt extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 650;
	$this->faction = "EA";
        $this->phpclass = "HecateBolt";
        $this->imagePath = "img/ships/hyperion.png";
        $this->shipClass = "Hecate Bolt Cruiser (Beta)";
        $this->shipSizeClass = 3;
        $this->occurence = "unique";
        $this->limited = 10; //not marked on SCS but since base hull is and this is unique variant of it...
	$this->variantOf = 'Hecate Testbed Cruiser (Alpha)';
	$this->isd = 2232;
        $this->forwardDefense = 14;
        $this->sideDefense = 16;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;        
         
        $this->addPrimarySystem(new Reactor(5, 20, 0, 0));
        $this->addPrimarySystem(new CnC(5, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 18, 3, 6));
        $this->addPrimarySystem(new Engine(5, 17, 0, 6, 4));
	$this->addPrimarySystem(new Hangar(5, 2));
        $this->addPrimarySystem(new StdParticleBeam(2, 4, 1, 0, 360));
	$this->addPrimarySystem(new StdParticleBeam(2, 4, 1, 0, 360));
	$this->addPrimarySystem(new StdParticleBeam(2, 4, 1, 0, 360));
        
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new MediumBolter(3, 8, 4, 300, 60));
	$this->addFrontSystem(new MediumBolter(3, 8, 4, 300, 60));
	$this->addFrontSystem(new MediumBolter(3, 8, 4, 300, 60));
        $this->addFrontSystem(new InterceptorMkI(2, 4, 1, 240, 60));
        $this->addFrontSystem(new InterceptorMkI(2, 4, 1, 300, 120));

        $this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
	$this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
        $this->addAftSystem(new JumpEngine(4, 16, 3, 24));
	$this->addAftSystem(new InterceptorMkI(2, 4, 1, 120, 300));
        $this->addAftSystem(new InterceptorMkI(2, 4, 1, 60, 240));
        
	$this->addLeftSystem(new Thruster(3, 13, 0, 4, 3));
	$this->addLeftSystem(new HeavyBolter(4, 10, 6, 300, 0));
	$this->addLeftSystem(new HeavyBolter(4, 10, 6, 180, 240));
	$this->addLeftSystem(new LightBolter(2, 6, 2, 180, 0));

	$this->addRightSystem(new Thruster(3, 13, 0, 4, 4));
	$this->addRightSystem(new HeavyBolter(4, 10, 6, 0, 60));
	$this->addRightSystem(new HeavyBolter(4, 10, 6, 120, 180));
	$this->addRightSystem(new LightBolter(2, 6, 2, 0, 180));
        
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 52));
        $this->addAftSystem(new Structure( 4, 42));
        $this->addLeftSystem(new Structure( 4, 60));
        $this->addRightSystem(new Structure( 4, 60));
        $this->addPrimarySystem(new Structure( 5, 54));
        $this->hitChart = array(
                0=> array(
                        11 => "Structure",
                        13 => "Standard Particle Beam",
                        15 => "Scanner",
                        17 => "Engine",
                        18 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        4 => "Thruster",
                        8 => "Medium Bolter",
                        12 => "Interceptor I",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        6 => "Thruster",
                        10 => "Jump Engine",
                        13 => "Interceptor I",
                        18 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        4 => "Thruster",
                        9 => "Heavy Bolter",
                        11 => "Light Bolter",
                        18 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                        4 => "Thruster",
                        9 => "Heavy Bolter",
                        11 => "Light Bolter",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );
    }
}
?>
