<?php
class Sarlon extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 380;
        $this->faction = "Usuuth";
        $this->phpclass = "Sarlon";
        $this->imagePath = "img/ships/UsuuthSerron.png";
		$this->canvasSize = 200;
        $this->shipClass = "Sarlon Sniper";
        $this->isd = 1965;
        $this->occurence = "rare";
        $this->variantOf = "Serron Attack Ship";
        
        $this->forwardDefense = 13;
        $this->sideDefense = 15;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.5;
        $this->accelcost = 3;
        $this->rollcost = 1.5;
        $this->pivotcost = 2;
        $this->iniativebonus = 35;
        
        $this->addPrimarySystem(new Reactor(5, 18, 0, 4));
        $this->addPrimarySystem(new CnC(6, 9, 0, 0));
        $this->addPrimarySystem(new Scanner(6, 15, 4, 6));
        $this->addPrimarySystem(new Engine(5, 12, 0, 8, 3));
        $this->addPrimarySystem(new Hangar(5, 1));
        $this->addPrimarySystem(new Thruster(4, 9, 0, 3, 3));
        $this->addPrimarySystem(new Thruster(4, 9, 0, 3, 4));
        
        $this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));
        $this->addFrontSystem(new ParticleProjector(2, 6, 1, 300, 60));
        $this->addFrontSystem(new LightParticleProjector(2, 3, 1, 240, 60));
        $this->addFrontSystem(new ParticleHammer(5, 12, 5, 300, 60));
        $this->addFrontSystem(new LightParticleProjector(2, 3, 1, 300, 120));
        $this->addFrontSystem(new ParticleProjector(2, 6, 1, 300, 60));
        
        $this->addAftSystem(new Thruster(4, 10, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 10, 0, 4, 2));
        $this->addAftSystem(new ParticleProjector(2, 6, 1, 240, 60));
        $this->addAftSystem(new ParticleProjector(2, 6, 1, 300, 120));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 44));
        $this->addAftSystem(new Structure( 4, 44));
        $this->addPrimarySystem(new Structure( 6, 45));
        $this->hitChart = array(
            0=> array(
                8 => "Structure",
                10 => "Thruster",
                12 => "Scanner",
                15 => "Engine",
                16 => "Hangar",
                19 => "Reactor",
                20 => "C&C",
            ),
            1=> array(
                4 => "Thruster",
                5 => "Particle Hammer",
                7 => "Particle Projector",
                9 => "Light Particle Projector",
                18 => "Structure",
                20 => "Primary",
            ),
            2=> array(
                6 => "Thruster",
                8 => "Particle Projector",
                18 => "Structure",
                20 => "Primary",
            ),
        );
    }
}


?>
