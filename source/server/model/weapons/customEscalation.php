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
	
        public $intercept = 1;	
        public $loadingtimeArray = array(1=>2, 2=>2); //mode 1 should be the one with longest loading time
        public $rangePenaltyArray = array(1=>0.33, 2=>0.5);
        public $fireControlArray = array( 1=>array(2, 4, 5), 2=>array(2, 4, 5) ); 
	
		public $weaponClassArray = array(1=>'Particle', 2=>'Particle');
		public $firingModes = array(1=>'Dual', 2=>'Particle Cannons');
		public $damageTypeArray = array(1=>'Raking', 2=>'Raking'); 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
			//maxhealth and power reqirement are fixed; left option to override with hand-written values
			if ( $maxhealth == 0 ){
				$maxhealth = 12;
			}
			if ( $powerReq == 0 ){
				$powerReq = 14;
			}
			parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
	
        public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
			$this->data["Special"] = 'Can fire as either a Particle Lance or two Particle Cannons.';
        }
	
        public function getDamage($fireOrder){ 
		switch($this->firingMode){
			case 1:
				return Dice::d(10, 3)+20; //ParticleLance
				break;
			case 2:
				return Dice::d(10, 2)+15; //ParticleCannon
				break;	
		}
	}
        public function setMinDamage(){ 
		switch($this->firingMode){
			case 1:
				$this->minDamage = 23; //ParticleLance
				break;
			case 2:
				$this->minDamage = 17; //ParticleCannon
				break;	
		}
		$this->minDamageArray[$this->firingMode] = $this->minDamage;
	}
        public function setMaxDamage(){
		switch($this->firingMode){
			case 1:
				$this->maxDamage = 50; //ParticleLance
				break;
			case 2:
				$this->maxDamage = 35; //LightParticleCannon
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
        public $priority = 4;

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
        public $priority = 3;

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


    class EWParticleMaul extends Particle{
        public $trailColor = array(30, 170, 255);

        public $name = "EWParticleMaul";
        public $displayName = "Particle Maul";
	    public $iconPath = "EWParticleMaul.png";		
		public $animation = "bolt";
        public $animationColor = array(255, 250, 230);
        public $animationExplosionScale = 0.10;
        public $animationWidth = 1.5;
        public $animationWidth2 = 0.2;

        public $loadingtime = 3;
        public $priority = 5;

        public $rangePenalty = 0.5;
        public $fireControl = array(-1, 1, 2); // fighters, <mediums, <capitals

        public $damageType = "Standard"; 
        public $weaponClass = "Particle";
        public $firingModes = array( 1 => "Standard");
        
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 8;
            }
            if ( $powerReq == 0 ){
                $powerReq = 3;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }


        public function getDamage($fireOrder){ return Dice::d(10, 1)+12;   }
        public function setMinDamage(){     $this->minDamage = 13 ;      }
        public function setMaxDamage(){     $this->maxDamage = 22 ;      }
    }  //endof EWParticleMaul
	
	
	    class EWGatlingParticleBeam extends Particle{
        public $trailColor = array(30, 170, 255);

        public $name = "EWGatlingParticleBeam";
        public $displayName = "Gatling Particle Beam";
	    public $iconPath = "EWGatlingParticleBeam.png";		
		public $animation = "beam";
        public $animationColor = array(255, 250, 230);
        public $animationExplosionScale = 0.10;
        public $animationWidth = 1.5;
        public $animationWidth2 = 0.2;
		public $guns = 3;

        public $intercept = 1;
        public $loadingtime = 1;
        public $priority = 4;

        public $rangePenalty = 2;
        public $fireControl = array(5, 2, 2); // fighters, <mediums, <capitals

        public $damageType = "Standard"; 
        public $weaponClass = "Particle";
        public $firingModes = array( 1 => "Standard");
        
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 4;
            }
            if ( $powerReq == 0 ){
                $powerReq = 3;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }


        public function getDamage($fireOrder){ return Dice::d(10, 1)+2;   }
        public function setMinDamage(){     $this->minDamage = 3 ;      }
        public function setMaxDamage(){     $this->maxDamage = 12 ;      }
    }  //endof EWGatlingParticleBeam



// END PARTICLE WEAPONS


// LASER WEAPONS


class EWGatlingLaser extends Pulse{

        public $name = "EWGatlingLaser";
        public $displayName = "Gatling Laser";
		public $iconPath = "EWGatlingLaser.png"; 
        public $animation = "bolt";
        public $trailLength = 12;
        public $animationWidth = 4;
        public $projectilespeed = 9;
        public $animationExplosionScale = 0.10;
        public $rof = 2;
        public $grouping = 20;
        public $maxpulses = 5;
        public $uninterceptable = true; // This is a laser        
        public $loadingtime = 2;
        public $intercept = 2; 
		public $ballisticIntercept = true;
        public $priority = 5; // 
	protected $useDie = 3; //die used for base number of hits

