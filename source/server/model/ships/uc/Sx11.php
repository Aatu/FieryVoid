<?php
class Sx11 extends MediumShip
{

    public function __construct($id, $userid, $name, $slot)
    {
        parent::__construct($id, $userid, $name, $slot);

        $this->pointCost = 375;
        $this->faction = "UC";
        $this->phpclass = "Sx11";
        $this->shipClass = 'SX-11 Enforcer "Rhino"';
        $this->shipModel = "Rhino";

        $this->isd = 2246;

        $this->forwardDefense = 13;
        $this->sideDefense = 13;

        $this->accelcost = 1;
        $this->rollcost = 1;
        $this->pivotcost = 1;
        $this->iniativebonus = 60;
        $this->evasioncost = 2;

        $this->addPrimarySystem(100, new Reactor(4, 13, 0, 0));
        $this->addPrimarySystem(101, new CnC(4, 9, 0, 0));
        $this->addPrimarySystem(102, new Scanner(4, 12, 3, 6));
        $this->addPrimarySystem(103, new Engine(4, 11, 0, 16, 2));
        $this->addPrimarySystem(104, new Hangar(4, 2));

        $this->addFrontSystem(202, new MediumPulse(3, 6, 3, 240, 360));
        $this->addFrontSystem(203, new LightPulse(2, 4, 2, 270, 90));
        $this->addFrontSystem(204, new InterceptorMkI(2, 4, 1, 270, 90));
        $this->addFrontSystem(205, new LightPulse(2, 4, 2, 270, 90));
        $this->addFrontSystem(206, new MediumPulse(3, 6, 3, 0, 120));
        $this->addFrontSystem(207, new ManouveringThruster(3, 8, 0, 3, 3));

        $this->addAftSystem(300, new Thruster(4, 8, 0, 10, 3));
        $this->addAftSystem(302, new LightPulse(2, 4, 2, 180, 0));
        $this->addAftSystem(303, new InterceptorMkI(2, 4, 1, 90, 270));
        $this->addAftSystem(304, new LightPulse(2, 4, 2, 0, 180));
        $this->addAftSystem(305, new ManouveringThruster(3, 8, 0, 3, 3));

        $this->addPrimarySystem(1, new Structure(4, 38));
    }

}
