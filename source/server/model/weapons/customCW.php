<?php
/*file for Star Wars - Clone Wars universe weapons*/

    class CWHeavyTurbolaser extends Weapon{
     
        public $name = "CWHeavyTurbolaser";
        public $displayName = "Heavy Turbolaser";  
	    public $iconPath = "HeavyParticleBeam.png";
	    
        public $animation = "trail";
        public $animationColor = array(245, 0, 0);
		public $trailColor = array(245, 0, 0);
        public $animationExplosionScale = 0.16;
        public $animationWidth = 6;
//        public $animationWidth2 = 0.3;
		public $trailLength = 18;
        public $priority = 7;
        public $loadingtime = 2;
        public $intercept = 1;
        
        public $rangePenalty = 0.5; //-1 / 2 hexes
        public $fireControl = array(-4, 2, 4); // fighters, <mediums, <capitals 

		public $firingMode = "Standard";
        public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
        public $weaponClass = "Laser";
        public $uninterceptable = true; // This is a laser

		public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
			$this->data["Special"] = 'Uninterceptable and can intercept.'; 
		}
    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
	    //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 6;
            }
            if ( $powerReq == 0 ){
                $powerReq = 4;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){        return Dice::d(10, 2)+6;   }
        public function setMinDamage(){     $this->minDamage = 8 ;      }
        public function setMaxDamage(){     $this->maxDamage = 26 ;      }
    } //endof CWHeavyTurbolaser


    class CWTwinHeavyTurbolaser extends Weapon{
     
        public $name = "CWTwinHeavyTurbolaser";
        public $displayName = "Twin Heavy Turbolaser";  
	    public $iconPath = "heavyArray.png";
	    
        public $animation = "bolt";
        public $animationColor = array(245, 0, 0);
		public $trailColor = array(245, 0, 0);
        public $animationExplosionScale = 0.16;
        public $animationWidth = 6;
//        public $animationWidth2 = 0.3;
        public $projectilespeed = 20;
		public $trailLength = 18;
        public $priority = 7;
        public $loadingtime = 1;
        public $intercept = 1;
        
        public $rangePenalty = 0.5; //-1 / 2 hexes
        public $fireControl = array(-4, 2, 4); // fighters, <mediums, <capitals 

		public $firingMode = "Standard";
        public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
        public $weaponClass = "Laser";
        public $uninterceptable = true; // This is a laser

		public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
			$this->data["Special"] = 'Uninterceptable and can intercept.'; 
		}
    
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
        
        public function getDamage($fireOrder){        return Dice::d(10, 2)+6;   }
        public function setMinDamage(){     $this->minDamage = 8 ;      }
        public function setMaxDamage(){     $this->maxDamage = 26 ;      }
    } //endof CWTwinHeavyTurbolaser


    class CWQuadHeavyTurbolaser extends Weapon{
     
        public $name = "CWQuadHeavyTurbolaser";
        public $displayName = "Quad Heavy Turbolaser";  
	    public $iconPath = "quadParticleBeam.png";
	    
        public $animation = "trail";
        public $animationColor = array(245, 0, 0);
		public $trailColor = array(245, 0, 0);
        public $animationExplosionScale = 0.16;
        public $animationWidth = 6;
//        public $animationWidth2 = 0.3;
		public $trailLength = 18;
        public $priority = 7;
        public $loadingtime = 1;
		public $guns = 2;
        
        public $rangePenalty = 0.5; //-1 / 2 hexes
        public $fireControl = array(-4, 2, 4); // fighters, <mediums, <capitals 

		public $firingMode = "Standard";
        public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
        public $weaponClass = "Laser";
        public $uninterceptable = true; // This is a laser

		public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
			$this->data["Special"] = 'Uninterceptable and can intercept.'; 
		}
    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
	    //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 10;
            }
            if ( $powerReq == 0 ){
                $powerReq = 6;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){        return Dice::d(10, 2)+6;   }
        public function setMinDamage(){     $this->minDamage = 8 ;      }
        public function setMaxDamage(){     $this->maxDamage = 26 ;      }
    } //endof CWQuadHeavyTurbolaser


    class CWTurbolaser extends Weapon{
     
        public $name = "CWTurbolaser";
        public $displayName = "Turbolaser";  
	    public $iconPath = "stdParticleBeam.png";
	    
        public $animation = "trail";
        public $animationColor = array(245, 0, 0);
		public $trailColor = array(245, 0, 0);
        public $animationExplosionScale = 0.16;
        public $animationWidth = 6;
//        public $animationWidth2 = 0.3;
		public $trailLength = 18;
        public $priority = 6;
        public $loadingtime = 2;
        public $intercept = 1;
        
        public $rangePenalty = 0.66; //-2 / 3 hexes
        public $fireControl = array(-2, 2, 3); // fighters, <mediums, <capitals 

		public $firingMode = "Standard";
        public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
        public $weaponClass = "Laser";
        public $uninterceptable = true; // This is a laser

		public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
			$this->data["Special"] = 'Uninterceptable and can intercept.'; 
		}
    
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
        
        public function getDamage($fireOrder){        return Dice::d(10, 1)+6;   }
        public function setMinDamage(){     $this->minDamage = 7 ;      }
        public function setMaxDamage(){     $this->maxDamage = 16 ;      }

    } //endof CWTurbolaser


    class CWTwinTurbolaser extends Weapon{
     
        public $name = "CWTwinTurbolaser";
        public $displayName = "Twin Turbolaser";  
	    public $iconPath = "twinArray.png";
	    
        public $animation = "trail";
        public $animationColor = array(245, 0, 0);
		public $trailColor = array(245, 0, 0);
        public $animationExplosionScale = 0.16;
        public $animationWidth = 6;
//        public $animationWidth2 = 0.3;
		public $trailLength = 18;
        public $priority = 6;
        public $loadingtime = 1;
        public $intercept = 1;
        
        public $rangePenalty = 0.66; //-2 / 3 hexes
        public $fireControl = array(-2, 2, 3); // fighters, <mediums, <capitals 

		public $firingMode = "Standard";
        public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
        public $weaponClass = "Laser";
        public $uninterceptable = true; // This is a laser

		public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
			$this->data["Special"] = 'Uninterceptable and can intercept.'; 
		}
    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
	    //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 6;
            }
            if ( $powerReq == 0 ){
                $powerReq = 3;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){        return Dice::d(10, 1)+6;   }
        public function setMinDamage(){     $this->minDamage = 7 ;      }
        public function setMaxDamage(){     $this->maxDamage = 16 ;      }

    } //endof CWTwinTurbolaser


    class CWQuadTurbolaser extends Weapon{
     
        public $name = "CWQuadTurbolaser";
        public $displayName = "Quad Turbolaser";  
	    public $iconPath = "starwars/clonewars/quad_turbolaser.png";
	    
        public $animation = "trail";
        public $animationColor = array(245, 0, 0);
		public $trailColor = array(245, 0, 0);
        public $animationExplosionScale = 0.16;
        public $animationWidth = 6;
//        public $animationWidth2 = 0.3;
		public $trailLength = 18;
        public $priority = 6;
        public $loadingtime = 1;
        public $intercept = 1;
		public $guns = 2;
		
        public $rangePenalty = 0.66; //-2 / 3 hexes
        public $fireControl = array(-2, 2, 3); // fighters, <mediums, <capitals 

		public $firingMode = "Standard";
        public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
        public $weaponClass = "Laser";
        public $uninterceptable = true; // This is a laser

		public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
			$this->data["Special"] = 'Uninterceptable and can intercept.'; 
		}
    
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
        
        public function getDamage($fireOrder){        return Dice::d(10, 1)+6;   }
        public function setMinDamage(){     $this->minDamage = 7 ;      }
        public function setMaxDamage(){     $this->maxDamage = 16 ;      }

    } //endof CWQuadTurbolaser


    class CWLaserCannon extends Weapon{
     
        public $name = "CWLaserCannon";
        public $displayName = "Laser Cannon";  
	    public $iconPath = "lightLaser.png";
	    
        public $animation = "trail";
        public $animationColor = array(245, 0, 0);
		public $trailColor = array(245, 0, 0);
        public $animationExplosionScale = 0.16;
        public $animationWidth = 6;
//        public $animationWidth2 = 0.3;
		public $trailLength = 18;
        public $priority = 5;
        public $loadingtime = 2;
        public $intercept = 1;
        
        public $rangePenalty = 1; //-1 / hex
        public $fireControl = array(0, 2, 2); // fighters, <mediums, <capitals 

		public $firingMode = "Standard";
        public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
        public $weaponClass = "Laser";
        public $uninterceptable = true; // This is a laser

		public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
			$this->data["Special"] = 'Uninterceptable and can intercept.'; 
		}
    
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
        
        public function getDamage($fireOrder){        return Dice::d(10, 1)+4;   }
        public function setMinDamage(){     $this->minDamage = 5 ;      }
        public function setMaxDamage(){     $this->maxDamage = 14 ;      }

    } //endof CWLaserCannon


    class CWTwinLaserCannon extends Weapon{
     
        public $name = "CWTwinLaserCannon";
        public $displayName = "Twin Laser Cannon";  
	    public $iconPath = "EWTwinLaserCannon.png";
	    
        public $animation = "trail";
        public $animationColor = array(245, 0, 0);
		public $trailColor = array(245, 0, 0);
        public $animationExplosionScale = 0.16;
        public $animationWidth = 5;
//        public $animationWidth2 = 0.3;
		public $trailLength = 18;
        public $priority = 6;
        public $loadingtime = 1;
        public $intercept = 1;
        
        public $rangePenalty = 1; //-1 / hex
        public $fireControl = array(0, 2, 2); // fighters, <mediums, <capitals 

		public $firingMode = "Standard";
        public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
        public $weaponClass = "Laser";
        public $uninterceptable = true; // This is a laser

		public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
			$this->data["Special"] = 'Uninterceptable and can intercept.'; 
		}
    
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
        
        public function getDamage($fireOrder){        return Dice::d(10, 1)+4;   }
        public function setMinDamage(){     $this->minDamage = 5 ;      }
        public function setMaxDamage(){     $this->maxDamage = 14 ;      }

    } //endof CWTwinLaserCannon


    class CWQuadLaserCannon extends Weapon{
     
        public $name = "CWQuadLaserCannon";
        public $displayName = "Quad Laser Cannon";  
	    public $iconPath = "EWTwinLaserCannon.png";
	    
        public $animation = "trail";
        public $animationColor = array(245, 0, 0);
		public $trailColor = array(245, 0, 0);
        public $animationExplosionScale = 0.16;
        public $animationWidth = 6;
//        public $animationWidth2 = 0.3;
		public $trailLength = 18;
        public $priority = 5;
        public $loadingtime = 1;
        public $intercept = 1;
		public $guns = 2;
		
        public $rangePenalty = 1; //-1 / hex
        public $fireControl = array(0, 2, 2); // fighters, <mediums, <capitals 

		public $firingMode = "Standard";
        public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
        public $weaponClass = "Laser";
        public $uninterceptable = true; // This is a laser

		public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
			$this->data["Special"] = 'Uninterceptable and can intercept.'; 
		}
    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
	    //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 6;
            }
            if ( $powerReq == 0 ){
                $powerReq = 3;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){        return Dice::d(10, 1)+4;   }
        public function setMinDamage(){     $this->minDamage = 5 ;      }
        public function setMaxDamage(){     $this->maxDamage = 14 ;      }

    } //endof CWQuadLaserCannon


    class CWPointDefenseLaser extends Weapon{
     
        public $name = "CWPointDefenseLaser";
        public $displayName = "Point Defense Laser";  
	    public $iconPath = "NexusPointDefenseLaser.png";
	    
        public $animation = "trail";
        public $animationColor = array(245, 0, 0);
		public $trailColor = array(245, 0, 0);
        public $animationExplosionScale = 0.16;
        public $animationWidth = 6;
//        public $animationWidth2 = 0.3;
		public $trailLength = 18;
        public $priority = 4;
        public $loadingtime = 1;
        public $intercept = 2;
        
        public $rangePenalty = 2; //-2 / hex
        public $fireControl = array(4, null, null); // fighters, <mediums, <capitals 

		public $firingMode = "Standard";
        public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
        public $weaponClass = "Laser";
        public $uninterceptable = true; // This is a laser

		public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
			$this->data["Special"] = 'Uninterceptable and can intercept.'; 
		}
    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
	    //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 4;
            }
            if ( $powerReq == 0 ){
                $powerReq = 1;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){        return Dice::d(10, 1)+2;   }
        public function setMinDamage(){     $this->minDamage = 3 ;      }
        public function setMaxDamage(){     $this->maxDamage = 12 ;      }

    } //endof CWPointDefenseLaser


