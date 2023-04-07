<?php
class Tlaca extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 300;
		$this->faction = "Raiders";
        $this->phpclass = "Tlaca";
        $this->imagePath = "img/ships/NarnPrivateerTlaca.png";
        $this->shipClass = "Narn Privateer T'Laca Light Carrier";
        $this->canvasSize = 100;
        
		$this->notes = "Used only by Narn privateers";
		$this->notes .= "<br> ";

		$this->isd = 2243;
        
        $this->forwardDefense = 12;
        $this->sideDefense = 15;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
    	$this->iniativebonus = -4 * 5;
    	$this->fighters = array("medium"=>12);
         
        $this->addPrimarySystem(new Reactor(3, 8, 0, 0));
        $this->addFrontSystem(new CnC(3, 4, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 8, 2, 4));
        $this->addPrimarySystem(new Engine(3, 6, 0, 4, 3));        
        $this->addFrontSystem(new Hangar(3, 1));
    	$this->addPrimarySystem(new Thruster(2, 9, 0, 2, 3));
    	$this->addPrimarySystem(new Thruster(2, 9, 0, 2, 4));
    	
		
        $this->addFrontSystem(new Thruster(2, 9, 0, 2, 1));
        $this->addFrontSystem(new Thruster(2, 9, 0, 2, 1));
        $this->addFrontSystem(new MediumPlasma(4, 5, 3, 240, 360));
        $this->addFrontSystem(new MediumPlasma(4, 5, 3, 0, 120));
        $this->addFrontSystem(new LightPulse(2, 4, 1, 240, 360));
        $this->addFrontSystem(new LightPulse(3, 6, 3, 0, 120));		

        $this->addAftSystem(new Hangar(1, 12));

	
        $this->addPrimarySystem(new Structure( 4, 46));
        
        $this->hitChart = array(
        		0=> array(
        				11 => "Thruster",
        				14 => "Scanner",
        				17 => "Engine",
        				20 => "Reactor",

        		),
        		1=> array(
        				4 => "Thruster",
        				6 => "Medium Plasma Cannon",
        				8 => "Light Pulse Cannon",
        	        	9 => "C&C",	
        	        	10 => "Hangar",		
        				17 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				6 => "Thruster",
        				10 => "Hangar",	
        				17 => "Structure",
        				20 => "Primary",
        		),
        );
    }

}



?>