        public $rangePenalty = 1;
        public $fireControl = array(1, 1, 2); // fighters, <mediums, <capitals

		public $firingMode = "Laser";
        public $damageType = "Pulse"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
        public $weaponClass = "Pulse";

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            if ( $maxhealth == 0 ) $maxhealth = 7;
            if ( $powerReq == 0 ) $powerReq = 4;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            $this->data["Special"] .= "<br>Uninterceptable";
        }

        public function getDamage($fireOrder){ return Dice::d(10, 1)+4; }
        public function setMinDamage(){ $this->minDamage = 5 ; }
        public function setMaxDamage(){ $this->maxDamage = 14 ; }		
		
    } // endof EWGatlingLaser


class EWHeavyGatlingLaser extends Pulse{

        public $name = "EWHeavyGatlingLaser";
        public $displayName = "Heavy Gatling Laser";
		public $iconPath = "EWHeavyGatlingLaser.png"; 
        public $animation = "bolt";
        public $trailLength = 12;
        public $animationWidth = 4;
        public $projectilespeed = 9;
        public $animationExplosionScale = 0.10;
        public $rof = 2;
        public $grouping = 20;
        public $maxpulses = 5;
        public $uninterceptable = true; // This is a laser        
        public $loadingtime = 2;
        public $intercept = 2; 
		public $ballisticIntercept = true;
        public $priority = 6; // 
	protected $useDie = 3; //die used for base number of hits

        public $rangePenalty = 0.5;
        public $fireControl = array(1, 1, 2); // fighters, <mediums, <capitals

		public $firingMode = "Laser";
        public $damageType = "Pulse"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
        public $weaponClass = "Pulse";

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            if ( $maxhealth == 0 ) $maxhealth = 8;
            if ( $powerReq == 0 ) $powerReq = 6;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            $this->data["Special"] .= "<br>Uninterceptable";
        }

        public function getDamage($fireOrder){ return Dice::d(10, 2)+5; }
        public function setMinDamage(){ $this->minDamage = 7 ; }
        public function setMaxDamage(){ $this->maxDamage = 25 ; }		
		
    } // endof EWHeavyGatlingLaser


    class EWLightLaserBeam extends LinkedWeapon{

        public $name = "EWLightLaserBeam";
        public $iconPath = "EWLightLaserBeam.png";
        public $displayName = "Light Laser Beam";
        public $animation = "laser";
        public $animationColor = array(220, 100, 30);
        public $animationExplosionScale = 0.1;
        public $animationWidth = 1;
        public $animationWidth2 = 0.2;
        public $priority = 3;
        public $uninterceptable = true; // This is a laser        
		

        public $intercept = 2;

        public $loadingtime = 1;
        public $shots = 2;
        public $defaultShots = 2;

        public $rangePenalty = 1.5;
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals

        public $damageType = "Standard"; 
        public $weaponClass = "Laser"; 
        
        function __construct($startArc, $endArc, $damagebonus, $nrOfShots = 2){
            $this->damagebonus = $damagebonus;
            $this->defaultShots = $nrOfShots;
            $this->shots = $nrOfShots;
            $this->intercept = $nrOfShots;

            if ($damagebonus >= 3) $this->priority++; //heavier varieties fire later in the queue
            if ($damagebonus >= 5) $this->priority++;
            if ($damagebonus >= 7) $this->priority++;
			
            if($nrOfShots === 1){
                $this->iconPath = "EWLightLaserBeamSingle.png";
            }
            if($nrOfShots >2){//no special icon for more than 3 linked weapons
                $this->iconPath = "lightParticleBeam3.png";
            }
			
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
			$this->data["Special"] = 'Laser. Uninterceptable. Able to intercept.';
        }

        public function getDamage($fireOrder){        return Dice::d(6)+$this->damagebonus;   }
        public function setMinDamage(){     $this->minDamage = 1+$this->damagebonus ;      }
        public function setMaxDamage(){     $this->maxDamage = 6+$this->damagebonus ;      }

    }  // endof EWLightLaserBeam


    class EWTwinLaserCannon extends Laser{
     
        public $name = "EWTwinLaserCannon";
        public $displayName = "Twin Laser Cannon";  
	    public $iconPath = "EWTwinLaserCannon.png";
	    
        public $animation = "laser";
        public $animationColor = array(255, 91, 91);
        public $animationExplosionScale = 0.16;
        public $animationWidth = 3;
        public $animationWidth2 = 0.3;
        public $priority = 7;
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
                $powerReq = 5;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){        return Dice::d(10, 2)+14;   }
        public function setMinDamage(){     $this->minDamage = 16 ;      }
        public function setMaxDamage(){     $this->maxDamage = 34 ;      }
    } //endof EWTwinLaserCannon



    class EWDefenseLaser extends Weapon{
        public $name = "EWDefenseLaser";
        public $displayName = "Defense Laser";
        public $iconPath = "EWDefenseLaser.png"; 
        public $animationColor = array(255, 30, 30);
        public $animation = "bolt"; //a bolt, not beam
        public $animationExplosionScale = 0.15;
        public $projectilespeed = 25;
        public $animationWidth = 10;
        public $trailLength = 5;
        public $priority = 3; //light Standard weapons
        public $uninterceptable = true; // This is a laser

        public $loadingtime = 1;

        public $intercept = 2;
		public $ballisticIntercept = true;

        public $rangePenalty = 2;
        public $fireControl = array(3, null, null); // fighters, <mediums, <capitals

        public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
        public $weaponClass = "Laser";

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            if ( $maxhealth == 0 ) $maxhealth = 2;
            if ( $powerReq == 0 ) $powerReq = 1;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
		public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
			$this->data["Special"] = 'Uninterceptable and can intercept.'; 
		}

        public function getDamage($fireOrder){ return Dice::d(10, 1)+2; }
        public function setMinDamage(){ $this->minDamage = 3 ; }
        public function setMaxDamage(){ $this->maxDamage = 12 ; }

    } //endof class EWDefenseLaser



