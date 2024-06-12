<?php
class Enbrak extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 230;
	$this->faction = "Civilians";
        $this->phpclass = "Enbrak";
        $this->imagePath = "img/ships/brathon.png";
        $this->shipClass = "Brakiri Enbrak Transport";
        $this->shipSizeClass = 3;
		$this->isd = 2199;
        
		$this->notes = 'Tor-Sikar';//Corporation producing the design
		
        $this->forwardDefense = 15;
        $this->sideDefense = 13;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 6;
        $this->rollcost = 99; //cannot roll
        $this->pivotcost = 99; //cannot pivot
        $this->iniativebonus = -30;
        
        $this->gravitic = true;
	    
		$t1l = new GraviticThruster(4, 10, 0, 3, 1);
		$t3l = new GraviticThruster(3, 13, 0, 3, 3);
		$t2l = new GraviticThruster(3, 10, 0, 3, 2);
	    
		$t1r = new GraviticThruster(4, 10, 0, 3, 1);
		$t4r = new GraviticThruster(3, 13, 0, 3, 4);
		$t2r = new GraviticThruster(3, 10, 0, 3, 2);
	
		$this->addLeftSystem($t1l);
		$this->addLeftSystem($t3l);
		$this->addLeftSystem($t2l);
		$this->addRightSystem($t1r);
		$this->addRightSystem($t4r);
		$this->addRightSystem($t2r);

        $this->addPrimarySystem(new Reactor(3, 11, 0, 0));
        $this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 16, 2, 3));
        $this->addPrimarySystem(new Engine(3, 16, 0, 6, 4));
		$this->addPrimarySystem(new Hangar(2, 4));
   
        $this->addFrontSystem(new GraviticBolt(2, 5, 2, 240, 60));
        $this->addFrontSystem(new GraviticBolt(2, 5, 2, 300, 120));

        $this->addAftSystem(new GraviticBolt(2, 5, 2, 120, 300));
        $this->addAftSystem(new GraviticBolt(2, 5, 2, 60, 240));
		$this->addAftSystem(new CargoBay(1, 30));
		$this->addAftSystem(new CargoBay(1, 30));
		
        $this->addLeftSystem(new CargoBay(1, 40));

        $this->addRightSystem(new CargoBay(1, 40));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 32));
        $this->addAftSystem(new Structure(4, 32));
        $this->addLeftSystem(new Structure(4, 40));
        $this->addRightSystem(new Structure(4, 40));
        $this->addPrimarySystem(new Structure(4, 64));

	$this->hitChart = array(
                0=> array(
                        12 => "Structure",
                        14 => "Scanner",
                        16 => "Engine",
                        17 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        5 => "Gravitic Bolt",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        4 => "Gravitic Bolt",
						8 => "Cargo Bay",
                        18 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        9 => "Thruster",
						13 => "Cargo Bay",
                        18 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                        9 => "Thruster",
						13 => "Cargo Bay",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );
	    
    }
}
?>
