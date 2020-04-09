<?php
class Bashnar extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 300;
	$this->faction = "Brakiri";
        $this->phpclass = "Bashnar";
        $this->imagePath = "img/ships/brathon.png";
        $this->shipClass = "Bashnar Auxiliary Carrier";
        $this->shipSizeClass = 3;
        $this->variantOf = "Brathon Auxiliary Cruiser";
	$this->isd = 2212;
	
		$this->notes = 'Ly-Nakir Industries';//Corporation producing the design
        
        $this->occurence = "uncommon";
        $this->fighters = array("light"=>18);
        
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
	
	$t1l->displayName = "Retro Thrust";
	$t3l->displayName = "Port/Stb Thrust";
	$t2l->displayName = "Main Thrust";
	    
	$t1r = new GraviticThruster(4, 10, 0, 3, 1);
	$t4r = new GraviticThruster(3, 13, 0, 3, 4);
        $t2r = new GraviticThruster(3, 10, 0, 3, 2);
	
	$t1r->displayName = "Retro Thrust";
	$t4r->displayName = "Port/Stb Thrust";
	$t2r->displayName = "Main Thrust";

        $this->addPrimarySystem(new Reactor(3, 15, 0, 2));
        $this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 16, 2, 4));
        $this->addPrimarySystem(new Engine(3, 16, 0, 6, 3));
	$this->addPrimarySystem(new Hangar(2, 8));
   
        $this->addFrontSystem(new GraviticBolt(3, 5, 2, 240, 60));
        $this->addFrontSystem(new GraviticBolt(3, 5, 2, 300, 120));

        $this->addAftSystem(new GraviticBolt(3, 5, 2, 120, 300));
        $this->addAftSystem(new GraviticBolt(3, 5, 2, 60, 240));

        $this->addLeftSystem($t1l);
        $this->addLeftSystem(new Hangar(3, 6));
        $this->addLeftSystem($t3l);
        $this->addLeftSystem($t2l);

        $this->addRightSystem($t1r);
        $this->addRightSystem(new Hangar(3, 6));
        $this->addRightSystem($t4r);
        $this->addRightSystem($t2r);
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 32));
        $this->addAftSystem(new Structure(4, 32));
        $this->addLeftSystem(new Structure(4, 40));
        $this->addRightSystem(new Structure(4, 40));
        $this->addPrimarySystem(new Structure(4, 64));
	    
	$this->hitChart = array(
                0=> array(
                        11 => "Structure",
                        13 => "Scanner",
                        15 => "Engine",
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
                        3 => "Gravitic Bolt",
                        18 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        3 => "Retro Thrust",
			7 => "Port/Stb Thrust",
                        9 => "Main Thrust",
			13 => "Hangar",
                        18 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                        3 => "Retro Thrust",
			7 => "Port/Stb Thrust",
                        9 => "Main Thrust",
			13 => "Hangar",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );
    }
}
?>
