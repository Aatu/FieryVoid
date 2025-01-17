<?php
class TGan extends OSAT{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 225;
	$this->faction = "Narn Regime";
        $this->phpclass = "TGan";
        $this->imagePath = "img/ships/tgan.png";
        $this->shipClass = "T'Gan Satellite";
	$this->isd = 2242;
        
        $this->forwardDefense = 10;
        $this->sideDefense = 10;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60;

        $this->addPrimarySystem(new OSATCnC(0, 1, 0, 0));
        $this->addPrimarySystem(new Reactor(4, 7, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 7, 2, 6)); 
        $this->addAftSystem(new Thruster(3, 6, 0, 0, 2));
        
        $this->addFrontSystem(new IonTorpedo(3, 5, 4, 270, 90));
        $this->addFrontSystem(new EnergyMine(2, 5, 4, 270, 90));
        $this->addFrontSystem(new IonTorpedo(3, 5, 4, 270, 90));
        $this->addAftSystem(new LightPulse(2, 4, 2, 180, 360));
        $this->addAftSystem(new LightPulse(2, 4, 2, 0, 180));
        

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        
        $this->addPrimarySystem(new Structure(4, 33));
	    
	$this->hitChart = array(
				0=> array(
				9 => "Structure",
				11 => "2:Thruster",
				13 => "1:Ion Torpedo",
				15 => "2:Light Pulse Cannon",
				17 => "Scanner",
				19 => "Reactor",
				20 => "1:Energy Mine",
			),
        );
    }
}
