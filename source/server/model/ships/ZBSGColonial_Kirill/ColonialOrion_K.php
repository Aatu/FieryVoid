<?php
class ColonialOrion_K extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 600;
		$this->faction = "ZPlaytest 12 Colonies of Kobol (Tier 1)";
        $this->phpclass = "ColonialOrion_K";
        $this->imagePath = "img/ships/BSG/ColonialOrion.png";
        $this->shipClass = "Orion Reconisance Battlestar";
        $this->canvasSize = 80;
        $this->limited = 33;
//	    $this->isd = 2007;

	    $this->notes = 'May boost sensors normally.';

        $this->fighters = array("normal"=>6, "superheavy"=>2);
        
        $this->forwardDefense = 13;
        $this->sideDefense = 15;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 2;
		$this->iniativebonus = 60;
         
        $this->addPrimarySystem(new Reactor(3, 12, 0, 0));
        $this->addPrimarySystem(new CnC(3, 8, 0, 0));
        $this->addPrimarySystem(new ElintScanner(3, 15, 5, 6));  //full ELINT, not under the 2 point only boost
        $this->addPrimarySystem(new Engine(3, 16, 0, 8, 3));
        $this->addPrimarySystem(new ReloadRack(3, 9));
        $this->addPrimarySystem(new LMissileRack(3, 6, 0, 0, 360));
        $this->addPrimarySystem(new LMissileRack(3, 6, 0, 0, 360));
		$this->addPrimarySystem(new Thruster(3, 9, 0, 3, 3));
		$this->addPrimarySystem(new Thruster(3, 9, 0, 3, 4));        
		$hyperdrive = new JumpEngine(3, 12, 6, 20);
			$hyperdrive->displayName = 'Phasing Drive';
			$this->addPrimarySystem($hyperdrive);

		$this->addFrontSystem(new Hangar(3, 8));
        $this->addFrontSystem(new Thruster(2, 9, 0, 4, 1));
		$this->addFrontSystem(new FlakCannon(2, 4, 2, 240, 120));
		$this->addFrontSystem(new FlakCannon(2, 4, 2, 240, 120));
		$this->addFrontSystem(new LtBlastCannon(3, 4, 1, 180, 30));
		$this->addFrontSystem(new LtBlastCannon(3, 4, 1, 180, 30));
		$this->addFrontSystem(new LtBlastCannon(3, 4, 1, 330, 180));
		$this->addFrontSystem(new LtBlastCannon(3, 4, 1, 330, 180));

		$this->addAftSystem(new LtBlastCannon(3, 4, 1, 150, 360));
		$this->addAftSystem(new LtBlastCannon(3, 4, 1, 150, 360));
		$this->addAftSystem(new LtBlastCannon(3, 4, 1, 0, 210));
		$this->addAftSystem(new LtBlastCannon(3, 4, 1, 0, 210));
		$this->addAftSystem(new FlakCannon(2, 4, 2, 60, 300));
		$this->addAftSystem(new FlakCannon(2, 4, 2, 60, 300));
        $this->addAftSystem(new ElintScanner(3, 15, 4, 3));
        $this->addAftSystem(new Thruster(2, 12, 0, 4, 2));    
        $this->addAftSystem(new Thruster(2, 12, 0, 4, 2));    
       
        $this->addPrimarySystem(new Structure(3, 50));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			7 => "Thruster",
			9 => "Class-L Missile Rack",
			10 => "Reload Rack",
			12 => "ELINT Scanner",
			15 => "Engine",
			17 => "Reactor",
			19 => "Phasing Drive",
			20 => "C&C",
		),
		1=> array(
			4 => "Thruster",
			8 => "Light Blast Cannon",
			10 => "Flak Cannon",
			12 => "Hangar",
			19 => "Structure",
			20 => "Primary",
		),
		2=> array(
			4 => "Thruster",
			8 => "Light Blast Cannon",
			10 => "Flak Cannon",
			12 => "ELINT Scanner",
			19 => "Structure",
			20 => "Primary",
		),
	);

			
        }
    }
?>
