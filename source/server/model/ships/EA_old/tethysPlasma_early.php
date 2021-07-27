<?php
class TethysPlasma_early extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 350;
		$this->faction = "EA (early)";
		$this->phpclass = "TethysPlasma_early";
		$this->imagePath = "img/ships/tethys.png";
		$this->shipClass = "Tethys Plasma Boat (Epsilon)";
			$this->variantOf = "Tethys Police Cutter (Alpha)";
			$this->occurence = "common";
		$this->canvasSize = 100;

        $this->isd = 2191;

		$this->forwardDefense = 13;
		$this->sideDefense = 13;

		$this->turncost = 0.33;
		$this->turndelaycost = 0.5;
		$this->accelcost = 2;
		$this->rollcost = 1;
		$this->pivotcost = 1;
		$this->iniativebonus = 60;

		$this->addPrimarySystem(new Reactor(4, 9, 0, 0));
		$this->addPrimarySystem(new CnC(4, 9, 0, 0));
		$this->addPrimarySystem(new Scanner(4, 10, 3, 5));
		$this->addPrimarySystem(new Engine(4, 11, 0, 8, 3));
		$this->addPrimarySystem(new Hangar(4, 2));
		$this->addPrimarySystem(new Thruster(2, 13, 0, 4, 3));
		$this->addPrimarySystem(new Thruster(2, 13, 0, 4, 4));
		
        $this->addFrontSystem(new Thruster(2, 7, 0, 3, 1));
        $this->addFrontSystem(new Thruster(2, 7, 0, 3, 1));
        $this->addFrontSystem(new MediumPlasma(3, 5, 3, 240, 360));
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 270, 90));
        $this->addFrontSystem(new InterceptorMkI(2, 4, 1, 270, 90));
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 270, 90));
        $this->addFrontSystem(new MediumPlasma(3, 5, 3, 0, 120));
		
        $this->addAftSystem(new Thruster(3, 8, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 8, 0, 4, 2));
        $this->addAftSystem(new StdParticleBeam(2, 4, 1, 180, 0));
        $this->addAftSystem(new InterceptorMkI(2, 4, 1, 90, 270));
        $this->addAftSystem(new StdParticleBeam(2, 4, 1, 0, 180));
	
        $this->addPrimarySystem(new Structure( 4, 38));
    }

}



?>