// END LASER WEAPONS



// PLASMA WEAPONS


    class EWPlasmaGun extends LinkedWeapon{
        public $trailColor = array(30, 170, 255);

        public $name = "EWPlasmaGun";
        public $iconPath = "EWPlasmaGun.png";
        public $displayName = "Plasma Gun";
        public $animation = "trail";
        public $animationColor = array(75, 250, 90);
        public $animationExplosionScale = 0.10;
        public $projectilespeed = 15;
        public $animationWidth = 1;
        public $trailLength = 5;
        public $priority = 3;
    	public $rangeDamagePenalty = 1;

        public $intercept = 1;

        public $loadingtime = 1;
        public $shots = 1;
        public $defaultShots = 1;

        public $rangePenalty = 2;
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals
        private $damagebonus = 0;

        public $damageType = "Standard"; 
        public $weaponClass = "Plasma"; 
        
        function __construct($startArc, $endArc, $damagebonus, $nrOfShots = 2){
            $this->damagebonus = $damagebonus;
            $this->defaultShots = $nrOfShots;
            $this->shots = $nrOfShots;
            $this->intercept = $nrOfShots;

            if ($damagebonus >= 3) $this->priority++; //heavier varieties fire later in the queue
            if ($damagebonus >= 5) $this->priority++;
            if ($damagebonus >= 7) $this->priority++;
			
            if($nrOfShots === 1){
                $this->iconPath = "EWPlasmaGun.png";
            }
            if($nrOfShots >2){//no special icon for more than 3 linked weapons
                $this->iconPath = "lightParticleBeam3.png";
            }
			
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
			$this->data["Special"] = 'Plasma weapon. Does less damage ';
			$this->data["Special"] .= '<br>over distance (1 per hex). Ignores half of armor.';			
        }
		
        public function getDamage($fireOrder){        return Dice::d(6)+$this->damagebonus;   }
        public function setMinDamage(){     $this->minDamage = 1+$this->damagebonus ;      }
        public function setMaxDamage(){     $this->maxDamage = 6+$this->damagebonus ;      }

    }  // endof EWPlasmaGun
	
	
	    class EWUltralightPlasmaGun extends LinkedWeapon{
        public $trailColor = array(30, 170, 255);

        public $name = "EWUltralightPlasmaGun";
        public $iconPath = "EWUltralightPlasmaGun.png";
        public $displayName = "Ultralight Plasma Gun";
        public $animation = "trail";
        public $animationColor = array(75, 250, 90);
        public $animationExplosionScale = 0.10;
        public $projectilespeed = 15;
        public $animationWidth = 1;
        public $trailLength = 5;
        public $priority = 3;
    	public $rangeDamagePenalty = 1;

        public $loadingtime = 1;
        public $shots = 2;
        public $defaultShots = 2;

        public $rangePenalty = 2;
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals
        private $damagebonus = 0;

        public $damageType = "Standard"; 
        public $weaponClass = "Plasma"; 
        
        function __construct($startArc, $endArc, $damagebonus, $nrOfShots = 2){
            $this->damagebonus = $damagebonus;
            $this->defaultShots = $nrOfShots;
            $this->shots = $nrOfShots;
            $this->intercept = $nrOfShots;

            if ($damagebonus >= 3) $this->priority++; //heavier varieties fire later in the queue
            if ($damagebonus >= 5) $this->priority++;
            if ($damagebonus >= 7) $this->priority++;
			
            if($nrOfShots === 1){
                $this->iconPath = "EWUltralightPlasmaGun.png";
            }
            if($nrOfShots >2){//no special icon for more than 3 linked weapons
                $this->iconPath = "lightParticleBeam3.png";
            }
			
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
        }

        public function getDamage($fireOrder){        return Dice::d(3)+$this->damagebonus;   }
        public function setMinDamage(){     $this->minDamage = 1+$this->damagebonus ;      }
        public function setMaxDamage(){     $this->maxDamage = 3+$this->damagebonus ;      }

    }  // endof EWUltralightPlasmaGun


