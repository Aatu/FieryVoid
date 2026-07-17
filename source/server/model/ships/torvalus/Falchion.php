<?php
class Falchion extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 450;
		$this->faction = "Torvalus Speculators";
        $this->phpclass = "Falchion";
        $this->shipClass = "Falchion";
        $this->imagePath = "img/ships/TorvalusFalchion.png";
        $this->canvasSize = 128;
	    $this->isd = 'Ancient';
		$this->notes = "Atmospheric Capable";

		$this->factionAge = 3; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
        $this->agile = true;
        $this->jinkinglimit = 2;
				
        $this->gravitic = true;
		$this->advancedArmor = true;   
        
        $this->forwardDefense = 5;
        $this->sideDefense = 8;
        
        $this->turncost = 0.25;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 1;
		$this->iniativebonus = 16 *5;

		$this->trueStealth = true; //For ships that can actually be hidden, not just jammer from range.  Important for Front End.
		$this->canPreOrder = true;				
        
		/*Torvalus use their own enhancement set */		
		Enhancements::nonstandardEnhancementSet($this, 'TorvalusShip');
		
         
		$this->addPrimarySystem(new Reactor( 5, 8, 0, 0));//armor, structure, power req, output
        $this->addPrimarySystem(new CnC(6, 8, 0, 0));
		$scanner = new Scanner(5, 10, 0, 5);
		$scanner->markAdvanced();
		$this->addPrimarySystem($scanner);			
		$this->addPrimarySystem(new Engine(5, 13, 0, 10, 2));
        //$this->addPrimarySystem(new SelfRepair(5, 6, 4)); //None shown on SCS
		$this->addPrimarySystem(new JumpEngine(6, 9, 8, 12));
		$this->addPrimarySystem(new GraviticThruster(6, 10, 0, 5, 3));
		$this->addPrimarySystem(new GraviticThruster(6, 10, 0, 5, 4));			
             
        $this->addFrontSystem(new GraviticThruster(6, 8, 0, 4, 1));		
        $this->addFrontSystem(new VolleyLaser(5, 0, 0, 240, 120));         
        $this->addFrontSystem(new GraviticThruster(6, 8, 0, 4, 1));

		$this->addAftSystem(new GraviticThruster(5, 12, 0, 4, 2));
		$this->addAftSystem(new ShadingField(5, 12, 5, 4, 0, 360)); 		
		$this->addAftSystem(new GraviticThruster(5, 12, 0, 4, 2));		        

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure( 5, 48 ));
		
	
		$this->hitChart = array(
			0=> array( //PRIMARY
				8 => "TAG:Thruster",
				11 => "Jump Engine",
				//11 => "Self Repair", //Listed in hit chart, but not on SCS!
				14 => "Scanner",                
				17 => "Engine",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array( //Fwd
				5 => "Thruster",
				8 => "Volley Laser",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array( //Aft
				6 => "Thruster",
				9 => "Shading Field",
				18 => "Structure",                                
				20 => "Primary",
			),
		);
		
    }
}



?>