class CWConcussionMissile extends Torpedo{
        public $name = "CWConcussionMissile";
        public $displayName = "Concussion Missile";
		    public $iconPath = "starwars/mjsCapConcussion1.png";
        public $animation = "trail";
        public $trailColor = array(11, 224, 255);
        public $animationColor = array(50, 50, 50);
        public $animationExplosionScale = 0.2;
        public $projectilespeed = 12;
        public $animationWidth = 4;
        public $trailLength = 100;    

        public $useOEW = false; //torpedo
        public $ballistic = true; //missile
        public $range = 15;
		public $guns = 1;
        
        public $loadingtime = 2; // 1 turn
        public $rangePenalty = 0;
        public $fireControl = array(null, 1, 3); // fighters, <mediums, <capitals; INCLUDES BOTH LAUNCHER AND MISSILE DATA!
	    
	public $priority = 7; //Standard weapon
	    
	public $firingMode = 'Ballistic'; //firing mode - just a name essentially
	public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    	public $weaponClass = "Ballistic"; //should be Ballistic and Matter, but FV does not allow that. Instead decrease advanced armor encountered by 2 points (if any) (usually system does that, but it will account for Ballistic and not Matter)
	 
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		        //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 6;
            if ( $powerReq == 0 ) $powerReq = 0;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
			$this->data["Special"] = 'Benefits from offensive EW.';			
        }
        
        public function getDamage($fireOrder){        return 30;   }
        public function setMinDamage(){     $this->minDamage = 30;      }
        public function setMaxDamage(){     $this->maxDamage = 30;      }

}//endof CWConcussionMissile


