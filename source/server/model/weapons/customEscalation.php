<?php
/*file for Escalation Wars universe weapons*/


// PARTICLE WEAPONS

class EWParticleLance extends Raking{
        public $name = "EWParticleLance";
        public $displayName = "Particle Lance";
	    public $iconPath = "EWParticleLance.png";
	
	public $animationArray = array(1=>'laser', 2=>'laser');
        public $animationColor = array(255, 250, 230);
        public $animationWidthArray = array(1=>5, 2=>3);
        public $animationExplosionScale = 0.25;
   	    	      
	//actual weapons data
   		public $priorityArray = array(1=>8, 2=>8);
    	public $gunsArray = array(1=>1, 2=>2); //one Lance, but two Beam shots!
	
        public $intercept = 2;	
        public $loadingtimeArray = array(1=>2, 2=>2); //mode 1 should be the one with longest loading time
        public $rangePenaltyArray = array(1=>0.5, 2=>1);
        public $fireControlArray = array( 1=>array(0, 2, 4), 2=>array(0, 2, 4) ); 
	
		public $weaponClassArray = array(1=>'Particle', 2=>'Particle');
		public $firingModes = array(1=>'Dual', 2=>'Light Particle Cannons');
		public $damageTypeArray = array(1=>'Raking', 2=>'Raking'); 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
			//maxhealth and power reqirement are fixed; left option to override with hand-written values
			if ( $maxhealth == 0 ){
				$maxhealth = 10;
			}
			if ( $powerReq == 0 ){
				$powerReq = 10;
			}
			parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
	
        public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
			$this->data["Special"] = 'Can fire as either a Particle Lance or two Light Particle Cannons.';
        }
	
        public function getDamage($fireOrder){ 
		switch($this->firingMode){
			case 1:
				return Dice::d(10, 3)+16; //ParticleLance
				break;
			case 2:
				return Dice::d(10, 2)+8; //LightParticleCannon
				break;	
		}
	}
        public function setMinDamage(){ 
		switch($this->firingMode){
			case 1:
				$this->minDamage = 19; //ParticleLance
				break;
			case 2:
				$this->minDamage = 10; //LightParticleCannon
				break;	
		}
		$this->minDamageArray[$this->firingMode] = $this->minDamage;
	}
        public function setMaxDamage(){
		switch($this->firingMode){
			case 1:
				$this->maxDamage = 46; //ParticleLance
				break;
			case 2:
				$this->maxDamage = 28; //LightParticleCannon
				break;	
		}
		$this->maxDamageArray[$this->firingMode] = $this->maxDamage;
	}	
	
} //end of EWParticleLance


    class EWParticleGun extends Particle{
        public $trailColor = array(30, 170, 255);

        public $name = "EWParticleGun";
        public $displayName = "Particle Gun";
		public $animation = "bolt";
        public $animationColor = array(255, 250, 230);
        public $animationExplosionScale = 0.15;
        public $animationWidth = 2;
        public $animationWidth2 = 0.2;

        public $intercept = 1;
        public $loadingtime = 2;
        public $priority = 8;

        public $rangePenalty = 1;
        public $fireControl = array(2, 2, 2); // fighters, <mediums, <capitals

        public $damageType = "Standard"; 
        public $weaponClass = "Particle";
        public $firingModes = array( 1 => "Standard");
        
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 4;
            }
            if ( $powerReq == 0 ){
                $powerReq = 2;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }


        public function getDamage($fireOrder){ return Dice::d(10, 2)+0;   }
        public function setMinDamage(){     $this->minDamage = 2 ;      }
        public function setMaxDamage(){     $this->maxDamage = 20 ;      }
    }  //endof EWParticleGun


    class EWLightParticleGun extends Particle{
        public $trailColor = array(30, 170, 255);

        public $name = "EWLightParticleGun";
        public $displayName = "Light Particle Gun";
		public $animation = "bolt";
        public $animationColor = array(255, 250, 230);
        public $animationExplosionScale = 0.10;
        public $animationWidth = 1.5;
        public $animationWidth2 = 0.2;

        public $intercept = 1;
        public $loadingtime = 1;
        public $priority = 8;

        public $rangePenalty = 2;
        public $fireControl = array(1, 1, 0); // fighters, <mediums, <capitals

        public $damageType = "Standard"; 
        public $weaponClass = "Particle";
        public $firingModes = array( 1 => "Standard");
        
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 2;
            }
            if ( $powerReq == 0 ){
                $powerReq = 1;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }


        public function getDamage($fireOrder){ return Dice::d(10, 1)+1;   }
        public function setMinDamage(){     $this->minDamage = 2 ;      }
        public function setMaxDamage(){     $this->maxDamage = 11 ;      }
    }  //endof EWLightParticleGun


