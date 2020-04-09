<?php
class Brathon extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 325;
	$this->faction = "Brakiri";
        $this->phpclass = "Brathon";
        $this->imagePath = "img/ships/brathon.png";
        $this->shipClass = "Brathon Auxiliary Cruiser";
        $this->shipSizeClass = 3;
	$this->isd = 2230;
        
		$this->notes = 'Kam-Lassit';//Corporation producing the design
		
        $this->forwardDefense = 15;
        $this->sideDefense = 13;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 0;
        
        $this->gravitic = true;
	    
	$t1l = new GraviticThruster(4, 10, 0, 3, 1);
	$t3l = new GraviticThruster(3, 13, 0, 3, 3);
    	$t2l = new GraviticThruster(3, 10, 0, 3, 2);
	
	    /* I am commenting this out - makes thruster recognition in game impossible
	$t1l->displayName = "Retro Thrust";
	$t3l->displayName = "Port/Stb Thrust";
	$t2l->displayName = "Main Thrust";
	*/
	    
	$t1r = new GraviticThruster(4, 10, 0, 3, 1);
	$t4r = new GraviticThruster(3, 13, 0, 3, 4);
   	$t2r = new GraviticThruster(3, 10, 0, 3, 2);
	
	    /* I am commenting this out - makes thruster recognition in game impossible
	$t1r->displayName = "Retro Thrust";
	$t4r->displayName = "Port/Stb Thrust";
	$t2r->displayName = "Main Thrust";
	*/
	    
	$this->addLeftSystem($t1l);
   	$this->addLeftSystem($t3l);
   	$this->addLeftSystem($t2l);
   	$this->addRightSystem($t1r);
   	$this->addRightSystem($t4r);
    	$this->addRightSystem($t2r);

        $this->addPrimarySystem(new Reactor(3, 15, 0, 0));
        $this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 16, 2, 5));
        $this->addPrimarySystem(new Engine(3, 16, 0, 6, 3));
	$this->addPrimarySystem(new Hangar(2, 4));
   
        $this->addFrontSystem(new GraviticBolt(3, 5, 2, 240, 60));
        $this->addFrontSystem(new GraviticBolt(3, 5, 2, 300, 120));

        $this->addAftSystem(new GraviticBolt(3, 5, 2, 120, 300));
        $this->addAftSystem(new GraviticBolt(3, 5, 2, 60, 240));

        $this->addLeftSystem(new GraviticCutter(3, 6, 5, 240, 0));

        $this->addRightSystem(new GraviticCutter(3, 6, 5, 0, 120));
        
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
                        18 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        9 => "Thruster",
			12 => "Gravitic Cutter",
                        18 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                        9 => "Thruster",
			12 => "Gravitic Cutter",
                        18 => "Structure",
                        20 => "Primary",
                ),
		/*sadly thrusters need to be combined into one location...
                3=> array(
                        3 => "Retro Thrust",
			7 => "Port/Stb Thrust",
                        9 => "Main Thrust",
			12 => "Gravitic Cutter",
                        18 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                        3 => "Retro Thrust",
			7 => "Port/Stb Thrust",
                        9 => "Main Thrust",
			12 => "Gravitic Cutter",
                        18 => "Structure",
                        20 => "Primary",
                ),
		*/
        );
	    
    }
}
?>