class CWProtonTorpedo extends Torpedo{
        public $name = "CWProtonTorpedo";
        public $displayName = "Proton Torpedo";
		    public $iconPath = "starwars/mjsCapProton1.png";
        public $animation = "trail";
        public $trailColor = array(11, 224, 255);
        public $animationColor = array(50, 50, 50);
        public $animationExplosionScale = 0.2;
        public $projectilespeed = 12;
        public $animationWidth = 4;
        public $trailLength = 100;    

        public $useOEW = true; //torpedo
        public $ballistic = true; //missile
        public $range = 30;
		public $guns = 1;
        
        public $loadingtime = 2; // 1 turn
        public $rangePenalty = 0;
        public $fireControl = array(null, 1, 3); // fighters, <mediums, <capitals; INCLUDES BOTH LAUNCHER AND MISSILE DATA!
	    
	public $priority = 7; //Standard weapon
	    
	public $firingMode = 'Ballistic'; //firing mode - just a name essentially
	public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    	public $weaponClass = "Ballistic"; //should be Ballistic and Matter, but FV does not allow that. Instead decrease advanced armor encountered by 2 points (if any) (usually system does that, but it will account for Ballistic and not Matter)
	 
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		        //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 5;
            if ( $powerReq == 0 ) $powerReq = 3;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
			$this->data["Special"] = 'Benefits from offensive EW.';			
        }
        
        public function getDamage($fireOrder){        return 15;   }
        public function setMinDamage(){     $this->minDamage = 15;      }
        public function setMaxDamage(){     $this->maxDamage = 15;      }

}//endof CWConcussionMissile


