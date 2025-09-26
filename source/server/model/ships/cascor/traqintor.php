<?php
class Traqintor extends SmallStarBaseFourSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 2500;
		$this->base = true;
		$this->smallBase = true;
		$this->faction = "Cascor Commonwealth";
		$this->phpclass = "Traqintor";
		$this->shipClass = "Traqintor Waystation";
		$this->imagePath = "img/ships/CascorTaqintorWaystation.png";
		$this->canvasSize = 200; 
		$this->fighters = array("normal"=>48); 
		$this->isd = 2219;

		$this->shipSizeClass = 3; 
		$this->Enormous = true;
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;

		$this->forwardDefense = 20;
		$this->sideDefense = 20;



		/*replaced by TAGed versions!
		$this->addFrontSystem(new Structure( 4, 120));
		$this->addAftSystem(new Structure( 4, 120));
		$this->addLeftSystem(new Structure( 4, 120));
		$this->addRightSystem(new Structure( 4, 120));
		*/
		$this->addPrimarySystem(new Structure( 5, 120));
		$this->addFrontSystem(Structure::createAsOuter(4, 120, 270,90));
		$this->addAftSystem(Structure::createAsOuter(4, 120, 90, 270));
		$this->addLeftSystem(Structure::createAsOuter(4, 120, 180, 360));
		$this->addRightSystem(Structure::createAsOuter(4, 120, 0, 180));
		
		$this->hitChart = array(			
			0=> array(
				11 => "Structure",
				13 => "Ion Torpedo",
				14 => "Hangar",
				16 => "Scanner",
				18 => "Reactor",
				20 => "TAG:C&C",
			),
			1=> array(
				2 => "TAG:Ion Torpedo",
				4 => "TAG:Ion Cannon",
				6 => "TAG:Dual Ion Bolter",
				8 => "TAG:Cargo Bay",
			    9 => "TAG:Sub Reactor",
			    10 => "TAG:Hangar",
				18 => "Outer Structure",
				20 => "Primary",
			),
			2=> array(
				2 => "TAG:Ion Torpedo",
				4 => "TAG:Ion Cannon",
				6 => "TAG:Dual Ion Bolter",
				8 => "TAG:Cargo Bay",
			    9 => "TAG:Sub Reactor",
			    10 => "TAG:Hangar",
				18 => "Outer Structure",
				20 => "Primary",
			),	
			3=> array(
				2 => "TAG:Ion Torpedo",
				4 => "TAG:Ion Cannon",
				6 => "TAG:Dual Ion Bolter",
				8 => "TAG:Cargo Bay",
			    9 => "TAG:Sub Reactor",
			    10 => "TAG:Hangar",
				18 => "Outer Structure",
				20 => "Primary",
			),
			4=> array(
				2 => "TAG:Ion Torpedo",
				4 => "TAG:Ion Cannon",
				6 => "TAG:Dual Ion Bolter",
				8 => "TAG:Cargo Bay",
			    9 => "TAG:Sub Reactor",
			    10 => "TAG:Hangar",
				18 => "Outer Structure",
				20 => "Primary",
			),
		);


		/* let's replace this with appropriate two C&Cs!
		$this->addPrimarySystem(new ProtectedCnC(6, 40, 0, 0)); //originally 2 systems with sructure 20, armor 5 each
		*/
		$cnc = new CnC(5, 20, 0, 0);
		$cnc->startArc = 0;
		$cnc->endArc = 360;
        $this->addPrimarySystem($cnc);
		$cnc = new SecondaryCnC(5, 20, 0, 0);//all-around by default
        $this->addPrimarySystem($cnc);
		
		$this->addPrimarySystem(new Reactor(5, 26, 0, 0));
		$this->addPrimarySystem(new Scanner(5, 18, 5, 7));
		$this->addPrimarySystem(new Scanner(5, 18, 5, 7));
		$this->addPrimarySystem(new Hangar(5, 4));
		$this->addPrimarySystem(new IonTorpedo(5, 5, 4, 0, 360));
		$this->addPrimarySystem(new IonTorpedo(5, 5, 4, 0, 360));
		
		
			/*Cargo Bays, Hangars and SubReactors need to have arc defined for TAG to work*/
			/*
		$this->addFrontSystem(new Hangar(4, 6));
		$this->addFrontSystem(new Hangar(5, 6));
		$this->addFrontSystem(new CargoBay(4, 25));
		$this->addFrontSystem(new SubReactorUniversal(5, 25, 0, 0)); //combining 2 original subreacotrs, from outer section (4/20) and inter-section (5/11)
		*/
			$cargoBay = new CargoBay(4, 25);
			$cargoBay->startArc = 270;
			$cargoBay->endArc = 90;
			$this->addFrontSystem($cargoBay);
			$subReactor = new SubReactorUniversal(5, 25, 0, 0);
			$subReactor->startArc = 270;
			$subReactor->endArc = 90;
			$this->addFrontSystem($subReactor);
			$hangar = new Hangar(4, 6);			
			$hangar->startArc = 270;
			$hangar->endArc = 90;
			$this->addFrontSystem($hangar);
			$hangar = new Hangar(5, 6);			
			$hangar->startArc = 270;
			$hangar->endArc = 90;
			$this->addFrontSystem($hangar);
		$this->addFrontSystem(new DualIonBolter(5, 4, 4, 270, 360));
		$this->addFrontSystem(new DualIonBolter(5, 4, 4, 0, 90));
		$this->addFrontSystem(new IonTorpedo(4, 5, 4, 300, 60));
		$this->addFrontSystem(new DualIonBolter(4, 4, 4, 300, 60));
		$this->addFrontSystem(new IonTorpedo(4, 5, 4, 300, 60));
		$this->addFrontSystem(new IonCannon(4, 6, 4, 300, 60));
		$this->addFrontSystem(new IonCannon(4, 6, 4, 300, 60));
		
		/*
		$this->addAftSystem(new Hangar(4, 6));
		$this->addAftSystem(new Hangar(5, 6));
		$this->addAftSystem(new CargoBay(4, 25));
		$this->addAftSystem(new SubReactorUniversal(5, 25, 0, 0)); //combining 2 original subreacotrs, from outer section (4/20) and inter-section (5/11)
		*/
			$cargoBay = new CargoBay(4, 25);
			$cargoBay->startArc = 90;
			$cargoBay->endArc = 270;
			$this->addAftSystem($cargoBay);
			$subReactor = new SubReactorUniversal(5, 25, 0, 0);
			$subReactor->startArc = 90;
			$subReactor->endArc = 270;
			$this->addAftSystem($subReactor);
			$hangar = new Hangar(4, 6);	
			$hangar->startArc = 90;
			$hangar->endArc = 270;
			$this->addAftSystem($hangar);
			$hangar = new Hangar(5, 6);	
			$hangar->startArc = 90;
			$hangar->endArc = 270;
			$this->addAftSystem($hangar);
		$this->addAftSystem(new DualIonBolter(5, 4, 4, 90, 180));
		$this->addAftSystem(new DualIonBolter(5, 4, 4, 180, 270));
		$this->addAftSystem(new IonTorpedo(4, 5, 4, 120, 240));
		$this->addAftSystem(new DualIonBolter(4, 4, 4, 120, 240));
		$this->addAftSystem(new IonTorpedo(4, 5, 4, 120, 240));
		$this->addAftSystem(new IonCannon(4, 6, 4, 120, 240));
		$this->addAftSystem(new IonCannon(4, 6, 4, 120, 240));
		
		/*
		$this->addRightSystem(new Hangar(4, 6));
		$this->addRightSystem(new Hangar(5, 6));
		$this->addRightSystem(new CargoBay(4, 25));
		$this->addRightSystem(new SubReactorUniversal(5, 25, 0, 0)); //combining 2 original subreacotrs, from outer section (4/20) and inter-section (5/11)
		*/		
			$cargoBay = new CargoBay(4, 25);
			$cargoBay->startArc = 0;
			$cargoBay->endArc = 180;
			$this->addRightSystem($cargoBay);
			$subReactor = new SubReactorUniversal(5, 25, 0, 0);
			$subReactor->startArc = 0;
			$subReactor->endArc = 180;
			$this->addRightSystem($subReactor);
			$hangar = new Hangar(4, 6);	
			$hangar->startArc = 0;
			$hangar->endArc = 180;
			$this->addRightSystem($hangar);
			$hangar = new Hangar(5, 6);	
			$hangar->startArc = 0;
			$hangar->endArc = 180;
			$this->addRightSystem($hangar);
		$this->addRightSystem(new DualIonBolter(5, 4, 4, 0, 90));
		$this->addRightSystem(new DualIonBolter(5, 4, 4, 90, 180));
		$this->addRightSystem(new IonTorpedo(4, 5, 4, 30, 150));
		$this->addRightSystem(new DualIonBolter(4, 4, 4, 30, 150));
		$this->addRightSystem(new IonTorpedo(4, 5, 4, 30, 150));
		$this->addRightSystem(new IonCannon(4, 6, 4, 30, 150));
		$this->addRightSystem(new IonCannon(4, 6, 4, 30, 150));
		
		/*
		$this->addLeftSystem(new Hangar(4, 6));
		$this->addLeftSystem(new Hangar(5, 6));
		$this->addLeftSystem(new CargoBay(4, 25));
		$this->addLeftSystem(new SubReactorUniversal(5, 25, 0, 0)); //combining 2 original subreacotrs, from outer section (4/20) and inter-section (5/11)
				*/		
			$cargoBay = new CargoBay(4, 25);
			$cargoBay->startArc = 180;
			$cargoBay->endArc = 360;
			$this->addLeftSystem($cargoBay);
			$subReactor = new SubReactorUniversal(5, 25, 0, 0);
			$subReactor->startArc = 180;
			$subReactor->endArc = 360;
			$this->addLeftSystem($subReactor);
			$hangar = new Hangar(4, 6);	
			$hangar->startArc = 180;
			$hangar->endArc = 360;
			$this->addLeftSystem($hangar);
			$hangar = new Hangar(5, 6);
			$hangar->startArc = 180;
			$hangar->endArc = 360;
			$this->addLeftSystem($hangar);
		$this->addLeftSystem(new DualIonBolter(5, 4, 4, 270, 360));
		$this->addLeftSystem(new DualIonBolter(5, 4, 4, 180, 270));
		$this->addLeftSystem(new IonTorpedo(4, 5, 4, 210, 330));
		$this->addLeftSystem(new DualIonBolter(4, 4, 4, 210, 330));
		$this->addLeftSystem(new IonTorpedo(4, 5, 4, 210, 330));
		$this->addLeftSystem(new IonCannon(4, 6, 4, 210, 330));
		$this->addLeftSystem(new IonCannon(4, 6, 4, 210, 330));	
		}
    }
?>
