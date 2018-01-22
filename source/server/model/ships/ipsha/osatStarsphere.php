<?php
class OsatStarsphere extends OSAT{
	/*Showdowns-7 unit*/
    /*Ipsha general:
     - remember about EM hardening!
     - instead of -2 bonus to dropout/crit when caused by Ion weapon, just add -1 overall crit/dropout bonus
     - remind player of the above in comments in fleet selection phase!
     - Singularity Drive replaced by standard engine
     - add Sensor power demand to Reactor output (it's considered 'baseline' for tabletop!)
    */
  
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 200;
        $this->faction = "Ipsha";
        $this->phpclass = "OsatStarsphere";
        $this->imagePath = "img/ships/IpshaOSAT.png";
        $this->shipClass = 'Starsphere OSAT';
        
        $this->forwardDefense = 10;
        $this->sideDefense = 10;
        
        $this->notes .= '<br>EM hardened';	  
        $this->notes .= '<br>-1 critical roll bonus';
        $this->EMHardened = true; //EM Hardening - some weapons would check for this value!
        $this->critRollMod = -1; //generalbonus to critical rolls!
      
        $this->isd = 2225;
      
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60;
      
        
    
	$this->addPrimarySystem(new MagGravReactor(4, 12, 0, 14+2));
	$this->addPrimarySystem(new Scanner(4, 7, 2, 5));
	$this->addPrimarySystem(new MagGraviticThruster(3, 6, 0, 99, 2));    
	$this->addPrimarySystem(new SparkField(2, 0, 0, 0, 360));   
	$this->addPrimarySystem(new SurgeCannon(3, 0, 0, 300, 60)); 
	$this->addPrimarySystem(new SurgeCannon(3, 0, 0, 300, 60)); 
	$this->addPrimarySystem(new SurgeCannon(3, 0, 0, 300, 60)); 
	$this->addPrimarySystem(new SurgeCannon(3, 0, 0, 300, 60)); 
	    
      
      
      
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        
        $this->addPrimarySystem(new Structure(4, 26));
      
      
	
        $this->hitChart = array(
          0=> array(
            9 => "Structure",
            11 => "Thruster",
            15 => "Surge Cannon",
            17 => "Scanner",
            19 => "Reactor",
            20 => "Spark Field",
          ),
        );
      
    }
}

?>
