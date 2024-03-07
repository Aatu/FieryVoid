<?php
class TrekSulibanHelix extends VreeCapital{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

        $this->pointCost = 2000;
        $this->faction = "ZTrek Playtest Other Factions";
        $this->phpclass = "TrekSulibanHelix";
        $this->imagePath = "img/ships/StarTrek/SulibanHelix.png";
        $this->shipClass = "Suliban Helix";

	$this->unofficial = true;
	$this->isd = 2125;
        $this->fighters = array("normal"=>72);	

		$this->shipSizeClass = 3;
		$this->iniativebonus = -20;
		
        $this->turncost = 2;
        $this->turndelaycost = 2;
        $this->accelcost = 6;
        $this->rollcost = 999;
        $this->pivotcost = 0;	
        $this->gravitic = true;        	

		$this->forwardDefense = 19;
		$this->sideDefense = 19;

		$this->addPrimarySystem(new Reactor(4, 40, 0, 0));
		$this->addPrimarySystem(new CnC(5, 16, 0, 0));
		$this->addPrimarySystem(new Scanner(5, 12, 6, 6));
		$impulseDrive = new TrekImpulseDrive(4,30,0,1,8); //Impulse Drive is an engine in its own right, in addition to serving as hub for Nacelle output: $armour, $maxhealth, $powerReq, $output, $boostEfficiency
		$this->addPrimarySystem(new CargoBay(2, 40));


