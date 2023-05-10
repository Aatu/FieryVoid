<?php
class Areko extends HeavyCombatVesselLeftRight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 475;
		$this->faction = "Raiders";
        $this->phpclass = "Areko";
        $this->imagePath = "img/ships/RaiderShokanAreko.png";
        $this->shipClass = "Shokan Areko Light Cruiser";
	    $this->fighters = array("normal"=>6);
        
		$this->notes = 'Used only by Brakiri Shokan Privateers';
		$this->isd = 2237;
		
        $this->forwardDefense = 14;
        $this->sideDefense = 15;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 6 *5;

        $this->addPrimarySystem(new Reactor(5, 18, 0, 0));
        $this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 12, 5, 6));
 //       $this->addPrimarySystem(new Engine(6, 14, 0, 10, 3));
		$this->addPrimarySystem(new CargoBay(3, 10)); 
        $this->addPrimarySystem(new Hangar(5, 8));
        
        $this->addAftSystem(new Thruster(5, 15, 0, 6, 1));        
        $this->addAftSystem(new Thruster(5, 18, 0, 10, 2));
        $this->addAftSystem(new StdParticleBeam(4, 4, 1, 90, 270));

        $this->addLeftSystem(new Engine(4, 9, 0, 4, 4));        
        $this->addLeftSystem(new HeavyPlasma(3, 8, 5, 240, 360));
        $this->addLeftSystem(new MediumBolter(3, 8, 4, 240, 360));
        $this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 360));
        $this->addLeftSystem(new Thruster(4, 15, 0, 4, 3));

        $this->addRightSystem(new Engine(4, 9, 0, 4, 4));        
        $this->addRightSystem(new HeavyPlasma(3, 8, 5, 0, 120));
        $this->addRightSystem(new MediumBolter(3, 8, 4, 0, 120));
        $this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
        $this->addRightSystem(new Thruster(4, 15, 0, 4, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(5, 40));
        $this->addLeftSystem(new Structure(4, 48));
        $this->addRightSystem(new Structure(4, 48));
		
		$this->hitChart = array(
			0=> array(
					7 => "Structure",
					9 => "Cargo Bay",
					11 => "2:Thruster",
					12 => "2:Standard Particle Beam",
					15 => "Scanner",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			3=> array(
					4 => "Thruster",
					6 => "Standard Particle Beam",
					8 => "Heavy Plasma Cannon",
					10 => "Medium Bolter",
					11 => "Engine",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					4 => "Thruster",
					6 => "Standard Particle Beam",
					8 => "Heavy Plasma Cannon",
					10 => "Medium Bolter",
					11 => "Engine",
					18 => "Structure",
					20 => "Primary",
			),
		);
		
    }
}
