<?php
class Xixx extends VreeHCV{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 650;
		$this->faction = "Vree Conglomerate";
		$this->phpclass = "Xixx";
		$this->shipClass = "Xixx Torpedo Saucer";
		$this->isd = 2251;
        $this->limited = 10; //Restricted Deployment

		$this->iniativebonus = 6 *5; 
		
        $this->turncost = 0.66;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 3;
        $this->pivotcost = 0;	
        $this->gravitic = true;        	

		$this->forwardDefense = 13;
		$this->sideDefense = 13;

		$this->imagePath = "img/ships/VreeXixx.png";
		$this->canvasSize = 200;

		$this->addPrimarySystem(new Reactor(4, 20, 0, 0));
		$this->addPrimarySystem(new Hangar(4, 1, 1));
		$this->addPrimarySystem(new CnC(4, 12, 0, 0));
		$this->addPrimarySystem(new Scanner(4, 14, 9, 7));
        $this->addPrimarySystem(new Engine(4, 11, 0, 7, 2));
		
		//Turret mounts: the two weapons sharing a turret have their fire linked, so their targets must
		//be within 60 degrees of each other (they are 360-degree mounts, so weapon arcs can't express
		//this). The group tag doubles as the turret's display name shown in the system window.
		//Turret 1 = two defenders, Turret 2 = defender + gun.
		//setArcRestriction: every primary-mounted Vree weapon can JAM. Whenever it is damaged it rolls
		//a separate d20 (17+) and locks to the forward 330..30 - and because a turret is one mount,
		//a jam on either linked weapon restricts both. See Weapon::testArcRestriction.
		$turretDefenderA = new AntiprotonDefender(3, 0, 0, 0, 360);
		$turretDefenderA->linkedFiringGroup = 'Turret 1';
		$turretDefenderA->linkedFiringSpread = 60;
		$turretDefenderA->setArcRestriction(330, 30);
		$this->addPrimarySystem($turretDefenderA);
		$turretDefenderB = new AntiprotonDefender(3, 0, 0, 0, 360);
		$turretDefenderB->linkedFiringGroup = 'Turret 1';
		$turretDefenderB->linkedFiringSpread = 60;
		$turretDefenderB->setArcRestriction(330, 30);
		$this->addPrimarySystem($turretDefenderB);
		$turretDefenderC = new AntiprotonDefender(3, 0, 0, 0, 360);
		$turretDefenderC->linkedFiringGroup = 'Turret 2';
		$turretDefenderC->linkedFiringSpread = 60;
		$turretDefenderC->setArcRestriction(330, 30);
		$this->addPrimarySystem($turretDefenderC);
		$turretGun = new AntiprotonGun(3, 0, 0, 0, 360);
		$turretGun->linkedFiringGroup = 'Turret 2';
		$turretGun->linkedFiringSpread = 60;
		$turretGun->setArcRestriction(330, 30);
		$this->addPrimarySystem($turretGun);
		
		$thrust = new GraviticThruster(3, 12, 0, 7, 3);
		$thrust->startArc = 240;
		$thrust->endArc = 300;
		$this->addPrimarySystem($thrust);
		$thrust = new GraviticThruster(3, 12, 0, 7, 1);
		$thrust->startArc = 300;
		$thrust->endArc = 60;
		$this->addFrontSystem($thrust);		
        $thrust = new GraviticThruster(3, 12, 0, 7, 2);
		$thrust->startArc = 120;
		$thrust->endArc = 240;
		$this->addAftSystem($thrust);	
		$thrust = new GraviticThruster(3, 12, 0, 7, 4);
		$thrust->startArc = 60;
		$thrust->endArc = 120;
		$this->addPrimarySystem($thrust);					

		$this->addFrontSystem(new AntimatterTorpedo(2, 0, 0, 240, 0));
		$this->addFrontSystem(new AntimatterTorpedo(2, 0, 0, 300, 60)); 
		$this->addFrontSystem(new AntimatterTorpedo(2, 0, 0, 300, 60)); 
		$this->addFrontSystem(new AntimatterTorpedo(2, 0, 0, 0, 120)); 

		$this->addAftSystem(new AntimatterTorpedo(2, 0, 0, 180, 300));  
		$this->addAftSystem(new AntimatterTorpedo(2, 0, 0, 120, 240)); 		
 		$this->addAftSystem(new AntimatterTorpedo(2, 0, 0, 120, 240));
		$this->addAftSystem(new AntimatterTorpedo(2, 0, 0, 60, 180));	 
		     
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
		/*remade for Tags!
        $this->addFrontSystem(new Structure( 4, 50, true));
        $this->addAftSystem(new Structure( 4, 50, true));
		*/
		$structArmor = 4;
		$structHP = 50;
		
		$struct = new Structure( $structArmor, $structHP, true);
		$struct->addTag("Outer Structure");
		$struct->startArc = 270;
		$struct->endArc = 90;
        $this->addFrontSystem($struct);
		
		$struct = new Structure( $structArmor, $structHP, true);
		$struct->addTag("Outer Structure");
		$struct->startArc = 90;
		$struct->endArc = 270;
        $this->addAftSystem($struct);		
		
        $this->addPrimarySystem(new Structure( 4, 45));
	    
	//d20 hit chart
        $this->hitChart = array(
            0=> array(
                    10 => "Structure",
                    12 => "Jump Engine",
                    14 => "Scanner",
                    16 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
           		 ),
            1=> array(
                    4 => "TAG:Thruster",                        
                    8 => "TAG:Weapon", 
                    17 => "Structure",
                    20 => "Primary",
           		 ),
            2=> array(
                    4 => "TAG:Thruster",                        
                    8 => "TAG:Weapon", 
                    17 => "Structure",
                    20 => "Primary",
           		 ),
           	);
	
	
		/*remade for Tags!
        $this->hitChart = array(
            0=> array(
                    10 => "Structure",
                    12 => "Jump Engine",
                    14 => "Scanner",
                    16 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
           		 ),
            1=> array(
                    4 => "0:Thruster",   
                    6 => "Antiproton Torpedo",
                    7 => "0:Antiproton Gun",
                    8 => "0: Antiproton Defender",
                    17 => "Structure",
                    20 => "Primary",
           		 ),
            2=> array(
                    4 => "0:Thruster",   
                    6 => "Antiproton Torpedo",
                    7 => "0:Antiproton Gun",
                    8 => "0: Antiproton Defender",
                    17 => "Structure",
                    20 => "Primary",
           		 ),
           	);
			*/
       		
		}
	}
		
?>		