class EWPointPlasmaGun extends Plasma{
    	public $name = "EWPointPlasmaGun";
        public $displayName = "Point Plasma Gun";
		public $iconPath = "EWPointPlasmaGun.png";
        public $animation = "trail";
        public $animationColor = array(75, 250, 90);
    	public $trailColor = array(75, 250, 90);
    	public $projectilespeed = 15;
        public $animationWidth = 1;
    	public $animationExplosionScale = 0.10;
    	public $trailLength = 10;
        public $priority = 4;
    	public $rangeDamagePenalty = 1;
        public $guns = 1;

        public $intercept = 1;
		public $ballisticIntercept = true;
    		        
        public $loadingtime = 1;
			
        public $rangePenalty = 2;
        public $fireControl = array(2, 1, 1); // fighters, <=mediums, <=capitals 


        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 3;
            }
            if ( $powerReq == 0 ){
                $powerReq = 1;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
		
    	public function getDamage($fireOrder){        return Dice::d(6, 2)+0;   }
        public function setMinDamage(){     $this->minDamage = 2 /*- $this->dp*/;      }
        public function setMaxDamage(){     $this->maxDamage = 12 /*- $this->dp*/;      }

}  // endof EWPointPlasmaGun

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
        public $priority = 5;		
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


class EWHeavyPlasmaGun extends Plasma{
    	public $name = "EWHeavyPlasmaGun";
        public $displayName = "Heavy Plasma Gun";
		public $iconPath = "EWHeavyPlasmaGun.png";
        public $animation = "trail";
        public $animationColor = array(75, 250, 90);
    	public $trailColor = array(75, 250, 90);
    	public $projectilespeed = 10;
        public $animationWidth = 3;
    	public $animationExplosionScale = 0.20;
    	public $trailLength = 15;
        public $priority = 5;		
    	public $rangeDamagePenalty = 1;
        public $guns = 1;

        public $loadingtime = 3;
			
        public $rangePenalty = 1;
        public $fireControl = array(-5, 1, 2); // fighters, <=mediums, <=capitals 


        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 5;
            }
            if ( $powerReq == 0 ){
                $powerReq = 3;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
		
    	public function getDamage($fireOrder){        return Dice::d(10, 2)+6;   }
        public function setMinDamage(){     $this->minDamage = 8 /*- $this->dp*/;      }
        public function setMaxDamage(){     $this->maxDamage = 26 /*- $this->dp*/;      }

}  // endof EWHeavyPlasmaGun



class EWPlasmaMine extends Plasma{
        public $name = "EWPlasmaMine";
        public $displayName = "Plasma Mine";
		    public $iconPath = "EWPlasmaMine.png";
        public $animation = "trail";
        public $trailColor = array(11, 224, 255);
        public $animationColor = array(50, 50, 50);
        public $animationExplosionScale = 0.2;
        public $projectilespeed = 12;
        public $animationWidth = 4;
        public $trailLength = 100;    
        public $useOEW = false;

        public $ballistic = true; //missile
        public $range = 30;
        public $distanceRange = 30;
        
        public $loadingtime = 3; // 1 turn
        public $rangePenalty = 0;
        public $fireControl = array(null, 0, 2); // fighters, <mediums, <capitals; INCLUDES BOTH LAUNCHER AND MISSILE DATA!
	    
	public $priority = 5; 
	    
	public $firingMode = 'Ballistic'; //firing mode - just a name essentially
	public $damageType = "Plasma"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    	public $weaponClass = "Plasma"; //should be Ballistic and Matter, but FV does not allow that. Instead decrease advanced armor encountered by 2 points (if any) (usually system does that, but it will account for Ballistic and not Matter)
	 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		        //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 7;
            if ( $powerReq == 0 ) $powerReq = 3;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
    	public function getDamage($fireOrder){        return Dice::d(10, 2)+0;   }
        public function setMinDamage(){     $this->minDamage = 2;      }
        public function setMaxDamage(){     $this->maxDamage = 20;      }
}//endof EWPlasmaMine