class CWIonCannon extends Weapon{
	public $name = "CWIonCannon";
	public $displayName = "Ion Cannon";
    public $iconPath = "starwars/mjsIonMedium1.png";
		
	public $animation = "bolt"; //originally Laser, but Bolt seems more appropriate
	public $animationColor = array(158, 240, 255);
	public $animationExplosionScale = 0.30;
	/*
	public $trailColor = array(158, 240, 255);
	public $projectilespeed = 15;
	public $animationWidth = 2;
	public $animationWidth2 = 0.2;
	public $animationExplosionScale = 0.10;
	public $trailLength = 30;
	*/
//	public $noOverkill = true;
		        
	public $loadingtime = 2;
	public $priority = 10; //as antiship weapon, going last
	public $priorityAFArray = array(1=>3); //as antifighter weapon, going early
			
	public $rangePenalty = 1;
	public $fireControl = array(-1, 3, 3); // fighters, <=mediums, <=capitals 
	
	public $damageType = "Standard"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	public $weaponClass = "Electromagnetic"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!

	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}	      
		$this->data["Special"] .= "Effect depends on system hit:";    
		$this->data["Special"] .= "<br> - Structure: Reactor output reduced by 2."; 
		$this->data["Special"] .= "<br> - Powered system: forced shutdown next turn."; 
		$this->data["Special"] .= "<br> - Other system: critical roll forced (at +4)."; 
		$this->data["Special"] .= "<br> - Fighter: immediate dropout (excluding superheavy)."; 
