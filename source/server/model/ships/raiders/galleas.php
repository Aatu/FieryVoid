<?php
class Galleas extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
  		$this->pointCost = 400;
  		$this->faction = "Raiders";
        $this->phpclass = "Galleas";
        $this->imagePath = "img/ships/battlewagon.png"; //needs to be changed
        $this->shipClass = "Galleas";
        $this->shipSizeClass = 3;
        $this->fighters = array("normal"=>18);
        
		$this->notes = "Generic raider unit.";
		$this->notes .= "<br> ";
		$this->notes .= "<br>More detailed deployment restrictions are in the Faction List document.";
		$this->notes .= "<br> ";

		$this->isd = 2233;
        
        $this->forwardDefense = 15;
        $this->sideDefense = 13;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 0;
         
        $this->addPrimarySystem(new Reactor(3, 15, 0, 0));
        $this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 16, 2, 5));
        $this->addPrimarySystem(new Hangar(2, 7));
		
		$this->addFrontSystem(new MediumBolter(3, 8, 4, 240, 360));
		$this->addFrontSystem(new MediumBolter(3, 8, 4, 0, 120));
			
        $this->addAftSystem(new Engine(3, 16, 0, 6, 3));
		$this->addAftSystem(new TwinArray(3, 6, 2, 90, 270));
		
		$temp1 = new Thruster(4, 10, 0, 3, 1);
			$temp1->displayName = "Retro Thruster";
		$temp2 = new Thruster(3, 13, 0, 3, 3);
			$temp2->displayName = "Port Thruster";
		$temp3 = new Thruster(3, 10, 0, 3, 2);
			$temp3->displayName = "Main Thruster";
		$temp4 = new Thruster(4, 10, 0, 3, 1);
			$temp4->displayName = "Retro Thruster";
		$temp5 = new Thruster(3, 13, 0, 3, 4);
			$temp5->displayName = "Starboard Thruster";
		$temp6 = new Thruster(3, 10, 0, 3, 2);
			$temp6->displayName = "Main Thruster";
		
		$this->addLeftSystem(new TwinArray(3, 6, 2, 240, 60));
		$this->addLeftSystem(new Hangar(2, 6));
		$this->addLeftSystem($temp1);
		$this->addLeftSystem($temp2);
		$this->addLeftSystem($temp3);
		
		$this->addRightSystem(new TwinArray(3, 6, 2, 300, 120));
		$this->addRightSystem(new Hangar(2, 6));
		$this->addRightSystem($temp4);
		$this->addRightSystem($temp5);
		$this->addRightSystem($temp6);
		
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 24));
        $this->addAftSystem(new Structure( 4, 24));
        $this->addLeftSystem(new Structure( 4, 32));
        $this->addRightSystem(new Structure( 4, 32));
        $this->addPrimarySystem(new Structure( 4, 64));
        
        $this->hitChart = array(
        		0=> array(
        				12 => "Structure",
        				15 => "Scanner",
        				17 => "Hangar",
        				19 => "Reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				5 => "Medium Bolter",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				4 => "Twin Array",
        				7 => "Engine",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		3=> array(
        				3 => "Retro Thruster",
        				6 => "Port Thruster",
        				8 => "Main Thruster",
        				11 => "Twin Array",
        				13 => "Hangar",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		4=> array(
        				3 => "Retro Thruster",
        				6 => "Starboard Thruster",
        				8 => "Main Thruster",
        				11 => "Twin Array",
        				13 => "Hangar",
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
    }
}