class EWPlasmaArc extends Raking{
	public $name = "EWPlasmaArc";
	public $displayName = "Plasma Arc";
    public $iconPath = "EWPlasmaArc.png";
	public $animation = "beam";
	public $animationColor = array(75, 250, 90);
	public $trailColor = array(75, 250, 90);
	public $projectilespeed = 20;
	public $animationWidth = 2;
	public $animationExplosionScale = 0.15;
	public $trailLength = 300;
	public $priority = 1;
		        
	public $raking = 5;
	public $loadingtime = 2;
	public $rangeDamagePenalty = 1;	
	public $rangePenalty = 1;
	public $fireControl = array(-3, 2, 2); // fighters, <=mediums, <=capitals 
	
	public $damageType = "Raking"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	public $weaponClass = "Plasma"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!

		public $firingModes = array(
			1 => "Raking"
		);
	
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		        //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 5;
            if ( $powerReq == 0 ) $powerReq = 4;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
	public function setSystemDataWindow($turn){		
		parent::setSystemDataWindow($turn);
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
	    $this->data["Special"] .= "Damage reduced by 1 point per hex.";
	    $this->data["Special"] .= "<br>Reduces armor of systems hit.";	
	    $this->data["Special"] .= "<br>Ignores half of armor.";	 //now handled by standard routines
	    $this->data["Special"] .= "<br>Does not ignore already pierced armor (eg. every rake needs to pierce armor anew, even to the same location).";
	}
		 
	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){
		parent::onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder);
		if (!$system->advancedArmor){//advanced armor prevents effect 
			$crit = new ArmorReduced(-1, $ship->id, $system->id, "ArmorReduced", $gamedata->turn);
			$crit->updated = true;
			    $crit->inEffect = false;
			    $system->criticals[] =  $crit;
		}
	}
	
	protected function doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata, $damageWasDealt, $location = null)
    {
		parent::doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata, $damageWasDealt, $location);
		$fireOrder->armorIgnored = array(); //clear armorIgnored array - next rake should be met with full armor value!
	}
	
    	public function getDamage($fireOrder){        return Dice::d(10, 1)+4;   }
        public function setMinDamage(){     $this->minDamage = 5;      }
        public function setMaxDamage(){     $this->maxDamage = 14;      }
		
}//endof class EWPlasmaArc




// END PLASMA WEAPONS




// BALLISTIC WEAPONS


class EWOMissileRack extends MissileLauncher
{
    public $name = "EWOMissileRack";
    public $displayName = "Class-O Missile Rack";
    public $range = 20;
    public $distanceRange = 60;
    public $loadingtime = 3;
    public $iconPath = "missile1.png";

    public $fireControl = array(1, 1, 1); // fighters, <mediums, <capitals 
    
    public function getDamage($fireOrder)
    {
        return 20;
    }
    public function setMinDamage(){     $this->minDamage = 20 ;}
    public function setMaxDamage(){     $this->maxDamage = 20 ;}     
} // end of EWOMissileRack


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
        
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
			$this->data["Special"] = 'Benefits from offensive EW.';			
        }
        
        public function getDamage($fireOrder){ 
		
			return Dice::d(6, 2)+2;   
		}

        public function setMinDamage(){     $this->minDamage = 4;      }
        public function setMaxDamage(){     $this->maxDamage = 14;      }
}//endof EWRocketLauncher



class EWRangedRocketLauncher extends Weapon{
        public $name = "EWRangedRocketLauncher";
        public $displayName = "Ranged Rocket Launcher";
		    public $iconPath = "EWRocketLauncher.png";
        public $animation = "trail";
        public $trailColor = array(11, 224, 255);
        public $animationColor = array(50, 50, 50);
        public $animationExplosionScale = 0.2;
        public $projectilespeed = 16;
        public $animationWidth = 4;
        public $trailLength = 100;    

        public $useOEW = true; //torpedo
        public $ballistic = true; //missile
        public $range = 30;
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
        
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
			$this->data["Special"] = 'Benefits from offensive EW.';			
        }

        public function getDamage($fireOrder){ 
		
			return Dice::d(6, 2)+2;   
		}

        public function setMinDamage(){     $this->minDamage = 4;      }
        public function setMaxDamage(){     $this->maxDamage = 14;      }
}//endof EWRangedRocketLauncher


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
        
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
			$this->data["Special"] = 'Benefits from offensive EW.';			
        }

        public function getDamage($fireOrder){ 
			return Dice::d(6, 2)+2;   
		}

        public function setMinDamage(){     $this->minDamage = 4;      }
        public function setMaxDamage(){     $this->maxDamage = 14;      }
}//endof EWDualRocketLauncher



