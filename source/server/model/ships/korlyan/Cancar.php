<?php
class Cancar extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 175;
		$this->faction = "Civilians";
        $this->phpclass = "Cancar";
        $this->imagePath = "img/ships/korlyan_cancar.png";
        $this->shipClass = "Kor-Lyan Cancar Freighter";
        $this->canvasSize = 70;
	    
	    $this->isd = 2210;

	    $this->notes = 'Atmospheric Capable.';
        
        $this->forwardDefense = 14;
        $this->sideDefense = 15;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
		$this->iniativebonus = -20;

	$ammoMagazine = new AmmoMagazine(40); //pass magazine capacity 
	    $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
	    $ammoMagazine->addAmmoEntry(new AmmoMissileI(), 40); //add full load of basic missiles  	      

	    $this->enhancementOptionsEnabled[] = 'AMMO_A';//add enhancement options for other missiles - Class-A
	    $this->enhancementOptionsEnabled[] = 'AMMO_C';//add enhancement options for other missiles - Class-C	
	             
        $this->addPrimarySystem(new Reactor(3, 8, 0, 0));
        $this->addPrimarySystem(new CnC(3, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 10, 3, 3));
        $this->addPrimarySystem(new Engine(3, 11, 0, 6, 3));
		$this->addPrimarySystem(new Hangar(2, 2));
		$this->addPrimarySystem(new Thruster(3, 13, 0, 4, 3));
		$this->addPrimarySystem(new Thruster(3, 13, 0, 4, 4));
		
        $this->addFrontSystem(new Thruster(3, 8, 0, 2, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 2, 1));
		$this->addFrontSystem(new CargoBay(2, 50));
        $this->addFrontSystem(new AmmoMissileRackD(2, 6, 0, 180, 360, $ammoMagazine, false));
        $this->addFrontSystem(new AmmoMissileRackD(2, 6, 0, 0, 180, $ammoMagazine, false));
		
        $this->addAftSystem(new Thruster(3, 13, 0, 6, 2));
		$this->addAftSystem(new CargoBay(2, 35));
        $this->addAftSystem(new StdParticleBeam(2, 4, 1, 0, 360));
		$this->addAftSystem(new CargoBay(2, 35));
	
        $this->addPrimarySystem(new Structure( 3, 40));
        
		$this->hitChart = array(
                0=> array(
                        8 => "Thruster",
                        11 => "Scanner",
                        15 => "Engine",
						17 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        5 => "Thruster",
						9 => "Cargo Bay",
                        12 => "Class-D Missile Rack",
                        17 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        5 => "Thruster",
                        6 => "Standard Particle Beam",
						12 => "Cargo Bay",
                        17 => "Structure",
                        20 => "Primary",
                ),
        );

    }

}



?>
