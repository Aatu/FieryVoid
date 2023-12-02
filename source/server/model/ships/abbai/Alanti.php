<?php
class Alanti extends OSAT{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 225; //-25 PV for having no Shield Projector
	$this->faction = "Abbai Matriarchate";
        $this->phpclass = "Alanti";
        $this->imagePath = "img/ships/AbbaiAlanti.png";
        $this->shipClass = 'Alanti Defense Satellite';
        $this->canvasSize = 100;
	    $this->unofficial = 'S'; //Semi-official - added as reasonably close to official Ishtaka, while Grav Shifters are unavailable in FV        

        $this->isd = 2230;

        $this->forwardDefense = 9;
        $this->sideDefense = 9;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60;

        $this->addPrimarySystem(new Reactor(4, 7, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 9, 3, 7)); 
        $this->addPrimarySystem(new Thruster(3, 6, 0, 0, 2)); 
        $this->addPrimarySystem(new CombatLaser(3, 0, 0, 270, 90)); 
        $this->addPrimarySystem(new QuadArray(3, 0, 0, 0, 360));
        $this->addPrimarySystem(new ShieldGenerator(3, 8, 2, 2));
        $this->addPrimarySystem(new GraviticShield(0, 6, 0, 2, 0, 360));

        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        
        $this->addPrimarySystem(new Structure(4, 30));


		
		$this->hitChart = array(
			0=> array(
					9 => "Structure",
					10 => "Thruster",
					12 => "Assault Laser",
       				13 => "Light Particle Beam",
					15 => "Gravitic Shield",
					17 => "Scanner",
					19 => "Reactor",
					20 => "Shield Generator",
			)
		);
    
    }
}
?>
