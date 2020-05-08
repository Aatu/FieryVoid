<?php
class Strikebird extends HeavyCombatVesselLeftRight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 400;
        $this->faction = "Drazi";
        $this->phpclass = "Strikebird";
        $this->imagePath = "img/ships/drazi/warbird.png";
        $this->shipClass = "Strikebird Carrier";
        $this->occurence = "uncommon";
        $this->fighters = array("light" => 12);
	    $this->variantOf = "Warbird Cruiser";
	    $this->isd = 2234;
        $this->canvasSize = 256;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 12;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 40;

        $this->addPrimarySystem(new Reactor(5, 10, 0, 6));
        $this->addPrimarySystem(new CnC(5, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 4, 7));
        $this->addPrimarySystem(new Engine(5, 11, 0, 8, 2));
        $this->addPrimarySystem(new Hangar(4, 1));
        $this->addPrimarySystem(new ParticleRepeater(4, 6, 4, 240, 120));
        $this->addPrimarySystem(new Thruster(4, 13, 0, 4, 1));
        $this->addPrimarySystem(new Thruster(5, 19, 0, 8, 2));

        $this->addLeftSystem(new ParticleCannon(3, 8, 7, 240, 60));
        $this->addLeftSystem(new Thruster(4, 13, 0, 4, 3));
        $this->addLeftSystem(new Hangar(4, 6));

        $this->addRightSystem(new ParticleCannon(3, 8, 7, 300, 120));
        $this->addRightSystem(new Thruster(4, 13, 0, 4, 4));
        $this->addRightSystem(new Hangar(4, 6));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(5, 32));
        $this->addLeftSystem(new Structure(4, 40));
        $this->addRightSystem(new Structure(4, 40));
    
                    $this->hitChart = array(
        		0=> array(
        				8 => "Structure",
        				11 => "Thruster",
					12 => "Particle Repeater",
					14 => "Scanner",
        				16 => "Engine",
        				17 => "Hangar",
        				19 => "Reactor",
        				20 => "C&C",
        		),
        		3=> array(
        				4 => "Thruster",
        				6 => "Particle Cannon",
					9 => "Hangar",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		4=> array(
        				4 => "Thruster",
        				6 => "Particle Cannon",
					9 => "Hangar",
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
    
    }
}
