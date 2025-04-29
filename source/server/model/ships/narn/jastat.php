<?php


class JaStat extends StarBaseFiveSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 5000;
		$this->faction = "Narn Regime";
		$this->phpclass = "JaStat";
		$this->shipClass = "Ja'Stat Warbase";
		$this->fighters = array("heavy"=>36); 
	    $this->isd = 2243;

		$this->shipSizeClass = 3;
        $this->Enormous = true;		
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;

		$this->forwardDefense = 20;
		$this->sideDefense = 20;

		$this->imagePath = "img/ships/jastat.png";
		$this->canvasSize = 280; //Enormous Starbase

		$this->locations = array(1, 41, 42, 32, 31);
		$this->hitChart = array(			
			0=> array(
				11 => "Structure",
				12 => "Pulsar Mine",
				13 => "Energy Mine",
				16 => "Scanner",
				18 => "Reactor",
				20 => "TAG:C&C",
			)
		);

		
		/* replaced with proper two C&Cs!
		//$this->addPrimarySystem(new CnC(6, 25, 0, 0)); 
		$this->addPrimarySystem(new ProtectedCnC(7, 50, 0, 0)); //instead of two 6x25 C&C, make it 1 7x50
		*/
		$cnc = new CnC(6, 25, 0, 0);
		$cnc->startArc = 0;
		$cnc->endArc = 360;
        $this->addPrimarySystem($cnc);
		$cnc = new SecondaryCnC(6, 25, 0, 0);//all-around by default
        $this->addPrimarySystem($cnc);
		
		$this->addPrimarySystem(new Scanner(6, 28, 4, 8));
		$this->addPrimarySystem(new Scanner(6, 28, 4, 8));
		$this->addPrimarySystem(new Reactor(6, 25, 0, 0));
		$this->addPrimarySystem(new PulsarMine(6, 0, 0, 0, 360));
		$this->addPrimarySystem(new PulsarMine(6, 0, 0, 0, 360));
		$this->addPrimarySystem(new EnergyMine(6, 5, 4, 0, 360));
		$this->addPrimarySystem(new EnergyMine(6, 5, 4, 0, 360));
		$this->addPrimarySystem(new EnergyMine(6, 5, 4, 0, 360));
		$this->addPrimarySystem(new EnergyMine(6, 5, 4, 0, 360));
		$this->addPrimarySystem(new EnergyMine(6, 5, 4, 0, 360));

		$this->addPrimarySystem(new Structure(6, 155));


		for ($i = 0; $i < sizeof($this->locations); $i++){

			$min = 270 + ($i*72);
			$max = 90 + ($i*72);
			
			/*some systems need pre-definition to have arcs set for TAGs!*/
			$struct = Structure::createAsOuter(5, 90,$min,$max);
			$cargoBay = new CargoBay(5, 36);
			$cargoBay->startArc = $min;
			$cargoBay->endArc = $max;
			$subReactor = new SubReactorUniversal(5, 35, 0, 0);
			$subReactor->startArc = $min;
			$subReactor->endArc = $max;
			$hangar = new Hangar(5, 7, 6);
			$hangar->startArc = $min;
			$hangar->endArc = $max;

			$systems = array(
				new MagGun(5, 9, 8, $min, $max),
				new HeavyPulse(5, 6, 4, $min, $max),
				new HeavyLaser(5, 8, 6, $min, $max),
				new IonTorpedo(5, 5, 4, $min, $max),
				new TwinArray(5, 6, 2, $min, $max),
				new TwinArray(5, 6, 2, $min, $max),
				new LightPulse(5, 4, 2, $min, $max),
				new LightPulse(5, 4, 2, $min, $max),
				/* replaced with arced systems - for TAG
				new CargoBay(5, 36),
				new SubReactorUniversal(5, 35, 0, 0),
				new Hangar(5, 7, 6),
				new Structure(5, 90)
				*/
				$cargoBay,
				$subReactor,
				$hangar,
				$struct
			);
			
			$loc = $this->locations[$i];

			$this->hitChart[$loc] = array(
				1 => "TAG:Mag Gun",
				2 => "TAG:Heavy Pulse Cannon",
				3 => "TAG:Heavy Laser",
				4 => "TAG:Ion Torpedo",
				5 => "TAG:Twin Array",
				6 => "TAG:Light Pulse Cannon",
				8 => "TAG:Cargo Bay",
				9 => "TAG:Sub Reactor",
				10 => "TAG:Hangar",
				18 => "TAG:Outer Structure",
				20 => "Primary",
			);

			foreach ($systems as $system){
				$this->addSystem($system, $loc);
			}
		}
    }
}