//		$this->data["Special"] .= "<br>Automatically hits EM shield if interposed.";
		$this->data["Special"] .= "<br>Does not affect units protected by Advanced Armor.";  	
	}	
	
	//Burst Beams ignore armor; advanced armor halves effect (due to weapon being Electromagnetic)
//	public function getSystemArmourBase($target, $system, $gamedata, $fireOrder, $pos = null){
//		if (WeaponEM::isTargetEMResistant($target,$system)){
//			$returnArmour = parent::getSystemArmourBase($target, $system, $gamedata, $fireOrder, $pos);
//			$returnArmour = floor($returnArmour/2);
//			return $returnArmour;
//		}else{
//			return 0;
//		}
//	}
	
		
	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){
		$crit = null;
		
		if (!WeaponEM::isTargetEMResistant($ship,$system)){ //no effect at all vs Advanced Armor
			if ($system instanceof Fighter && !($ship->superheavy)){
				$crit = new DisengagedFighter(-1, $ship->id, $system->id, "DisengagedFighter", $gamedata->turn);
				$crit->updated = true;
				$crit->inEffect = true;
				$system->criticals[] =  $crit;
				$fireOrder->pubnotes .= " DROPOUT! ";
			}else if ($system instanceof Structure){
				$reactor = $ship->getSystemByName("Reactor");
				$crit = new OutputReduced2(-2, $ship->id, $reactor->id, "OutputReduced2", $gamedata->turn);
				$crit->updated = true;
				$reactor->criticals[] =  $crit;
			}else if ($system->powerReq > 0 || $system->canOffLine ){
				$system->addCritical($ship->id, "ForcedOfflineOneTurn", $gamedata);
			} else { //force critical roll at +4
				$system->forceCriticalRoll = true;
				$system->critRollMod += 4;
			}
		}
	}		

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		        //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 6;
            if ( $powerReq == 0 ) $powerReq = 3;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
    public function getDamage($fireOrder){        return Dice::d(10, 1)+6;   }
	public function setMinDamage(){     $this->minDamage = 7;      }
	public function setMaxDamage(){     $this->maxDamage = 16;      }

}//endof class CWIonCannon


class CWHeavyIonCannon extends Weapon{
	public $name = "CWHeavyIonCannon";
	public $displayName = "Heavy Ion Cannon";
    public $iconPath = "starwars/mjsIonHvy1.png";
		
	public $animation = "bolt"; //originally Laser, but Bolt seems more appropriate
	public $animationColor = array(158, 240, 255);
	public $animationExplosionScale = 0.30;
	/*
	public $trailColor = array(158, 240, 255);
	public $projectilespeed = 15;
	public $animationWidth = 2;
	public $animationWidth2 = 0.2;
	public $animationExplosionScale = 0.10;
	public $trailLength = 30;
	*/
//	public $noOverkill = true;
		        
	public $loadingtime = 3;
	public $priority = 10; //as antiship weapon, going last
	public $priorityAFArray = array(1=>3); //as antifighter weapon, going early
			
