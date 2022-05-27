<?php
class ZShadowXonn extends VreeCapital{
  /*custom ship, put into FV as a prize for tournament winner (LordBolton/MrMordensGhost) */

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 2900;
		$this->faction = "Vree";
		$this->phpclass = "ZShadowXonn";
		$this->shipClass = "Shadow Xonn Dreadnought";
		$this->isd = 'not known';
        $this->limited = 10; //Restricted Deployment
	    $this->unofficial = true;
        $this->enhancementOptionsDisabled[] = 'SHAD_DIFF'; //no diffuser upgrades for Young ships - they don't have know how to tamper with Shadow systems to that extent!

		$this->shipSizeClass = 3;
		$this->iniativebonus = 0;
		
        $this->turncost = 1.5;
        $this->turndelaycost = 1;
        $this->accelcost = 5;
        $this->rollcost = 6;
        $this->pivotcost = 0;	
        $this->gravitic = true;        	

		$this->forwardDefense = 16;
		$this->sideDefense = 16;

		$this->imagePath = "img/ships/XonnShadow.png";
		$this->canvasSize = 200;

		$this->addPrimarySystem(new Reactor(6, 25, 0, 0));
		$this->addPrimarySystem(new Hangar(6, 3));
		$this->addPrimarySystem(new CnC(7, 16, 0, 0));
		$scanner = new Scanner(6, 18, 9, 10);   
		$scanner->markImproved();
        $this->addPrimarySystem($scanner);
    $this->addPrimarySystem(new Engine(6, 18, 0, 10, 3));
		$this->addPrimarySystem(new JumpEngine(7, 16, 6, 24));
    
