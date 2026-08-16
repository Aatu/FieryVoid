<?php
class TraqintorNew extends SmallStarBaseFourSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 2500;
		$this->base = true;
		$this->smallBase = true;
		$this->faction = "Cascor Commonwealth";
		$this->phpclass = "TraqintorNew";
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
		

		$cnc = new CnC(5, 20, 0, 0);
		$cnc->startArc = 0;
		$cnc->endArc = 360;
        $this->addPrimarySystem($cnc);
		$cnc = new SecondaryCnC(5, 20, 0, 0);//all-around by default
        $this->addPrimarySystem($cnc);
		
		$this->addPrimarySystem(new Reactor(5, 26, 0, 0));
		$this->addPrimarySystem(new Scanner(5, 18, 5, 7));
		$this->addPrimarySystem(new Scanner(5, 18, 5, 7));
		$this->addPrimarySystem(new Hangar(5, 4, 1));
		$this->addPrimarySystem(new IonTorpedo(5, 5, 4, 0, 360));
		$this->addPrimarySystem(new IonTorpedo(5, 5, 4, 0, 360));

		//Forward	
		$this->addFrontSystem(new IonTorpedo(4, 5, 4, 300, 60));
		$this->addFrontSystem(new DualIonBolter(4, 4, 4, 300, 60));
		$this->addFrontSystem(new IonTorpedo(4, 5, 4, 300, 60));
		$this->addFrontSystem(new IonCannon(4, 6, 4, 300, 60));
		$this->addFrontSystem(new IonCannon(4, 6, 4, 300, 60));		
			/*Cargo Bays, Hangars and SubReactors need to have arc defined for TAG to work*/
			$cargoBay = new CargoBay(4, 25);
			$cargoBay->startArc = 300;
			$cargoBay->endArc = 60;
			$this->addFrontSystem($cargoBay);
			$subReactor = new SubReactorUniversal(4, 20, 0, 0);
			$subReactor->startArc = 300;
			$subReactor->endArc = 60;
			$this->addFrontSystem($subReactor);
			$hangar = new Hangar(4, 6, 6);			
			$hangar->startArc = 300;
			$hangar->endArc = 60;
			$this->addFrontSystem($hangar);

		
		//Aft
		$this->addAftSystem(new IonTorpedo(4, 5, 4, 120, 240));
		$this->addAftSystem(new DualIonBolter(4, 4, 4, 120, 240));
		$this->addAftSystem(new IonTorpedo(4, 5, 4, 120, 240));
		$this->addAftSystem(new IonCannon(4, 6, 4, 120, 240));
		$this->addAftSystem(new IonCannon(4, 6, 4, 120, 240));
			$cargoBay = new CargoBay(4, 25);
			$cargoBay->startArc = 120;
			$cargoBay->endArc = 240;
			$this->addAftSystem($cargoBay);
			$subReactor = new SubReactorUniversal(4, 25, 0, 0);
			$subReactor->startArc = 120;
			$subReactor->endArc = 240;
			$this->addAftSystem($subReactor);
			$hangar = new Hangar(5, 6, 6);	
			$hangar->startArc = 120;
			$hangar->endArc = 240;
			$this->addAftSystem($hangar);		

		//Fwd Port
			$hangar = new Hangar(4, 6, 6);	
			$hangar->startArc = 270;
			$hangar->endArc = 360;
			$hangar->overkillArcStructures = array(1, 3); //overkill spills to whichever Port quarter is in arc					
			$hangar->setStructureHome(array(1, 3));				
			$this->addLeftFrontSystem($hangar);
			$subReactor = new SubReactorUniversal(5, 14, 0, 0);
			$subReactor->startArc = 270;
			$subReactor->endArc = 360;
			$subReactor->overkillArcStructures = array(1, 3); //overkill spills to whichever Port quarter is in arc					
			$subReactor->setStructureHome(array(1, 3));				
			$this->addLeftFrontSystem($subReactor);	
			$bolter1 = new DualIonBolter(5, 4, 4, 270, 360);
			$bolter1->overkillArcStructures = array(1, 3); //overkill spills to whichever Port quarter is in arc					
			$bolter1->setStructureHome(array(1, 3));	
			$this->addLeftFrontSystem($bolter1);
			$bolter2 = new DualIonBolter(5, 4, 4, 270, 360);
			$bolter2->overkillArcStructures = array(1, 3); //overkill spills to whichever Port quarter is in arc					
			$bolter2->setStructureHome(array(1, 3));	
			$this->addLeftFrontSystem($bolter2);												


		//Port Section
		$this->addLeftSystem(new IonTorpedo(4, 5, 4, 210, 330));
		$this->addLeftSystem(new DualIonBolter(4, 4, 4, 210, 330));
		$this->addLeftSystem(new IonTorpedo(4, 5, 4, 210, 330));
		$this->addLeftSystem(new IonCannon(4, 6, 4, 210, 330));
		$this->addLeftSystem(new IonCannon(4, 6, 4, 210, 330));
			$cargoBay = new CargoBay(4, 25);
			$cargoBay->startArc = 210;
			$cargoBay->endArc = 330;
			$this->addLeftSystem($cargoBay);
			$subReactor = new SubReactorUniversal(4, 20, 0, 0);
			$subReactor->startArc = 210;
			$subReactor->endArc = 330;
			$this->addLeftSystem($subReactor);
			$hangar = new Hangar(5, 6, 6);
			$hangar->startArc = 210;
			$hangar->endArc = 330;
			$this->addLeftSystem($hangar);			


		//Aft Port
			$hangar = new Hangar(4, 6, 6);	
			$hangar->startArc = 180;
			$hangar->endArc = 270;
			$hangar->overkillArcStructures = array(2, 3); //overkill spills to whichever Port quarter is in arc					
			$hangar->setStructureHome(array(2, 3));				
			$this->addLeftAftSystem($hangar);
			$subReactor = new SubReactorUniversal(5, 14, 0, 0);
			$subReactor->startArc = 180;
			$subReactor->endArc = 270;
			$subReactor->overkillArcStructures = array(2, 3); //overkill spills to whichever Port quarter is in arc					
			$subReactor->setStructureHome(array(2, 3));				
			$this->addLeftAftSystem($subReactor);	
			$bolter1 = new DualIonBolter(5, 4, 4, 180, 270);
			$bolter1->overkillArcStructures = array(2, 3); //overkill spills to whichever Port quarter is in arc					
			$bolter1->setStructureHome(array(2, 3));	
			$this->addLeftAftSystem($bolter1);
			$bolter2 = new DualIonBolter(5, 4, 4, 180, 270);
			$bolter2->overkillArcStructures = array(2, 3); //overkill spills to whichever Port quarter is in arc					
			$bolter2->setStructureHome(array(2, 3));	
			$this->addLeftAftSystem($bolter2);	


		//Fwd Stbd
			$hangar = new Hangar(4, 6, 6);	
			$hangar->startArc = 0;
			$hangar->endArc = 90;
			$hangar->overkillArcStructures = array(1, 4); //overkill spills to whichever Port quarter is in arc					
			$hangar->setStructureHome(array(1, 4));				
			$this->addRightFrontSystem($hangar);
			$subReactor = new SubReactorUniversal(5, 14, 0, 0);
			$subReactor->startArc = 0;
			$subReactor->endArc = 90;
			$subReactor->overkillArcStructures = array(1, 4); //overkill spills to whichever Port quarter is in arc					
			$subReactor->setStructureHome(array(1, 4));				
			$this->addRightFrontSystem($subReactor);	
			$bolter1 = new DualIonBolter(5, 4, 4, 0, 90);
			$bolter1->overkillArcStructures = array(1, 4); //overkill spills to whichever Port quarter is in arc					
			$bolter1->setStructureHome(array(1, 4));	
			$this->addRightFrontSystem($bolter1);
			$bolter2 = new DualIonBolter(5, 4, 4, 0, 90);
			$bolter2->overkillArcStructures = array(1, 4); //overkill spills to whichever Port quarter is in arc					
			$bolter2->setStructureHome(array(1, 4));	
			$this->addRightFrontSystem($bolter2);	


		//Stbd Section
		$this->addRightSystem(new IonTorpedo(4, 5, 4, 30, 150));
		$this->addRightSystem(new DualIonBolter(4, 4, 4, 30, 150));
		$this->addRightSystem(new IonTorpedo(4, 5, 4, 30, 150));
		$this->addRightSystem(new IonCannon(4, 6, 4, 30, 150));
		$this->addRightSystem(new IonCannon(4, 6, 4, 30, 150));
			$cargoBay = new CargoBay(4, 25);
			$cargoBay->startArc = 0;
			$cargoBay->endArc = 180;
			$this->addRightSystem($cargoBay);
			$subReactor = new SubReactorUniversal(4, 20, 0, 0);
			$subReactor->startArc = 0;
			$subReactor->endArc = 180;
			$this->addRightSystem($subReactor);
			$hangar = new Hangar(5, 6);	
			$hangar->startArc = 0;
			$hangar->endArc = 180;
			$this->addRightSystem($hangar);		
	

		//Fwd Stbd
			$hangar = new Hangar(4, 6, 6);	
			$hangar->startArc = 90;
			$hangar->endArc = 180;
			$hangar->overkillArcStructures = array(2, 4); //overkill spills to whichever Port quarter is in arc					
			$hangar->setStructureHome(array(2, 4));				
			$this->addRightAftSystem($hangar);
			$subReactor = new SubReactorUniversal(5, 14, 0, 0);
			$subReactor->startArc = 90;
			$subReactor->endArc = 180;
			$subReactor->overkillArcStructures = array(2, 4); //overkill spills to whichever Port quarter is in arc					
			$subReactor->setStructureHome(array(2, 4));				
			$this->addRightAftSystem($subReactor);	
			$bolter1 = new DualIonBolter(5, 4, 4, 90, 180);
			$bolter1->overkillArcStructures = array(2, 4); //overkill spills to whichever Port quarter is in arc					
			$bolter1->setStructureHome(array(2, 4));	
			$this->addRightAftSystem($bolter1);
			$bolter2 = new DualIonBolter(5, 4, 4, 90, 180);
			$bolter2->overkillArcStructures = array(2, 4); //overksill spills to whichever Port quarter is in arc					
			$bolter2->setStructureHome(array(2, 4));	
			$this->addRightAftSystem($bolter2);			


		$this->addPrimarySystem(new Structure( 5, 120));
		$this->addFrontSystem(Structure::createAsOuter(4, 120, 300, 60));
		$this->addAftSystem(Structure::createAsOuter(4, 120, 120, 240));
		$this->addLeftSystem(Structure::createAsOuter(4, 120, 210, 330));
		$this->addRightSystem(Structure::createAsOuter(4, 120, 30, 150));


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
		
		
		}
    }
?>
