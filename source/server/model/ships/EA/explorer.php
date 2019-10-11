<?php
class Explorer extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 1000;
		$this->faction = "EA";
        $this->phpclass = "Explorer";
        $this->enormous = true;
        $this->imagePath = "img/ships/explorer.png";
			$this->canvasSize = 300; //img has 300px per side
        $this->shipClass = "Explorer Survey Ship (Alpha)";
        $this->fighters = array("heavy"=>24);
			$this->occurence = "common";
			$this->limited = 10;
		$this->isd = 2253;
        $this->forwardDefense = 18;
        $this->sideDefense = 20;
		
		$this->notes = "Not a combat ship (->not eligible for pickup battles)";
        
        $this->turncost = 2;
        $this->turndelaycost = 2;
        $this->accelcost = 8;
        $this->rollcost = 5; //4+6
        $this->pivotcost = 99; //cannot pivot   

        $this->iniativebonus = -4*5; //-4 Ini


		
         
        $this->addPrimarySystem(new Reactor(6, 35, 0, 0));
        $this->addPrimarySystem(new CnC(6, 28, 0, 0));
        $this->addPrimarySystem(new ElintScanner(6, 36, 8, 10));
        $this->addPrimarySystem(new Engine(6, 30, 0, 12, 3));
		$this->addPrimarySystem(new Hangar(6, 30));
		$cargo = new CargoBay(5,50);
		$cargo->displayName = "Cargo Bay A";
		$this->addPrimarySystem($cargo);
		$cargo = new CargoBay(5,50);
		$cargo->displayName = "Cargo Bay B";
		$this->addPrimarySystem($cargo);
		$this->addPrimarySystem(new JumpEngine(6, 25, 5, 20));
        
        $this->addFrontSystem(new Thruster(3, 25, 0, 6, 1));
        $this->addFrontSystem(new Thruster(3, 25, 0, 6, 1));
        $this->addFrontSystem(new ConnectionStrut(4));
		$this->addFrontSystem(new HeavyPulse(4, 6, 4, 300, 0));
		$this->addFrontSystem(new HeavyPulse(4, 6, 4, 0, 60));
        $this->addFrontSystem(new InterceptorMkI(2, 4, 1, 240, 60));
        $this->addFrontSystem(new InterceptorMkI(2, 4, 1, 300, 120));
		
        $this->addAftSystem(new Thruster(4, 12, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 12, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 12, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 12, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 12, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 12, 0, 2, 2));
        $this->addAftSystem(new ConnectionStrut(4));
		$this->addAftSystem(new InterceptorMkI(2, 4, 1, 120, 300));
        $this->addAftSystem(new InterceptorMkI(2, 4, 1, 60, 240));
        
		$this->addLeftSystem(new Thruster(3, 30, 0, 8, 3));
        $this->addLeftSystem(new ConnectionStrut(4));
		$this->addLeftSystem(new InterceptorMkI(2, 4, 1, 180, 0));
		$this->addLeftSystem(new InterceptorMkI(2, 4, 1, 180, 0));
		$this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 0));
		$this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 0));
		$this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 0));
		$this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 0));

		$this->addRightSystem(new Thruster(3, 30, 0, 8, 4));
        $this->addRightSystem(new ConnectionStrut(4));
		$this->addRightSystem(new InterceptorMkI(2, 4, 1, 0, 180));
		$this->addRightSystem(new InterceptorMkI(2, 4, 1, 0, 180));
		$this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
		$this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
		$this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
		$this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 128));
        $this->addAftSystem(new Structure( 4, 120));
        $this->addLeftSystem(new Structure( 4, 160));
        $this->addRightSystem(new Structure( 4, 160));
        $this->addPrimarySystem(new Structure( 6, 128));
        $this->hitChart = array(
                0=> array(
                        6 => "Structure",
                        8 => "Cargo Bay A",
                        10 => "Cargo Bay B",
						11 => "Jump Engine",
						13 => "Scanner",
                        15 => "Engine",
                        17 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        4 => "Thruster",
                        6 => "Heavy Pulse Cannon",
                        9 => "Interceptor I",
                        16 => "Structure",
						18 => "Connection Strut",
                        20 => "Primary",
                ),
                2=> array(
                        6 => "Thruster",
                        9 => "Interceptor I",
                        16 => "Structure",
						18 => "Connection Strut",
                        20 => "Primary",
                ),
                3=> array(
                        4 => "Thruster",
						8 => "Standard Particle Beam",
						12 => "Interceptor I",
                        16 => "Structure",
						18 => "Connection Strut",
                        20 => "Primary",
                ),
                4=> array(
                        4 => "Thruster",
						8 => "Standard Particle Beam",
						12 => "Interceptor I",
                        16 => "Structure",
						18 => "Connection Strut",
                        20 => "Primary",
                ),
        );
    }
}
?>