	public $rangePenalty = 0.66;
	public $fireControl = array(-2, 3, 3); // fighters, <=mediums, <=capitals 
	
	public $damageType = "Standard"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	public $weaponClass = "Electromagnetic"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!

	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}	      
		$this->data["Special"] .= "Effect depends on system hit:";    
		$this->data["Special"] .= "<br> - Structure: Reactor output reduced by 4."; 
		$this->data["Special"] .= "<br> - Powered system: forced shutdown next turn."; 
		$this->data["Special"] .= "<br> - Other system: critical roll forced (at +4)."; 
		$this->data["Special"] .= "<br> - Fighter: immediate dropout (excluding superheavy)."; 
//		$this->data["Special"] .= "<br>Automatically hits EM shield if interposed.";
		$this->data["Special"] .= "<br>Does not affect units protected by Advanced Armor.";  	
	}	
	
	//Burst Beams ignore armor; advanced armor halves effect (due to weapon being Electromagnetic)
//	public function getSystemArmourBase($target, $system, $gamedata, $fireOrder, $pos = null){
//		if (WeaponEM::isTargetEMResistant($target,$system)){
//			$returnArmour = parent::getSystemArmourBase($target, $system, $gamedata, $fireOrder, $pos);
//			$returnArmour = floor($returnArmour/2);
//			return $returnArmour;
//		}else{
//			return 0;
//		}
//	}
	
		
	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){
		$crit = null;
		
		if (!WeaponEM::isTargetEMResistant($ship,$system)){ //no effect at all vs Advanced Armor
			if ($system instanceof Fighter && !($ship->superheavy)){
				$crit = new DisengagedFighter(-1, $ship->id, $system->id, "DisengagedFighter", $gamedata->turn);
				$crit->updated = true;
				$crit->inEffect = true;
				$system->criticals[] =  $crit;
				$fireOrder->pubnotes .= " DROPOUT! ";
			}else if ($system instanceof Structure){
				$reactor = $ship->getSystemByName("Reactor");
				$crit = new OutputReduced4(-4, $ship->id, $reactor->id, "OutputReduced4", $gamedata->turn);
				$crit->updated = true;
				$reactor->criticals[] =  $crit;
			}else if ($system->powerReq > 0 || $system->canOffLine ){
				$system->addCritical($ship->id, "ForcedOfflineOneTurn", $gamedata);
			} else { //force critical roll at +4
				$system->forceCriticalRoll = true;
				$system->critRollMod += 4;
			}
		}
	}		

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		        //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 8;
            if ( $powerReq == 0 ) $powerReq = 4;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
    public function getDamage($fireOrder){        return Dice::d(10, 2)+6;   }
	public function setMinDamage(){     $this->minDamage = 8;      }
	public function setMaxDamage(){     $this->maxDamage = 26;      }

}//endof class CWHeavyIonCannon


class CWShield extends Shield implements DefensiveSystem{
    public $name = "CWShield";
    public $displayName = "Shield";
    public $iconPath = "shield.png";
//    public $boostable = true; //$this->boostEfficiency and $this->maxBoostLevel in __construct() 
//    public $baseOutput = 0; //base output, before boost
	
	
 	protected $possibleCriticals = array( //different than usual B5Wars shield
            16=>"OutputReduced1",
            23=>array("OutputReduced1", "OutputReduced1")
	);
	
    function __construct($armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc){
        // shieldfactor is handled as output.
        parent::__construct($armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc);
//	$this->baseOutput = $shieldFactor;
//	$this->boostEfficiency = $powerReq;
//	$this->maxBoostLevel = min(2,$shieldFactor); //maximum of +2 effect, costs $powerReq each - but can't more than double shield!
    }
	
//    public function onConstructed($ship, $turn, $phase){
//        parent::onConstructed($ship, $turn, $phase);
//		$this->tohitPenalty = 0;
//		$this->damagePenalty = $this->getOutput();
//    }
	
    public function getDefensiveHitChangeMod($target, $shooter, $pos, $turn, $weapon){ //no defensive hit chance change
            return 0;
    }
    private function checkIsFighterUnderShield($target, $shooter, $weapon){ //no flying under SW shield
        return false;
    }
	
