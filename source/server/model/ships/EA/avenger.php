<?php
class Avenger extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 580;
	$this->faction = "EA";
        $this->phpclass = "Avenger";
        $this->imagePath = "img/ships/avenger.png";
        $this->shipClass = "Avenger Heavy Carrier (Gamma)";
        $this->shipSizeClass = 3;
        $this->fighters = array("normal"=>48);
	    $this->isd = 2240;
        
        $this->forwardDefense = 14;
        $this->sideDefense = 17;
        
        $this->turncost = 1.25;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
	
        $this->addPrimarySystem(new Reactor(5, 18, 0, 0));
        $this->addPrimarySystem(new CnC(5, 20, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 16, 3, 6));
        $this->addPrimarySystem(new Engine(5, 18, 0, 8, 3));
        $this->addPrimarySystem(new Hangar(5, 2));
        $this->addPrimarySystem(new JumpEngine(5, 20, 4, 24));

        $this->addFrontSystem(new LightPulse(2, 4, 2, 240, 0));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new MediumPlasma(4, 5, 3, 300, 60));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new LightPulse(2, 4, 2, 0, 120));
        $this->addFrontSystem(new InterceptorMkI(2, 4, 1, 240, 0));
        $this->addFrontSystem(new InterceptorMkI(2, 4, 1, 0, 120));

        $this->addAftSystem(new Thruster(4, 12, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 12, 0, 4, 2));
        $this->addAftSystem(new InterceptorMkI(2, 4, 1, 180, 300));
        $this->addAftSystem(new InterceptorMkI(2, 4, 1, 60, 180));

        $this->addLeftSystem(new Thruster(3, 15, 0, 4, 3));
        $this->addLeftSystem(new MediumPlasma(3, 5, 3, 240, 0));
        $this->addLeftSystem(new LightPulse(2, 4, 2, 180, 0));
        $this->addLeftSystem(new LightPulse(2, 4, 2, 180, 300));
        $this->addLeftSystem(new InterceptorMkI(2, 4, 1, 240, 0));
        $this->addLeftSystem(new InterceptorMkI(2, 4, 1, 180, 300));
        $this->addLeftSystem(new Hangar(3, 6));
        $this->addLeftSystem(new Hangar(3, 6));
        $this->addLeftSystem(new Hangar(3, 6));
        $this->addLeftSystem(new Hangar(3, 6));

        $this->addRightSystem(new Thruster(3, 15, 0, 4, 4));
        $this->addRightSystem(new MediumPlasma(3, 5, 3, 0, 120));
        $this->addRightSystem(new LightPulse(2, 4, 2, 0, 180));
        $this->addRightSystem(new LightPulse(2, 4, 2, 60, 180));
        $this->addRightSystem(new InterceptorMkI(2, 4, 1, 0, 120));
        $this->addRightSystem(new InterceptorMkI(2, 4, 1, 60, 180));
        $this->addRightSystem(new Hangar(3, 6));
        $this->addRightSystem(new Hangar(3, 6));
        $this->addRightSystem(new Hangar(3, 6));
        $this->addRightSystem(new Hangar(3, 6));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 40));
        $this->addAftSystem(new Structure( 4, 40));
        $this->addLeftSystem(new Structure( 4, 60));
        $this->addRightSystem(new Structure( 4, 60));
        $this->addPrimarySystem(new Structure( 5, 48));


        $this->hitChart = array(
            0=> array(
                    10 => "Structure",
                    12 => "Jump Engine",
                    14 => "Scanner",
                    16 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    4 => "Thruster",
                    6 => "Light Pulse Cannon",
                    7 => "Medium Plasma Cannon",
                    10 => "Interceptor I",
                    18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    6 => "Thruster",
                    10 => "Interceptor I",
                    18 => "Structure",
                    20 => "Primary",
            ),
            3=> array(
                    3 => "Thruster",
                    4 => "Medium Plasma Cannon",
                    5 => "Light Pulse Cannon",
                    7 => "Interceptor I",
                    11 => "Hangar",
                    18 => "Structure",
                    20 => "Primary",
            ),
            4=> array(
                    3 => "Thruster",
                    4 => "Medium Plasma Cannon",
                    5 => "Light Pulse Cannon",
                    7 => "Interceptor I",
                    11 => "Hangar",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }

}
