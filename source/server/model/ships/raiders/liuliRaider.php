<?php
class LiuliRaider extends HeavyCombatVesselLeftRight{
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
    	$this->pointCost = 400;
        $this->faction = "Raiders";
        $this->phpclass = "LiuliRaider";
        $this->imagePath = "img/ships/falenna.png"; //it's not based on Falenna hull but it's more or less similar in shape, I use it rather than create my own with meager skill
        $this->shipClass = "Raider Liuli Destroyer";
        $this->isd = 1965;
        $this->variantOf = "Centauri Privateer Liuli Destroyer";
        
        $this->forwardDefense = 15;
        $this->sideDefense = 13;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 6 *5;

        $this->addPrimarySystem(new Reactor(5, 15, 0, 0));
        $this->addPrimarySystem(new CnC(5, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 3, 5));
        $this->addPrimarySystem(new Engine(4, 13, 0, 9, 3));
        $this->addPrimarySystem(new Hangar(4, 2));
        $this->addPrimarySystem(new Thruster(3, 12, 0, 5, 1));
        $this->addPrimarySystem(new Thruster(3, 8, 0, 3, 2));
        $this->addPrimarySystem(new Thruster(3, 8, 0, 3, 2));
        $this->addPrimarySystem(new Thruster(3, 8, 0, 3, 2));
		$this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 240, 60));
		$this->addPrimarySystem(new LightParticleCannon(2, 6, 5, 300, 60));
		$this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 300, 120));


        $this->addLeftSystem(new Thruster(3, 10, 0, 4, 3));
        $this->addLeftSystem(new MediumPlasma(3, 4, 2, 300, 60));

        $this->addRightSystem(new Thruster(3, 10, 0, 4, 4));
        $this->addRightSystem(new MediumPlasma(3, 4, 2, 300, 60));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(4, 44));
        $this->addLeftSystem(new Structure(4, 45));
        $this->addRightSystem(new Structure(4, 45));
		
		
		
        $this->hitChart = array(//hit chart slightly modified, original one has "Particle Weapon" as single location
            0=> array(
                    7 => "Structure",
                    10 => "Thruster",
                    11 => "Light Particle Beam",
                    13 => "Light Particle Cannon",
                    15 => "Scanner",
                    17 => "Engine",
                    18 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            3=> array( 
                    5 => "Thruster",
                    8 => "Medium Plasma Cannon",
                    9 => "0:Light Particle Beam",
                    10 => "0:Light Particle Cannon",
                    18 => "Structure",
                    20 => "Primary",
            ),
            4=> array(
                    5 => "Thruster",
                    8 => "Medium Plasma Cannon",
                    9 => "0:Light Particle Beam",
                    10 => "0:Light Particle Cannon",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>
