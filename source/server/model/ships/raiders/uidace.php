<?php
class Uidace extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
  $this->pointCost = 550;
  $this->faction = "Raiders";
        $this->phpclass = "Uidace";
        $this->imagePath = "img/ships/battlewagon.png"; //need to change this
        $this->shipClass = "Uid'Ac'e Cruiser";
        $this->shipSizeClass = 3;
        $this->fighters = array("normal"=>12);
        
        $this->forwardDefense = 16;
        $this->sideDefense = 18;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = -5;
       
        $this->addPrimarySystem(new Reactor(4, 20, 0, 0));
        $this->addPrimarySystem(new CnC(4, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 6, 6));
        $this->addPrimarySystem(new Engine(4, 14, 0, 10, 3));
        $this->addPrimarySystem(new Hangar(4, 16));
  
        $this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
        $this->addFrontSystem(new MediumBolter(2, 8, 4, 270, 90));
        $this->addFrontSystem(new ScatterPulsar(2, 4, 2, 180, 360));
        $this->addFrontSystem(new ScatterPulsar(2, 4, 2, 0, 180));
        
        $this->addAftSystem(new Thruster(2, 13, 0, 5, 2));
        $this->addAftSystem(new Thruster(2, 13, 0, 5, 2));
        $this->addAftSystem(new ScatterPulsar(2, 4, 2, 180, 360));
        $this->addAftSystem(new ScatterPulsar(2, 4, 2, 0, 180));
        
		$this->addLeftSystem(new Thruster(4, 13, 0, 5, 3));
		$this->addLeftSystem(new MediumLaser(3, 6, 5, 240, 0));
		$this->addLeftSystem(new MediumPlasma(2, 5, 3, 180, 0));
        
		$this->addRightSystem(new Thruster(4, 13, 0, 5, 4));        
		$this->addRightSystem(new MediumLaser(3, 6, 5, 0, 120));
		$this->addRightSystem(new MediumPlasma(2, 5, 3, 0, 180));
		
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 36));
        $this->addAftSystem(new Structure( 2, 36));
        $this->addLeftSystem(new Structure( 4, 40));
        $this->addRightSystem(new Structure( 4, 40));
        $this->addPrimarySystem(new Structure( 5, 30));
        
        $this->hitChart = array(
        		0=> array(
        				1 => "structure",
        				2 => "structure",
        				3 => "structure",
        				4 => "structure",
        				5 => "structure",
        				6 => "structure",
        				7 => "structure",
        				8 => "structure",
        				9 => "structure",
        				10 => "scanner",
        				11 => "scanner",
        				12 => "engine",
        				13 => "engine",
        				14 => "engine",
        				15 => "hangar",
        				16 => "hangar",
        				17 => "reactor",
        				18 => "reactor",
        				19 => "reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				1 => "thruster",
        				2 => "thruster",
        				3 => "thruster",
        				4 => "thruster",
        				5 => "medium Bolter",
        				6 => "scatter Pulsar",
        				7 => "scatter Pulsar",
        				8 => "structure",
        				9 => "structure",
        				10 => "structure",
        				11 => "structure",
        				12 => "structure",
        				13 => "structure",
        				14 => "structure",
        				15 => "structure",
        				16 => "structure",
        				17 => "structure",
        				18 => "structure",
        				19 => "primary",
        				20 => "primary",
        		),
        		2=> array(
        				1 => "thruster",
        				2 => "thruster",
        				3 => "thruster",
        				4 => "thruster",
        				5 => "thruster",
        				6 => "scatter Pulsar",
        				7 => "scatter Pulsar",
        				8 => "scatter Pulsar",
        				9 => "scatter Pulsar",
        				10 => "structure",
        				11 => "structure",
        				12 => "structure",
        				13 => "structure",
        				14 => "structure",
        				15 => "structure",
        				16 => "structure",
        				17 => "structure",
        				18 => "structure",
        				19 => "primary",
        				20 => "primary",
        		),
        		3=> array(
        				1 => "thruster",
        				2 => "thruster",
        				3 => "thruster",
        				4 => "thruster",
        				5 => "medium Laser",
        				6 => "medium Laser",
        				7 => "medium Plasma",
        				8 => "medium Plasma",
        				9 => "structure",
        				10 => "structure",
        				11 => "structure",
        				12 => "structure",
        				13 => "structure",
        				14 => "structure",
        				15 => "structure",
        				16 => "structure",
        				17 => "structure",
        				18 => "structure",
        				19 => "primary",
        				20 => "primary",
        		),
        		4=> array(
        				1 => "thruster",
        				2 => "thruster",
        				3 => "thruster",
        				4 => "thruster",
        				5 => "medium Laser",
        				6 => "medium Laser",
        				7 => "medium Plasma",
        				8 => "medium Plasma",
        				9 => "structure",
        				10 => "structure",
        				11 => "structure",
        				12 => "structure",
        				13 => "structure",
        				14 => "structure",
        				15 => "structure",
        				16 => "structure",
        				17 => "structure",
        				18 => "structure",
        				19 => "primary",
        				20 => "primary",
        		),
        );
        
    }
}