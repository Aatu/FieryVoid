<?php
class ShaveenPoliceLeader extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

	$this->pointCost = 600;
	$this->faction = "Minbari";
        $this->phpclass = "ShaveenPoliceLeader";
        $this->imagePath = "img/ships/shaveen.png";
        $this->shipClass = "Shaveen Police Leader";
        $this->agile = true;
        $this->gravitic = true;
        $this->canvasSize = 100;
        $this->occurence = "uncommon";

        $this->forwardDefense = 11;
        $this->sideDefense = 13;

        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
	$this->iniativebonus = 75;

        $this->addPrimarySystem(new Reactor(4, 13, 0, 0));
        $this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 13, 3, 9));
        $this->addPrimarySystem(new Engine(3, 13, 0, 8, 2));
	$this->addPrimarySystem(new Hangar(4, 1));
	$this->addPrimarySystem(new GraviticThruster(4, 12, 0, 4, 3));
	$this->addPrimarySystem(new GraviticThruster(4, 12, 0, 4, 4));
        $this->addPrimarySystem(new Jammer(4, 8, 5));
        $this->addPrimarySystem(new TractorBeam(4, 4, 0, 0));

        $this->addFrontSystem(new GraviticThruster(4, 12, 0, 6, 1));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 270, 90));
        $this->addFrontSystem(new MolecularPulsar(3, 8, 2, 270, 90));
        $this->addFrontSystem(new ShockCannon(3, 6, 4, 270, 90));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 270, 90));

        $this->addAftSystem(new FusionCannon(3, 8, 1, 180, 0));
        $this->addAftSystem(new GraviticThruster(4, 10, 0, 4, 2));
        $this->addAftSystem(new GraviticThruster(4, 10, 0, 4, 2));
        $this->addAftSystem(new FusionCannon(3, 8, 1, 0, 180));

        $this->addPrimarySystem(new Structure( 4, 48));
    }
}
?>
