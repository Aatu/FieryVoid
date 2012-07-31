<?php
class WhiteStar extends MediumShip{

    function __construct($id, $userid, $name,  $movement){
        parent::__construct($id, $userid, $name,  $movement);

        $this->pointCost = 750;
        $this->faction = "Minbari";
        $this->phpclass = "WhiteStar";
        $this->imagePath = "img/ships/whitestarGS2.png";
        $this->shipClass = "White Star";
        $this->agile = true;
        $this->canvasSize = 200;
        $this->gravitic = true;

        $this->forwardDefense = 13;
        $this->sideDefense = 14;

        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 1;
        $this->iniativebonus = 65;

        $this->addPrimarySystem(new Reactor(5, 20, 0, -7));
        $this->addPrimarySystem(new CnC(5, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 15, 3, 10));
        $this->addPrimarySystem(new Engine(5, 15, 0, 12, 2));
        $this->addPrimarySystem(new Hangar(4, 2));
        $this->addPrimarySystem(new Thruster(4, 12, 0, 5, 3));
        $this->addPrimarySystem(new Thruster(4, 12, 0, 5, 4));
        $this->addPrimarySystem(new Shield(3, 6, 0, 2, 180, 360));
        $this->addPrimarySystem(new Shield(3, 6, 0, 2, 0, 180));
        $this->addPrimarySystem(new TractorBeam(4, 4, 0, 0));
        $this->addPrimarySystem(new Jammer(4, 8, 7));

        $this->addFrontSystem(new MolecularPulsar(4, 8, 2, 300, 60));
        $this->addFrontSystem(new MolecularPulsar(4, 8, 2, 300, 60));
        $this->addFrontSystem(new Thruster(4, 8, 0, 4, 1));
        $this->addFrontSystem(new ImprovedNeutronLaser(4, 11, 7, 330, 30));
        $this->addFrontSystem(new Thruster(4, 8, 0, 4, 1));
        $this->addFrontSystem(new MolecularPulsar(4, 8, 2, 300, 60));
        $this->addFrontSystem(new MolecularPulsar(4, 8, 2, 300, 60));

        $this->addAftSystem(new Thruster(4, 10, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 12, 0, 4, 2));
        $this->addAftSystem(new JumpEngine(5, 20, 4, 24));
        $this->addAftSystem(new Thruster(4, 10, 0, 4, 2));

        $this->addPrimarySystem(new Structure( 5, 48));                                                 
    }

}
?>
