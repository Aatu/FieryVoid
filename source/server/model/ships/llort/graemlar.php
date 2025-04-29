<?php
class Graemlar extends OSAT{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 180;
        $this->faction = "Llort";
        $this->phpclass = "Graemlar";
        $this->imagePath = "img/ships/LlortGraemlar.png";
        $this->shipClass = 'Graemlar Defense Satellite';
        $this->isd = 2223;
        
        $this->forwardDefense = 9;
        $this->sideDefense = 12;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60;
     
        $this->addPrimarySystem(new OSATCnC(0, 1, 0, 0));        
        $this->addPrimarySystem(new Reactor(4, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 6, 2, 5)); 
        $this->addAftSystem(new Thruster(4, 7, 0, 0, 2)); 
        
        $this->addFrontSystem(new ParticleCannon(3, 8, 7, 300, 60));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 120, 300));
        $this->addFrontSystem(new ScatterGun(2, 8, 3, 0, 120));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        
        $this->addPrimarySystem(new Structure(5, 35));
    
    		$this->hitChart = array(
			0=> array(
					8 => "Structure",
					10 => "2:Thruster",
					13 => "1:Particle Cannon",
       				14 => "1:Light Particle Beam",
       				15 => "2:Light Particle Beam",
					17 => "Scanner",
					19 => "Reactor",
					20 => "1:Scattergun",
			)
		);

    }

}

?>
