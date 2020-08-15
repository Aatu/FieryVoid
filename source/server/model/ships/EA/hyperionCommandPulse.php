<?php
class HyperionCommandPulse extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 760;
		$this->faction = "EA";
        $this->phpclass = "HyperionCommandPulse";
        $this->imagePath = "img/ships/hyperion.png";
        $this->shipClass = "Hyperion Pulse Command Cruiser (Iota)";
			$this->unofficial = true;
        $this->shipSizeClass = 3;
	    
        $this->occurence = "uncommon";
        $this->variantOf = 'Hyperion Heavy Cruiser (Theta)';
        $this->isd = 2246;
	    
        $this->fighters = array("normal"=>6);
        
        $this->forwardDefense = 14;
        $this->sideDefense = 16;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
        $this->iniativebonus = 5;
        
         
        $this->addPrimarySystem(new Reactor(5, 23, 0, 2));
        $this->addPrimarySystem(new CnC(5, 24, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 18, 3, 6));
        $this->addPrimarySystem(new Engine(5, 18, 0, 7, 4));
		$this->addPrimarySystem(new Hangar(5, 8));
        $this->addPrimarySystem(new StdParticleBeam(2, 4, 1, 0, 360));
		$this->addPrimarySystem(new StdParticleBeam(2, 4, 1, 0, 360));
		$this->addPrimarySystem(new StdParticleBeam(2, 4, 1, 0, 360));
        

        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
		$this->addFrontSystem(new HeavyPulse (3, 6, 4, 330, 30));
		$this->addFrontSystem(new MediumPulse(3, 6, 3, 240, 120));
		$this->addFrontSystem(new HeavyPulse (3, 6, 4, 330, 30));
        $this->addFrontSystem(new InterceptorMkI(2, 4, 1, 240, 60));
        $this->addFrontSystem(new InterceptorMkI(2, 4, 1, 300, 120));

        $this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
		$this->addAftSystem(new Thruster(4, 12, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
        $this->addAftSystem(new JumpEngine(4, 16, 3, 24));
		$this->addAftSystem(new InterceptorMkI(2, 4, 1, 120, 300));
        $this->addAftSystem(new InterceptorMkI(2, 4, 1, 60, 240));
        $this->addAftSystem(new MediumPulse (4, 6, 3, 60, 300));
        $this->addAftSystem(new MediumPulse (4, 6, 3, 60, 300));
        
		$this->addLeftSystem(new Thruster(5, 13, 0, 5, 3));
		$this->addLeftSystem(new HeavyPulse(3, 6, 4, 300, 0));
		
		$this->addRightSystem(new Thruster(5, 13, 0, 5, 4));
		$this->addRightSystem(new HeavyPulse(3, 6, 4, 0, 60));
		
        
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 52));
        $this->addAftSystem(new Structure( 4, 42));
        $this->addLeftSystem(new Structure( 4, 60));
        $this->addRightSystem(new Structure( 4, 60));
        $this->addPrimarySystem(new Structure( 5, 54));


        $this->hitChart = array(
                0=> array(
                        10 => "Structure",
                        12 => "Standard Particle Beam",
                        14 => "Scanner",
                        16 => "Engine",
                        18 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        4 => "Thruster",
                        7 => "Heavy Pulse Cannon",
                        8 => "Medium Pulse Cannon",
                        12 => "Interceptor I",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        5 => "Thruster",
                        6 => "Jump Engine",
                        11 => "Medium Pulse Cannon",
                        13 => "Interceptor I",
                        18 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        6 => "Thruster",
                        9 => "Heavy Pulse Cannon",
                        18 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                        6 => "Thruster",
                        9 => "Heavy Pulse Cannon",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );

    }

}
