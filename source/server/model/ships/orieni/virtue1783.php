<?php
class Virtue1783 extends MediumShip{
    /*Orieni Virtue Strike Force Corvette, variant ISD 1783; source: Variants-6*/
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
      	$this->pointCost = 445;
      	$this->faction = "Orieni";
        $this->phpclass = "Virtue1783";
        $this->imagePath = "img/ships/steadfast.png";
        $this->shipClass = "Virtue Strike Force Corvette (early)";  
        $this->variantOf = "Steadfast Escort Corvette";
	    $this->isd = 1783;
        $this->agile = true;
        $this->canvasSize = 100;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 14;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
	      $this->iniativebonus = 70;

        $this->occurence = "common"; //Strike Force: Common; Regular Navy: not eligible earlier than 2008, then uncommon	    
	    $this->notes = 'Strike Force only (can be taken only if force is led by Paragon).';
         
        $this->addPrimarySystem(new Reactor(4, 15, 0, 2));
        $this->addPrimarySystem(new CnC(4, 15, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 12, 3, 6));
        $this->addPrimarySystem(new Engine(3, 15, 0, 10, 2));
		    $this->addPrimarySystem(new Hangar(1, 1));
		    $this->addPrimarySystem(new Thruster(2, 10, 0, 6, 3));
		    $this->addPrimarySystem(new Thruster(2, 10, 0, 6, 4));        
		
        $this->addFrontSystem(new GaussCannon(1, 10, 4, 300, 60));
        $this->addFrontSystem(new GaussCannon(1, 10, 4, 300, 60));
        $this->addFrontSystem(new OrieniGatlingRG(1, 4, 1, 180, 60));
        $this->addFrontSystem(new OrieniGatlingRG(1, 4, 1, 300, 180));
        $this->addFrontSystem(new Thruster(1, 6, 0, 3, 1));
        $this->addFrontSystem(new Thruster(1, 6, 0, 3, 1));
        $this->addFrontSystem(new Thruster(1, 6, 0, 3, 1));


        $this->addAftSystem(new OrieniGatlingRG(1, 4, 1, 120, 360));
        $this->addAftSystem(new OrieniGatlingRG(1, 4, 1, 0, 240));
	$this->addAftSystem(new Engine(4, 7, 0, 3, 2));
        $this->addAftSystem(new Thruster(1, 8, 0, 4, 2));
        $this->addAftSystem(new Thruster(1, 10, 0, 5, 2));
        $this->addAftSystem(new Thruster(21, 8, 0, 4, 2));
        
        $this->addPrimarySystem(new Structure(4, 46));
		
		
		
        	//d20 hit chart
        	$this->hitChart = array(
        
        		0=> array( //PRIMARY
        			8 => "Thruster",
        			11 => "Scanner",
        			15 => "Engine",
        			17 => "Hangar",
        			19 => "Reactor",
        			20 => "C&C",
        		),
        
        		1=> array( //Fwd
        			5 => "Thruster",
        			8 => "Gauss Cannon",
        			11 => "Gatling Railgun",
        			17 => "Structure",
        			20 => "Primary",
        		),
        
        		2=> array( //Aft
        			6 => "Thruster",
        			8 => "Engine",
        			10 => "Gatling Railgun",
        			17 => "Structure",
        			20 => "Primary",
        		),
        
        	); //end of hit chart

		
        }
    }
?>