    public function getDefensiveDamageMod($target, $shooter, $pos, $turn, $weapon){
        if($this->isDestroyed($turn-1) || $this->isOfflineOnTurn()) return 0; //destroyed shield gives no protection
        $output = $this->output; //+ $this->getBoostLevel($turn);
		//Ballistic, Matter, SWIon - passes through!
		//if($weapon->weaponClass == 'Ballistic' || $weapon->weaponClass == 'Matter' || $weapon->weaponClass == 'SWIon' || $weapon->weaponClass == 'Ramming') $output = 0;
		//BALANCE CHANGE - Matter weapons are affected at half efficiency (important vs eg. Orieni and Belt Alliance)
//		$output += $this->outputMod; //outputMod itself is negative!
//		if($weapon->weaponClass == 'Ballistic' || $weapon->weaponClass == 'SWIon' || $weapon->weaponClass == 'Ramming') $output = 0;
//		if($weapon->weaponClass == 'Matter') $output = ceil($output/2);
//		if($weapon->damageType == 'Raking') $output = 2*$output;//Raking - double effect!
		$output=max(0,$output); //no less than 0!
        return $output;
    }
	
    public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		//$this->output = $this->baseOutput + $this->getBoostLevel($turn); //handled in front end
//		$this->data["Basic Strength"] = $this->baseOutput;    
		/* standard shield text is misleading!	
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
		*/
		$this->data["Special"] = "Reduces damage done by incoming shots (by shield rating), but does not decrease profile."; 
		$this->data["Special"] .= "<br>Cannot be flown under."; 
//		$this->data["Special"] .= "<br>Does not protect from Ramming, Ballistic, StarWars Ion damage."; 
//		$this->data["Special"] .= "<br>Doubly effective vs Raking weapons."; 
//		$this->data["Special"] .= "<br>Half efficiency (round up) vs Matter weapons (game balance, irrelevant in-universe)."; 
//		$this->data["Special"] .= "<br>Can be boosted."; 
	}
	  
//        private function getBoostLevel($turn){
//            $boostLevel = 0;
//            foreach ($this->power as $i){
//                    if ($i->turn != $turn) continue;
//                    if ($i->type == 2){
//                            $boostLevel += $i->amount;
//                    }
//            }
 //           return $boostLevel;
//        }
	
} //endof class CWShield




    class CWLaserCannonsFtr extends LinkedWeapon{

        public $name = "CWLaserCannonsFtr";
        public $iconPath = "EWLightLaserBeam.png";
        public $displayName = "Laser Cannons";
		
        public $animation = "trail";
        public $animationColor = array(220, 60, 120);
		public $trailColor = array(220, 60, 120);
        public $animationExplosionScale = 0.1;
        public $animationWidth = 3;
//        public $animationWidth2 = 0.3;
		public $trailLength = 10;
        public $uninterceptable = true; // This is a laser        
        public $intercept = 2;
		public $ballisticIntercept = true;
		
        public $priority = 3;

        public $loadingtime = 1;
        public $shots = 2;
        public $defaultShots = 2;

        public $rangePenalty = 2;
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals

        public $damageType = "Standard"; 
        public $weaponClass = "Laser"; 
        private $damagebonus = 0;
        
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
			$this->data["Special"] = 'Laser. Uninterceptable. Able to intercept ballistics.';
        }

        public function getDamage($fireOrder){        return Dice::d(6)+$this->damagebonus;   }
        public function setMinDamage(){     $this->minDamage = 1+$this->damagebonus ;      }
        public function setMaxDamage(){     $this->maxDamage = 6+$this->damagebonus ;      }

    }  // endof CWLaserCannonsFtr




class CWFighterTorpedoLauncher extends FighterMissileRack
{
    public $name = "NexusFighterTorpedoLauncher";
    public $displayName = "Fighter Torpedo Launcher";
    public $loadingtime = 1;
    public $iconPath = "fighterTorpedo.png";
    public $rangeMod = 0;
    public $firingMode = 1;
    public $maxAmount = 0;
    protected $distanceRangeMod = 0;
	public $weaponClass = "Standard";
    public $priority = 4; //priority: typical fighter weapon (correct for Light Ballistic Torpedo's 2d6)