class EWRangedDualRocketLauncher extends Weapon{
        public $name = "EWRangedDualRocketLauncher";
        public $displayName = "Ranged Dual Rocket Launcher";
		    public $iconPath = "EWDualRocketLauncher.png";
        public $animation = "trail";
        public $trailColor = array(11, 224, 255);
        public $animationColor = array(50, 50, 50);
        public $animationExplosionScale = 0.2;
        public $projectilespeed = 16;
        public $animationWidth = 4;
        public $trailLength = 100;    

        public $useOEW = true; //torpedo
        public $ballistic = true; //missile
        public $range = 30;
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
	    
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
			$this->data["Special"] = 'Benefits from offensive EW.';			
        }

        public function getDamage($fireOrder){ 
			return Dice::d(6, 2)+2;   
		}

        public function setMinDamage(){     $this->minDamage = 4;      }
        public function setMaxDamage(){     $this->maxDamage = 14;      }
}//endof EWRangedDualRocketLauncher


class EWHeavyRocketLauncher extends Weapon{
        public $name = "EWHeavyRocketLauncher";
        public $displayName = "Heavy Rocket Launcher";
		    public $iconPath = "EWHeavyRocketLauncher.png";
        public $animation = "trail";
        public $trailColor = array(11, 224, 255);
        public $animationColor = array(50, 50, 50);
        public $animationExplosionScale = 0.4;
        public $projectilespeed = 12;
        public $animationWidth = 6;
        public $trailLength = 100;    

        public $useOEW = true; //torpedo
        public $ballistic = true; //missile
        public $range = 25;
        
        public $loadingtime = 2; // 1 turn
        public $rangePenalty = 0;
        public $fireControl = array(0, 1, 2); // fighters, <mediums, <capitals; INCLUDES BOTH LAUNCHER AND MISSILE DATA!
	    
	public $priority = 6; //Standard weapon
	    
	public $firingMode = 'Ballistic'; //firing mode - just a name essentially
	public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    	public $weaponClass = "Ballistic"; //should be Ballistic and Matter, but FV does not allow that. Instead decrease advanced armor encountered by 2 points (if any) (usually system does that, but it will account for Ballistic and not Matter)
	 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		        //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 6;
            if ( $powerReq == 0 ) $powerReq = 2;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
			$this->data["Special"] = 'Benefits from offensive EW.';			
        }

        public function getDamage($fireOrder){ 
			return Dice::d(10, 2)+4;   
		}

        public function setMinDamage(){     $this->minDamage = 6;      }
        public function setMaxDamage(){     $this->maxDamage = 24;      }
}//endof EWHeavyRocketLauncher



class EWRangedDualHeavyRocketLauncher extends Weapon{
        public $name = "EWRangedDualHeavyRocketLauncher";
        public $displayName = "Ranged Dual Heavy Rocket Launcher";
		    public $iconPath = "EWRangedDualHeavyRocketLauncher.png";
        public $animation = "trail";
        public $trailColor = array(11, 224, 255);
        public $animationColor = array(50, 50, 50);
        public $animationExplosionScale = 0.4;
        public $projectilespeed = 16;
        public $animationWidth = 6;
        public $trailLength = 100;    

        public $useOEW = true; //torpedo
        public $ballistic = true; //missile
        public $range = 50;
        
        public $loadingtime = 1; // 1 turn
        public $rangePenalty = 0;
        public $fireControl = array(0, 1, 2); // fighters, <mediums, <capitals; INCLUDES BOTH LAUNCHER AND MISSILE DATA!
	    
	public $priority = 6; //Standard weapon
	    
	public $firingMode = 'Ballistic'; //firing mode - just a name essentially
	public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    	public $weaponClass = "Ballistic"; //should be Ballistic and Matter, but FV does not allow that. Instead decrease advanced armor encountered by 2 points (if any) (usually system does that, but it will account for Ballistic and not Matter)
	 
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		        //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 8;
            if ( $powerReq == 0 ) $powerReq = 4;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
	    
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
			$this->data["Special"] = 'Benefits from offensive EW.';			
        }

        public function getDamage($fireOrder){ 
			return Dice::d(10, 2)+4;   
		}

        public function setMinDamage(){     $this->minDamage = 6;      }
        public function setMaxDamage(){     $this->maxDamage = 24;      }
}//endof EWRangedDualHeavyRocketLauncher



class EWFighterTorpedoLauncher extends FighterMissileRack
{
    public $name = "EWFighterTorpedoLauncher";
    public $displayName = "Fighter Torpedo Launcher";
    public $loadingtime = 1;
    public $iconPath = "fighterTorpedo.png";
    public $rangeMod = 0;
    public $firingMode = 1;
    public $maxAmount = 0;
    protected $distanceRangeMod = 0;
	public $weaponClass = "Plasma";
    public $priority = 4; //priority: typical fighter weapon (correct for Light Ballistic Torpedo's 2d6)

    public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals 
    
    public $firingModes = array(
        1 => "LPM"
    );
    
