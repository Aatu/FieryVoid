<?php
class TrekSulibanHelix extends VreeCapital{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

        $this->pointCost = 1300;
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

//--section--
		$this->addPrimarySystem(new Reactor(4, 40, 0, 0));
		$this->addPrimarySystem(new CnC(5, 16, 0, 0));
		$this->addPrimarySystem(new Scanner(5, 12, 6, 6));
		$impulseDrive = new TrekImpulseDrive(4,30,0,1,8); //Impulse Drive is an engine in its own right, in addition to serving as hub for Nacelle output: $armour, $maxhealth, $powerReq, $output, $boostEfficiency
		$this->addPrimarySystem(new CargoBay(2, 40));

//--section--
		$cargoBay = new CargoBay(3, 15);
			$cargoBay->startArc = 300;
			$cargoBay->endArc = 60;
			$this->addFrontSystem($cargoBay);
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
	
		$projection = new TrekShieldProjection(0, 15, 6, 300, 60, 'F');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projector = new TrekShieldProjector(1, 8, 2, 3, 300, 60, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addFrontSystem($projector);
			$projector = new TrekShieldProjector(1, 8, 2, 3, 300, 60, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addFrontSystem($projector);
		$this->addFrontSystem($projection);



//--section--
		$cargoBay = new CargoBay(3, 15);
			$cargoBay->startArc = 120;
			$cargoBay->endArc = 240;
			$this->addAftSystem($cargoBay);
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
	
		$projection = new TrekShieldProjection(0, 15, 6, 120, 240, 'A');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projector = new TrekShieldProjector(1, 8, 2, 3, 120, 240, 'A'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addAftSystem($projector);
			$projector = new TrekShieldProjector(1, 8, 2, 3, 120, 240, 'A'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addAftSystem($projector);
		$this->addAftSystem($projection);      
     

//--section--
		$cargoBay = new CargoBay(3, 15);
			$cargoBay->startArc = 240;
			$cargoBay->endArc = 360;
			$this->addLeftFrontSystem($cargoBay);
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
	
		$projection = new TrekShieldProjection(0, 15, 6, 240, 360, 'L');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projector = new TrekShieldProjector(1, 8, 2, 3, 240, 360, 'L'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addLeftFrontSystem($projector);
			$projector = new TrekShieldProjector(1, 8, 2, 3, 240, 360, 'L'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addLeftFrontSystem($projector);
		$this->addLeftFrontSystem($projection);		
				
				
//--section--
		$cargoBay = new CargoBay(3, 15);
			$cargoBay->startArc = 180;
			$cargoBay->endArc = 300;
			$this->addLeftAftSystem($cargoBay);
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
	
		$projection = new TrekShieldProjection(0, 15, 6, 180, 300, 'L');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projector = new TrekShieldProjector(1, 8, 2, 3, 180, 300, 'L'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addLeftAftSystem($projector);
			$projector = new TrekShieldProjector(1, 8, 2, 3, 180, 300, 'L'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addLeftAftSystem($projector);
		$this->addLeftAftSystem($projection);		
	
		$warpNacelle = new TrekWarpDrive(3, 24, 0, 3); //armor, structure, power usage, impulse output
			$warpNacelle->startArc = 120;
			$warpNacelle->endArc = 360;
		$impulseDrive->addThruster($warpNacelle);
		$this->addLeftAftSystem($warpNacelle);	
		
		
//--section--
		$cargoBay = new CargoBay(3, 15);
			$cargoBay->startArc = 0;
			$cargoBay->endArc = 120;
			$this->addRightFrontSystem($cargoBay);
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
	
		$projection = new TrekShieldProjection(0, 15, 6, 0, 120, 'R');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projector = new TrekShieldProjector(1, 8, 2, 3, 0, 120, 'R'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addRightFrontSystem($projector);
			$projector = new TrekShieldProjector(1, 8, 2, 3, 0, 120, 'R'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addRightFrontSystem($projector);
		$this->addRightFrontSystem($projection);					
	
//--section--
		$cargoBay = new CargoBay(3, 15);
			$cargoBay->startArc = 60;
			$cargoBay->endArc = 180;
			$this->addRightAftSystem($cargoBay);
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
	
		$projection = new TrekShieldProjection(0, 15, 6, 60, 180, 'R');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projector = new TrekShieldProjector(1, 8, 2, 3, 60, 180, 'R'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addRightAftSystem($projector);
			$projector = new TrekShieldProjector(1, 8, 2, 3, 60, 180, 'R'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addRightAftSystem($projector);
		$this->addRightAftSystem($projection);			
	
		$warpNacelle = new TrekWarpDrive(3, 24, 0, 3); //armor, structure, power usage, impulse output
			$warpNacelle->startArc = 0;
			$warpNacelle->endArc = 240;
		$impulseDrive->addThruster($warpNacelle);
		$this->addRightAftSystem($warpNacelle);
       
	   
	   
	//technical thrusters - unlimited, like for LCVs		
	$this->addPrimarySystem(new InvulnerableThruster(1, 1, 0, 99, 3)); //unhitable and with unlimited thrust allowance
	$this->addPrimarySystem(new InvulnerableThruster(1, 1, 0, 99, 1)); //unhitable and with unlimited thrust allowance
	$this->addPrimarySystem(new InvulnerableThruster(1, 1, 0, 99, 2)); //unhitable and with unlimited thrust allowance
	$this->addPrimarySystem(new InvulnerableThruster(1, 1, 0, 99, 4)); //unhitable and with unlimited thrust allowance  
	
	   
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
                    4 => "TAG:Cargo Bay",
					6 => "TAG:Shield Projector",
                    10 => "TAG:Weapon",                    
                    17 => "TAG:Outer Structure",
                    20 => "Primary",
           		 ),
            2=> array(
                    4 => "TAG:Cargo Bay",
					6 => "TAG:Shield Projector",
                    9 => "TAG:Weapon",            
                    11 => "TAG:Nacelle",                     
                    17 => "TAG:Outer Structure",
                    20 => "Primary",
           		 ),
            31=> array(
                    4 => "TAG:Cargo Bay",
					6 => "TAG:Shield Projector",
                    9 => "TAG:Weapon",            
                    11 => "TAG:Nacelle",                     
                    17 => "TAG:Outer Structure",
                    20 => "Primary",
           		 ),
            32=> array(
                    4 => "TAG:Cargo Bay",
					6 => "TAG:Shield Projector",
                    9 => "TAG:Weapon",            
                    11 => "TAG:Nacelle",                     
                    17 => "TAG:Outer Structure",
                    20 => "Primary",
           		 ),
            41=> array(
                    4 => "TAG:Cargo Bay",
					6 => "TAG:Shield Projector",
                    9 => "TAG:Weapon",            
                    11 => "TAG:Nacelle",                     
                    17 => "TAG:Outer Structure",
                    20 => "Primary",
           		 ),
       		42=> array(
                    4 => "TAG:Cargo Bay",
					6 => "TAG:Shield Projector",
                    9 => "TAG:Weapon",            
                    11 => "TAG:Nacelle",                     
                    17 => "TAG:Outer Structure",
                    20 => "Primary",
           		 ),
           	);
	
		}
	}
		
?>		