// END PARTICLE WEAPONS


// GRAVITIC WEAPONS


class EWGraviticTractingRod extends SWDirectWeapon{
    /*StarWars Tractor Beam - modified
    */
    /*weapon that does no damage, but limits targets' maneuvrability next turn ('target held by tractor beam')
    */
    public $name = "EWGraviticTractingRod";
    public $displayName = "Gravitic Tracting Rod";
	
    public $priority = 10; //let's fire last
    public $loadingtime = 3;
    public $rangePenalty = 2;
    public $intercept = 0;
    public $fireControl = array(null, 2, 2); // can't fire at fighters, incompatible with crit behavior!
   
	//let's animate this as a very wide beam...
	public $animation = "laser";
        public $animationColor = array(55, 55, 55);
        public $animationColor2 = array(100, 100, 100);
        public $animationExplosionScale = 0.45;
        public $animationWidth = 15;
        public $animationWidth2 = 0.5;
	
 	public $possibleCriticals = array( //no point in damage reduced crit
            14=>"ReducedRange"
	);
	
    public function setSystemDataWindow($turn){
      parent::setSystemDataWindow($turn);
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
      $this->data["Special"] .= "Does no damage, but holds target next turn";      
      $this->data["Special"] .= "<br>limiting its maneuvering options"; 
      $this->data["Special"] .= "<br>(-1 thrust and -20 Initiative next turn).";  
    }	
    
	function __construct($armor, $startArc, $endArc, $nrOfShots){ //armor, arc and number of weapon in common housing: structure and power data are calculated!
		$this->intercept = 0;
		$this->iconPath = "EWGraviticTractingRod.png";
		
		parent::__construct($armor, 6, 4, $startArc, $endArc, $nrOfShots); //maxhealth and powerReq for single gun mount!
		$this->addSalvoMode();
	}    
	
	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){ //target is held critical on PRIMARY Structure!
		//marked to C&C
		$CnC = $ship->getSystemByName("CnC");
		if($CnC){
			$crit = new swtargetheld(-1, $ship->id, $CnC->id, 'swtargetheld', $gamedata->turn); 
			$crit->updated = true;
		      $CnC->criticals[] =  $crit;
		}
	}
	
	public function getDamage($fireOrder){ return  0;   }
	public function setMinDamage(){   $this->minDamage =  0 ;      }
	public function setMaxDamage(){   $this->maxDamage =  0 ;      }
} //endof EWGraviticTractingRod


// END GRAVITIC WEAPONS



// LASER WEAPONS


    class EWTwinLaserCannon extends Laser{
     
        public $name = "EWTwinLaserCannon";
        public $displayName = "Twin Laser Cannon";  
	    public $iconPath = "EWTwinLaserCannon.png";
	    
        public $animation = "laser";
        public $animationColor = array(255, 91, 91);
        public $animationExplosionScale = 0.16;
        public $animationWidth = 3;
        public $animationWidth2 = 0.3;
        public $priority = 8;
		public $guns = 2;
        public $loadingtime = 2;
        
        public $raking = 10;
        
        public $rangePenalty = 0.5; //-1 / 2 hexes
        public $fireControl = array(-2, 2, 3); // fighters, <mediums, <capitals 
    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
	    //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 8;
            }
            if ( $powerReq == 0 ){
                $powerReq = 4;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){        return Dice::d(10, 2)+5;   }
        public function setMinDamage(){     $this->minDamage = 7 ;      }
        public function setMaxDamage(){     $this->maxDamage = 25 ;      }
    } //endof EWTwinLaserCannon



// END LASER WEAPONS



