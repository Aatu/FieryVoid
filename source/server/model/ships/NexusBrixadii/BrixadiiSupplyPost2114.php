<?php
class BrixadiiSupplyPost2114 extends OSAT{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 210;
        $this->faction = "Nexus Brixadii Clans";       
        $this->phpclass = "BrixadiiSupplyPost2114";
        $this->imagePath = "img/ships/EscalationWars/SshelathKaumlar.png";
        $this->shipClass = "Norivar Supply Post (2114)";
        $this->isd = 2114;
		$this->unofficial = true;

        $this->forwardDefense = 11;
        $this->sideDefense = 13;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60;

		$this->addFrontSystem(new ScatterPulsar(2, 4, 2, 180, 60));
		$this->addFrontSystem(new HvyParticleProjector(2, 8, 4, 270, 90));
		$this->addFrontSystem(new NexusChaffLauncher(2, 2, 1, 0, 360));
		$this->addFrontSystem(new ScatterPulsar(2, 4, 2, 300, 180));

		$this->addAftSystem(new ScatterPulsar(2, 4, 2, 120, 360));
		$this->addAftSystem(new HvyParticleProjector(2, 8, 4, 90, 270));
		$this->addAftSystem(new ScatterPulsar(2, 4, 2, 0, 240));
        $this->addAftSystem(new Thruster(2, 20, 0, 0, 2));

        $this->addPrimarySystem(new OSATCnC(0, 1, 0, 0));
        $this->addPrimarySystem(new Reactor(2, 19, 0, 0));
        $this->addPrimarySystem(new Scanner(2, 12, 3, 4));   
		$this->addPrimarySystem(new Quarters(2, 20));
		$this->addPrimarySystem(new Quarters(2, 20));
		$this->addPrimarySystem(new CargoBay(2, 20));
		$this->addPrimarySystem(new CargoBay(2, 20));
                
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(3, 72));

        //Block some enhancements for OSAT units when bought
        Enhancements::nonstandardEnhancementSet($this, 'OSAT');

		$this->hitChart = array(
                0=> array(
                        6 => "Structure",
						8 => "Cargo Bay",
						10 => "2:Thruster",
                        11 => "1:Heavy Particle Projector",
                        12 => "2:Heavy Particle Projector",
						13 => "1:Scatter Pulsar",
						14 => "2:Scatter Pulsar",
                        16 => "Quarters",
						17 => "1:Chaff Launcher",
                        19 => "Scanner",
                        20 => "Reactor",
                )
        );
    }
}

?>
