<?php
class Orthuus  extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 600;
        $this->faction = "Usuuth";
        $this->phpclass = "Orthuus";
        $this->imagePath = "img/ships/UsuuthOrthuus.png";
		$this->canvasSize = 200;
        $this->shipClass = "Orthuus Battle Leader";
        $this->shipSizeClass = 3;
        $this->fighters = array("normal"=>24);
        $this->limited = 33;
        $this->iniativebonus = 5;
        $this->isd = 1970;
        
        $this->forwardDefense = 15;
        $this->sideDefense = 17;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 2;
        $this->pivotcost = 3;
        
        $this->addPrimarySystem(new Reactor(5, 36, 0, 0));
        $this->addPrimarySystem(new CnC(6, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(6, 18, 6, 8));
        $this->addPrimarySystem(new Engine(5, 24, 0, 12, 4));
        $this->addPrimarySystem(new Hangar(7, 23, 6));
        
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new ParticleProjector(2, 6, 1, 300, 60));
        $this->addFrontSystem(new ParticleProjector(2, 6, 1, 300, 60));
        $this->addFrontSystem(new ParticleProjector(2, 6, 1, 300, 60));
        $this->addFrontSystem(new ParticleHammer(4, 12, 5, 300, 60));
        $this->addFrontSystem(new ParticleHammer(4, 12, 5, 300, 60));
        
        $this->addAftSystem(new JumpEngine(4, 12, 3, 30));
        $this->addAftSystem(new Thruster(4, 10, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 10, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 10, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 10, 0, 4, 2));
        $this->addAftSystem(new ParticleProjector(2, 6, 1, 120, 240));
        $this->addAftSystem(new HvyParticleProjector(4, 8, 3, 120, 240));
        $this->addAftSystem(new ParticleProjector(2, 6, 1, 120, 240));
                
        $this->addLeftSystem(new Thruster(4, 12, 0, 4, 3));
        $this->addLeftSystem(new HvyParticleProjector(4, 8, 3, 180, 0));
        $this->addLeftSystem(new LightParticleProjector(2, 3, 1, 180, 0));
        $this->addLeftSystem(new LightParticleProjector(2, 3, 1, 180, 0));
        $this->addLeftSystem(new LightParticleProjector(2, 3, 1, 180, 0));
        
        $this->addRightSystem(new Thruster(4, 12, 0, 4, 4));
        $this->addRightSystem(new HvyParticleProjector(4, 8, 3, 0, 180));
        $this->addRightSystem(new LightParticleProjector(2, 3, 1, 0, 180));
        $this->addRightSystem(new LightParticleProjector(2, 3, 1, 0, 180));
        $this->addRightSystem(new LightParticleProjector(2, 3, 1, 0, 180));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 30));
        $this->addAftSystem(new Structure( 4, 30));
        $this->addLeftSystem(new Structure( 4, 30));
        $this->addRightSystem(new Structure( 4, 30));
        $this->addPrimarySystem(new Structure( 6, 30));
        
        $this->hitChart = array(
            0=> array(
                9 => "Structure",
                12 => "Hangar",
                14 => "Scanner",
                16 => "Engine",
                19 => "Reactor",
                20 => "C&C",
            ),
            1=> array(
                4 => "Thruster",
                7 => "Particle Hammer",
                9 => "Particle Projector",
                18 => "Structure",
                20 => "Primary",
            ),
            2=> array(
                5 => "Thruster",
                7 => "Particle Projector",
                9 => "Heavy Particle Projector",
                11 => "Jump Engine",
                18 => "Structure",
                20 => "Primary",
            ),
            3=> array(
                5 => "Thruster",
                7 => "Heavy Particle Projector",
                9 => "Light Particle Projector",
                18 => "Structure",
                20 => "Primary",
            ),
            4=> array(
                5 => "Thruster",
                7 => "Heavy Particle Projector",
                9 => "Light Particle Projector",
                18 => "Structure",
                20 => "Primary",
            ),
        );
    }
}
