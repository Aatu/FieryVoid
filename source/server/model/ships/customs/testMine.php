<?php
class testMine extends MineClass{
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 15*6;
        $this->faction = "Custom Ships";
        $this->phpclass = "testMine";
        $this->shipClass = "Test Mine";
        $this->imagePath = "img/ships/UsuuthDovarum.png";
        
		$this->isd = 1950;
       
        $this->forwardDefense = 5;
        $this->sideDefense = 5;
        $this->freethrust = 0;
        $this->offensivebonus = 6; 
        $this->turncost = 0.33; //actually not all that relevant...
        
		$this->hangarRequired = ""; //they don't require any hangars... although of course cannot be used in pickup battle either!
		$this->unitSize = 6; //number of craft in squadron
		
    	$this->iniativebonus = -20; 
//    	$this->superheavy = true;
        $this->maxFlightSize = 6;//this is a superheavy fighter originally intended as single unit, limit flight size to 3
		
        $this->populate();
		
    }
    
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(3, 3, 3, 3);
            $fighter = new Fighter("Test Mine", $armour, 5, $this->id);
            $fighter->displayName = "Test Mine";
            $fighter->imagePath = "img/ships/UsuuthDovarum.png";
            $fighter->iconPath = "img/ships/UsuuthDovarum_large.png"; 

			$LPB = new LightParticleBeamFtr(300, 60, 1);
			$fighter->addFrontSystem($LPB);

			$SPB = new StdParticleBeamFtr(300, 60, 1);
			$fighter->addFrontSystem($SPB);

			$TA = new TwinArrayFtr(300, 60, 1);
			$fighter->addFrontSystem($TA);

			$MC = new MatterCannonFtr(300, 60, 1);
			$fighter->addAftSystem($MC);

			$MPC = new MedPlasmaCannonFtr(300, 60, 1);
			$fighter->addAftSystem($MPC);


		            
//			$hvyGun = new HvyParticleProjector(0, 1, 0, 330, 30); 
//			$hvyGun->fireControl = array(-4, 0, 0); // fighters, <mediums, <capitals	
//			$fighter->addFrontSystem($hvyGun);
			
       		//original SCS has LightProjectors linked
//            $lightGun = new ParticleProjector(0, 1, 0, 330, 30); 
			//two linked guns...
/*			$lightGun->isLinked = true;
			$lightGun->shots = 2;
			$lightGun->defaultShots = 2;
			$lightGun->displayName = 'Twin-linked Particle Projector';		
			$lightGun->fireControl = array(-2, 0, 0); // fighters, <mediums, <capitals	
            $fighter->addFrontSystem($lightGun);
*/            
			
        	$this->addSystem($fighter);
       }
    }
    
    
}
?>
