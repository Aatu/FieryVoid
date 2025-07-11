<?php
class alanti extends OSAT{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 250; //-25 PV for having no Shield Projector
	$this->faction = "Abbai Matriarchate";
        $this->phpclass = "alanti";
        $this->imagePath = "img/ships/AbbaiAlanti.png";
        $this->shipClass = 'Alanti Defense Satellite';
        $this->canvasSize = 100;    

        $this->isd = 2230;

        $this->forwardDefense = 9;
        $this->sideDefense = 9;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60;

        $this->addPrimarySystem(new OSATCnC(0, 1, 0, 0));
        $this->addPrimarySystem(new Reactor(4, 7, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 9, 3, 7)); 
        $this->addPrimarySystem(new ShieldGenerator(3, 8, 2, 2));
		
        $this->addFrontSystem(new CombatLaser(3, 0, 0, 270, 90));     
        $this->addFrontSystem(new QuadArray(3, 0, 0, 0, 360));
        
        $this->addAftSystem(new AbbaiShieldProjector(2, 0, 0, 0, 360, 2));		
        $this->addAftSystem(new GraviticShield(0, 6, 0, 2, 0, 360));
        $this->addAftSystem(new Particleimpeder(2, 0, 0, 0, 360));
        $this->addAftSystem(new Thruster(3, 6, 0, 0, 2)); 

        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;        
        $this->addPrimarySystem(new Structure(4, 30));


		
		$this->hitChart = array(
			0=> array(
					8 => "Structure",
					9 => "2:Thruster",
					11 => "1:Combat Laser",
       				12 => "1:Quad Array",
					13 => "2:Gravitic Shield",
					14 => "2:Shield Projector",
					15 => "2:Particle Impeder",
					17 => "Scanner",
					19 => "Reactor",
					20 => "Shield Generator",
			)
		);
    
    }
}
?>
