<?php
class Martoba extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 480;
        $this->faction = "Markab Theocracy";
        $this->phpclass = "Martoba";
        $this->imagePath = "img/ships/MarkabPatrolShip.png"; //change
        $this->shipClass = "Martoba Patrol Cutter";
        
        $this->forwardDefense = 14;
        $this->sideDefense = 15;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.66;
        $this->accelcost = 2;
        $this->rollcost = 3;
        $this->pivotcost = 2;
        $this->iniativebonus = 30;
        $this->isd = 2014;
        $this->fighters = array("normal"=>6);

		$this->enhancementOptionsEnabled[] = 'MARK_FERV'; //To activate Religious Fervor attributes.   

	$this->addPrimarySystem(new Reactor(4, 15, 0, 0));
	$this->addPrimarySystem(new CnC(4, 8, 0, 0));
	$this->addPrimarySystem(new Scanner(4, 16, 7, 7));
	$this->addPrimarySystem(new Engine(4, 12, 0, 6, 2));
	$this->addPrimarySystem(new Hangar(4, 9));
	$this->addPrimarySystem(new Thruster(3, 11, 0, 3, 3));
	$this->addPrimarySystem(new Thruster(3, 11, 0, 3, 4));
	  
	$this->addFrontSystem(new Thruster(3, 8, 0, 2, 1));
	$this->addFrontSystem(new Thruster(3, 8, 0, 2, 1));
	$this->addFrontSystem(new MediumPlasma(4, 5, 3, 180, 360));
	$this->addFrontSystem(new MediumPlasma(4, 5, 3, 0, 180));
	$this->addFrontSystem(new PlasmaWaveTorpedo(4, 0, 0, 240, 360));
	$this->addFrontSystem(new PlasmaWaveTorpedo(4, 0, 0, 0, 120));
	$this->addFrontSystem(new ParticleCannon(4, 8, 7, 300, 60));	
	
	$this->addAftSystem(new Thruster(3, 10, 0, 3, 2));
	$this->addAftSystem(new Thruster(3, 10, 0, 3, 2));
	$this->addAftSystem(new ScatterGun(2, 0, 0, 120, 360));
	$this->addAftSystem(new ScatterGun(2, 0, 0, 0, 240));	
 
    //0:primary, 1:front, 2:rear, 3:left, 4:right;
    $this->addFrontSystem(new Structure(4, 48));
    $this->addAftSystem(new Structure(4, 42));
    $this->addPrimarySystem(new Structure(4, 42));

    $this->hitChart = array(
        0=> array(
                9 => "Structure",
                11 => "Thruster",
                13 => "Scanner",
                15 => "Engine",
                17 => "Hangar",
                19 => "Reactor",
                20 => "C&C",
        ),
        1=> array(
                4 => "Thruster",
                6 => "Plasma Wave",
                7 => "Particle Cannon",
                9 => "Medium Plasma Cannon",
                18 => "Structure",
                20 => "Primary",
        ),
        2=> array(
                6 => "Thruster",
                9 => "Scattergun",
                18 => "Structure",
                20 => "Primary",
        ),
	);


    }
}
?>
