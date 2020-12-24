<?php
class gaimMearc extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 700;
		$this->faction = 'Gaim';
		$this->phpclass = "gaimMearc";
		$this->imagePath = "img/ships/GaimMoas.png";
		$this->shipClass = "Mearc Command Gunship";
			$this->variantOf = "Moas Gunship";
			$this->occurence = "rare";        
		$this->shipSizeClass = 3;
	    
        $this->isd = 2256;

		$this->forwardDefense = 15;
		$this->sideDefense = 15;

		$this->turncost = 0.66;
		$this->turndelaycost = 0.66;
		$this->accelcost = 3;
		$this->rollcost = 3;
		$this->pivotcost = 4;

		$this->iniativebonus = 5;

		$this->addPrimarySystem(new Reactor(4, 18, 0, 0));
		$this->addPrimarySystem(new CnC(5, 15, 0, 0));
		$this->addPrimarySystem(new Scanner(4, 18, 8, 8));
		$this->addPrimarySystem(new Engine(4, 14, 0, 10, 2));
		$this->addPrimarySystem(new Hangar(4, 4));

		$this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
		$this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
		$this->addFrontSystem(new ParticleConcentrator(4, 9, 7, 300, 60));
		$this->addFrontSystem(new ParticleConcentrator(4, 9, 7, 300, 60));
		$this->addFrontSystem(new ParticleConcentrator(4, 9, 7, 300, 60));
		$this->addFrontSystem(new Bulkhead(0, 2));
		$this->addFrontSystem(new Bulkhead(0, 2));

		$this->addAftSystem(new Thruster(4, 15, 0, 5, 2));
		$this->addAftSystem(new Thruster(4, 15, 0, 5, 2));
		$this->addAftSystem(new ScatterGun(2, 8, 3, 120, 360));
		$this->addAftSystem(new ScatterGun(2, 8, 3, 0, 240));
		$this->addAftSystem(new Bulkhead(0, 2));
		$this->addAftSystem(new Bulkhead(0, 2));

		$this->addLeftSystem(new Thruster(4, 15, 0, 4, 3));
		$this->addLeftSystem(new PacketTorpedo(4, 6, 5, 240, 360));
		$this->addLeftSystem(new ScatterGun(2, 8, 3, 180, 60));
		$this->addLeftSystem(new Bulkhead(0, 4));

		$this->addRightSystem(new Thruster(4, 15, 0, 4, 4));
		$this->addRightSystem(new PacketTorpedo(4, 6, 5, 0, 120));
		$this->addRightSystem(new ScatterGun(2, 8, 3, 300, 180));
		$this->addRightSystem(new Bulkhead(0, 4));
        
        $this->addFrontSystem(new Structure( 4, 36));
        $this->addAftSystem(new Structure( 4, 36));
        $this->addLeftSystem(new Structure( 4, 52));
        $this->addRightSystem(new Structure( 4, 52));
        $this->addPrimarySystem(new Structure( 4, 36));
		
		
		$this->hitChart = array(
                0=> array(
                        9 => "Structure",
                        12 => "Scanner",
                        15 => "Engine",
                        17 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        4 => "Thruster",
                        8 => "Particle Concentrator",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        6 => "Thruster",
                        8 => "Scattergun",
                        18 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        4 => "Thruster",
						6 => "Packet Torpedo",
                        8 => "Scattergun",
                        18 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                        4 => "Thruster",
						6 => "Packet Torpedo",
                        8 => "Scattergun",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );

    }
}
?>
