<?php
class Torotha extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

	$this->pointCost = 550;
	$this->faction = "Minbari";
        $this->phpclass = "Torotha";
        $this->imagePath = "img/ships/torotha.png";
        $this->shipClass = "Torotha";

        $this->forwardDefense = 15;
        $this->sideDefense = 15;

        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
	$this->iniativebonus = 65;


        $this->addPrimarySystem(new Reactor(5, 15, 0, 0));
        $this->addPrimarySystem(new CnC(6, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 16, 4, 9));
        $this->addPrimarySystem(new Engine(5, 14, 0, 8, 2));
	$this->addPrimarySystem(new Hangar(5, 2));
	$this->addPrimarySystem(new Thruster(4, 10, 0, 4, 3));
	$this->addPrimarySystem(new Thruster(4, 10, 0, 4, 4));
        $this->addPrimarySystem(new Jammer(4, 8, 5));

        $this->addFrontSystem(new Thruster(4, 12, 0, 5, 1));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 240, 360));
        $this->addFrontSystem(new MolecularDisruptor(3, 8, 6, 240, 360));
        $this->addFrontSystem(new ElectroPulseGun(2, 6, 3, 300, 60));
        $this->addFrontSystem(new MolecularDisruptor(3, 8, 6, 0, 120));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 0, 120));

        $this->addAftSystem(new FusionCannon(3, 8, 1, 120, 240));
        $this->addAftSystem(new Thruster(4, 21, 0, 8, 2));
        $this->addAftSystem(new FusionCannon(3, 8, 1, 120, 240));

        $this->addPrimarySystem(new Structure( 6, 60));
    }

}
?>