// PLASMA WEAPONS


class EWHeavyPointPlasmaGun extends Plasma{
    	public $name = "EWHeavyPointPlasmaGun";
        public $displayName = "Heavy Point Plasma Gun";
		public $iconPath = "EWHeavyPointPlasmaGun.png";
        public $animation = "trail";
        public $animationColor = array(75, 250, 90);
    	public $trailColor = array(75, 250, 90);
    	public $projectilespeed = 15;
        public $animationWidth = 5;
    	public $animationExplosionScale = 0.30;
    	public $trailLength = 20;
    	public $rangeDamagePenalty = 1;
        public $guns = 2;

        public $intercept = 2;
		public $ballisticIntercept = true;
    		        
        public $loadingtime = 1;
			
        public $rangePenalty = 2;
        public $fireControl = array(4, 3, 2); // fighters, <=mediums, <=capitals 


        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 7;
            }
            if ( $powerReq == 0 ){
                $powerReq = 2;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
		
    	public function getDamage($fireOrder){        return Dice::d(10, 2)+0;   }
        public function setMinDamage(){     $this->minDamage = 2 /*- $this->dp*/;      }
        public function setMaxDamage(){     $this->maxDamage = 20 /*- $this->dp*/;      }

}  // endof EWHeavyPointPlasmaGun


// END PLASMA WEAPONS



// BALLISTIC WEAPONS


class EWRocketLauncher extends Weapon{
        public $name = "EWRocketLauncher";
        public $displayName = "Rocket Launcher";
		    public $iconPath = "EWRocketLauncher.png";
        public $animation = "trail";
        public $trailColor = array(11, 224, 255);
        public $animationColor = array(50, 50, 50);
        public $animationExplosionScale = 0.2;
        public $projectilespeed = 12;
        public $animationWidth = 4;
        public $trailLength = 100;    

        public $useOEW = true; //torpedo
        public $ballistic = true; //missile
        public $range = 15;
        public $distanceRange = 30;
        public $ammunition = 40; //limited number of shots
		public $guns = 1;
	    
        
        public $loadingtime = 1; // 1 turn
        public $rangePenalty = 0;
        public $fireControl = array(1, 1, 1); // fighters, <mediums, <capitals; INCLUDES BOTH LAUNCHER AND MISSILE DATA!
	    
	public $priority = 4; //Standard weapon
	    
	public $firingMode = 'Ballistic'; //firing mode - just a name essentially
	public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    	public $weaponClass = "Ballistic"; //should be Ballistic and Matter, but FV does not allow that. Instead decrease advanced armor encountered by 2 points (if any) (usually system does that, but it will account for Ballistic and not Matter)
	 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		        //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 4;
            if ( $powerReq == 0 ) $powerReq = 1;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function stripForJson() {
            $strippedSystem = parent::stripForJson();    
            $strippedSystem->ammunition = $this->ammunition;           
            return $strippedSystem;
        }
        
	    
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            $this->data["Ammunition"] = $this->ammunition;
        }
        

        public function getDamage($fireOrder){ 
		
			return Dice::d(6, 2)+2;   
		}

        public function setAmmo($firingMode, $amount){
            $this->ammunition = $amount;
        }
       public function fire($gamedata, $fireOrder){ //note ammo usage
            parent::fire($gamedata, $fireOrder);
            $this->ammunition--;
            Manager::updateAmmoInfo($fireOrder->shooterid, $this->id, $gamedata->id, $this->firingMode, $this->ammunition, $gamedata->turn);
        }
    
        public function setMinDamage(){     $this->minDamage = 4;      }
        public function setMaxDamage(){     $this->maxDamage = 14;      }
}//endof EWRocketLauncher


class EWDualRocketLauncher extends Weapon{
        public $name = "EWDualRocketLauncher";
        public $displayName = "Dual Rocket Launcher";
		    public $iconPath = "EWDualRocketLauncher.png";
        public $animation = "trail";
        public $trailColor = array(11, 224, 255);
        public $animationColor = array(50, 50, 50);
        public $animationExplosionScale = 0.2;
        public $projectilespeed = 12;
        public $animationWidth = 4;
        public $trailLength = 100;    

