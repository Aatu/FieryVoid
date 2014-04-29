<?php
class HyperionAlpha extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 575;
	$this->faction = "EA";
        $this->phpclass = "HyperionAlpha";
	$this->imagePath = "img/ships/hyperion.png";
        $this->shipClass = "Hyperion Heavy Cruiser (Alpha Model)";
        $this->shipSizeClass = 3;
	$this->fighters = array("normal"=>6);
        
        $this->forwardDefense = 14;
        $this->sideDefense = 16;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
	$this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(4, 20, 0, 0));
        $this->addPrimarySystem(new CnC(4, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 16, 3, 6));
        $this->addPrimarySystem(new Engine(4, 17, 0, 6, 4));
	$this->addPrimarySystem(new Hangar(4, 8));
        $this->addPrimarySystem(new StdParticleBeam(2, 4, 1, 0, 360));
	$this->addPrimarySystem(new StdParticleBeam(2, 4, 1, 0, 360));
	$this->addPrimarySystem(new StdParticleBeam(2, 4, 1, 0, 360));

        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
	$this->addFrontSystem(new MediumPlasma(3, 5, 3, 300, 60));
	$this->addFrontSystem(new MediumPlasma(3, 5, 3, 240, 120));
	$this->addFrontSystem(new MediumPlasma(3, 5, 3, 300, 60));
        $this->addFrontSystem(new InterceptorMkI(2, 4, 1, 240, 60));
        $this->addFrontSystem(new InterceptorMkI(2, 4, 1, 300, 120));

        $this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
	$this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
        $this->addAftSystem(new JumpEngine(4, 16, 3, 24));
	$this->addAftSystem(new InterceptorMkI(2, 4, 1, 120, 300));
        $this->addAftSystem(new InterceptorMkI(2, 4, 1, 60, 240));
        
	$this->addLeftSystem(new Thruster(3, 13, 0, 4, 3));
	$this->addLeftSystem(new MediumPlasma(3, 5, 3, 180, 360));
	$this->addLeftSystem(new ParticleCannon(4, 8, 7, 300, 0));

        $this->addRightSystem(new Thruster(3, 13, 0, 4, 4));
	$this->addRightSystem(new MediumPlasma(3, 5, 3, 0, 180));
	$this->addRightSystem(new ParticleCannon(4, 8, 7, 0, 60));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 52));
        $this->addAftSystem(new Structure( 4, 42));
        $this->addLeftSystem(new Structure( 4, 60));
        $this->addRightSystem(new Structure( 4, 60));
        $this->addPrimarySystem(new Structure( 4, 54));
    }
}

?>
