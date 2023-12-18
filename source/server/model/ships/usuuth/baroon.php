<?php
class Baroon extends LCV{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 190;
        $this->faction = "Usuuth Coalition";
        $this->phpclass = "Baroon";
        $this->shipClass = "Baroon Escort Cutter";
        $this->imagePath = "img/ships/UsuuthBaroon.png";
        $this->canvasSize = 100;
        $this->agile = true;
        
        $this->forwardDefense = 10;
        $this->sideDefense = 11;
        
        $this->isd = 1950;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 1;
        $this->rollcost = 1;
        $this->pivotcost = 1;
        $this->iniativebonus = 14 *5;       
        
        $this->addFrontSystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
        $this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
        $this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
        $this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance
        
        $this->addPrimarySystem(new Reactor(4, 7, 0, 0));
        $this->addPrimarySystem(new CnC(99, 99, 0, 0)); //C&C should be unhittable anyway
	    	$sensors = new Scanner(4, 12, 2, 3);
		    $sensors->markLCV();
		    $this->addPrimarySystem($sensors);
        $this->addPrimarySystem(new Engine(4, 12, 0, 6, 1));

        $this->addFrontSystem(new ParticleProjector(2, 6, 1, 300, 60));
        $this->addFrontSystem(new ParticleProjector(2, 6, 1, 300, 60));
        $this->addFrontSystem(new LightParticleProjector(2, 3, 1, 180, 360));
        $this->addFrontSystem(new LightParticleProjector(2, 3, 1, 0, 180));
        
        $this->addPrimarySystem(new Structure(5, 30));   
        
        $this->hitChart = array(
            0=> array( //should never happen
                11 => "Structure",
                13 => "1:Light Particle Projector",
                16 => "1:Particle Projector",
                18 => "0:Engine",
                19 => "0:Reactor",
                20 => "0:Scanner",
            ),
            1=> array( //PRIMARY hit table, effectively
                11 => "Structure",
                13 => "1:Light Particle Projector",
                16 => "1:Particle Projector",
                18 => "0:Engine",
                19 => "0:Reactor",
                20 => "0:Scanner",
            ),
            2=> array( //same as Fwd
                11 => "Structure",
                13 => "1:Light Particle Projector",
                16 => "1:Particle Projector",
                18 => "0:Engine",
                19 => "0:Reactor",
                20 => "0:Scanner",
            ),         
        ); //end of hit chart              
    }
}
?>