        public $useOEW = true; //torpedo
        public $ballistic = true; //missile
        public $range = 15;
        public $distanceRange = 30;
        public $ammunition = 40; //limited number of shots
		public $guns = 2;
	    
        
        public $loadingtime = 1; // 1 turn
        public $rangePenalty = 0;
        public $fireControl = array(1, 1, 1); // fighters, <mediums, <capitals; INCLUDES BOTH LAUNCHER AND MISSILE DATA!
	    
	public $priority = 4; //Standard weapon
	    
	public $firingMode = 'Ballistic'; //firing mode - just a name essentially
	public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    	public $weaponClass = "Ballistic"; //should be Ballistic and Matter, but FV does not allow that. Instead decrease advanced armor encountered by 2 points (if any) (usually system does that, but it will account for Ballistic and not Matter)
	 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		        //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 6;
            if ( $powerReq == 0 ) $powerReq = 2;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function stripForJson() {
            $strippedSystem = parent::stripForJson();    
            $strippedSystem->ammunition = $this->ammunition;           
            return $strippedSystem;
        }
        
	    
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            $this->data["Ammunition"] = $this->ammunition;
        }
        

        public function getDamage($fireOrder){ 
			return Dice::d(6, 2)+2;   
		}

        public function setAmmo($firingMode, $amount){
            $this->ammunition = $amount;
        }
       public function fire($gamedata, $fireOrder){ //note ammo usage
            parent::fire($gamedata, $fireOrder);
            $this->ammunition--;
            Manager::updateAmmoInfo($fireOrder->shooterid, $this->id, $gamedata->id, $this->firingMode, $this->ammunition, $gamedata->turn);
        }
    
        public function setMinDamage(){     $this->minDamage = 4;      }
        public function setMaxDamage(){     $this->maxDamage = 14;      }
}//endof EWDualRocketLauncher


class EWHeavyRocketLauncher extends Weapon{
        public $name = "EWHeavyRocketLauncher";
        public $displayName = "Heavy Rocket Launcher";
		    public $iconPath = "EWHeavyRocketLauncher.png";
        public $animation = "trail";
        public $trailColor = array(11, 224, 255);
        public $animationColor = array(50, 50, 50);
        public $animationExplosionScale = 0.2;
        public $projectilespeed = 12;
        public $animationWidth = 4;
        public $trailLength = 100;    

        public $useOEW = true; //torpedo
        public $ballistic = true; //missile
        public $range = 25;
        public $distanceRange = 35;
        public $ammunition = 15; //limited number of shots
        
        public $loadingtime = 2; // 1 turn
        public $rangePenalty = 0;
        public $fireControl = array(-3, 1, 2); // fighters, <mediums, <capitals; INCLUDES BOTH LAUNCHER AND MISSILE DATA!
	    
	public $priority = 6; //Standard weapon
	    
	public $firingMode = 'Ballistic'; //firing mode - just a name essentially
	public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    	public $weaponClass = "Ballistic"; //should be Ballistic and Matter, but FV does not allow that. Instead decrease advanced armor encountered by 2 points (if any) (usually system does that, but it will account for Ballistic and not Matter)
	 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		        //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 9;
            if ( $powerReq == 0 ) $powerReq = 1;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function stripForJson() {
            $strippedSystem = parent::stripForJson();    
            $strippedSystem->ammunition = $this->ammunition;           
            return $strippedSystem;
        }
	    
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            $this->data["Ammunition"] = $this->ammunition;
        }

        public function getDamage($fireOrder){ 
			return Dice::d(10, 2)+4;   
		}

        public function setAmmo($firingMode, $amount){
            $this->ammunition = $amount;
        }
       public function fire($gamedata, $fireOrder){ //note ammo usage
            parent::fire($gamedata, $fireOrder);
            $this->ammunition--;
            Manager::updateAmmoInfo($fireOrder->shooterid, $this->id, $gamedata->id, $this->firingMode, $this->ammunition, $gamedata->turn);
        }
    
        public function setMinDamage(){     $this->minDamage = 6;      }
        public function setMaxDamage(){     $this->maxDamage = 24;      }
}//endof EWHeavyRocketLauncher


// END BALLISTIC WEAPONS


?>