    function __construct($maxAmount, $startArc, $endArc){
        parent::__construct($maxAmount, $startArc, $endArc);
        
        $LPMine = new EWLightPlasmaMine($startArc, $endArc, $this->fireControl);
        
        $this->missileArray = array(
            1 => $LPMine
        );
        
        $this->maxAmount = $maxAmount;
    }
    
}

class EWLightPlasmaMine extends MissileFB
{
    public $name = "EWLightPlasmaMine";
    public $missileClass = "LPM";
    public $displayName = "Light Plasma Mine";
    public $cost = 8;
    //public $surCharge = 0;
	public $damage = 10;
    public $amount = 0;
    public $range = 15;
    public $distanceRange = 15;
    public $hitChanceMod = 0;
    public $priority = 3;
	public $damageType = "Standard";
	public $weaponClass = "Plasma";
	
		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
			$this->data["Special"] = "Plasma. Ignores half of armor.";
		}
    
    function __construct($startArc, $endArc, $fireControl=null){
        parent::__construct($startArc, $endArc, $fireControl);
    }

    public function getDamage($fireOrder){        return Dice::d(6,2);   }
    public function setMinDamage(){     $this->minDamage = 2;      }
    public function setMaxDamage(){     $this->maxDamage = 12;      }        
} // end EWLightPlasmaMine






// END BALLISTIC WEAPONS


// GRAVITIC WEAPONS


class EWGraviticTractingRod extends SWDirectWeapon{
    /*StarWars Tractor Beam 
    */
    /*weapon that does no damage, but limits targets' maneuvrability next turn ('target held by tractor beam')
    */
    public $name = "EWGraviticTractingRod";
    public $displayName = "Gravitic Tracting Rod";
	
    public $priority = 10; //let's fire last
    public $loadingtime = 3;
    public $rangePenalty = 2;
    public $intercept = 0;
    public $fireControl = array(null, 2, 4); // can't fire at fighters, incompatible with crit behavior!
   
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
		
		parent::__construct($armor, 10, 6, $startArc, $endArc, $nrOfShots); //maxhealth and powerReq for single gun mount!
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
} //end of class EWGraviticTractingRod


// END GRAVITIC WEAPONS


// ELECTROMAGNETIC WEAPONS


    class EWEMTorpedo extends Torpedo{
        public $name = "EWEMTorpedo";
        public $displayName = "EM Torpedo";
        public $iconPath = "EWEMTorpedo.png";
        public $loadingtime = 3;
		public $specialRangeCalculation = true; //to inform front end that it should use weapon-specific range penalty calculation - such a method should be present in .js!
        
        public $weaponClass = "Electromagnetic"; //deals Electromagnetic, not Ballistic, damage. Should be Ballistic(Plasma), but I had to choose ;)
        public $damageType = "Flash"; 
        
        public $fireControl = array(-6, 1, 3); // fighters, <mediums, <capitals 
        
        public $trailColor = array(75, 230, 90);
        public $animation = "torpedo";
        public $animationColor = array(75, 230, 90);
        public $animationExplosionScale = 0.3;
        public $projectilespeed = 11;
        public $animationWidth = 10;
        public $trailLength = 10;
        public $priority = 1; //Flash! should strike first (?)
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 6;
            }
            if ( $powerReq == 0 ){
                $powerReq = 5;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
			$this->data["Special"] = "Ignores armor and deactivates power using systems.";
			$this->data["Special"] .= "<br>+4 to criticals and +2 to dropout rolls";
			$this->data["Special"] .= "<br>This weapon suffers range penalty (like direct fire weapons do), but only after first 20 hexes of distance.";

		}

		public $rangePenalty = 1; //-1 hex - BUT ONLY AFTER 20 HEXES

	    //override standard to skip first 20 hexes when calculating range penalty
	    public function calculateRangePenalty(OffsetCoordinate $pos, BaseShip $target)
	    {
			$targetPos = $target->getHexPos();
			$dis = mathlib::getDistanceHex($pos, $targetPos);
			$dis = max(0,$dis-20);//first 20 hexes are "free"

			$rangePenalty = ($this->rangePenalty * $dis);
			$notes = "shooter: " . $pos->q . "," . $pos->r . " target: " . $targetPos->q . "," . $targetPos->r . " dis: $dis, rangePenalty: $rangePenalty";
			return Array("rp" => $rangePenalty, "notes" => $notes);
	    }	

	//ignore armor; advanced armor halves effect (due to this weapon being Electromagnetic)
	public function getSystemArmourBase($target, $system, $gamedata, $fireOrder, $pos = null){
		if (WeaponEM::isTargetEMResistant($target,$system)){
			$returnArmour = parent::getSystemArmourBase($target, $system, $gamedata, $fireOrder, $pos);
			$returnArmour = floor($returnArmour/2);
			return $returnArmour;
		}else{
			return 0;
		}
	}

	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){
		$crit = null;
		if (WeaponEM::isTargetEMResistant($ship,$system)) return;
		if ($ship instanceof FighterFlight) 2;
		if ($system->powerReq > 0 || $system->canOffLine){
			$system->addCritical($ship->id, "ForcedOfflineOneTurn", $gamedata);
		}
		$system->criticalRollMod = 4;
	}
		
        public function getDamage($fireOrder){        return Dice::d(10, 2);   }
        public function setMinDamage(){     $this->minDamage = 2;      }
        public function setMaxDamage(){     $this->maxDamage = 20;      }
    
    }//endof class EWEMTorpedo
	
	

    class EWElectronPolarizer extends Weapon{
        public $name = "EWElectronPolarizer";
        public $displayName = "Electron Polarizer";
        public $iconPath = "EWElectronPolarizer.png";
        public $loadingtime = 4;
        
        public $weaponClass = "Electromagnetic"; //deals Electromagnetic, not Ballistic, damage. Should be Ballistic(Plasma), but I had to choose ;)
        public $damageType = "Flash"; 
        
		public $rangePenalty = 0.33; //-1 / 3 hexes
        public $fireControl = array(null, 2, 2); // fighters, <mediums, <capitals 
        
        public $trailColor = array(75, 230, 90);
        public $animation = "beam";
        public $animationColor = array(75, 230, 90);
        public $animationExplosionScale = 0.3;
//        public $projectilespeed = 11;
        public $animationWidth = 10;
        public $trailLength = 10;
        public $priority = 1; //Flash! should strike first (?)
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 8;
            }
            if ( $powerReq == 0 ){
                $powerReq = 5;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
			$this->data["Special"] = "+3 to criticals and +2 to dropout rolls";
		}

	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){
		$crit = null;
		if (WeaponEM::isTargetEMResistant($ship,$system)) return;
		if ($ship instanceof FighterFlight) 2;
		if ($system->powerReq > 0 || $system->canOffLine){
			$system->addCritical($ship->id, "ForcedOfflineOneTurn", $gamedata);
		}
		$system->criticalRollMod = 3;
	}
		
        public function getDamage($fireOrder){        return Dice::d(10, 5);   }
        public function setMinDamage(){     $this->minDamage = 5;      }
        public function setMaxDamage(){     $this->maxDamage = 50;      }
    
    }//endof class EWElectronPolarizer	
	


