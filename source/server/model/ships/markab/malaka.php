<?php
class Malaka extends BaseShip{
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        $this->pointCost = 220;
        $this->faction = "Civilians";
        $this->phpclass = "Malaka";
        $this->isd = 2000;        
        $this->imagePath = "img/ships/MarkabAssaultShip.png"; //needs to be changed
        $this->shipClass = "Markab Malaka Transport";
        $this->shipSizeClass = 3;
        $this->canvasSize = 200;
        $this->forwardDefense = 16;
        $this->sideDefense = 17;
        $this->turncost = 1;
        $this->turndelaycost = 0.66;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 6;
        $this->variantOf = 'Mafka Transport Cruiser';
        $this->occurence = "common";
        $this->iniativebonus -20;
        
        $this->addPrimarySystem(new Reactor(4, 18, 0, 0));
        $this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 16, 3, 4));
        $this->addPrimarySystem(new Engine(4, 14, 0, 8, 3));
        $this->addPrimarySystem(new Hangar(4, 8));
        
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new ScatterGun(2, 0, 0, 300, 60));
        $this->addFrontSystem(new CargoBay(3, 40));
        
        $this->addAftSystem(new Thruster(3, 13, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 13, 0, 4, 2));
	$this->addAftSystem(new CargoBay(4, 40));
	$this->addAftSystem(new CargoBay(3, 24));
	$this->addAftSystem(new CargoBay(4, 40));
        
	$this->addLeftSystem(new ScatterGun(2, 0, 0, 180, 60));
        $this->addLeftSystem(new Thruster(3, 13, 0, 3, 3));
        
        $this->addRightSystem(new ScatterGun(2, 0, 0, 300, 180));
        $this->addRightSystem(new Thruster(3, 13, 0, 3, 4));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 48));
        $this->addAftSystem(new Structure( 4, 40));
        $this->addLeftSystem(new Structure( 4, 56));
        $this->addRightSystem(new Structure( 4, 56));
        $this->addPrimarySystem(new Structure( 4, 40));
    
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
                    3 => "Thruster",
            		8 => "Cargo Bay",
            		10 => "Scattergun",
                    18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    6 => "Thruster",
            		10 => "Cargo Bay",
                    18 => "Structure",
                    20 => "Primary",
            ),
            3=> array(
                    5 => "Thruster",
                    7 => "Scattergun",
                    18 => "Structure",
                    20 => "Primary",
            ),
            4=> array(
                    5 => "Thruster",
                    7 => "Scattergun",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
