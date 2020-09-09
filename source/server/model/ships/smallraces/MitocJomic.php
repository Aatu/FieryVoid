<?php
class MitocJomic extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
		
        $this->pointCost = 300;
        $this->faction = "Small Races";
        $this->phpclass = "MitocJomic";
        $this->imagePath = "img/ships/MitocJomic.png";
			$this->canvasSize = 130; //img has 130px per side
        $this->shipClass = "Mitoc Jomic Frigate";
       	$this->isd = 2220;
                
		$this->agile = true;
		$this->forwardDefense = 11;
		$this->sideDefense = 13;
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 1;
        $this->iniativebonus = 13*5;

		$this->addPrimarySystem(new Reactor(4, 11, 0, 0));
		$this->addPrimarySystem(new CnC(5, 9, 0, 0));
		$this->addPrimarySystem(new Scanner(4, 10, 4, 5));
		$this->addPrimarySystem(new Engine(4, 11, 0, 6, 1));
		$this->addPrimarySystem(new Hangar(3, 2));
		$this->addPrimarySystem(new Thruster(4, 10, 0, 3, 3));
		$this->addPrimarySystem(new Thruster(4, 10, 0, 3, 4));

        $this->addFrontSystem(new Thruster(4, 15, 0, 4, 1));
		$this->addFrontSystem(new LightParticleCannon(2, 6, 5, 240, 360));
		$this->addFrontSystem(new MediumPlasma(3, 5, 3, 300, 60));
		$this->addFrontSystem(new MediumPlasma(3, 5, 3, 300, 60));
		$this->addFrontSystem(new LightParticleCannon(2, 6, 5, 0, 120));
                
        $this->addAftSystem(new Thruster(4, 18, 0, 6, 2));
        $this->addAftSystem(new LightParticleBeamShip(1, 2, 1, 180, 360));
        $this->addAftSystem(new LightParticleBeamShip(1, 2, 1, 0, 180));
        $this->addAftSystem(new LightParticleBeamShip(1, 2, 1, 90, 270));
 
		$this->addPrimarySystem(new Structure(5, 44));
 
        //0:primary, 1:front, 2:rear, 3:left, 4:right;

		//d20 hit chart
        $this->hitChart = array(
            0=> array(
				10 => "Thruster",
				13 => "Scanner",
				16 => "Engine",
				17 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
            ),
            1=> array(
				5 => "Thruster",
				7 => "Medium Plasma",
				9 => "Light Particle Cannon",
				17 => "Structure",
				20 => "Primary",
            ),
            2=> array(
				6 => "Thruster",
				9 => "Light Particle Beam",
				17 => "Structure",
				20 => "Primary",
            ),
        );
    }
}
?>