		$this->addPrimarySystem(new AntimatterShredder(4, 0, 0, 0, 360));		         			
		$this->addPrimarySystem(new AntimatterShredder(4, 0, 0, 0, 360));
    $this->addPrimarySystem(new MolecularSlicerBeamL(4, 0, 0, 0, 360));
    $this->addPrimarySystem(new MolecularSlicerBeamL(4, 0, 0, 0, 360));
    $this->addPrimarySystem(new MultiphasedCutter(4, 0, 0, 0, 360));  
    						

     
        $diffuser = new EnergyDiffuser(4, 9, 2, 300, 60);//($armour, $maxhealth, $dissipation, $startArc, $endArc)
          $tendril=new DiffuserTendril(10,'L');//absorbtion capacity,side
          $diffuser->addTendril($tendril);
          $this->addFrontSystem($tendril);
          $tendril=new DiffuserTendril(10,'R');//absorbtion capacity,side
          $diffuser->addTendril($tendril);
          $this->addFrontSystem($tendril);
        $this->addFrontSystem($diffuser);		
        $this->addFrontSystem(new GraviticThruster(5, 20, 0, 10, 1));  
        $this->addFrontSystem(new AntiprotonGun(3, 0, 0, 300, 60));
		 
    
        $diffuser = new EnergyDiffuser(4, 9, 2, 120, 240);//($armour, $maxhealth, $dissipation, $startArc, $endArc)
          $tendril=new DiffuserTendril(10,'L');//absorbtion capacity,side
          $diffuser->addTendril($tendril);
          $this->addAftSystem($tendril);
          $tendril=new DiffuserTendril(10,'R');//absorbtion capacity,side
          $diffuser->addTendril($tendril);
          $this->addAftSystem($tendril);
        $this->addAftSystem($diffuser);		
        $this->addAftSystem(new GraviticThruster(5, 20, 0, 10, 2));   
        $this->addAftSystem(new AntiprotonGun(3, 0, 0, 120, 240));     
    
    
        $diffuser = new EnergyDiffuser(4, 9, 2, 240, 360);//($armour, $maxhealth, $dissipation, $startArc, $endArc)
          $tendril=new DiffuserTendril(10,'L');//absorbtion capacity,side
          $diffuser->addTendril($tendril);
          $this->addLeftFrontSystem($tendril);
          $tendril=new DiffuserTendril(10,'L');//absorbtion capacity,side
          $diffuser->addTendril($tendril);
          $this->addLeftFrontSystem($tendril);
        $this->addLeftFrontSystem($diffuser);	
		$this->addLeftFrontSystem(new AntiprotonGun(3, 0, 0, 240, 360));	
				
				
        $diffuser = new EnergyDiffuser(4, 9, 2, 180, 300);//($armour, $maxhealth, $dissipation, $startArc, $endArc)
          $tendril=new DiffuserTendril(10,'L');//absorbtion capacity,side
          $diffuser->addTendril($tendril);
          $this->addLeftAftSystem($tendril);
          $tendril=new DiffuserTendril(10,'L');//absorbtion capacity,side
          $diffuser->addTendril($tendril);
          $this->addLeftAftSystem($tendril);
        $this->addLeftAftSystem($diffuser);	
		$this->addLeftAftSystem(new GraviticThruster(5, 20, 0, 10, 3));
		$this->addLeftAftSystem(new AntiprotonGun(3, 0, 0, 180, 300));
		
    
        $diffuser = new EnergyDiffuser(4, 9, 2, 0, 120);//($armour, $maxhealth, $dissipation, $startArc, $endArc)
          $tendril=new DiffuserTendril(10,'R');//absorbtion capacity,side
          $diffuser->addTendril($tendril);
          $this->addRightFrontSystem($tendril);
          $tendril=new DiffuserTendril(10,'R');//absorbtion capacity,side
          $diffuser->addTendril($tendril);
          $this->addRightFrontSystem($tendril);
        $this->addRightFrontSystem($diffuser);	
		$this->addRightFrontSystem(new AntiprotonGun(3, 0, 0, 0, 120));
				
				
        $diffuser = new EnergyDiffuser(4, 9, 2, 60, 180);//($armour, $maxhealth, $dissipation, $startArc, $endArc)
          $tendril=new DiffuserTendril(10,'R');//absorbtion capacity,side
          $diffuser->addTendril($tendril);
          $this->addRightAftSystem($tendril);
          $tendril=new DiffuserTendril(10,'R');//absorbtion capacity,side
          $diffuser->addTendril($tendril);
          $this->addRightAftSystem($tendril);
        $this->addRightAftSystem($diffuser);	
		$this->addRightAftSystem(new GraviticThruster(5, 20, 0, 10, 4));	
		$this->addRightAftSystem(new AntiprotonGun(3, 0, 0, 60, 180));
		
       
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 36, true));
        $this->addAftSystem(new Structure( 5, 36, true));
        $this->addLeftFrontSystem(new Structure( 5, 36, true));
        $this->addLeftAftSystem(new Structure( 5, 36, true));
        $this->addRightFrontSystem(new Structure( 5, 36, true));
        $this->addRightAftSystem(new Structure( 5, 36, true));      
        $this->addPrimarySystem(new Structure( 6, 60));
	    
	//d20 hit chart
        $this->hitChart = array(

            0=> array(
                    9 => "Structure",
                    10 => "Jump Engine",
                    13 => "Scanner",
                    15 => "Engine",
                    16 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
           		 ),
            1=> array(
                    4 => "Thruster",
                    6 => "Antiproton Gun",
                    7 => "0:Antimatter Shredder",                    
                    8 => "0:Light Slicer Beam",                  
                    9 => "0:Multiphased Cutter",
                    11 => "Energy Diffuser",
                    17 => "Structure",
                    20 => "Primary",
           		 ),
            2=> array(
                    4 => "Thruster",
                    6 => "Antiproton Gun",
                    7 => "0:Antimatter Shredder",                    
                    8 => "0:Light Slicer Beam",                  
                    9 => "0:Multiphased Cutter",
                    11 => "Energy Diffuser",
                    17 => "Structure",
                    20 => "Primary",
           		 ),
            31=> array(
                    4 => "32:Thruster",
                    6 => "Antiproton Gun",
                    7 => "0:Antimatter Shredder",                    
                    8 => "0:Light Slicer Beam",                  
                    9 => "0:Multiphased Cutter",
                    11 => "Energy Diffuser",
                    17 => "Structure",
                    20 => "Primary",
           		 ),
            32=> array(
                    4 => "Thruster",
                    6 => "Antiproton Gun",
                    7 => "0:Antimatter Shredder",                    
                    8 => "0:Light Slicer Beam",                  
                    9 => "0:Multiphased Cutter",
                    11 => "Energy Diffuser",
                    17 => "Structure",
                    20 => "Primary",
           		 ),
            41=> array(
                    4 => "42:Thruster",
                    6 => "Antiproton Gun",
                    7 => "0:Antimatter Shredder",                    
                    8 => "0:Light Slicer Beam",                  
                    9 => "0:Multiphased Cutter",
                    11 => "Energy Diffuser",
                    17 => "Structure",
                    20 => "Primary",
           		 ),
       		42=> array(
                    4 => "Thruster",
                    6 => "Antiproton Gun",
                    7 => "0:Antimatter Shredder",                    
                    8 => "0:Light Slicer Beam",                  
                    9 => "0:Multiphased Cutter",
                    11 => "Energy Diffuser",
                    17 => "Structure",
                    20 => "Primary",
           		 ),
           	);
       		
		}
	}
		
?>		
