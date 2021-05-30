<?php
class Uidace extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
  $this->pointCost = 550;
  $this->faction = "Raiders";
        $this->phpclass = "Uidace";
        $this->imagePath = "img/ships/GaimShamor.png"; //Currently using Gaim version of this hull
        $this->shipClass = "Uid'Ac'e Cruiser";
//        $this->shipSizeClass = 3;
		$this->canvasSize = 175;
			$this->limited = 10;
        $this->fighters = array("normal"=>12);
        
		$this->notes = "Generic raider unit.";
		$this->notes .= "<br> ";
		$this->notes .= "<br>More detailed deployment restrictions are in the Faction List document.";
		$this->notes .= "<br> ";

		$this->isd = 2235;
        
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
        				9 => "Structure",
        				11 => "Scanner",
        				14 => "Engine",
        				15 => "Hangar",
        				16 => "Hangar",
        				19 => "Reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				4 => "Thruster",
        				5 => "Medium Bolter",
        				7 => "Scatter Pulsar",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				5 => "Thruster",
        				9 => "Scatter Pulsar",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		3=> array(
        				4 => "Thruster",
        				6 => "Medium Laser",
        				8 => "Medium Plasma Cannon",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		4=> array(
        				4 => "Thruster",
        				6 => "Medium Laser",
        				8 => "Medium Plasma Cannon",
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
        
    }
}