<?php
class Kirva1980 extends OSAT{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 210;
		$this->faction = "Abbai Matriarchate (WotCR)";
        $this->phpclass = "Kirva1980";
        $this->imagePath = "img/ships/AbbaiBochi.png";
        $this->shipClass = 'Kirva Jammer Satellite (1980)';
			$this->occurence = 'rare'; 
			$this->variantOf = "Bochi Defense Satellite";
        $this->canvasSize = 200;
        
        $this->isd = 1980;

        $this->forwardDefense = 8;
        $this->sideDefense = 8;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60;

        $this->addPrimarySystem(new Reactor(4, 6, 0, 0));
        $this->addPrimarySystem(new ELINTScanner(4, 6, 2, 4)); 
        $this->addAftSystem(new Thruster(4, 5, 0, 0, 2)); 
        $this->addFrontSystem(new SensorSpear(3, 0, 0, 270, 90)); 
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 0, 360));
        $this->addFrontSystem(new ImpCommJammer(3, 0, 0, 270, 90)); 
        $this->addPrimarySystem(new ShieldGenerator(3, 8, 4, 1));
        $this->addAftSystem(new GraviticShield(0, 6, 0, 1, 180, 360));
        $this->addAftSystem(new GraviticShield(0, 6, 0, 1, 0, 180, 0));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        
        $this->addPrimarySystem(new Structure(4, 25));


		
		$this->hitChart = array(
			0=> array(
					9 => "Structure",
					10 => "2:Thruster",
					11 => "1:Sensor Spear",
					12 => "1:Improved Comm Jammer",
       				13 => "1:Light Particle Beam",
					15 => "2:Gravitic Shield",
					17 => "Elint Scanner",
					19 => "Reactor",
					20 => "Shield Generator",
			)
		);
    
    }
}
?>