    public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals 
    
    public $firingModes = array(
        1 => "FCM"
    );
    
    function __construct($maxAmount, $startArc, $endArc){
        parent::__construct($maxAmount, $startArc, $endArc);
        
        $FCM = new CWConcussionFtr($startArc, $endArc, $this->fireControl);
        
        $this->missileArray = array(
            1 => $FCM
        );
        
        $this->maxAmount = $maxAmount;
    }
    
}


class CWConcussionFtr extends MissileFB
{
    public $name = "CWConcussionFtr";
    public $missileClass = "FCM";
    public $displayName = "Fighter Concussion Missile";
    public $cost = 8;
    //public $surCharge = 0;
	public $damage = 12;
    public $amount = 0;
    public $range = 6;
    public $distanceRange = 18;
    public $hitChanceMod = 0;
    public $priority = 3;
	public $damageType = "Standard";
	public $weaponClass = "Standard";
	
    function __construct($startArc, $endArc, $fireControl=null){
        parent::__construct($startArc, $endArc, $fireControl);
    }

    public function getDamage($fireOrder){        return 12;   }
    public function setMinDamage(){     $this->minDamage = 12;      }
    public function setMaxDamage(){     $this->maxDamage = 12;      }        
	
} // end CWConcussionFtr


class CWFighterProtonLauncher extends FighterMissileRack
{
    public $name = "NexusFighterTorpedoLauncher";
    public $displayName = "Fighter Torpedo Launcher";
    public $loadingtime = 1;
    public $iconPath = "fighterTorpedo.png";
    public $rangeMod = 0;
    public $firingMode = 1;
    public $maxAmount = 0;
    protected $distanceRangeMod = 0;
	public $weaponClass = "Standard";
    public $priority = 4; //priority: typical fighter weapon (correct for Light Ballistic Torpedo's 2d6)

    public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals 
    
    public $firingModes = array(
        1 => "FPT"
    );
    
    function __construct($maxAmount, $startArc, $endArc){
        parent::__construct($maxAmount, $startArc, $endArc);
        
        $FPT = new CWProtonFtr($startArc, $endArc, $this->fireControl);
        
        $this->missileArray = array(
            1 => $FPT
        );
        
        $this->maxAmount = $maxAmount;
    }
    
}

class CWProtonFtr extends MissileFB
{
    public $name = "CWProtonFtr";
    public $missileClass = "FPT";
    public $displayName = "Fighter Proton Torpedo";
    public $cost = 8;
    //public $surCharge = 0;
	public $damage = 10;
    public $amount = 0;
    public $range = 10;
    public $distanceRange = 30;
    public $hitChanceMod = 0;
    public $priority = 3;
	public $damageType = "Standard";
	public $weaponClass = "Standard";
	
    function __construct($startArc, $endArc, $fireControl=null){
        parent::__construct($startArc, $endArc, $fireControl);
    }

    public function getDamage($fireOrder){        return 10;   }
    public function setMinDamage(){     $this->minDamage = 10;      }
    public function setMaxDamage(){     $this->maxDamage = 10;      }        
	
} // end CWProtonFtr



/* class CWFtrConcussion extends FighterMissileRack
{
    public $name = "CWFtrConcussion";
    public $displayName = "Concussion Missile Launcher";
    public $loadingtime = 1;
    public $iconPath = "starwars/mjsLightConcussion1.png";
    public $rangeMod = 0;
    public $firingMode = 1;
    public $maxAmount = 0;
    public $cost = 8;
    public $damage = 12;
    public $amount = 0;
    public $range = 8;
    protected $distanceRangeMod = 0;

    public $priority = 3; //priority: typical fighter weapon (correct for Light Ballistic Torpedo's 2d6)

    public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals 
    
    public $firingModes = array(
        1 => "Con"
    );
    
    function __construct($maxAmount, $startArc, $endArc){
        parent::__construct($maxAmount, $startArc, $endArc);
        
        $ConM = new CWFtrConcussion($startArc, $endArc, $this->fireControl);
        
        $this->missileArray = array(
            1 => $ConM
        );
        
        $this->maxAmount = $maxAmount;
    }
    
}  */




?>