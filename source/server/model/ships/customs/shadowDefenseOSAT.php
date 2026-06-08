<?php
class shadowDefenseOSAT extends OSAT{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 400;
        $this->faction = "Custom Ships";       
        $this->phpclass = "shadowDefenseOSAT";
        $this->imagePath = "img/ships/ShadowDestroyer.png";
        $this->shipClass = "Shadow Defense OSAT";
			$this->variantOf = "Shadow OSAT";
			$this->occurence = "common";
			$this->canvasSize = 90; 
        $this->isd = 'Ancient';
        $this->shipSizeClass = 1; //it's actually an MCV :)
		$this->factionAge = 3; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
        
        $this->forwardDefense = 12;
        $this->sideDefense = 14;
        
		$this->advancedArmor = true;   
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 70;

		Enhancements::nonstandardEnhancementSet($this, 'ShadowShip');

	    $this->notes = 'For All Alone in the Night campaign';

       	//Primary systems
		$this->addPrimarySystem(new Reactor(6, 12, 0, 0));
		$scanner = new Scanner(5, 12, 3, 12);
		$scanner->markAdvanced();
       		$this->addPrimarySystem($scanner);
       	$this->addPrimarySystem(new SelfRepair(2, 3, 2)); //armor, structure, output

		//EnergyDiffuser		
		$diffuserPort = new EnergyDiffuser(4, 9, 10, 180, 360);//($armour, $maxhealth, $dissipation, $startArc, $endArc)
		$tendril=new DiffuserTendril(10,'L');//absorbtion capacity,side
		$diffuserPort->addTendril($tendril);
		$this->addLeftSystem($tendril);
		$tendril=new DiffuserTendril(10,'L');//absorbtion capacity,side
		$diffuserPort->addTendril($tendril);
		$this->addLeftSystem($tendril);
		$tendril=new DiffuserTendril(10,'L');//absorbtion capacity,side
		$diffuserPort->addTendril($tendril);
		$this->addLeftSystem($tendril);
        	$this->addPrimarySystem($diffuserPort);

		$diffuserStbd = new EnergyDiffuser(4, 9, 10, 0, 180);//($armour, $maxhealth, $dissipation, $startArc, $endArc)
		$tendril=new DiffuserTendril(10,'R');//absorbtion capacity,side
		$diffuserStbd->addTendril($tendril);
		$this->addRightSystem($tendril);
		$tendril=new DiffuserTendril(10,'R');//absorbtion capacity,side
		$diffuserStbd->addTendril($tendril);
		$this->addRightSystem($tendril);
		$tendril=new DiffuserTendril(10,'R');//absorbtion capacity,side
		$diffuserStbd->addTendril($tendril);
		$this->addRightSystem($tendril);
        	$this->addPrimarySystem($diffuserStbd);

		//weapons - Forward for visual reasons!
        	$this->addFrontSystem(new MultiphasedCutter(4, 0, 0, 300, 60));

		//thruster - Aft for visual reasons!
		//NOTE: Used a standard thruster as this is an OSAT and functionally works like a bio thruster
			$this->addAftSystem(new Thruster(4, 6, 0, 0, 2)); 

        $this->addPrimarySystem(new OSATCnC(0, 1, 0, 0));
                
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(6, 22));

		/*systems on Shadow ships CANNOT be targeted by called shots!*/
		$this->notes .= "<br>Cannot be targeted by called shots.";
		foreach ($this->systems as $sys){
			$sys->isPrimaryTargetable = false; 
			$sys->isTargetable = false; //cannot be targeted ever!
		}

		$this->hitChart = array(
                0=> array(
                        8 => "Structure",
                        11 => "0:Energy Diffuser",
                        12 => "2:Thruster",
                        14 => "0:Self Repair",
						16 => "1:Multiphased Cutter",
                        18 => "Scanner",
						20 => "Reactor",
                ),
                1=> array(
                        20 => "Primary",
                ),
                2=> array(
                        20 => "Primary",
                ),
        );
    }
}

?>