		$this->addFrontSystem(new GraviticThruster(4, 14, 0, 6, 1));   
		$this->addFrontSystem(new CargoBay(3, 15));
		$this->addFrontSystem(new AntimatterTorpedo(2, 0, 0, 300, 60));		
		$cutter = new ParticleCutter(4, 8, 3, 300, 60);
		$cutter->addTag("Weapon");
        $this->addFrontSystem($cutter);		
	$lightgun = new SWMediumLaser(2, 270, 90, 4);
	$lightgun->addTag("Weapon");
	$lightgun->displayName = "Defense Guns";
	$this->addFrontSystem($lightgun);
	$lightgun = new SWMediumLaser(2, 270, 90, 4);
	$lightgun->addTag("Weapon");
	$lightgun->displayName = "Defense Guns";
	$this->addFrontSystem($lightgun);
		$projection = new TrekShieldProjection(0, 20, 6, 270, 90, 'F');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projector = new TrekShieldProjector(1, 8, 2, 4, 270, 90, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addFrontSystem($projector);
			$projector = new TrekShieldProjector(1, 8, 2, 4, 270, 90, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addFrontSystem($projector);
		$this->addFrontSystem($projection);

		$this->addAftSystem(new GraviticThruster(4, 14, 0, 6, 2)); 
		$this->addAftSystem(new CargoBay(3, 15));
        $this->addAftSystem(new AntimatterTorpedo(2, 0, 0, 120, 240));
		$cutter = new ParticleCutter(4, 8, 3, 120, 240);
		$cutter->addTag("Weapon");
        $this->addAftSystem($cutter);
	$lightgun = new SWMediumLaser(2, 90, 270, 4);
	$lightgun->addTag("Weapon");
	$lightgun->displayName = "Defense Guns";
	$this->addAftSystem($lightgun);  
	$lightgun = new SWMediumLaser(2, 90, 270, 4);
	$lightgun->addTag("Weapon");
	$lightgun->displayName = "Defense Guns";
	$this->addAftSystem($lightgun);  
		$projection = new TrekShieldProjection(0, 18, 6, 90, 270, 'A');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projector = new TrekShieldProjector(1, 8, 2, 4, 90, 270, 'A'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addAftSystem($projector);
			$projector = new TrekShieldProjector(1, 8, 2, 4, 90, 270, 'A'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addAftSystem($projector);
		$this->addAftSystem($projection);      
     

		$this->addLeftFrontSystem(new CargoBay(3, 15));
        $this->addLeftFrontSystem(new AntimatterTorpedo(2, 0, 0, 240, 360));
		$cutter = new ParticleCutter(4, 8, 3, 240, 360);
		$cutter->addTag("Weapon");
        $this->addLeftFrontSystem($cutter);
	$lightgun = new SWMediumLaser(2, 210, 30, 4);
	$lightgun->addTag("Weapon");
	$lightgun->displayName = "Defense Guns";
	$this->addLeftFrontSystem($lightgun);
	$lightgun = new SWMediumLaser(2, 210, 30, 4);
	$lightgun->addTag("Weapon");
	$lightgun->displayName = "Defense Guns";
	$this->addLeftFrontSystem($lightgun);
		$projection = new TrekShieldProjection(0, 20, 6, 270, 90, 'L');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projector = new TrekShieldProjector(1, 8, 2, 4, 270, 90, 'L'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addLeftFrontSystem($projector);
			$projector = new TrekShieldProjector(1, 8, 2, 4, 270, 90, 'L'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addLeftFrontSystem($projector);
		$this->addLeftFrontSystem($projection);		
				
		$this->addLeftAftSystem(new GraviticThruster(4, 14, 0, 6, 3));
		$this->addLeftAftSystem(new CargoBay(3, 15));	
        $this->addLeftAftSystem(new AntimatterTorpedo(2, 0, 0, 180, 300));
		$cutter = new ParticleCutter(4, 8, 3, 180, 300);
		$cutter->addTag("Weapon");
        $this->addLeftAftSystem($cutter);
	$lightgun = new SWMediumLaser(2, 150, 330, 4);
	$lightgun->addTag("Weapon");
	$lightgun->displayName = "Defense Guns";
	$this->addLeftAftSystem($lightgun);
	$lightgun = new SWMediumLaser(2, 150, 330, 4);
	$lightgun->addTag("Weapon");
	$lightgun->displayName = "Defense Guns";
	$this->addLeftAftSystem($lightgun);
		$warpNacelle = new TrekWarpDrive(3, 24, 0, 3); //armor, structure, power usage, impulse output
		$impulseDrive->addThruster($warpNacelle);
		$this->addLeftAftSystem($warpNacelle);	
		
		$this->addRightFrontSystem(new CargoBay(3, 15));
        $this->addRightFrontSystem(new AntimatterTorpedo(2, 0, 0, 0, 120));
		$cutter = new ParticleCutter(4, 8, 3, 0, 120);
		$cutter->addTag("Weapon");
        $this->addRightFrontSystem($cutter);
	$lightgun = new SWMediumLaser(2, 330, 150, 4);
	$lightgun->displayName = "Defense Guns";
	$lightgun->addTag("Weapon");
	$this->addRightFrontSystem($lightgun);
	$lightgun = new SWMediumLaser(2, 330, 150, 4);
	$lightgun->addTag("Weapon");
	$lightgun->displayName = "Defense Guns";
	$this->addRightFrontSystem($lightgun);
		$projection = new TrekShieldProjection(0, 20, 6, 270, 90, 'R');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projector = new TrekShieldProjector(1, 8, 2, 4, 270, 90, 'R'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addRightFrontSystem($projector);
			$projector = new TrekShieldProjector(1, 8, 2, 4, 270, 90, 'R'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addRightFrontSystem($projector);
		$this->addRightFrontSystem($projection);					
	
		$this->addRightAftSystem(new GraviticThruster(4, 14, 0, 6, 4));	
		$this->addRightAftSystem(new CargoBay(3, 15));
        $this->addRightAftSystem(new AntimatterTorpedo(2, 0, 0, 60, 180));
		$cutter = new ParticleCutter(4, 8, 3, 60, 180);
		$cutter->addTag("Weapon");
        $this->addRightAftSystem($cutter);
	$lightgun = new SWMediumLaser(2, 30, 210, 4);
	$lightgun->addTag("Weapon");
	$lightgun->displayName = "Defense Guns";
	$this->addRightAftSystem($lightgun);
	$lightgun = new SWMediumLaser(2, 30, 210, 4);
	$lightgun->addTag("Weapon");
	$lightgun->displayName = "Defense Guns";
	$this->addRightAftSystem($lightgun);
		$warpNacelle = new TrekWarpDrive(3, 24, 0, 3); //armor, structure, power usage, impulse output
		$impulseDrive->addThruster($warpNacelle);
		$this->addLeftAftSystem($warpNacelle);
       
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
		$structArmor = 4;
		$structHP = 45;
		
		$struct = new Structure( $structArmor, $structHP, true);
		$struct->addTag("Outer Structure");
		$struct->startArc = 300;
		$struct->endArc = 60;
        $this->addFrontSystem($struct);
		
		$struct = new Structure( $structArmor, $structHP, true);
		$struct->addTag("Outer Structure");
		$struct->startArc = 120;
		$struct->endArc = 240;
        $this->addAftSystem($struct);
		
		$struct = new Structure( $structArmor, $structHP, true);
		$struct->addTag("Outer Structure");
		$struct->startArc = 240;
		$struct->endArc = 0;
        $this->addLeftFrontSystem($struct);
		
		$struct = new Structure( $structArmor, $structHP, true);
		$struct->addTag("Outer Structure");
		$struct->startArc = 180;
		$struct->endArc = 300;
        $this->addLeftAftSystem($struct);
		
		$struct = new Structure( $structArmor, $structHP, true);
		$struct->addTag("Outer Structure");
		$struct->startArc = 0;
		$struct->endArc = 120;
        $this->addRightFrontSystem($struct);
		
		$struct = new Structure( $structArmor, $structHP, true);
		$struct->addTag("Outer Structure");
		$struct->startArc = 60;
		$struct->endArc = 180;
        $this->addRightAftSystem($struct);  
		
        $this->addPrimarySystem(new Structure( 5, 63));
	    
	//d20 hit chart
        $this->hitChart = array(
            0=> array(
                    7 => "Structure",
                    11 => "Cargo Bay",
                    14 => "Scanner",
                    17 => "Engine",
                    19 => "Reactor",
                    20 => "C&C",
           		 ),
            1=> array(
                    4 => "TAG:Thruster",
                    6 => "Cargo Bay",
				8 => "Shield Projector",
                    12 => "TAG:Weapon",                    
                    17 => "TAG:Outer Structure",
                    20 => "Primary",
           		 ),
            2=> array(
                    4 => "TAG:Thruster",
                    6 => "Cargo Bay",
					8 => "Shield Projector",
                    12 => "TAG:Weapon",                      
                    17 => "TAG:Outer Structure",
                    20 => "Primary",
           		 ),
            31=> array(
                    4 => "TAG:Thruster",
                    6 => "Cargo Bay",
					8 => "Shield Projector",
                    12 => "TAG:Weapon",
					13 => "32:Nacelle",                      
                    17 => "TAG:Outer Structure",
                    20 => "Primary",
           		 ),
            32=> array(
                    4 => "TAG:Thruster",
                    6 => "Cargo Bay",
					9 => "Nacelle",
                    12 => "TAG:Weapon",
					13 => "31:Shield Projector",                        
                    17 => "TAG:Outer Structure",
                    20 => "Primary",
           		 ),
            41=> array(
                    4 => "TAG:Thruster",
                    6 => "Cargo Bay",
					8 => "Shield Projector",
                    12 => "TAG:Weapon",
					13 => "42:Nacelle",                      
                    17 => "TAG:Outer Structure",
                    20 => "Primary",
           		 ),
       		42=> array(
                    4 => "TAG:Thruster",
                    6 => "Cargo Bay",
					9 => "Nacelle",
                    12 => "TAG:Weapon",
					13 => "41:Shield Projector",                      
                    17 => "TAG:Outer Structure",
                    20 => "Primary",
           		 ),
           	);
	
		}
	}
		
?>		