// END ELECTROMAGNETIC WEAPONS


// MATTER WEAPONS


    class EWLightGaussCannon extends MatterCannon
    {
        public $name = "EWLightGaussCannon";
        public $displayName = "Light Gauss Cannon";
        public $iconPath = "EWLightGaussCannon.png"; 		
        public $animation = "trail";
        public $animationColor = array(250, 250, 190);
        public $projectilespeed = 28;
        public $animationWidth = 3;
        public $animationExplosionScale = 0.15;
        public $trailLength = 6;
        
        public $loadingtime = 1;
        public $priority = 9; // Matter weapon	

        public $damageType = "Matter"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
        
        public $rangePenalty = 1;
        public $fireControl = array(-2, 2, 1); // fighters, <mediums, <capitals 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            if ( $maxhealth == 0 ) $maxhealth = 6;
            if ( $powerReq == 0 ) $powerReq = 3;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
        public function getDamage($fireOrder){ return Dice::d(10, 1)+3; }
        public function setMinDamage(){ $this->minDamage = 4 ; }
        public function setMaxDamage(){ $this->maxDamage = 13 ; }
    }


    class EWEarlyRailgun extends MatterCannon
    {
        public $name = "EWEarlyRailgun";
        public $displayName = "Early Railgun";
        public $iconPath = "EWEarlyRailgun.png"; 		
        public $animation = "trail";
        public $animationColor = array(250, 250, 190);
        public $projectilespeed = 28;
        public $animationWidth = 3;
        public $animationExplosionScale = 0.15;
        public $trailLength = 6;
        
        public $loadingtime = 3;
        public $priority = 9; // Matter weapon	

        public $damageType = "Matter"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
        
        public $rangePenalty = 0.5;
        public $fireControl = array(-3, 2, 2); // fighters, <mediums, <capitals 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            if ( $maxhealth == 0 ) $maxhealth = 9;
            if ( $powerReq == 0 ) $powerReq = 6;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
        public function getDamage($fireOrder){ return Dice::d(10, 2)+2; }
        public function setMinDamage(){ $this->minDamage = 4 ; }
        public function setMaxDamage(){ $this->maxDamage = 22 ; }
    }  // end EWEarlyRailgun


// END MATTER WEAPONS


?>