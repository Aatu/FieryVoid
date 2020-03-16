<?php
class Talafat extends BaseShip{
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        $this->pointCost = 650;
        $this->faction = "Markab";
        $this->phpclass = "Talafat";
        $this->isd = 2015;        
        $this->imagePath = "img/ships/MarkabScout.png"; //needs to be changed
        $this->shipClass = "Talafat Scout";
        $this->shipSizeClass = 3;
        $this->canvasSize = 200;
        $this->forwardDefense = 15;
        $this->sideDefense = 17;
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->fighters = array("normal"=>6);
        $this->limited = 33;
		
		
        
        $this->addPrimarySystem(new Reactor(4, 18, 0, 0));
        $this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new ElintScanner(4, 24, 6, 11));
        $this->addPrimarySystem(new Engine(4, 16, 0, 9, 3));
        $this->addPrimarySystem(new Hangar(4, 8));
        
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new ScatterGun(3, 0, 0, 240, 60));
        $this->addFrontSystem(new ScatterGun(3, 0, 0, 300, 120));
        $this->addFrontSystem(new StunBeam(3, 0, 0, 300, 60));
        
        $this->addAftSystem(new Thruster(3, 8, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 8, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 8, 0, 3, 2));
        $this->addAftSystem(new ScatterGun(2, 8, 3, 120, 300));
        $this->addAftSystem(new ScatterGun(2, 8, 3, 60, 240));
        $this->addAftSystem(new JumpEngine(4, 16, 4, 24));
        
		$this->addLeftSystem(new ScatterGun(2, 0, 0, 180, 360));
        $this->addLeftSystem(new Thruster(3, 13, 0, 4, 3));
        $this->addLeftSystem(new StunBeam(3, 0, 0, 240, 360));
        
        $this->addRightSystem(new ScatterGun(2, 0, 0, 0, 180));
        $this->addRightSystem(new Thruster(3, 13, 0, 4, 4));
        $this->addRightSystem(new StunBeam(3, 0, 0, 0, 120));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 60));
        $this->addAftSystem(new Structure( 4, 44));
        $this->addLeftSystem(new Structure( 4, 60));
        $this->addRightSystem(new Structure( 4, 60));
        $this->addPrimarySystem(new Structure( 4, 48));
    
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
            		6 => "Stun Beam",
            		10 => "Scattergun",
                    18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    6 => "Thruster",
            		9 => "Scattergun",
            		11 => "Jump Engine",
                    18 => "Structure",
                    20 => "Primary",
            ),
            3=> array(
                    5 => "Thruster",
                    7 => "Stun Beam",
            		9 => "Scattergun",
                    18 => "Structure",
                    20 => "Primary",
            ),
            4=> array(
                    5 => "Thruster",
                    7 => "Stun Beam",
            		9 => "Scattergun",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
