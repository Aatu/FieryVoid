<?php
class shadowAvioki extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 1000;
	$this->faction = "Custom Ships";
        $this->phpclass = "shadowAvioki";
        $this->imagePath = "img/ships/shadowAvioki.png";
        $this->shipClass = "Shadow Avioki Heavy Cruiser";
        $this->shipSizeClass = 3;
		
		$this->notes = 'Ak-Habil Conglomerate';//Corporation producing the design
		$this->isd = 2262;

		$this->unofficial = true; 

		$this->enhancementOptionsDisabled[] = 'SHAD_DIFF'; //no diffuser upgrades for EA ships - they don't have know how to tabper with Shadow systems to that extent!
		$this->advancedArmor = true;   
        
        $this->forwardDefense = 14;
        $this->sideDefense = 18;
        
        $this->turncost = 1;
        $this->turndelaycost = 0.5;
        $this->accelcost = 4;
        $this->rollcost = 2;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;
        
        $this->gravitic = true;

        $this->addPrimarySystem(new Reactor(6, 22, 0, 0));
        $this->addPrimarySystem(new CnC(8, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 13, 8, 9));
        $this->addPrimarySystem(new Engine(6, 16, 0, 15, 4));
        $this->addPrimarySystem(new JumpEngine(5, 12, 4, 28));
		$this->addPrimarySystem(new Hangar(5, 2));
   
        $this->addFrontSystem(new MultiphasedCutterL(3, 4, 3, 240, 60));
        $this->addFrontSystem(new MultiphasedCutterL(3, 4, 3, 300, 120));
        $this->addFrontSystem(new GraviticThruster(5, 10, 0, 4, 1));
        $this->addFrontSystem(new GraviticThruster(5, 10, 0, 4, 1));

        $this->addAftSystem(new MultiphasedCutterL(3, 4, 3, 120, 300));
        $this->addAftSystem(new MultiphasedCutterL(3, 4, 3, 60, 240));
        $this->addAftSystem(new GraviticThruster(5, 15, 0, 8, 2));
        $this->addAftSystem(new GraviticThruster(5, 15, 0, 8, 2));

		$diffuserPort = new EnergyDiffuser(4, 9, 3, 180, 360);//($armour, $maxhealth, $dissipation, $startArc, $endArc)
		$tendril=new DiffuserTendril(10,'L');//absorbtion capacity,side
		$diffuserPort->addTendril($tendril);
		$this->addLeftSystem($tendril);
		$tendril=new DiffuserTendril(10,'L');//absorbtion capacity,side
		$diffuserPort->addTendril($tendril);
		$this->addLeftSystem($tendril);
        $this->addLeftSystem($diffuserPort);		

        $this->addLeftSystem(new PhasingPulseCannonH(5, 0, 0, 240, 360));
        $this->addLeftSystem(new PhasingPulseCannonH(5, 0, 0, 240, 360));
        $this->addLeftSystem(new GraviticThruster(5, 15, 0, 6, 3));

		$diffuserStbd = new EnergyDiffuser(4, 9, 3, 0, 180);//($armour, $maxhealth, $dissipation, $startArc, $endArc)
		$tendril=new DiffuserTendril(10,'R');//absorbtion capacity,side
		$diffuserStbd->addTendril($tendril);
		$this->addRightSystem($tendril);
		$tendril=new DiffuserTendril(10,'R');//absorbtion capacity,side
		$diffuserStbd->addTendril($tendril);
		$this->addRightSystem($tendril);
        $this->addRightSystem($diffuserStbd);

        $this->addRightSystem(new PhasingPulseCannonH(5, 0, 0, 0, 120));
        $this->addRightSystem(new PhasingPulseCannonH(5, 0, 0, 0, 120));
        $this->addRightSystem(new GraviticThruster(5, 15, 0, 6, 4));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(6, 36));
        $this->addAftSystem(new Structure(6, 36));
        $this->addLeftSystem(new Structure(6, 48));
        $this->addRightSystem(new Structure(6, 48));
        $this->addPrimarySystem(new Structure(6, 44));
		
		$this->hitChart = array(
			0=> array(
					8 => "Structure",
					10 => "Jump Engine",
					12 => "Scanner",
					15 => "Engine",
					16 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					7 => "Light Multiphased Cutter",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					9 => "Light Multiphased Cutter",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					4 => "Thruster",
					8 => "Heavy Phasing Pulse Cannon",
					10 => "Energy Diffuser",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					4 => "Thruster",
					8 => "Heavy Phasing Pulse Cannon",
					10 => "Energy Diffuser",
					18 => "Structure",
					20 => "Primary",
			),
		);		
    }
}