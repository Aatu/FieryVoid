<?php
class gaimTracha extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 425;
		$this->faction = 'Gaim';
		$this->phpclass = "gaimTracha";
		$this->imagePath = "img/ships/GaimTracha.png";
		$this->shipClass = "Tracha Troop Transport";
		$this->shipSizeClass = 3;

		$this->notes = 'Atmospheric Capable';

		$this->fighters = array("assault shuttles"=>6);
	    
        $this->isd = 2255;

		$this->forwardDefense = 15;
		$this->sideDefense = 13;

		$this->turncost = 0.66;
		$this->turndelaycost = 1;
		$this->accelcost = 3;
		$this->rollcost = 2;
		$this->pivotcost = 2;

		$this->iniativebonus = 0;

		$this->addPrimarySystem(new Reactor(4, 15, 0, 2));
		$this->addPrimarySystem(new CnC(4, 12, 0, 0));
		$this->addPrimarySystem(new Scanner(4, 16, 5, 6));
		$this->addPrimarySystem(new Engine(4, 16, 0, 10, 3));
		$this->addPrimarySystem(new Hangar(4, 8));

		$this->addFrontSystem(new ScatterGun(3, 8, 3, 240, 120));

		$this->addAftSystem(new ScatterGun(3, 8, 3, 60, 300));

		$this->addLeftSystem(new Thruster(4, 10, 0, 3, 1));
		$this->addLeftSystem(new Thruster(4, 13, 0, 5, 3));
		$this->addLeftSystem(new Thruster(4, 10, 0, 5, 2));
		$this->addLeftSystem(new PacketTorpedo(3, 6, 5, 240, 360));

		$this->addRightSystem(new Thruster(4, 10, 0, 3, 1));
		$this->addRightSystem(new Thruster(4, 13, 0, 5, 4));
		$this->addRightSystem(new Thruster(4, 10, 0, 5, 2));
		$this->addRightSystem(new PacketTorpedo(3, 6, 5, 0, 120));
        
        $this->addFrontSystem(new Structure( 4, 32));
        $this->addAftSystem(new Structure( 4, 32));
        $this->addLeftSystem(new Structure( 4, 40));
        $this->addRightSystem(new Structure( 4, 40));
        $this->addPrimarySystem(new Structure( 4, 64));
		
		
		$this->hitChart = array(
                0=> array(
                        12 => "Structure",
                        14 => "Scanner",
                        16 => "Engine",
                        17 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        3 => "Scattergun",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        3 => "Scattergun",
                        18 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        10 => "Thruster",
						12 => "Packet Torpedo",
                        18 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                        10 => "Thruster",
						12 => "Packet Torpedo",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );

    }
}
?>
