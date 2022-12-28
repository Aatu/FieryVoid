<?php
/*file for Nexus universe weapons*/


//BALLISTIC and TORPEDO WEAPONS

class NexusKineticBoxLauncher extends Weapon{
        public $name = "nexusKineticBoxLauncher";
        public $displayName = "Kinetic Box Launcher";
		    public $iconPath = "NexusKineticBoxLauncher.png";
        public $animation = "trail";
        public $trailColor = array(141, 240, 255);
        public $animationColor = array(50, 50, 50);
        public $animationExplosionScale = 0.2;
        public $projectilespeed = 10;
        public $animationWidth = 4;
        public $trailLength = 100;    

        public $useOEW = false; //missile
        public $ballistic = true; //missile
        public $range = 15;
        public $distanceRange = 30;
        public $ammunition = 4; //limited number of shots
	    
        
        public $loadingtime = 2; // 1/2 turns
        public $rangePenalty = 0;
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals; INCLUDES BOTH LAUNCHER AND MISSILE DATA!
	    
	public $noOverkill = true; //Matter weapon
	public $priority = 9; //Matter weapon
	    
	public $firingMode = 'Ballistic'; //firing mode - just a name essentially
	public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    	public $weaponClass = "Matter"; //should be Ballistic and Matter, but FV does not allow that. Instead decrease advanced armor encountered by 2 points (if any) (usually system does that, but it will account for Ballistic and not Matter)
	 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		        //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 4;
            if ( $powerReq == 0 ) $powerReq = 0;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function stripForJson() {
            $strippedSystem = parent::stripForJson();    
            $strippedSystem->ammunition = $this->ammunition;           
            return $strippedSystem;
        }
        
	    
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            $this->data["Special"] = "Ignores armor, no overkill (Ballistic+Matter weapon).";
            $this->data["Ammunition"] = $this->ammunition;
        }
        
        public function getDamage($fireOrder){
            $dmg = 8;
            return $dmg;
       }
        public function setAmmo($firingMode, $amount){
            $this->ammunition = $amount;
        }
       public function fire($gamedata, $fireOrder){ //note ammo usage
            parent::fire($gamedata, $fireOrder);
            $this->ammunition--;
            Manager::updateAmmoInfo($fireOrder->shooterid, $this->id, $gamedata->id, $this->firingMode, $this->ammunition, $gamedata->turn);
        }
    
        public function setMinDamage(){     $this->minDamage = 8;      }
        public function setMaxDamage(){     $this->maxDamage = 8;      }
}//endof NexusKineticBoxLauncher



class NexusRangedKineticBoxLauncher extends Weapon{
        public $name = "NexusRangedKineticBoxLauncher";
        public $displayName = "Ranged Kinetic Box Launcher";
		    public $iconPath = "NexusKineticBoxLauncher.png";
        public $animation = "trail";
        public $trailColor = array(141, 240, 255);
        public $animationColor = array(50, 50, 50);
        public $animationExplosionScale = 0.2;
        public $projectilespeed = 10;
        public $animationWidth = 4;
        public $trailLength = 100;    

        public $useOEW = false; //missile
        public $ballistic = true; //missile
        public $range = 45;
        public $distanceRange = 60;
        public $ammunition = 10; //limited number of shots
        
        public $loadingtime = 2; // 1/2 turns
        public $rangePenalty = 0;
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals; INCLUDES BOTH LAUNCHER AND MISSILE DATA!
	    
	public $noOverkill = true; //Matter weapon
	public $priority = 9; //Matter weapon
	    
	public $firingMode = 'Ballistic'; //firing mode - just a name essentially
	public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    	public $weaponClass = "Matter"; //should be Ballistic and Matter, but FV does not allow that. Instead decrease advanced armor encountered by 2 points (if any) (usually system does that, but it will account for Ballistic and not Matter)
	 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		        //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 10;
            if ( $powerReq == 0 ) $powerReq = 0;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function stripForJson() {
            $strippedSystem = parent::stripForJson();    
            $strippedSystem->ammunition = $this->ammunition;           
            return $strippedSystem;
        }
        
	    
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            $this->data["Special"] = "Ignores armor, no overkill (Ballistic+Matter weapon).";
            $this->data["Ammunition"] = $this->ammunition;
        }
        
        public function getDamage($fireOrder){
            $dmg = 8;
            return $dmg;
       }
        public function setAmmo($firingMode, $amount){
            $this->ammunition = $amount;
        }
       public function fire($gamedata, $fireOrder){ //note ammo usage
            parent::fire($gamedata, $fireOrder);
            $this->ammunition--;
            Manager::updateAmmoInfo($fireOrder->shooterid, $this->id, $gamedata->id, $this->firingMode, $this->ammunition, $gamedata->turn);
        }
    
        public function setMinDamage(){     $this->minDamage = 8;      }
        public function setMaxDamage(){     $this->maxDamage = 8;      }
}//endof NexusRangedKineticBoxLauncher



class NexusAdvKineticBoxLauncher extends Weapon{
        public $name = "NexusAdvKineticBoxLauncher";
        public $displayName = "Advanced Kinetic Box Launcher";
		    public $iconPath = "NexusAdvKineticBoxLauncher.png";
        public $animation = "trail";
        public $trailColor = array(11, 224, 255);
        public $animationColor = array(50, 50, 50);
        public $animationExplosionScale = 0.2;
        public $projectilespeed = 12;
        public $animationWidth = 4;
        public $trailLength = 100;    

        public $useOEW = false; //missile
        public $ballistic = true; //missile
        public $range = 15;
        public $distanceRange = 30;
        public $ammunition = 5; //limited number of shots
	    
        
        public $loadingtime = 2; // 1/2 turns
        public $rangePenalty = 0;
        public $fireControl = array(2, 2, 2); // fighters, <mediums, <capitals; INCLUDES BOTH LAUNCHER AND MISSILE DATA!
	    
	public $noOverkill = true; //Matter weapon
	public $priority = 9; //Matter weapon
	    
	public $firingMode = 'Ballistic'; //firing mode - just a name essentially
	public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    	public $weaponClass = "Matter"; //should be Ballistic and Matter, but FV does not allow that. Instead decrease advanced armor encountered by 2 points (if any) (usually system does that, but it will account for Ballistic and not Matter)
	 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		        //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 5;
            if ( $powerReq == 0 ) $powerReq = 0;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function stripForJson() {
            $strippedSystem = parent::stripForJson();    
            $strippedSystem->ammunition = $this->ammunition;           
            return $strippedSystem;
        }
        
	    
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            $this->data["Special"] = "Ignores armor, no overkill (Ballistic+Matter weapon).";
            $this->data["Ammunition"] = $this->ammunition;
        }
        
        public function getDamage($fireOrder){
            $dmg = 8;
            return $dmg;
       }
        public function setAmmo($firingMode, $amount){
            $this->ammunition = $amount;
        }
       public function fire($gamedata, $fireOrder){ //note ammo usage
            parent::fire($gamedata, $fireOrder);
            $this->ammunition--;
            Manager::updateAmmoInfo($fireOrder->shooterid, $this->id, $gamedata->id, $this->firingMode, $this->ammunition, $gamedata->turn);
        }
    
        public function setMinDamage(){     $this->minDamage = 8;      }
        public function setMaxDamage(){     $this->maxDamage = 8;      }
}//endof NexusAdvKineticBoxLauncher



class NexusLaserMissile extends Laser{
        public $name = "NexusLaserMissile";
        public $displayName = "Laser Missile";
		    public $iconPath = "NexusLaserMissile.png";
        public $animationArray = array(1=>'laser', 2=>'laser');
        public $trailColor = array(141, 240, 255);
        public $animationColorArray = array(1=>array(220, 60, 120), array(220, 60, 120));
//        public $animationExplosionScale = 0.5;
        public $projectilespeed = 10;
//        public $animationWidth = 5;
        public $trailLength = 100;    
        public $uninterceptableArray = array(1=>false, 2=>false); 
		
		public $doInterceptDegradation = true; //Will be intercepted with normal degradation even though a ballistic
        public $useOEW = false; //missile
        public $ballistic = true; //missile
        public $rangeArray = array(1=>20, 2=>10);
        public $distanceRangeArray = array(1=>30, 2=>15);
        public $ammunition = 20; //limited number of shots
	    
        public $loadingtimeArray = array(1=>2, 2=>2); // 1/2 turns
        public $rangePenaltyArray = array(1=>0, 2=>0);
        public $fireControlArray = array(1=>array(null, 2, 2), 2=>array(null, 2, 2)); // fighters, <mediums, <capitals; INCLUDES BOTH LAUNCHER AND MISSILE DATA!
	    
        public $raking = 8;
		public $priorityArray = array(1=>8, 2=>8); 
	    
		public $firingModes = array(1=>'Long-range', 2=>'Short-range'); //firing mode - just a name essentially
		public $damageTypeArray = array(1=>'Raking', 2=>'Raking'); //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
//    	public $weaponClass = "Ballistic"; //should be Ballistic and Matter, but FV does not allow that. Instead decrease advanced armor encountered by 2 points (if any) (usually system does that, but it will account for Ballistic and not Matter)
	 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		        //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 6;
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
            $this->data["Special"] = "Bomb-pumped laser. Ballistic weapon that scores raking (8) damage.";
            $this->data["Special"] .= "<br>Long-range: 20 hex launch and 30 hex max range, 2d10+2 damage.";
            $this->data["Special"] .= "<br>Short-range: 10 hex launch and 15 hex max range, 3d10+4 damage.";
            $this->data["Ammunition"] = $this->ammunition;
        }
        
        public function setAmmo($firingMode, $amount){
            $this->ammunition = $amount;
        }
       public function fire($gamedata, $fireOrder){ //note ammo usage
            parent::fire($gamedata, $fireOrder);
            $this->ammunition--;
            Manager::updateAmmoInfo($fireOrder->shooterid, $this->id, $gamedata->id, $this->firingMode, $this->ammunition, $gamedata->turn);
        }

        public function getDamage($fireOrder){ 
		switch($this->firingMode){
			case 1:
				return Dice::d(10, 2)+3; //Light Chemical Laser
				break;
			case 2:
				return Dice::d(10, 3)+4; //Medium Chemical Laser
				break;	
		}
	}
        public function setMinDamage(){ 
		switch($this->firingMode){
			case 1:
				$this->minDamage = 5; //Light Chemical Laser
				break;
			case 2:
				$this->minDamage = 7; //Medium Chemical Laser
				break;	
		}
		$this->minDamageArray[$this->firingMode] = $this->minDamage;
	}
        public function setMaxDamage(){
		switch($this->firingMode){
			case 1:
				$this->maxDamage = 23; //Light Chemical Laser
				break;
			case 2:
				$this->maxDamage = 34; //Medium Chemical Laser
				break;	
		}
		$this->maxDamageArray[$this->firingMode] = $this->maxDamage;
	}
    
//        public function getDamage($fireOrder){ return Dice::d(10, 2)+2;   }
//        public function setMinDamage(){     $this->minDamage = 5;      }
//        public function setMaxDamage(){     $this->maxDamage = 23;      }
		
}//endof NexusLaserMissile


class NexusHeavyLaserMissile extends Laser{
        public $name = "NexusHeavyLaserMissile";
        public $displayName = "Heavy Laser Missile";
		    public $iconPath = "NexusHeavyLaserMissile.png";
        public $animationArray = array(1=>'laser', 2=>'laser');
        public $trailColor = array(141, 240, 255);
        public $animationColorArray = array(1=>array(220, 60, 120), array(220, 60, 120));
//        public $animationExplosionScale = 0.5;
        public $projectilespeed = 10;
//        public $animationWidth = 5;
        public $trailLength = 100;    
        public $uninterceptableArray = array(1=>false, 2=>false); 
		
		public $doInterceptDegradation = true; //Will be intercepted with normal degradation even though a ballistic
        public $useOEW = false; //missile
        public $ballistic = true; //missile
        public $rangeArray = array(1=>50, 2=>20);
        public $distanceRangeArray = array(1=>60, 2=>30);
        public $ammunition = 30; //limited number of shots
	    
        public $loadingtimeArray = array(1=>2, 2=>2); // 1/2 turns
        public $rangePenaltyArray = array(1=>0, 2=>0);
        public $fireControlArray = array(1=>array(null, 2, 2), 2=>array(null, 2, 2)); // fighters, <mediums, <capitals; INCLUDES BOTH LAUNCHER AND MISSILE DATA!
	    
        public $raking = 8;
		public $priorityArray = array(1=>8, 2=>8); 
	    
		public $firingModes = array(1=>'Long-range', 2=>'Short-range'); //firing mode - just a name essentially
		public $damageTypeArray = array(1=>'Raking', 2=>'Raking'); //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
//    	public $weaponClass = "Ballistic"; //should be Ballistic and Matter, but FV does not allow that. Instead decrease advanced armor encountered by 2 points (if any) (usually system does that, but it will account for Ballistic and not Matter)
	 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		        //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 6;
            if ( $powerReq == 0 ) $powerReq = 3;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function stripForJson() {
            $strippedSystem = parent::stripForJson();    
            $strippedSystem->ammunition = $this->ammunition;           
            return $strippedSystem;
        }
	    
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            $this->data["Special"] = "Bomb-pumped laser. Ballistic weapon that scores raking (8) damage.";
            $this->data["Special"] .= "<br>Long-range: 50 hex launch and 60 hex max range, 2d10+2 damage.";
            $this->data["Special"] .= "<br>Short-range: 20 hex launch and 30 hex max range, 3d10+4 damage.";
            $this->data["Ammunition"] = $this->ammunition;
        }
        
        public function setAmmo($firingMode, $amount){
            $this->ammunition = $amount;
        }
       public function fire($gamedata, $fireOrder){ //note ammo usage
            parent::fire($gamedata, $fireOrder);
            $this->ammunition--;
            Manager::updateAmmoInfo($fireOrder->shooterid, $this->id, $gamedata->id, $this->firingMode, $this->ammunition, $gamedata->turn);
        }

        public function getDamage($fireOrder){ 
		switch($this->firingMode){
			case 1:
				return Dice::d(10, 2)+3; //Light Chemical Laser
				break;
			case 2:
				return Dice::d(10, 3)+4; //Medium Chemical Laser
				break;	
		}
	}
        public function setMinDamage(){ 
		switch($this->firingMode){
			case 1:
				$this->minDamage = 5; //Light Chemical Laser
				break;
			case 2:
				$this->minDamage = 7; //Medium Chemical Laser
				break;	
		}
		$this->minDamageArray[$this->firingMode] = $this->minDamage;
	}
        public function setMaxDamage(){
		switch($this->firingMode){
			case 1:
				$this->maxDamage = 23; //Light Chemical Laser
				break;
			case 2:
				$this->maxDamage = 34; //Medium Chemical Laser
				break;	
		}
		$this->maxDamageArray[$this->firingMode] = $this->maxDamage;
	}
    
		
}//endof NexusHeavyLaserMissile



class NexusLargeRocket extends Weapon{
        public $name = "NexusLargeRocket";
        public $displayName = "Large Rocket Launcher";
		    public $iconPath = "NexusLargeRocket.png";
        public $animation = "trail";
        public $trailColor = array(11, 224, 255);
        public $animationColor = array(50, 50, 50);
        public $animationExplosionScale = 0.2;
        public $projectilespeed = 12;
        public $animationWidth = 4;
        public $trailLength = 100;    

        public $useOEW = true; //torpedo
        public $ballistic = true; //missile
        public $range = 20;
        public $distanceRange = 30;
        public $ammunition = 10; //limited number of shots
        
        public $loadingtime = 3; // 1 turn
        public $rangePenalty = 0;
        public $fireControl = array(-2, 1, 3); // fighters, <mediums, <capitals; INCLUDES BOTH LAUNCHER AND MISSILE DATA!
	    
	public $priority = 6; //Standard weapon
	    
	public $firingMode = 'Ballistic'; //firing mode - just a name essentially
	public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    	public $weaponClass = "Ballistic"; //should be Ballistic and Matter, but FV does not allow that. Instead decrease advanced armor encountered by 2 points (if any) (usually system does that, but it will account for Ballistic and not Matter)
	 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		        //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 9;
            if ( $powerReq == 0 ) $powerReq = 3;
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
			$this->data["Special"] = '<br>Benefits from offensive EW.';			
        }

        public function getDamage($fireOrder){ 
			return Dice::d(10, 2)+10;   
		}

        public function setAmmo($firingMode, $amount){
            $this->ammunition = $amount;
        }
       public function fire($gamedata, $fireOrder){ //note ammo usage
            parent::fire($gamedata, $fireOrder);
            $this->ammunition--;
            Manager::updateAmmoInfo($fireOrder->shooterid, $this->id, $gamedata->id, $this->firingMode, $this->ammunition, $gamedata->turn);
        }
    
        public function setMinDamage(){     $this->minDamage = 12;      }
        public function setMaxDamage(){     $this->maxDamage = 30;      }
}//endof NexusLargeRocket


class NexusStandardRocket extends Weapon{
        public $name = "NexusStandardRocket";
        public $displayName = "Standard Rocket Launcher";
		    public $iconPath = "NexusStandardRocket.png";
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
        public $distanceRange = 25;
        public $ammunition = 15; //limited number of shots
        
        public $loadingtime = 2; // 1 turn
        public $rangePenalty = 0;
        public $fireControl = array(-1, 1, 2); // fighters, <mediums, <capitals; INCLUDES BOTH LAUNCHER AND MISSILE DATA!
	    
	public $priority = 6; //Standard weapon
	    
	public $firingMode = 'Ballistic'; //firing mode - just a name essentially
	public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    	public $weaponClass = "Ballistic"; //should be Ballistic and Matter, but FV does not allow that. Instead decrease advanced armor encountered by 2 points (if any) (usually system does that, but it will account for Ballistic and not Matter)
	 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		        //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 6;
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
			$this->data["Special"] = '<br>Benefits from offensive EW.';			
        }

        public function getDamage($fireOrder){ 
			return Dice::d(10, 1)+6;   
		}

        public function setAmmo($firingMode, $amount){
            $this->ammunition = $amount;
        }
       public function fire($gamedata, $fireOrder){ //note ammo usage
            parent::fire($gamedata, $fireOrder);
            $this->ammunition--;
            Manager::updateAmmoInfo($fireOrder->shooterid, $this->id, $gamedata->id, $this->firingMode, $this->ammunition, $gamedata->turn);
        }
    
        public function setMinDamage(){     $this->minDamage = 7;      }
        public function setMaxDamage(){     $this->maxDamage = 16;      }
}//endof NexusLargeRocket


class NexusMiniRocket extends Weapon{
        public $name = "NexusMiniRocket";
        public $displayName = "Mini Rocket Launcher";
		    public $iconPath = "NexusMiniRocket.png";
        public $animation = "trail";
        public $trailColor = array(11, 224, 255);
        public $animationColor = array(50, 50, 50);
        public $animationExplosionScale = 0.2;
        public $projectilespeed = 12;
        public $animationWidth = 4;
        public $trailLength = 100;    

        public $useOEW = true; //torpedo
        public $ballistic = true; //missile
        public $range = 10;
        public $distanceRange = 20;
        public $ammunition = 15; //limited number of shots
        
        public $loadingtime = 1; // 1 turn
        public $rangePenalty = 0;
        public $fireControl = array(0, 1, 2); // fighters, <mediums, <capitals; INCLUDES BOTH LAUNCHER AND MISSILE DATA!
	    
	public $priority = 6; //Standard weapon
	    
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
			$this->data["Special"] = '<br>Benefits from offensive EW.';			
        }

        public function getDamage($fireOrder){ 
			return Dice::d(6, 1)+5;   
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
        public function setMaxDamage(){     $this->maxDamage = 11;      }
}//endof NexusMiniRocket


class NexusPlasmaBombRack extends Plasma{
        public $name = "NexusPlasmaBombRack";
        public $displayName = "Plasma Bomb Rack";
		    public $iconPath = "NexusPlasmaBomb.png";
        public $animation = "trail";
        public $trailColor = array(11, 224, 255);
        public $animationColor = array(50, 50, 50);
        public $animationExplosionScale = 0.2;
        public $projectilespeed = 12;
        public $animationWidth = 4;
        public $trailLength = 100;    

        public $ballistic = true; //missile
        public $range = 15;
        public $distanceRange = 25;
        public $ammunition = 8; //limited number of shots
        
        public $loadingtime = 2; // 1 turn
        public $rangePenalty = 0;
        public $fireControl = array(null, 2, 2); // fighters, <mediums, <capitals; INCLUDES BOTH LAUNCHER AND MISSILE DATA!
	    
	public $priority = 6; //Standard weapon
	    
	public $firingMode = 'Ballistic'; //firing mode - just a name essentially
	public $damageType = "Plasma"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    	public $weaponClass = "Plasma"; //should be Ballistic and Matter, but FV does not allow that. Instead decrease advanced armor encountered by 2 points (if any) (usually system does that, but it will account for Ballistic and not Matter)
	 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		        //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 6;
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
			return 12;   
		}

        public function setAmmo($firingMode, $amount){
            $this->ammunition = $amount;
        }
       public function fire($gamedata, $fireOrder){ //note ammo usage
            parent::fire($gamedata, $fireOrder);
            $this->ammunition--;
            Manager::updateAmmoInfo($fireOrder->shooterid, $this->id, $gamedata->id, $this->firingMode, $this->ammunition, $gamedata->turn);
        }
    
        public function setMinDamage(){     $this->minDamage = 12;      }
        public function setMaxDamage(){     $this->maxDamage = 12;      }
}//endof NexusPlasmaBombRack




class NexusDartInterceptor extends Weapon{
        public $name = "NexusDartInterceptor";
        public $displayName = "Dart Interceptor";
		    public $iconPath = "NexusDartInterceptor.png";
        public $animation = "trail";
        public $trailColor = array(11, 224, 255);
        public $animationColor = array(50, 50, 50);
        public $animationExplosionScale = 0.2;
        public $projectilespeed = 12;
        public $animationWidth = 4;
        public $trailLength = 100;    
		public $guns = 2;

        public $useOEW = true; //torpedo
        public $ballistic = true; //missile
        public $range = 8;
        public $distanceRange = 24;
        public $ammunition = 40; //limited number of shots
        
        public $loadingtime = 1; // 1 turn
        public $rangePenalty = 0;
        public $fireControl = array(3, null, null); // fighters, <mediums, <capitals; INCLUDES BOTH LAUNCHER AND MISSILE DATA!
	    
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
			$this->data["Special"] = '<br>Benefits from offensive EW.';			
        }

        public function getDamage($fireOrder){ 
			return 8;   
		}

        public function setAmmo($firingMode, $amount){
            $this->ammunition = $amount;
        }
       public function fire($gamedata, $fireOrder){ //note ammo usage
            parent::fire($gamedata, $fireOrder);
            $this->ammunition--;
            Manager::updateAmmoInfo($fireOrder->shooterid, $this->id, $gamedata->id, $this->firingMode, $this->ammunition, $gamedata->turn);
        }
    
        public function setMinDamage(){     $this->minDamage = 8;      }
        public function setMaxDamage(){     $this->maxDamage = 8;      }
		
}//endof NexusDartInterceptor


class NexusStreakInterceptor extends Weapon{
        public $name = "NexusStreakInterceptor";
        public $displayName = "Streak Interceptor";
		    public $iconPath = "NexusStreakInterceptor.png";
        public $animation = "trail";
        public $trailColor = array(11, 224, 255);
        public $animationColor = array(50, 50, 50);
        public $animationExplosionScale = 0.2;
        public $projectilespeed = 12;
        public $animationWidth = 4;
        public $trailLength = 100;    
		public $guns = 2;

        public $useOEW = true; //torpedo
        public $ballistic = true; //missile
        public $range = 10;
        public $distanceRange = 30;
        public $ammunition = 40; //limited number of shots
        
        public $loadingtime = 1; // 1 turn
        public $rangePenalty = 0;
        public $fireControl = array(4, null, null); // fighters, <mediums, <capitals; INCLUDES BOTH LAUNCHER AND MISSILE DATA!
	    
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
			$this->data["Special"] = '<br>Benefits from offensive EW.';			
        }

        public function getDamage($fireOrder){ 
			return 10;   
		}

        public function setAmmo($firingMode, $amount){
            $this->ammunition = $amount;
        }
       public function fire($gamedata, $fireOrder){ //note ammo usage
            parent::fire($gamedata, $fireOrder);
            $this->ammunition--;
            Manager::updateAmmoInfo($fireOrder->shooterid, $this->id, $gamedata->id, $this->firingMode, $this->ammunition, $gamedata->turn);
        }
    
        public function setMinDamage(){     $this->minDamage = 10;      }
        public function setMaxDamage(){     $this->maxDamage = 10;      }
		
}//endof NexusStreakInterceptor



class NexusSmallEarlyRocket extends Weapon{
        public $name = "NexusSmallEarlyRocket";
        public $displayName = "Small Early Rocket";
		    public $iconPath = "NexusSmallEarlyRocket.png";
        public $animation = "trail";
        public $trailColor = array(11, 224, 255);
        public $animationColor = array(50, 50, 50);
        public $animationExplosionScale = 0.2;
        public $projectilespeed = 12;
        public $animationWidth = 4;
        public $trailLength = 100;    

        public $useOEW = true; //torpedo
        public $ballistic = true; //missile
        public $range = 10;
        public $distanceRange = 20;
        public $ammunition = 15; //limited number of shots
        
        public $loadingtime = 1; 
        public $rangePenalty = 1;
        public $fireControl = array(null, 1, 2); // fighters, <mediums, <capitals; INCLUDES BOTH LAUNCHER AND MISSILE DATA!
	    
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
			$this->data["Special"] = "This has a -1 per 2 hex range penalty.";
			$this->data["Special"] = '<br>Benefits from offensive EW.';			
        }

        public function getDamage($fireOrder){ 
			return Dice::d(6, 1)+3;   
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
        public function setMaxDamage(){     $this->maxDamage = 9;      }
		
}//endof NexusSmallEarlyRocket



class NexusEarlyRocket extends Weapon{
        public $name = "NexusEarlyRocket";
        public $displayName = "Early Rocket";
		    public $iconPath = "NexusEarlyRocket.png";
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
        public $distanceRange = 25;
        public $ammunition = 10; //limited number of shots
        
        public $loadingtime = 2; // 1 turn
        public $rangePenalty = 0.5;
        public $fireControl = array(null, 1, 2); // fighters, <mediums, <capitals; INCLUDES BOTH LAUNCHER AND MISSILE DATA!
	    
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
			$this->data["Special"] = "This has a -1 per 2 hex range penalty.";
			$this->data["Special"] = '<br>Benefits from offensive EW.';			
        }

        public function getDamage($fireOrder){ 
			return Dice::d(10, 1)+5;   
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
        public function setMaxDamage(){     $this->maxDamage = 15;      }
		
}//endof NexusEarlyRocket


class NexusLargeEarlyRocket extends Weapon{
        public $name = "NexusLargeEarlyRocket";
        public $displayName = "Large Early Rocket";
		    public $iconPath = "NexusLargeEarlyRocket.png";
        public $animation = "trail";
        public $trailColor = array(11, 224, 255);
        public $animationColor = array(50, 50, 50);
        public $animationExplosionScale = 0.2;
        public $projectilespeed = 12;
        public $animationWidth = 4;
        public $trailLength = 100;    

        public $useOEW = true; //torpedo
        public $ballistic = true; //missile
        public $range = 20;
        public $distanceRange = 35;
        public $ammunition = 8; //limited number of shots
        
        public $loadingtime = 3; // 1 turn
        public $rangePenalty = 0.33;
        public $fireControl = array(null, 1, 3); // fighters, <mediums, <capitals; INCLUDES BOTH LAUNCHER AND MISSILE DATA!
	    
	public $priority = 7; //Standard weapon
	    
	public $firingMode = 'Ballistic'; //firing mode - just a name essentially
	public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    	public $weaponClass = "Ballistic"; //should be Ballistic and Matter, but FV does not allow that. Instead decrease advanced armor encountered by 2 points (if any) (usually system does that, but it will account for Ballistic and not Matter)
	 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		        //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 9;
            if ( $powerReq == 0 ) $powerReq = 9;
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
			$this->data["Special"] = "This has a -1 per 2 hex range penalty.";
			$this->data["Special"] = '<br>Benefits from offensive EW.';			
        }

        public function getDamage($fireOrder){ 
			return Dice::d(10, 2)+6;   
		}

        public function setAmmo($firingMode, $amount){
            $this->ammunition = $amount;
        }
       public function fire($gamedata, $fireOrder){ //note ammo usage
            parent::fire($gamedata, $fireOrder);
            $this->ammunition--;
            Manager::updateAmmoInfo($fireOrder->shooterid, $this->id, $gamedata->id, $this->firingMode, $this->ammunition, $gamedata->turn);
        }

        public function setMinDamage(){     $this->minDamage = 8;      }
        public function setMaxDamage(){     $this->maxDamage = 26;      }
		
}//endof NexusLargeEarlyRocket







class NexusHeavyPlasmaCharge extends Torpedo{
        public $name = "NexusHeavyPlasmaCharge";
        public $displayName = "Heavy Plasma Charge";
		public $iconPath = "NexusHeavyPlasmaCharge.png";
        public $loadingtime = 2; // 2 turn
	public $specialRangeCalculation = true; //to inform front end that it should use weapon-specific range penalty calculation - such a method should be present in .js!

    	public $weaponClass = "Plasma"; //should be Ballistic and Matter, but FV does not allow that. Instead decrease advanced armor encountered by 2 points (if any) (usually system does that, but it will account for Ballistic and not Matter)
//		public $firingMode = 'Ballistic'; //firing mode - just a name essentially
		public $damageType = "Plasma"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!

        public $fireControl = array(-4, 2, 3); // fighters, <mediums, <capitals; INCLUDES BOTH LAUNCHER AND MISSILE DATA!
		public $rangePenalty = 1; // -1/hex - BUT ONLY AFTER 12 HEXES

        public $animation = "trail";
        public $trailColor = array(40, 199, 251);
        public $animationColor = array(52, 249, 11);
		public $animationWidth = 0.5;
        public $animationExplosionScale = 0.2;
        public $projectilespeed = 12;
        public $trailLength = 12;    
        public $useOEW = true; //torpedo
        public $ballistic = true; //missile
        public $range = 0; //unlimited range, but suffers range penalty

		public $rangeDamagePenaltyHPCharge = 1;  // -1/hex, but only after 10 hexes!
	    
	public $priority = 6; //Standard weapon
	    
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
		$this->data["Special"] .= "Launch and distance range is unlimited.";
		$this->data["Special"] .= "<br>Plasma weapon. Armor treated as half.";
		$this->data["Special"] .= "<br>Loses -1 damage per hex after the first 10 hexes.";
		$this->data["Special"] .= "<br>This weapon suffers range penalty (like direct fire weapons do), but only after first 12 hexes of distance.";
	}	 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		        //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 9;
            if ( $powerReq == 0 ) $powerReq = 5;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

// Variable damage reduction with range from the Descari Plasma Bolter

	//skip first 10 hexes when calculating the damage modifier
	protected function getDamageMod($damage, $shooter, $target, $pos, $gamedata)
	{
		parent::getDamageMod($damage, $shooter, $target, $pos, $gamedata);
					if ($pos != null) {
					$sourcePos = $pos;
					} 
					else {
					$sourcePos = $shooter->getHexPos();
					}
			$dis = mathlib::getDistanceHex($sourcePos, $target);				
			if ($dis <= 10) {
				$damage -= 0;
				}
			else {
				$damage -= round(($dis - 10) * $this->rangeDamagePenaltyHPCharge);
			}	
		        $damage = max(0, $damage); //at least 0	    
        		$damage = floor($damage); //drop fractions, if any were generated
      			 return $damage;
	}		

// Variable range penalty from the Gaim Packet Torpedo

	    //override standard to skip first 12 hexes when calculating range penalty
	    /*public function calculateRangePenalty(OffsetCoordinate $pos, BaseShip $target)
	    {
			$targetPos = $target->getHexPos();
			$dis = mathlib::getDistanceHex($pos, $targetPos);
			$dis = max(0,$dis-12);//first 12 hexes are "free"

			$rangePenalty = ($this->rangePenalty * $dis);
			$notes = "shooter: " . $pos->q . "," . $pos->r . " target: " . $targetPos->q . "," . $targetPos->r . " dis: $dis, rangePenalty: $rangePenalty";
			return Array("rp" => $rangePenalty, "notes" => $notes);
	    }*/
		public function calculateRangePenalty($distance){
			$rangePenalty = 0;//base penalty
			$rangePenalty += $this->rangePenalty * max(0,$distance-12); //everything above X hexes receives range penalty
			return $rangePenalty;
		}

        public function getDamage($fireOrder){ 
			return Dice::d(10, 1)+10;   
			//return 10; To test damage and range penalty effects
		}

        public function setMinDamage(){     $this->minDamage = 11 ;      }
        public function setMaxDamage(){     $this->maxDamage = 20 ;      }
		
		
}//endof NexusHeavyPlasmaCharge




class NexusBoltTorpedo extends Weapon{
        public $name = "NexusBoltTorpedo";
        public $displayName = "Bolt Torpedo";
		    public $iconPath = "NexusBoltTorpedo.png";
        public $animation = "trail";
        public $trailColor = array(11, 224, 255);
        public $animationColor = array(50, 50, 50);
        public $animationExplosionScale = 0.2;
        public $projectilespeed = 12;
        public $animationWidth = 4;
        public $trailLength = 100;    

        public $useOEW = true; //torpedo
        public $ballistic = true; //missile
        public $range = 20;
        
        public $loadingtime = 2; // 1 turn
        public $rangePenalty = 0;
        public $fireControl = array(-1, 2, 2); // fighters, <mediums, <capitals; INCLUDES BOTH LAUNCHER AND MISSILE DATA!
	    
	public $priority = 5; //Standard weapon
	    
	public $firingMode = 'Ballistic'; //firing mode - just a name essentially
	public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    	public $weaponClass = "Ballistic"; //should be Ballistic and Matter, but FV does not allow that. Instead decrease advanced armor encountered by 2 points (if any) (usually system does that, but it will account for Ballistic and not Matter)
	 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		        //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 5;
            if ( $powerReq == 0 ) $powerReq = 2;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
			$this->data["Special"] = '<br>Benefits from offensive EW.';			
        }

        public function getDamage($fireOrder){ 
			return 10;   
		}

        public function setMinDamage(){     $this->minDamage = 10;      }
        public function setMaxDamage(){     $this->maxDamage = 10;      }
		
}//endof NexusBoltTorpedo



class NexusRangedBoltTorpedo extends Weapon{
        public $name = "NexusRangedBoltTorpedo";
        public $displayName = "Ranged Bolt Torpedo";
		    public $iconPath = "NexusBoltTorpedo.png";
        public $animation = "trail";
        public $trailColor = array(11, 224, 255);
        public $animationColor = array(50, 50, 50);
        public $animationExplosionScale = 0.2;
        public $projectilespeed = 12;
        public $animationWidth = 4;
        public $trailLength = 100;    

        public $useOEW = true; //torpedo
        public $ballistic = true; //missile
        public $range = 40;
        
        public $loadingtime = 2; // 1 turn
        public $rangePenalty = 0;
        public $fireControl = array(-1, 2, 2); // fighters, <mediums, <capitals; INCLUDES BOTH LAUNCHER AND MISSILE DATA!
	    
	public $priority = 5; //Standard weapon
	    
	public $firingMode = 'Ballistic'; //firing mode - just a name essentially
	public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    	public $weaponClass = "Ballistic"; //should be Ballistic and Matter, but FV does not allow that. Instead decrease advanced armor encountered by 2 points (if any) (usually system does that, but it will account for Ballistic and not Matter)
	 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		        //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 5;
            if ( $powerReq == 0 ) $powerReq = 2;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
			$this->data["Special"] = '<br>Benefits from offensive EW.';			
        }

        public function getDamage($fireOrder){ 
			return 10;   
		}

        public function setMinDamage(){     $this->minDamage = 10;      }
        public function setMaxDamage(){     $this->maxDamage = 10;      }
		
}//endof NexusRangedBoltTorpedo



/*Chaff Launcher
intercepts all weapon fire (directed at self) from HEX (including uninterceptable weapons).
Done as: kind of offensive mode - player needs to pick hex to fire at. Animated as kind of EMine. 
All appropriate fire orders will get an interception set up before other intercepts are declared.
If weapon is left to its own devices it will simply provide a single interception (...if game allows non-1-per-turn weapon to be intercepting in the first place!)
*/
class NexusChaffLauncher extends Weapon{
        public $name = "nexusChaffLauncher";
        public $displayName = "Chaff Launcher";
		public $iconPath = "NexusChaffLauncher.png";
	
        public $trailColor = array(192,192,192);
        public $animation = "ball";
        public $animationColor = array(192,192,192);
        public $animationExplosionScale = 0.5;
        public $animationExplosionType = "AoE";
        public $explosionColor = array(235,235,235);
        public $projectilespeed = 12;
        public $animationWidth = 10;
        public $trailLength = 10;

        public $ballistic = false;
        public $hextarget = false; //for technical reasons this proved hard to do
        public $hidetarget = false;
        public $priority = 1; //to show effect quickly
        public $uninterceptable = true; //just so nothing tries to actually intercept this weapon
        public $doNotIntercept = true; //do not intercept this weapon, period
		public $canInterceptUninterceptable = true; //able to intercept shots that are normally uninterceptable, eg. Lasers
	
        public $useOEW = false; //not important, really	    
        
        public $loadingtime = 2; // 1/2 turns
		public $range = 100; //let's put maximum range here, but generous one
        public $rangePenalty = 0;
        public $fireControl = array(100, 100, 100); // fighters, <mediums, <capitals; just so the weapon is targetable
		public $intercept = 2; //intercept rating -2	    
	    
		public $firingMode = 'Intercept'; //firing mode - just a name essentially
		public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    	public $weaponClass = "Particle"; //not important really
	 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		        //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 2;
            if ( $powerReq == 0 ) $powerReq = 1;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
		public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            $this->data["Special"] = "Fired at hex (although You technically have to pick an unit). Will apply interception to all fire from target hex to Chaff-protected ship.";
            $this->data["Special"] .= "<br>Will affect uninterceptable weapons.";
        }
        
	//hit chance always 100 - so it always hits and is correctly animated
	public function calculateHitBase($gamedata, $fireOrder)
	{
		$fireOrder->needed = 100; //auto hit!
		$fireOrder->updated = true;
		
		//while we're at it - we may add appropriate interception orders!		
		$targetShip = $gamedata->getShipById($fireOrder->targetid);
		
		$shipsInRange = $gamedata->getShipsInDistance($targetShip); //all units on target hex
		foreach ($shipsInRange as $affectedShip) {
			$allOrders = $affectedShip->getAllFireOrders($gamedata->turn);
			foreach($allOrders as $subOrder) {
				if (($subOrder->type == 'normal') && ($subOrder->targetid == $fireOrder->shooterid) ){ //something is firing at protected unit - and is affected!
					//uninterceptable are affected all right, just those that outright cannot be intercepted - like ramming or mass driver - will not be affected
					$subWeapon = $affectedShip->getSystemById($subOrder->weaponid);
					if( $subWeapon->doNotIntercept != true ){
						//apply interception! Note that this weapon is technically not marked as firing defensively - it is marked as firing offensively though! (already)
						//like firing.php addToInterceptionTotal
						$subOrder->totalIntercept += $this->getInterceptionMod($gamedata, $subOrder);
        				$subOrder->numInterceptors++;
					}
				}
			}
		}
		
		//retarget at hex - this will affect how the weapon is animated/displayed in firing log!
		    //insert correct target coordinates: CURRENT target position
		    $pos = $targetShip->getHexPos();
		    $fireOrder->x = $pos->q;
		    $fireOrder->y = $pos->r;
		    $fireOrder->targetid = -1; //correct the error

	}//endof function calculateHitBase
	   
	
	public function fire($gamedata, $fireOrder)
	{ //sadly here it really has to be completely redefined... or at least I see no option to avoid this
		$this->changeFiringMode($fireOrder->firingMode);//changing firing mode may cause other changes, too!
		$shooter = $gamedata->getShipById($fireOrder->shooterid);
		/** @var MovementOrder $movement */
		$movement = $shooter->getLastTurnMovement($fireOrder->turn);
		$posLaunch = $movement->position;//at moment of launch!!!		
		//$this->calculateHit($gamedata, $fireOrder); //already calculated!
		$rolled = Dice::d(100);
		$fireOrder->rolled = $rolled; ///and auto-hit ;)
		$fireOrder->shotshit++;
		$fireOrder->pubnotes .= "Interception applied to all weapons at target hex that are firing at Chaff-launching ship. "; //just information for player, actual applying was done in calculateHitBase method

		$fireOrder->rolled = max(1, $fireOrder->rolled);//Marks that fire order has been handled, just in case it wasn't marked yet!
		TacGamedata::$lastFiringResolutionNo++;    //note for further shots
		$fireOrder->resolutionOrder = TacGamedata::$lastFiringResolutionNo;//mark order in which firing was handled!
	} //endof function fire
	
	
        public function getDamage($fireOrder){
            return 0; //this weapon does no damage, in case it actually hits something!
        }
        public function setMinDamage(){     $this->minDamage = 0;      }
        public function setMaxDamage(){     $this->maxDamage = 0;      }
}//endof NexusChaffLauncher


// END OF BALLISTIC and TORPEDO WEAPONS


// PARTICLE WEAPONS


/*custom extension of standard Particle Projector line*/
    class NexusParticleProjectorLight extends Particle{
        public $trailColor = array(30, 170, 255);

        public $name = "nexusParticleProjectorLight";
        public $displayName = "Light Particle Projector";
	public $iconPath = "NexusParticleProjectorLight.png";
	    
        public $animation = "beam";
        public $animationColor = array(205, 200, 200);
        public $animationExplosionScale = 0.15;
        public $projectilespeed = 10;
        public $animationWidth = 3;
        public $trailLength = 10;

        public $intercept = 2;
        public $loadingtime = 1;
        public $priority = 3;

        public $rangePenalty = 2; //-2/hex
        public $fireControl = array(3, 2, 2); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 3;
            if ( $powerReq == 0 ) $powerReq = 1;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){ return Dice::d(6, 1)+2;   }
        public function setMinDamage(){     $this->minDamage = 3 ;      }
        public function setMaxDamage(){     $this->maxDamage = 8 ;      }
    }


/*custom extension of standard Particle Projector line*/
    class NexusParticleProjectorHeavy extends Particle{
        public $trailColor = array(30, 170, 255);

        public $name = "nexusParticleProjectorHeavy";
        public $displayName = "Heavy Particle Projector";
		public $iconPath = "NexusParticleProjectorHeavy.png";
	
		public $animation = "beam";
        public $animationColor = array(205, 200, 200);
        public $animationExplosionScale = 0.35;
        public $projectilespeed = 17;
        public $animationWidth = 5;
        public $trailLength = 30;

        public $intercept = 1;
        public $loadingtime = 3;
        public $priority = 6;

        public $rangePenalty = 0.5; //-1/2hexes
        public $fireControl = array(0, 2, 3); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 8;
            if ( $powerReq == 0 ) $powerReq = 4;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){ return Dice::d(10, 2)+8;   }
        public function setMinDamage(){     $this->minDamage = 10 ;      }
        public function setMaxDamage(){     $this->maxDamage = 28 ;      }
    }

	class NexusParticleProjectorFtr extends Particle{
		/*fighter-mounted version of medium Particle Projector*/
        public $trailColor = array(30, 170, 255);

        public $name = "nexusParticleProjectorFtr";
        public $displayName = "Particle Projector";
        public $animation = "beam";
        public $animationColor = array(205, 200, 200);
        public $animationExplosionScale = 0.25;
        public $projectilespeed = 15;
        public $animationWidth = 4;
        public $trailLength = 20;
		public $iconPath = "particleProjector.png";

        public $intercept = 2;
        public $loadingtime = 2;
        public $priority = 5;

        public $rangePenalty = 1;
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals

        function __construct($startArc, $endArc){
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }

        public function getDamage($fireOrder){ return Dice::d(10, 1)+4;   }
        public function setMinDamage(){     $this->minDamage = 5 ;      }
        public function setMaxDamage(){     $this->maxDamage = 14 ;      }
    }//endof NexusParticleProjectorFtr


    class NexusParticleAgitator extends Particle{
        public $trailColor = array(30, 170, 255);

        public $name = "nexusParticleAgitator";
        public $displayName = "Particle Agitator";
		public $iconPath = "NexusParticleAgitator.png";
	    
        public $animation = "beam";
        public $animationColor = array(255, 250, 230);
        public $animationExplosionScale = 0.25;
        public $projectilespeed = 12;
        public $animationWidth = 4;
        public $trailLength = 10;

        public $intercept = 1;
        public $loadingtime = 2;
        public $priority = 6; //heavy Standard

        public $rangePenalty = 1; //-1/hex
        public $fireControl = array(0, 2, 2); // fighters, <mediums, <capitals

		public $firingMode = 'Standard'; //firing mode - just a name essentially
		public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    	public $weaponClass = "Particle"; //not important really
	    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 8;
            if ( $powerReq == 0 ) $powerReq = 3;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){ return Dice::d(10, 2)+6;   }
        public function setMinDamage(){     $this->minDamage = 8 ;      }
        public function setMaxDamage(){     $this->maxDamage = 26 ;      }
    }// endof NexusParticleAgitator 



/*custom extension of standard Particle Projector line*/
    class NexusProjectorArray extends Particle{
        public $trailColor = array(30, 170, 255);

        public $name = "NexusProjectorArray";
        public $displayName = "Projector Array";
		public $iconPath = "NexusProjectorArray.png";
	    
        public $animation = "beam";
        public $animationColor = array(205, 200, 200);
        public $animationExplosionScale = 0.25;
        public $projectilespeed = 15;
        public $animationWidth = 4;
        public $trailLength = 20;

        public $intercept = 1;
        public $loadingtime = 1;
        public $priority = 4;

        public $rangePenalty = 1; //-1/hex
        public $fireControl = array(1, 2, 2); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 6;
            if ( $powerReq == 0 ) $powerReq = 1;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){ return Dice::d(10, 1)+4;   }
        public function setMinDamage(){     $this->minDamage = 5 ;      }
        public function setMaxDamage(){     $this->maxDamage = 14 ;      }
	}// endof NexusProjectorArray
	
	
/*custom extension of standard Particle Projector line*/
    class NexusLightProjectorArray extends Particle{
        public $trailColor = array(30, 170, 255);

        public $name = "NexusLightProjectorArray";
        public $displayName = "Light Projector Array";
		public $iconPath = "NexusLightProjectorArray.png";
	    
        public $animation = "beam";
        public $animationColor = array(255, 250, 230);
        public $animationExplosionScale = 0.15;
        public $projectilespeed = 15;
        public $animationWidth = 3;
        public $trailLength = 3;

        public $intercept = 2;
        public $loadingtime = 1;
		public $guns = 2;
        public $priority = 4;

        public $rangePenalty = 2; //-2/hex
        public $fireControl = array(4, 2, 2); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 5;
            if ( $powerReq == 0 ) $powerReq = 2;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){ return Dice::d(6, 1)+3;   }
        public function setMinDamage(){     $this->minDamage = 4 ;      }
        public function setMaxDamage(){     $this->maxDamage = 9 ;      }
	}// endof NexusLightProjectorArray
	

class NexusShatterGun extends Weapon{

        public $name = "NexusShatterGun";
        public $displayName = "Shatter Gun";
        public $iconPath = "NexusShattergun.png"; 		
		
        public $animationArray = array(1=>'trail', 2=>'trail');
        public $animationColorArray = array(1=>array(245, 245, 44), 2=>array(245, 245, 44));
        public $trailLength = 5;
        public $animationWidth = 4;
        public $projectilespeed = 18;
        public $animationExplosionScale = 0.10;

	protected $useDie = 2; //die used for base number of hits
//		public $rof = 3;
		public $groupingArray = array(1=>25, 2=>0);
		public $maxpulses = 4;
        public $priorityArray = array(1=>5, 2=>7); // Matter weapon
	public $defaultShotsArray = array(1=>4, 2=>1); //for Pulse mode it should be equal to maxpulses
        
        public $loadingtimeArray = array(1=>1, 2=>1);
        public $rangePenaltyArray = array(1=>2, 2=>2);
        public $intercept = 1;
		public $ballisticIntercept = true;
		public $firingModes = array(1 =>'Burst', 2=>'Concentrated');
        
        public $fireControlArray = array(1=>array(2, 1, 1), 2=>array(0, -1, -1)); // fighters, <mediums, <capitals
        
        public $damageTypeArray = array(1=>"Pulse", 2=>"Standard"); //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
        public $weaponClassArray = array(1=>'Matter', 2=>'Matter');

		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
			$this->data["Special"] = "Ignores armor, does not overkill.";
			$this->data["Special"] .= "<br>Burst mode: 1d2 +1/25% pulses (max 4) of 3 damage.";
			$this->data["Special"] .= "<br>Concentrated mode:  1d2*4 damage but -10 fire control.";
			$this->data["Special"] .= "<br>Can intercept ballistic weapons only.";
		}

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            if ( $maxhealth == 0 ) $maxhealth = 2;
            if ( $powerReq == 0 ) $powerReq = 1;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

		public function getDamage($fireOrder){
        	switch($this->firingMode){ 
            	case 1:
                	return 3;
			    			break;
            	case 2:
            	   	return Dice::d(2, 1)*3;
			    			break;
        	}
		}

		public function setMinDamage(){
				switch($this->firingMode){
						case 1:
								$this->minDamage = 3;
								break;
						case 2:
								$this->minDamage = 3;
								break;
				}
				$this->minDamageArray[$this->firingMode] = $this->minDamage;
		}							
		
		public function setMaxDamage(){
				switch($this->firingMode){
						case 1:
								$this->maxDamage = 3;
								break;
						case 2:
								$this->maxDamage = 6;
								break;
				}
				$this->maxDamageArray[$this->firingMode] = $this->maxDamage;
		}

		public function stripForJson(){
			$strippedSystem = parent::stripForJson();
			$strippedSystem->data = $this->data;
			$strippedSystem->minDamage = $this->minDamage;
			$strippedSystem->minDamageArray = $this->minDamageArray;
			$strippedSystem->maxDamage = $this->maxDamage;
			$strippedSystem->maxDamageArray = $this->maxDamageArray;				

			return $strippedSystem;
		}

	//necessary for Pulse mode
        protected function getPulses($turn)
        {
            return Dice::d(2);
        }
        protected function getExtraPulses($needed, $rolled)
        {
            return floor(($needed - $rolled) / ($this->grouping));
        }
	public function rollPulses($turn, $needed, $rolled){
		$pulses = $this->getPulses($turn);
		$pulses+= $this->getExtraPulses($needed, $rolled);
		$pulses=min($pulses,$this->maxpulses);
		return $pulses;
	}
		
    } // endof NexusShatterGun	


/*fighter-mounted variant*/
class NexusShatterGunFtr extends Weapon{

        public $name = "NexusShatterGunFtr";
        public $displayName = "Light Shatter Gun";
        public $iconPath = "NexusShattergun.png"; 		
		
        public $animationArray = array(1=>'trail', 2=>'trail');
        public $animationColorArray = array(1=>array(245, 245, 44), 2=>array(245, 245, 44));
        public $trailLength = 5;
        public $animationWidth = 4;
        public $projectilespeed = 18;
        public $animationExplosionScale = 0.10;
        public $ammunition = 12;

	protected $useDie = 2; //die used for base number of hits
//		public $rof = 3;
		public $groupingArray = array(1=>0, 2=>0);
		public $maxpulses = 2;
        public $priorityArray = array(1=>5, 2=>7); // Matter weapon
	public $defaultShotsArray = array(1=>2, 2=>1); //for Pulse mode it should be equal to maxpulses
        
        public $loadingtimeArray = array(1=>1, 2=>1);
        public $rangePenaltyArray = array(1=>2, 2=>2);
        public $intercept = 1;
		public $ballisticIntercept = true;
		public $firingModes = array(1 =>'Burst', 2=>'Concentrated');
        
        public $fireControlArray = array(1=>array(0, 0, 0), 2=>array(-2, -2, -2)); // fighters, <mediums, <capitals
        
        public $damageTypeArray = array(1=>"Pulse", 2=>"Standard"); //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
        public $weaponClassArray = array(1=>'Matter', 2=>'Matter');

	function __construct($startArc, $endArc, $nrOfShots = 1){
            $this->defaultShots = $nrOfShots;
            $this->shots = $nrOfShots;
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }

		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
			$this->data["Special"] = "Ignores armor, does not overkill.";
			$this->data["Special"] .= "<br>Burst mode: 1d2 pulses of 3 damage.";
			$this->data["Special"] .= "<br>Concentrated mode:  3*d2 damage but -10 fire control.";
			$this->data["Special"] .= "<br>Can intercept ballistic weapons only.";
            $this->data["Ammunition"] = $this->ammunition;
		}
		
        public function setAmmo($firingMode, $amount){
            $this->ammunition = $amount;
        }

       public function fire($gamedata, $fireOrder){ //note ammo usage
        	//debug::log("fire function");
            parent::fire($gamedata, $fireOrder);
            $this->ammunition--;
            Manager::updateAmmoInfo($fireOrder->shooterid, $this->id, $gamedata->id, $this->firingMode, $this->ammunition, $gamedata->turn);
        }

		public function getDamage($fireOrder){
        	switch($this->firingMode){ 
            	case 1:
                	return 3;
			    			break;
            	case 2:
            	   	return Dice::d(2, 1)*3;
			    			break;
        	}
		}

		public function setMinDamage(){
				switch($this->firingMode){
						case 1:
								$this->minDamage = 3;
								break;
						case 2:
								$this->minDamage = 3;
								break;
				}
				$this->minDamageArray[$this->firingMode] = $this->minDamage;
		}							
		
		public function setMaxDamage(){
				switch($this->firingMode){
						case 1:
								$this->maxDamage = 3;
								break;
						case 2:
								$this->maxDamage = 6;
								break;
				}
				$this->maxDamageArray[$this->firingMode] = $this->maxDamage;
		}

		public function stripForJson(){
			$strippedSystem = parent::stripForJson();
			$strippedSystem->data = $this->data;
			$strippedSystem->minDamage = $this->minDamage;
			$strippedSystem->minDamageArray = $this->minDamageArray;
			$strippedSystem->maxDamage = $this->maxDamage;
			$strippedSystem->maxDamageArray = $this->maxDamageArray;				

            $strippedSystem->ammunition = $this->ammunition;

			return $strippedSystem;
		}

	//necessary for Pulse mode
        protected function getPulses($turn)
        {
            return Dice::d(2);
        }
        protected function getExtraPulses($needed, $rolled)
        {
            return floor(($needed - $rolled) / ($this->grouping));
        }
	public function rollPulses($turn, $needed, $rolled){
		$pulses = $this->getPulses($turn);
//		$pulses+= $this->getExtraPulses($needed, $rolled);
		$pulses=min($pulses,$this->maxpulses);
		return $pulses;
	}
		
    } // endof NexusShatterGunFtr		


    class NexusLightGasGun extends Matter{
//        public $trailColor = array(30, 170, 255);

        public $name = "NexusLightGasGun";
        public $displayName = "Light Gas Gun";
		public $iconPath = "NexusLightGasGun.png";
	    
        public $animation = "trail";
        public $animationColor = array(245, 245, 44);
        public $animationExplosionScale = 0.30;
        public $projectilespeed = 18;
        public $animationWidth = 2;
        public $trailLength = 35;

        public $loadingtime = 1;
		public $guns = 1;
        public $priority = 8;

        public $rangePenalty = 1.0 ; //-1 / hex
        public $fireControl = array(0, 1, 2); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 5;
            if ( $powerReq == 0 ) $powerReq = 1;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){ return Dice::d(6, 1)+2;   }
        public function setMinDamage(){     $this->minDamage = 3 ;      }
        public function setMaxDamage(){     $this->maxDamage = 8 ;      }
	}// endof NexusLightGasGun

/*fighter-mounted variant*/
    class NexusLightGasGunFtr extends Matter{
//        public $trailColor = array(30, 170, 255);

        public $name = "NexusLightGasGunFtr";
        public $displayName = "Fighter Gas Gun";
		public $iconPath = "NexusLightGasGun.png";
	    
        public $animation = "trail";
        public $animationColor = array(245, 245, 44);
        public $animationExplosionScale = 0.30;
        public $projectilespeed = 18;
        public $animationWidth = 2;
        public $trailLength = 35;

        public $loadingtime = 2;
        public $priority = 8;
        public $ammunition = 6;

        public $rangePenalty = 1.5 ; // -3/2 hexes
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals

        public function stripForJson() {
            $strippedSystem = parent::stripForJson();
    
            $strippedSystem->ammunition = $this->ammunition;
           
            return $strippedSystem;
        }

        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            $this->data["Ammunition"] = $this->ammunition;
        }

        public function setAmmo($firingMode, $amount){
            $this->ammunition = $amount;
        }

       public function fire($gamedata, $fireOrder){ //note ammo usage
        	//debug::log("fire function");
            parent::fire($gamedata, $fireOrder);
            $this->ammunition--;
            Manager::updateAmmoInfo($fireOrder->shooterid, $this->id, $gamedata->id, $this->firingMode, $this->ammunition, $gamedata->turn);
        }

	function __construct($startArc, $endArc, $nrOfShots = 1){
            $this->defaultShots = $nrOfShots;
            $this->shots = $nrOfShots;
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }

        public function getDamage($fireOrder){ return Dice::d(6, 1)+2;   }
        public function setMinDamage(){     $this->minDamage = 3 ;      }
        public function setMaxDamage(){     $this->maxDamage = 8 ;      }
	}// endof NexusLightGasGunFtr



    class NexusGasGun extends Matter{
        public $trailColor = array(30, 170, 255);

        public $name = "NexusGasGun";
        public $displayName = "Gas Gun";
		public $iconPath = "NexusGasGun.png";
	    
        public $animation = "trail";
        public $animationColor = array(245, 245, 44);
        public $animationExplosionScale = 0.30;
        public $projectilespeed = 18;
        public $animationWidth = 5;
        public $trailLength = 20;

        public $loadingtime = 2;
		public $guns = 1;
        public $priority = 9;

        public $rangePenalty = 0.66; //-2/3 hexes
        public $fireControl = array(-2, 2, 2); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 7;
            if ( $powerReq == 0 ) $powerReq = 2;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){ return Dice::d(10, 1)+8;   }
        public function setMinDamage(){     $this->minDamage = 9 ;      }
        public function setMaxDamage(){     $this->maxDamage = 18 ;      }
	}// endof NexusGasGun


/*    class NexusACIDS extends Particle{
        public $trailColor = array(206, 206, 83);

        public $name = "NexusACIDS";
        public $displayName = "Advanced Close-In Defense System";
		public $iconPath = "NexusACIDS.png";
	    
        public $animation = "trail";
        public $animationColor = array(245, 245, 44);
        public $animationExplosionScale = 0.10;
        public $projectilespeed = 10;
        public $animationWidth = 2;
        public $trailLength = 35;
        public $intercept = 1;		
        public $ballisticIntercept = true;
        public $loadingtime = 1;
		public $guns = 3;
        public $priority = 3;

        public $rangePenalty = 3; //-3/hex
        public $fireControl = array(3, 1, 1); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 6;
            if ( $powerReq == 0 ) $powerReq = 3;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){ return Dice::d(6, 1)+3;   }
        public function setMinDamage(){     $this->minDamage = 4 ;      }
        public function setMaxDamage(){     $this->maxDamage = 9 ;      }
	}// endof NexusACIDS
*/


    class NexusRCIDS extends Particle{
        public $trailColor = array(206, 206, 83);

        public $name = "NexusRCIDS";
        public $displayName = "Rapid Close-In Defense System";
		public $iconPath = "NexusRCIDS.png";
	    
        public $animation = "trail";
        public $animationColor = array(245, 245, 44);
        public $animationExplosionScale = 0.10;
        public $projectilespeed = 10;
        public $animationWidth = 2;
        public $trailLength = 35;
        public $intercept = 1;		
        public $ballisticIntercept = true;
        public $loadingtime = 1;
		public $guns = 2;
        public $priority = 3;

        public $rangePenalty = 3; //-3/hex
        public $fireControl = array(3, 1, 1); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 4;
            if ( $powerReq == 0 ) $powerReq = 2;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){ return Dice::d(6, 1)+3;   }
        public function setMinDamage(){     $this->minDamage = 4 ;      }
        public function setMaxDamage(){     $this->maxDamage = 9 ;      }
	}// endof NexusRCIDS



/*    class NexusCIDS extends Particle{
        public $trailColor = array(206, 206, 83);

        public $name = "NexusCIDS";
        public $displayName = "Close-In Defense System";
		public $iconPath = "NexusRCIDS.png";
	    
        public $animation = "trail";
        public $animationColor = array(245, 245, 44);
        public $animationExplosionScale = 0.10;
        public $projectilespeed = 10;
        public $animationWidth = 2;
        public $trailLength = 35;
        public $intercept = 1;		
        public $ballisticIntercept = true;
        public $loadingtime = 1;
		public $guns = 1;
        public $priority = 3;

        public $rangePenalty = 3; //-3/hex
        public $fireControl = array(3, 1, 1); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 2;
            if ( $powerReq == 0 ) $powerReq = 1;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){ return Dice::d(6, 1)+3;   }
        public function setMinDamage(){     $this->minDamage = 4 ;      }
        public function setMaxDamage(){     $this->maxDamage = 9 ;      }
	}// endof NexusCIDS
*/










    class NexusCIDS extends Weapon //this is NOT a Pulse weapon, disregard Pulse-specific settings...
    {
	public $name = "NexusCIDS";
        public $displayName = "Close-In Defense System";
        public $iconPath = "NexusRCIDS.png";
        public $animation = "trail";
        public $trailLength = 35;
        public $animationWidth = 2;
        public $projectilespeed = 10;
        public $animationExplosionScale = 0.10;
        public $animationColor =  array(245, 245, 44);
        public $trailColor = array(206, 206, 83);
		public $guns = 1; //multiplied to d4 at firing
	     
        public $loadingtime = 1;
        public $normalload = 1;	    
        public $priority = 6; 
        	    
		public $ballisticIntercept = true;
        public $intercept = 1; //as it should be, but here they CAN combine vs same shot!
	    
		public $rangePenalty = 2;
        public $fireControl = array(3, 1, 1); // fighters, <mediums, <capitals
	    
	    public $damageType = "Standard"; 
	    public $weaponClass = "Matter"; 
	    
	    //temporary private variables
	    private $multiplied = false;
	    private $alreadyIntercepted = array();
	    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
		if ( $maxhealth == 0 ) $maxhealth = 4;
		if ( $powerReq == 0 ) $powerReq = 2;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
	    
        public function setSystemDataWindow($turn){            
            parent::setSystemDataWindow($turn);		
			$this->data["Special"] = "Fires d4 separate shots (actual number rolled at firing resolution).";
			$this->data["Special"] .= "<br>When fired defensively, a single CIDS cannot engage the same incoming shot twice (even ballistic one).";
			$this->data["Special"] .= "<br>Ignores armor.";
			$this->data["Special"] .= "<br>Can intercept ballistic weapons only.";
        }
	    
	//if fired offensively - make d4 attacks (copies of 1 existing); 
	//if defensively - make weapon have d4 GUNS (would be temporary, but enough to assign multiple shots for interception)
	public function beforeFiringOrderResolution($gamedata){
		if($this->multiplied==true) return;//shots of this weapon are already multiplied
		$this->multiplied = true;//shots WILL be multiplied in a moment, mark this
		//is offensive fire declared?...
		$offensiveShot = null;
		$noOfShots = Dice::d(4,1); //actual number of shots for this turn

		foreach($this->fireOrders as $fire){
			if(($fire->type =='normal') && ($fire->turn == $gamedata->turn)) $offensiveShot = $fire;
		}
		if($offensiveShot!==null){ //offensive fire declared, multiply!
			while($noOfShots > 1){ //first shot is already declared!
				$multipliedFireOrder = new FireOrder( -1, $offensiveShot->type, $offensiveShot->shooterid, $offensiveShot->targetid,
					$offensiveShot->weaponid, $offensiveShot->calledid, $offensiveShot->turn, $offensiveShot->firingMode,
					0, 0, 1, 0, 0, null, null
				);
				$multipliedFireOrder->addToDB = true;
				$this->fireOrders[] = $multipliedFireOrder;
				$noOfShots--;	      
			}
		}else{//offensive fire NOT declared, multiply guns for interception!
			$this->guns = $noOfShots; //d6 intercept shots
		}
	} //endof function beforeFiringOrderResolution
        
	    /*return 0 if given fire order was already intercepted by this weapon - this should prevent such assignment*/
	public function getInterceptionMod($gamedata, $intercepted)
	{
		$wasIntercepted = false;
		$interceptMod = 0;
		foreach($this->alreadyIntercepted as $alreadyAssignedAgainst){
			if ($alreadyAssignedAgainst->id == $intercepted->id){ //this fire order was already intercepted by this weapon, this Scattergun cannot do so again
				$wasIntercepted = true;
				break;//foreach
			}
		}
		if(!$wasIntercepted) $interceptMod = parent::getInterceptionMod($gamedata, $intercepted);
		return $interceptMod;
	}//endof  getInterceptionMod
        
	//on weapon being ordered to intercept - note which shot (fireorder, actually) was intercepted!
	public function fireDefensively($gamedata, $interceptedWeapon)
	{
		parent::fireDefensively($gamedata, $interceptedWeapon);
		$this->alreadyIntercepted[] = $interceptedWeapon;
	}	    
	    
        public function getDamage($fireOrder){
            return 3; 
        }
 
        public function setMinDamage()
        {
            $this->minDamage = 3;
        }
        public function setMaxDamage()
        {
            $this->maxDamage =  3;
        }
		
    }  // endof NexusCIDS



    class NexusACIDS extends Weapon //this is NOT a Pulse weapon, disregard Pulse-specific settings...
    {
	public $name = "NexusACIDS";
        public $displayName = "Advanced Close-In Defense System";
        public $iconPath = "NexusACIDS.png";
        public $animation = "trail";
        public $trailLength = 35;
        public $animationWidth = 2;
        public $projectilespeed = 10;
        public $animationExplosionScale = 0.10;
        public $animationColor =  array(245, 245, 44);
        public $trailColor = array(206, 206, 83);
	public $guns = 1; //multiplied to d6 at firing
	     
        public $loadingtime = 1;
        public $normalload = 1;	    
        public $priority = 3; //very light weapon
        	    
		public $ballisticIntercept = true;
        public $intercept = 1; //as it should be, but here they CAN combine vs same shot!
	    
	public $rangePenalty = 2;
        public $fireControl = array(3, 1, 1); // fighters, <mediums, <capitals
	    
	    public $damageType = "Standard"; 
	    public $weaponClass = "Matter"; 
	    
	    //temporary private variables
	    private $multiplied = false;
	    private $alreadyIntercepted = array();
	    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
		if ( $maxhealth == 0 ) $maxhealth = 6;
		if ( $powerReq == 0 ) $powerReq = 2;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
	    
        public function setSystemDataWindow($turn){            
            parent::setSystemDataWindow($turn);		
			$this->data["Special"] = "Fires d6 separate shots (actual number rolled at firing resolution).";
			$this->data["Special"] .= "<br>When fired defensively, a single ACIDS cannot engage the same incoming shot twice (even ballistic one).";
			$this->data["Special"] .= "<br>Ignores armor.";
        }
	    
	//if fired offensively - make d6 attacks (copies of 1 existing); 
	//if defensively - make weapon have d6 GUNS (would be temporary, but enough to assign multiple shots for interception)
	public function beforeFiringOrderResolution($gamedata){
		if($this->multiplied==true) return;//shots of this weapon are already multiplied
		$this->multiplied = true;//shots WILL be multiplied in a moment, mark this
		//is offensive fire declared?...
		$offensiveShot = null;
		$noOfShots = Dice::d(6,1); //actual number of shots for this turn

		foreach($this->fireOrders as $fire){
			if(($fire->type =='normal') && ($fire->turn == $gamedata->turn)) $offensiveShot = $fire;
		}
		if($offensiveShot!==null){ //offensive fire declared, multiply!
			while($noOfShots > 1){ //first shot is already declared!
				$multipliedFireOrder = new FireOrder( -1, $offensiveShot->type, $offensiveShot->shooterid, $offensiveShot->targetid,
					$offensiveShot->weaponid, $offensiveShot->calledid, $offensiveShot->turn, $offensiveShot->firingMode,
					0, 0, 1, 0, 0, null, null
				);
				$multipliedFireOrder->addToDB = true;
				$this->fireOrders[] = $multipliedFireOrder;
				$noOfShots--;	      
			}
		}else{//offensive fire NOT declared, multiply guns for interception!
			$this->guns = $noOfShots; //d6 intercept shots
		}
	} //endof function beforeFiringOrderResolution
        
	    /*return 0 if given fire order was already intercepted by this weapon - this should prevent such assignment*/
	public function getInterceptionMod($gamedata, $intercepted)
	{
		$wasIntercepted = false;
		$interceptMod = 0;
		foreach($this->alreadyIntercepted as $alreadyAssignedAgainst){
			if ($alreadyAssignedAgainst->id == $intercepted->id){ //this fire order was already intercepted by this weapon, this Scattergun cannot do so again
				$wasIntercepted = true;
				break;//foreach
			}
		}
		if(!$wasIntercepted) $interceptMod = parent::getInterceptionMod($gamedata, $intercepted);
		return $interceptMod;
	}//endof  getInterceptionMod
        
	//on weapon being ordered to intercept - note which shot (fireorder, actually) was intercepted!
	public function fireDefensively($gamedata, $interceptedWeapon)
	{
		parent::fireDefensively($gamedata, $interceptedWeapon);
		$this->alreadyIntercepted[] = $interceptedWeapon;
	}	    
	    
        public function getDamage($fireOrder){
            return 3; 
        }
 
        public function setMinDamage()
        {
            $this->minDamage = 3;
        }
        public function setMaxDamage()
        {
            $this->maxDamage = 3 ;
        }
		
    }  // endof NexusACIDS


















class NexusAutocannon extends Matter{
        public $trailColor = array(206, 206, 83);

        public $name = "NexusAutocannon";
        public $displayName = "Autocannon";
		public $iconPath = "NexusAutocannon.png";
	    
        public $animation = "trail";
        public $animationColor = array(245, 245, 44);
        public $animationExplosionScale = 0.10;
        public $projectilespeed = 10;
        public $animationWidth = 2;
        public $trailLength = 35;
        public $loadingtime = 1;
		public $guns = 1;
        public $priority = 5;

        public $rangePenalty = 1; //-2/hex
        public $fireControl = array(2, 3, 3); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 4;
            if ( $powerReq == 0 ) $powerReq = 1;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){ return 6;   }
        public function setMinDamage(){     $this->minDamage = 6 ;      }
        public function setMaxDamage(){     $this->maxDamage = 6 ;      }
		
}// endof NexusAutocannon


/*fighter-mounted variant*/
class NexusAutocannonFtr extends Matter{
        public $trailColor = array(206, 206, 83);

        public $name = "NexusAutocannonFtr";
        public $displayName = "Light Autocannon";
		public $iconPath = "NexusAutocannon.png";
	    
        public $animation = "trail";
        public $animationColor = array(245, 245, 44);
        public $animationExplosionScale = 0.10;
        public $projectilespeed = 10;
        public $animationWidth = 2;
        public $trailLength = 35;
        public $loadingtime = 2;
		public $guns = 1;
        public $priority = 8;
        public $ammunition = 6;

        public $rangePenalty = 1.5; //
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals

        public function stripForJson() {
            $strippedSystem = parent::stripForJson();
    
            $strippedSystem->ammunition = $this->ammunition;
           
            return $strippedSystem;
        }

        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            $this->data["Ammunition"] = $this->ammunition;
        }

        public function setAmmo($firingMode, $amount){
            $this->ammunition = $amount;
        }

       public function fire($gamedata, $fireOrder){ //note ammo usage
        	//debug::log("fire function");
            parent::fire($gamedata, $fireOrder);
            $this->ammunition--;
            Manager::updateAmmoInfo($fireOrder->shooterid, $this->id, $gamedata->id, $this->firingMode, $this->ammunition, $gamedata->turn);
        }

	function __construct($startArc, $endArc, $nrOfShots = 1){
            $this->defaultShots = $nrOfShots;
            $this->shots = $nrOfShots;
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }

        public function getDamage($fireOrder){ return 6;   }
        public function setMinDamage(){     $this->minDamage = 6 ;      }
        public function setMaxDamage(){     $this->maxDamage = 6 ;      }
		
}// endof NexusAutocannonFtr



/*fighter-mounted variant*/
class NexusAutogun extends Matter{
        public $trailColor = array(206, 206, 83);

        public $name = "NexusAutogun";
        public $displayName = "Autogun";
		public $iconPath = "NexusAutocannon.png";
	    
        public $animation = "trail";
        public $animationColor = array(245, 245, 44);
        public $animationExplosionScale = 0.10;
        public $projectilespeed = 10;
        public $animationWidth = 2;
        public $trailLength = 35;
        public $loadingtime = 1;
		public $guns = 1;
        public $priority = 5;
        public $ammunition = 6;

//        public $intercept = 2;
//        public $ballisticIntercept = true;
		
        public $rangePenalty = 2; //
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals

	function __construct($startArc, $endArc, $nrOfShots = 1){
            $this->defaultShots = $nrOfShots;
            $this->shots = $nrOfShots;
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            $this->data["Ammunition"] = $this->ammunition;
        }

        public function stripForJson() {
            $strippedSystem = parent::stripForJson();    
            $strippedSystem->ammunition = $this->ammunition;           
            return $strippedSystem;
        }        

        public function setAmmo($firingMode, $amount){
            $this->ammunition = $amount;
        }


       public function fire($gamedata, $fireOrder){ //note ammo usage
        	//debug::log("fire function");
            parent::fire($gamedata, $fireOrder);
            $this->ammunition--;
            Manager::updateAmmoInfo($fireOrder->shooterid, $this->id, $gamedata->id, $this->firingMode, $this->ammunition, $gamedata->turn);			
        }

        public function getDamage($fireOrder){ return Dice::d(3, 2);   }
        public function setMinDamage(){     $this->minDamage = 2 ;      }
        public function setMaxDamage(){     $this->maxDamage = 6 ;      }
		
}// endof NexusAutogun




class NexusHeavySentryGun extends Matter{
        public $trailColor = array(206, 206, 83);

        public $name = "NexusHeavySentryGun";
        public $displayName = "Heavy Sentry Gun";
		public $iconPath = "NexusHeavySentryGun.png";
	    
        public $animation = "trail";
        public $animationColor = array(245, 245, 44);
        public $animationExplosionScale = 0.10;
        public $projectilespeed = 10;
        public $animationWidth = 2;
        public $trailLength = 35;
        public $loadingtime = 3;
        public $priority = 8;

        public $rangePenalty = 0.66; // -2 / 3 hexes
        public $fireControl = array(-2, 1, 2); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 7;
            if ( $powerReq == 0 ) $powerReq = 3;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){ return Dice::d(6, 4);   }
        public function setMinDamage(){     $this->minDamage = 4 ;      }
        public function setMaxDamage(){     $this->maxDamage = 24 ;      }
		
}// endof NexusHeavySentryGun


class NexusMedSentryGun extends Matter{
        public $trailColor = array(206, 206, 83);

        public $name = "NexusMedSentryGun";
        public $displayName = "Medium Sentry Gun";
		public $iconPath = "NexusMedSentryGun.png";
	    
        public $animation = "trail";
        public $animationColor = array(245, 245, 44);
        public $animationExplosionScale = 0.10;
        public $projectilespeed = 10;
        public $animationWidth = 2;
        public $trailLength = 35;
        public $loadingtime = 3;
        public $priority = 8;

        public $rangePenalty = 1.0;
        public $fireControl = array(-2, 1, 2); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 6;
            if ( $powerReq == 0 ) $powerReq = 2;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){ return Dice::d(6, 3);   }
        public function setMinDamage(){     $this->minDamage = 3 ;      }
        public function setMaxDamage(){     $this->maxDamage = 18 ;      }
		
}// endof NexusMedSentryGun



class NexusLightSentryGun extends Matter{
        public $trailColor = array(206, 206, 83);

        public $name = "NexusLightSentryGun";
        public $displayName = "Light Sentry Gun";
		public $iconPath = "NexusLightSentryGun.png";
	    
        public $animation = "trail";
        public $animationColor = array(245, 245, 44);
        public $animationExplosionScale = 0.10;
        public $projectilespeed = 10;
        public $animationWidth = 2;
        public $trailLength = 35;
        public $loadingtime = 2;
        public $priority = 8;

        public $rangePenalty = 1; 
        public $fireControl = array(-1, 1, 2); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 5;
            if ( $powerReq == 0 ) $powerReq = 1;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){ return Dice::d(6, 2);   }
        public function setMinDamage(){     $this->minDamage = 2 ;      }
        public function setMaxDamage(){     $this->maxDamage = 12 ;      }
		
}// endof NexusMedAutocannon


class NexusDualParticleBeam extends Particle{
        public $trailColor = array(30, 170, 255);

        public $name = "NexusDualParticleBeam";
        public $displayName = "Dual Particle Beam";
		public $iconPath = "NexusDualParticleBeam.png";
	    
        public $animation = "trail";
        public $animationColor = array(255, 250, 230);
        public $animationExplosionScale = 0.15;
        public $projectilespeed = 15;
        public $animationWidth = 4;
        public $trailLength = 10;
        public $loadingtime = 1;
		public $guns = 2;
        public $priority = 5;
        public $intercept = 2;

        public $rangePenalty = 1; //-1/hex
        public $fireControl = array(4, 4, 4); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 6;
            if ( $powerReq == 0 ) $powerReq = 2;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){ return Dice::d(10, 1)+6;   }
        public function setMinDamage(){     $this->minDamage = 7 ;      }
        public function setMaxDamage(){     $this->maxDamage = 16 ;      }
}// endof NexusDualParticleBeam


class NexusTwinParticleBeam extends Particle{
        public $trailColor = array(30, 170, 255);

        public $name = "NexusTwinParticleBeam";
        public $displayName = "Twin Particle Beam";
		public $iconPath = "NexusTwinParticleBeam.png";
	    
        public $animation = "trail";
        public $animationColor = array(255, 250, 230);
        public $animationExplosionScale = 0.15;
        public $projectilespeed = 15;
        public $animationWidth = 4;
        public $trailLength = 10;
        public $loadingtime = 1;
		public $guns = 2;
        public $priority = 5;
        public $intercept = 2;

        public $rangePenalty = 1; //-1/hex
        public $fireControl = array(3, 3, 3); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 6;
            if ( $powerReq == 0 ) $powerReq = 2;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){ return Dice::d(10, 1)+4;   }
        public function setMinDamage(){     $this->minDamage = 5 ;      }
        public function setMaxDamage(){     $this->maxDamage = 14 ;      }
}// endof NexusTwinParticleBeam


class NexusLightParticleArray extends Particle{
        public $trailColor = array(30, 170, 255);

        public $name = "NexusLightParticleArray";
        public $displayName = "Light Particle Array";
		public $iconPath = "NexusLightParticleArray.png";
	    
        public $animation = "trail";
        public $animationColor = array(255, 250, 230);
        public $animationExplosionScale = 0.15;
        public $projectilespeed = 15;
        public $animationWidth = 4;
        public $trailLength = 10;
        public $loadingtime = 1;
        public $priority = 4;
        public $intercept = 2;
		public $guns = 2;

        public $rangePenalty = 2; //-1/hex
        public $fireControl = array(3, 2, 2); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 2;
            if ( $powerReq == 0 ) $powerReq = 2;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){ return Dice::d(3, 2)+1;   }
        public function setMinDamage(){     $this->minDamage = 3 ;      }
        public function setMaxDamage(){     $this->maxDamage = 7 ;      }
}// endof NexusLightParticleArray






    class NexusFighterArray extends LinkedWeapon{
        public $trailColor = array(30, 170, 255);

        public $name = "NexusFighterArray";
        public $displayName = "Fighter Array";
		public $iconPath = "NexusLightParticleArray.png";
		
        public $animation = "trail";
        public $animationColor = array(30, 170, 255);
        public $animationExplosionScale = 0.10;
        public $projectilespeed = 12;
        public $animationWidth = 2;
        public $trailLength = 10;

        public $intercept = 2;

        public $loadingtime = 1;
        public $shots = 2;
        public $defaultShots = 2;
		public $priority = 3;

        public $rangePenalty = 2;
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals
        private $damagebonus = 0;
        
        public $damageType = "Standard"; 
        public $weaponClass = "Particle"; 
        

        function __construct($startArc, $endArc, $damagebonus, $nrOfShots = 2){
            $this->damagebonus = $damagebonus;
            $this->defaultShots = $nrOfShots;
            $this->shots = $nrOfShots;
            $this->intercept = $nrOfShots;		


            if ($damagebonus >= 4) $this->priority++; //heavier varieties fire later in the queue
            if ($damagebonus >= 6) $this->priority++;
            if ($damagebonus >= 8) $this->priority++;

//            if($nrOfShots === 3){
//                $this->iconPath = "pairedParticleGun3.png";
//            }

            parent::__construct(0, 1, 0, $startArc, $endArc);

        }

        public function setSystemDataWindow($turn){

            //$this->data["Weapon type"] = "Particle";
            //$this->data["Damage type"] = "Standard";

            parent::setSystemDataWindow($turn);
        }

        public function getDamage($fireOrder){        return Dice::d(3, 2)+$this->damagebonus;   }
        public function setMinDamage(){     $this->minDamage = 2+$this->damagebonus ;      }
        public function setMaxDamage(){     $this->maxDamage = 6+$this->damagebonus ;      }

    }



/*fighter-mounted light particle array*/
/*class NexusFighterArray extends Particle{
        public $trailColor = array(30, 170, 255);

        public $name = "NexusFighterArray";
        public $displayName = "Fighter Array";
		public $iconPath = "NexusLightParticleArray.png";
	    
        public $animation = "trail";
        public $animationColor = array(255, 250, 230);
        public $animationExplosionScale = 0.1;
        public $projectilespeed = 15;
        public $animationWidth = 4;
        public $trailLength = 6;
        public $loadingtime = 1;
        public $priority = 4;
        public $intercept = 2;
		public $guns = 1;

        public $rangePenalty = 2; //-1/hex
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals

	function __construct($startArc, $endArc, $nrOfShots = 1){
            $this->defaultShots = $nrOfShots;
            $this->shots = $nrOfShots;
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }

        public function getDamage($fireOrder){ return Dice::d(3, 2)+1;   }
        public function setMinDamage(){     $this->minDamage = 3 ;      }
        public function setMaxDamage(){     $this->maxDamage = 7 ;      }
}// endof NexusLightParticleArray
*/


class NexusParticleArray extends Particle{
        public $trailColor = array(30, 170, 255);

        public $name = "NexusParticleArray";
        public $displayName = "Particle Array";
		public $iconPath = "NexusParticleArray.png";
	    
        public $animation = "beam";
        public $animationColor = array(255, 250, 230);
        public $animationExplosionScale = 0.15;
        public $projectilespeed = 15;
        public $animationWidth = 4;
        public $trailLength = 10;
        public $loadingtime = 1;
        public $priority = 4;
        public $intercept = 2;
		public $guns = 2;

        public $rangePenalty = 1; //-1/hex
        public $fireControl = array(3, 2, 2); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 4;
            if ( $powerReq == 0 ) $powerReq = 2;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){ return Dice::d(3, 2)+3;   }
        public function setMinDamage(){     $this->minDamage = 5 ;      }
        public function setMaxDamage(){     $this->maxDamage = 9 ;      }
}// endof NexusLightParticleArray


class NexusHeavyParticleArray extends Particle{
        public $trailColor = array(30, 170, 255);

        public $name = "NexusHeavyParticleArray";
        public $displayName = "Heavy Particle Array";
		public $iconPath = "NexusHeavyParticleArray.png";
	    
        public $animation = "bolt";
        public $animationColor = array(255, 250, 230);
        public $animationExplosionScale = 0.15;
        public $projectilespeed = 15;
        public $animationWidth = 4;
        public $trailLength = 10;
        public $loadingtime = 1;
        public $priority = 5;
        public $intercept = 1;

        public $rangePenalty = 1; //-1/hex
        public $fireControl = array(3, 3, 3); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 6;
            if ( $powerReq == 0 ) $powerReq = 2;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){ return Dice::d(3, 2)+7;   }
        public function setMinDamage(){     $this->minDamage = 9 ;      }
        public function setMaxDamage(){     $this->maxDamage = 13 ;      }
}// endof NexusHeavyPartcleArray


class NexusGatlingParticleArray extends Particle{
        public $trailColor = array(30, 170, 255);

        public $name = "NexusGatlingParticleArray";
        public $displayName = "Gatling Particle Array";
		public $iconPath = "NexusGatlingParticleArray.png";
	    
        public $animation = "bolt";
        public $animationColor = array(255, 250, 230);
        public $animationExplosionScale = 0.15;
        public $projectilespeed = 15;
        public $animationWidth = 4;
        public $trailLength = 10;
        public $loadingtime = 1;
        public $priority = 5;
        public $intercept = 3;
		public $guns = 2;		

        public $rangePenalty = 1; //-1/hex
        public $fireControl = array(2, 2, 2); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 6;
            if ( $powerReq == 0 ) $powerReq = 2;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){ return Dice::d(3, 2)+5;   }
        public function setMinDamage(){     $this->minDamage = 7 ;      }
        public function setMaxDamage(){     $this->maxDamage = 11 ;      }
}// endof NexusGatlingParticleArray



class NexusParticleBolt extends Particle{
        public $trailColor = array(30, 170, 255);

        public $name = "NexusParticleBolt";
        public $displayName = "Particle Bolt";
		public $iconPath = "NexusParticleBolt.png";
	    
        public $animation = "bolt";
        public $animationColor = array(255, 250, 230);
        public $animationExplosionScale = 0.15;
        public $projectilespeed = 15;
        public $animationWidth = 4;
        public $trailLength = 10;
        public $loadingtime = 1;
        public $priority = 5;
        public $intercept = 2;

        public $rangePenalty = 2; //-1/hex
        public $fireControl = array(3, 2, 1); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 4;
            if ( $powerReq == 0 ) $powerReq = 1;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){ return Dice::d(10, 1)+2;   }
        public function setMinDamage(){     $this->minDamage = 3 ;      }
        public function setMaxDamage(){     $this->maxDamage = 12 ;      }
}// endof NexusParticleBolt


class NexusTwinBolt extends Particle{
        public $trailColor = array(30, 170, 255);

        public $name = "NexusTwinBolt";
        public $displayName = "Twin Bolt";
		public $iconPath = "NexusTwinBolt.png";
	    
        public $animation = "bolt";
        public $animationColor = array(255, 250, 230);
        public $animationExplosionScale = 0.15;
        public $projectilespeed = 15;
        public $animationWidth = 4;
        public $trailLength = 10;
        public $loadingtime = 1;
        public $priority = 5;
        public $intercept = 2;
		public $guns = 2;

        public $rangePenalty = 2; //-1/hex
        public $fireControl = array(3, 2, 1); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 6;
            if ( $powerReq == 0 ) $powerReq = 2;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){ return Dice::d(10, 1)+4;   }
        public function setMinDamage(){     $this->minDamage = 5 ;      }
        public function setMaxDamage(){     $this->maxDamage = 14 ;      }
}// endof NexusTwinBolt


class NexusImprovedParticleBeam extends Particle{
        public $trailColor = array(30, 170, 255);

        public $name = "NexusImprovedParticleBeam";
        public $displayName = "Improved Particle Beam";
		public $iconPath = "NexusImprovedParticleBeam.png";
	    
        public $animation = "beam";
        public $animationColor = array(255, 250, 230);
        public $animationExplosionScale = 0.15;
        public $projectilespeed = 15;
        public $animationWidth = 4;
        public $trailLength = 10;
        public $loadingtime = 1;
        public $priority = 4;
        public $intercept = 2;

        public $rangePenalty = 1; //-1/hex
        public $fireControl = array(3, 3, 3); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 3;
            if ( $powerReq == 0 ) $powerReq = 1;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){ return Dice::d(10, 1)+4;   }
        public function setMinDamage(){     $this->minDamage = 5 ;      }
        public function setMaxDamage(){     $this->maxDamage = 14 ;      }
}// endof NexusImprovedParticleBeam


class NexusMinigun extends Weapon{

        public $name = "NexusMinigun";
        public $displayName = "Minigun";
        public $iconPath = "NexusMinigun.png"; 		
		
        public $animationArray = array(1=>'trail', 2=>'trail');
        public $animationColorArray = array(1=>array(245, 245, 44), 2=>array(245, 245, 44));
        public $trailLength = 5;
        public $animationWidth = 4;
        public $projectilespeed = 18;
        public $animationExplosionScale = 0.10;

	protected $useDie = 4; //die used for base number of hits
//		public $rof = 3;
		public $groupingArray = array(1=>25, 2=>0);
		public $maxpulses = 6;
        public $priorityArray = array(1=>5, 2=>7); // Matter weapon
	public $defaultShotsArray = array(1=>6, 2=>1); //for Pulse mode it should be equal to maxpulses
        
        public $loadingtimeArray = array(1=>1, 2=>1);
        public $rangePenaltyArray = array(1=>2, 2=>2);
        public $intercept = 1;
		public $ballisticIntercept = true;
		public $firingModes = array(1 =>'Burst', 2=>'Concentrated');
        
        public $fireControlArray = array(1=>array(3, 2, 2), 2=>array(1, 0, 0)); // fighters, <mediums, <capitals
        
        public $damageTypeArray = array(1=>"Pulse", 2=>"Standard"); //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
        public $weaponClassArray = array(1=>'Matter', 2=>'Matter');

		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
			$this->data["Special"] = "Ignores armor, does not overkill.";
			$this->data["Special"] .= "<br>Burst mode: 1d4 +1/25% pulses (max 6) of 3 damage.";
			$this->data["Special"] .= "<br>Concentrated mode:  1d3*4 damage but -10 fire control.";
			$this->data["Special"] .= "<br>Can intercept ballistic weapons only.";
		}

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            if ( $maxhealth == 0 ) $maxhealth = 4;
            if ( $powerReq == 0 ) $powerReq = 1;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

		public function getDamage($fireOrder){
        	switch($this->firingMode){ 
            	case 1:
                	return 3;
			    			break;
            	case 2:
            	   	return Dice::d(3, 1)*4;
			    			break;
        	}
		}

		public function setMinDamage(){
				switch($this->firingMode){
						case 1:
								$this->minDamage = 3;
								break;
						case 2:
								$this->minDamage = 4;
								break;
				}
				$this->minDamageArray[$this->firingMode] = $this->minDamage;
		}							
		
		public function setMaxDamage(){
				switch($this->firingMode){
						case 1:
								$this->maxDamage = 3;
								break;
						case 2:
								$this->maxDamage = 12;
								break;
				}
				$this->maxDamageArray[$this->firingMode] = $this->maxDamage;
		}

		public function stripForJson(){
			$strippedSystem = parent::stripForJson();
			$strippedSystem->data = $this->data;
			$strippedSystem->minDamage = $this->minDamage;
			$strippedSystem->minDamageArray = $this->minDamageArray;
			$strippedSystem->maxDamage = $this->maxDamage;
			$strippedSystem->maxDamageArray = $this->maxDamageArray;				

			return $strippedSystem;
		}

	//necessary for Pulse mode
        protected function getPulses($turn)
        {
            return Dice::d(4);
        }
        protected function getExtraPulses($needed, $rolled)
        {
            return floor(($needed - $rolled) / ($this->grouping));
        }
	public function rollPulses($turn, $needed, $rolled){
		$pulses = $this->getPulses($turn);
		$pulses+= $this->getExtraPulses($needed, $rolled);
		$pulses=min($pulses,$this->maxpulses);
		return $pulses;
	}
		
    } // endof NexusMinigun	

/*fighter-mounted variant*/
class NexusMinigunFtr extends Weapon{

        public $name = "NexusMinigunFtr";
        public $displayName = "Light Minigun";
        public $iconPath = "NexusMinigun.png"; 		
		
        public $animationArray = array(1=>'trail', 2=>'trail');
        public $animationColorArray = array(1=>array(245, 245, 44), 2=>array(245, 245, 44));
        public $trailLength = 5;
        public $animationWidth = 4;
        public $projectilespeed = 18;
        public $animationExplosionScale = 0.10;
        public $ammunition = 12;

	protected $useDie = 3; //die used for base number of hits
//		public $rof = 3;
		public $groupingArray = array(1=>0, 2=>0);
		public $maxpulses = 3;
        public $priorityArray = array(1=>5, 2=>7); // Matter weapon
	public $defaultShotsArray = array(1=>3, 2=>1); //for Pulse mode it should be equal to maxpulses
        
        public $loadingtimeArray = array(1=>1, 2=>1);
        public $rangePenaltyArray = array(1=>2, 2=>2);
        public $intercept = 1;
		public $ballisticIntercept = true;
		public $firingModes = array(1 =>'Burst', 2=>'Concentrated');
        
        public $fireControlArray = array(1=>array(0, 0, 0), 2=>array(-2, -2, -2)); // fighters, <mediums, <capitals
        
        public $damageTypeArray = array(1=>"Pulse", 2=>"Standard"); //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
        public $weaponClassArray = array(1=>'Matter', 2=>'Matter');

	function __construct($startArc, $endArc, $nrOfShots = 1){
            $this->defaultShots = $nrOfShots;
            $this->shots = $nrOfShots;
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }

		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
			$this->data["Special"] = "Ignores armor, does not overkill.";
			$this->data["Special"] .= "<br>Burst mode: 1d3 pulses of 3 damage.";
			$this->data["Special"] .= "<br>Concentrated mode:  6 damage but -10 fire control.";
			$this->data["Special"] .= "<br>Can intercept ballistic weapons only.";
            $this->data["Ammunition"] = $this->ammunition;
		}
		
        public function setAmmo($firingMode, $amount){
            $this->ammunition = $amount;
        }

       public function fire($gamedata, $fireOrder){ //note ammo usage
        	//debug::log("fire function");
            parent::fire($gamedata, $fireOrder);
            $this->ammunition--;
            Manager::updateAmmoInfo($fireOrder->shooterid, $this->id, $gamedata->id, $this->firingMode, $this->ammunition, $gamedata->turn);
        }

		public function getDamage($fireOrder){
        	switch($this->firingMode){ 
            	case 1:
                	return 3;
			    			break;
            	case 2:
            	   	return 6;
			    			break;
        	}
		}

		public function setMinDamage(){
				switch($this->firingMode){
						case 1:
								$this->minDamage = 3;
								break;
						case 2:
								$this->minDamage = 6;
								break;
				}
				$this->minDamageArray[$this->firingMode] = $this->minDamage;
		}							
		
		public function setMaxDamage(){
				switch($this->firingMode){
						case 1:
								$this->maxDamage = 3;
								break;
						case 2:
								$this->maxDamage = 6;
								break;
				}
				$this->maxDamageArray[$this->firingMode] = $this->maxDamage;
		}

		public function stripForJson(){
			$strippedSystem = parent::stripForJson();
			$strippedSystem->data = $this->data;
			$strippedSystem->minDamage = $this->minDamage;
			$strippedSystem->minDamageArray = $this->minDamageArray;
			$strippedSystem->maxDamage = $this->maxDamage;
			$strippedSystem->maxDamageArray = $this->maxDamageArray;				

            $strippedSystem->ammunition = $this->ammunition;

			return $strippedSystem;
		}

	//necessary for Pulse mode
        protected function getPulses($turn)
        {
            return Dice::d(3);
        }
        protected function getExtraPulses($needed, $rolled)
        {
            return floor(($needed - $rolled) / ($this->grouping));
        }
	public function rollPulses($turn, $needed, $rolled){
		$pulses = $this->getPulses($turn);
//		$pulses+= $this->getExtraPulses($needed, $rolled);
		$pulses=min($pulses,$this->maxpulses);
		return $pulses;
	}
		
    } // endof NexusMinigunFtr	
	
	
class NexusAutocannonDefender extends Particle{

        public $name = "NexusAutocannonDefender";
        public $displayName = "Autocannon Defender";
        public $iconPath = "NexusAutocannonDefender.png"; 		
        public $animation = "trail";
        public $trailLength = 12;
        public $animationWidth = 4;
        public $projectilespeed = 9;
        public $animationExplosionScale = 0.10;
        
        public $loadingtime = 1;
        public $priority = 4; 
        
        public $rangePenalty = 1;
        public $fireControl = array(2, 2, 2); // fighters, <mediums, <capitals
        
        public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
        public $weaponClass = "Particle";

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            if ( $maxhealth == 0 ) $maxhealth = 4;
            if ( $powerReq == 0 ) $powerReq = 1;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
        public function getDamage($fireOrder){ return Dice::d(6, 2)+1; }
        public function setMinDamage(){ $this->minDamage = 3 ; }
        public function setMaxDamage(){ $this->maxDamage = 13 ; }		
		
    } // endof NexusAutocannonDefender	


    class NexusProtector extends Particle{ 
        public $trailColor = array(30, 170, 255);

        public $name = "NexusProtector";
        public $displayName = "Protector";
        public $animation = "beam";
        public $animationColor = array(255, 250, 230);
        public $animationExplosionScale = 0.1;
        public $projectilespeed = 10;
        public $animationWidth = 3;
        public $trailLength = 8;
	    public $iconPath = "NexusProtector.png";
		public $canInterceptUninterceptable = true; //able to intercept shots that are normally uninterceptable, eg. Lasers

        public $intercept = 2;
        public $freeintercept = true; //can intercept fire directed at different unit
        public $freeinterceptspecial = true; //has own custom routine for deciding whether third party interception is legal
        public $loadingtime = 1;


        public $rangePenalty = 2; //-2/hex
        public $fireControl = array(null, null, null); // fighters, <mediums, <capitals
        public $priority = 1; //light Standard 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
			if ( $maxhealth == 0 ) $maxhealth = 4;
            if ( $powerReq == 0 ) $powerReq = 1;            
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
			$this->data["Special"] = "May intercept for friendly units. Must have friendly and enemy unit in arc and have friendly unit within 3 hexes.";
			$this->data["Special"] .= "<br>Can intercept laser weapons.";
		}
		
		public function canFreeInterceptShot($gamedata, $fireOrder, $shooter, $target, $interceptingShip, $firingWeapon){
			//target must be within 3 hexes
			$distance = mathlib::getDistanceHex($interceptingShip, $target);
			if ($distance > 3) return false;
			
			//both source and target of fire must be in arc
			//first check target
			$targetBearing = $interceptingShip->getBearingOnUnit($target);
			if (!mathlib::isInArc($targetBearing, $this->startArc, $this->endArc)) return false;
			//check on source - launch hex for ballistics, current position for direct fire
			if ($firingWeapon->ballistic){
				$movement = $shooter->getLastTurnMovement($fireOrder->turn);
				$pos = mathlib::hexCoToPixel($movement->position); //launch hex
				$sourceBearing = $interceptingShip->getBearingOnPos($pos);				
			}else{ //direct fire
				$sourceBearing = $interceptingShip->getBearingOnUnit($shooter);
			}
			if (!mathlib::isInArc($sourceBearing, $this->startArc, $this->endArc)) return false;
						
			return true;
		}

        public function getDamage($fireOrder){        return 0;   }
        public function setMinDamage(){     $this->minDamage = 0 ;      }
        public function setMaxDamage(){     $this->maxDamage = 0 ;      }
    }	//endof class NexusProtector

	
    class NexusInterceptorArray extends Particle{ 
        public $trailColor = array(30, 170, 255);

        public $name = "NexusInterceptorArray";
        public $displayName = "Interceptor Array";
        public $animation = "beam";
        public $animationColor = array(255, 250, 230);
        public $animationExplosionScale = 0.1;
        public $projectilespeed = 10;
        public $animationWidth = 3;
        public $trailLength = 8;
	    public  $iconPath = "NexusInterceptorArray.png";

        public $intercept = 3;
        public $freeintercept = true; //can intercept fire directed at different unit
        public $freeinterceptspecial = true; //has own custom routine for deciding whether third party interception is legal
        public $loadingtime = 1;


        public $rangePenalty = 2; //-2/hex
        public $fireControl = array(4, 2, 0); // fighters, <mediums, <capitals
        public $priority = 4; //light Standard 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
			if ( $maxhealth == 0 ) $maxhealth = 4;
            if ( $powerReq == 0 ) $powerReq = 2;            
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
			$this->data["Special"] = "May intercept for friendly units. Must have friendly and enemy unit in arc and have friendly unit within 3 hexes.";
		}
		
		public function canFreeInterceptShot($gamedata, $fireOrder, $shooter, $target, $interceptingShip, $firingWeapon){
			//target must be within 3 hexes
			$distance = mathlib::getDistanceHex($interceptingShip, $target);
			if ($distance > 3) return false;
			
			//both source and target of fire must be in arc
			//first check target
			$targetBearing = $interceptingShip->getBearingOnUnit($target);
			if (!mathlib::isInArc($targetBearing, $this->startArc, $this->endArc)) return false;
			//check on source - launch hex for ballistics, current position for direct fire
			if ($firingWeapon->ballistic){
				$movement = $shooter->getLastTurnMovement($fireOrder->turn);
				$pos = mathlib::hexCoToPixel($movement->position); //launch hex
				$sourceBearing = $interceptingShip->getBearingOnPos($pos);				
			}else{ //direct fire
				$sourceBearing = $interceptingShip->getBearingOnUnit($shooter);
			}
			if (!mathlib::isInArc($sourceBearing, $this->startArc, $this->endArc)) return false;
						
			return true;
		}

        public function getDamage($fireOrder){        return Dice::d(3,2)+5;   }
        public function setMinDamage(){     $this->minDamage = 8 ;      }
        public function setMaxDamage(){     $this->maxDamage = 12 ;      }
    }	//endof class NexusInterceptorArray


/*fighter-mounted weapon*/
class NexusMauler extends Particle{

        public $name = "NexusMauler";
        public $displayName = "Mauler";
        public $iconPath = "NexusMauler.png"; 	


        public $animation = "trail";
        public $animationColor = array(139, 239, 250);
        public $animationExplosionScale = 0.15;
        public $projectilespeed = 9;
        public $animationWidth = 6;
        public $trailLength = 10;

        public $loadingtime = 2;
        public $exclusive = true;
        public $priority = 4; 
        
        public $rangePenalty = 1;
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals
        
        public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
        public $weaponClass = "Particle";

	function __construct($startArc, $endArc, $nrOfShots = 1){
            $this->defaultShots = $nrOfShots;
            $this->shots = $nrOfShots;
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }
		
        public function getDamage($fireOrder){ return Dice::d(6, 2)+6; }
        public function setMinDamage(){ $this->minDamage = 8 ; }
        public function setMaxDamage(){ $this->maxDamage = 18 ; }		
		
    } // endof NexusMauler	



    class NexusParticleGrid extends Particle{
        public $trailColor = array(30, 170, 255);

        public $name = "NexusParticleGrid";
        public $displayName = "Particle Grid";
		public $iconPath = "NexusParticleGrid.png";
	    
        public $animation = "beam";
        public $animationColor = array(205, 200, 200);
        public $animationExplosionScale = 0.15;
        public $projectilespeed = 10;
        public $animationWidth = 3;
        public $trailLength = 10;

        public $intercept = 2;
        public $loadingtime = 1;
        public $priority = 3;

        public $rangePenalty = 2; //-2/hex
        public $fireControl = array(3, 0, 0); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 3;
            if ( $powerReq == 0 ) $powerReq = 1;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){ return Dice::d(10, 1)+1;   }
        public function setMinDamage(){     $this->minDamage = 2 ;      }
        public function setMaxDamage(){     $this->maxDamage = 11 ;      }
    }



// END OF PARTICLE WEAPONS



// MATTER WEAPONS


class NexusSpiker extends Matter{
        public $trailColor = array(30, 170, 255);

        public $name = "NexusSpiker";
        public $displayName = "Spiker";
		public $iconPath = "NexusSpiker.png";
	    
        public $animation = "trail";
        public $animationColor = array(255, 250, 230);
        public $animationExplosionScale = 0.15;
        public $projectilespeed = 15;
        public $animationWidth = 4;
        public $trailLength = 10;
        public $loadingtime = 2;
        public $priority = 8;
		
        public $ammunition = 10; //limited number of shots				
 
        public $rangePenalty = 0.5; 
        public $fireControl = array(-4, 3, 0); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 6;
            if ( $powerReq == 0 ) $powerReq = 4;
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
		
        public function getDamage($fireOrder){ return 8; }
		
        public function setAmmo($firingMode, $amount){
            $this->ammunition = $amount;
        }
       public function fire($gamedata, $fireOrder){ //note ammo usage
            parent::fire($gamedata, $fireOrder);
            $this->ammunition--;
            Manager::updateAmmoInfo($fireOrder->shooterid, $this->id, $gamedata->id, $this->firingMode, $this->ammunition, $gamedata->turn);
        }		

        public function setMinDamage(){     $this->minDamage = 8 ;      }
        public function setMaxDamage(){     $this->maxDamage = 8 ;      }
}// endof NexusSpiker


    class NexusUltralightRailgun extends Matter
    {
        public $name = "NexusUltralightRailgun";
        public $displayName = "Ultralight Railgun";
        public $animation = "trail";
        public $iconPath = "NexusUltralightRailgun.png";
        public $animationColor = array(250, 250, 190);
        public $projectilespeed = 30;
        public $animationWidth = 4;
        public $animationExplosionScale = 0.20;
        public $priority = 6;
        
        public $loadingtime = 1;
		
        public $rangePenalty = 2;
        public $fireControl = array(2, 2, 0); // fighters, <mediums, <capitals
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
    	public function getDamage($fireOrder){        return Dice::d(6, 1)+2;   }
        public function setMinDamage(){     $this->minDamage = 3 ;      }
        public function setMaxDamage(){     $this->maxDamage = 8 ;      }    
    } // endof NexusUltralightRailgun



class NexusHeavyCoilgun extends Matter{
//		intended for base / OSAT use only        
        public $trailColor = array(226, 26, 20);

        public $name = "NexusHeavyCoilgun";
        public $displayName = "Heavy Coilgun";
		public $iconPath = "NexusCoilgun.png";
	    
        public $animation = "trail";
        public $animationColor = array(250, 191, 190);
        public $animationExplosionScale = 0.25;
        public $projectilespeed = 30;
        public $animationWidth = 4;
        public $trailLength = 10;
        public $loadingtime = 3;
		public $guns = 1;
        public $priority = 9;

        public $rangePenalty = 0.25; //-1/4 hexes
        public $fireControl = array(null, 3, 3); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 12;
            if ( $powerReq == 0 ) $powerReq = 5;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){ return Dice::d(10, 3)+3;   }
        public function setMinDamage(){     $this->minDamage = 6 ;      }
        public function setMaxDamage(){     $this->maxDamage = 33 ;      }
}

// endof NexusHeavyCoilgun



class NexusCoilgun extends Matter{
        public $trailColor = array(226, 26, 20);

        public $name = "NexusCoilgun";
        public $displayName = "Coilgun";
		public $iconPath = "NexusCoilgun.png";
	    
        public $animation = "trail";
        public $animationColor = array(250, 191, 190);
        public $animationExplosionScale = 0.25;
        public $projectilespeed = 20;
        public $animationWidth = 4;
        public $trailLength = 10;
        public $loadingtime = 3;
		public $guns = 1;
        public $priority = 9;

        public $rangePenalty = 0.33; //-1/3 hexes
        public $fireControl = array(-4, 2, 2); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 10;
            if ( $powerReq == 0 ) $powerReq = 4;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){ return Dice::d(10, 3)+3;   }
        public function setMinDamage(){     $this->minDamage = 6 ;      }
        public function setMaxDamage(){     $this->maxDamage = 33 ;      }
}// endof NexusCoilgun


class NexusLightCoilgun extends Matter{
        public $trailColor = array(226, 26, 20);

        public $name = "NexusLightCoilgun";
        public $displayName = "Light Coilgun";
		public $iconPath = "NexusLightCoilgun.png";
	    
        public $animation = "trail";
        public $animationColor = array(250, 191, 190);
        public $animationExplosionScale = 0.25;
        public $projectilespeed = 20;
        public $animationWidth = 4;
        public $trailLength = 10;
        public $loadingtime = 2;
		public $guns = 1;
        public $priority = 9;

        public $rangePenalty = 0.5; // -1 per 2 hexes
        public $fireControl = array(-4, 2, 2); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 7;
            if ( $powerReq == 0 ) $powerReq = 3;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){ return Dice::d(10, 2)+2;   }
        public function setMinDamage(){     $this->minDamage = 4 ;      }
        public function setMaxDamage(){     $this->maxDamage = 22 ;      }
		
}// endof NexusLightCoilgun



class NexusDualSlugCannon extends Matter{
        public $trailColor = array(11, 152, 155);

        public $name = "NexusDualSlugCannon";
        public $displayName = "Dual Slug Cannon";
		public $iconPath = "NexusDualSlugCannon.png";
	    
        public $animation = "trail";
        public $animationColor = array(189, 250, 251);
        public $animationExplosionScale = 0.25;
        public $projectilespeed = 25;
        public $animationWidth = 2;
        public $trailLength = 5;
        public $loadingtime = 1;
		public $guns = 2;
        public $priority = 6;

        public $ammunition = 40; //limited number of shots

        public $rangePenalty = 2; //-2 /hexes
        public $fireControl = array(2, 4, 0); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 3;
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
            $this->data["Special"] = "Ignores armor, no overkill (Ballistic+Matter weapon).";
            $this->data["Ammunition"] = $this->ammunition;
        }

        public function getDamage($fireOrder){
            $dmg = 3;
            return $dmg;
       }

        public function setAmmo($firingMode, $amount){
            $this->ammunition = $amount;
        }
       public function fire($gamedata, $fireOrder){ //note ammo usage
            parent::fire($gamedata, $fireOrder);
            $this->ammunition--;
            Manager::updateAmmoInfo($fireOrder->shooterid, $this->id, $gamedata->id, $this->firingMode, $this->ammunition, $gamedata->turn);
        }


        public function setMinDamage(){     $this->minDamage = 3 ;      }
        public function setMaxDamage(){     $this->maxDamage = 3 ;      }
}// endof NexusDualSlugCannon


class NexusMicroCannon extends Matter{
        public $trailColor = array(11, 152, 155);

        public $name = "NexusMicroCannon";
        public $displayName = "Micro Cannon";
		public $iconPath = "NexusMicroCannon.png";
	    
        public $animation = "trail";
        public $animationColor = array(189, 250, 251);
        public $animationExplosionScale = 0.25;
        public $projectilespeed = 25;
        public $animationWidth = 2;
        public $trailLength = 5;
        public $loadingtime = 1;
		public $guns = 2;
        public $priority = 6;

        public $ammunition = 40; //limited number of shots

        public $rangePenalty = 3; //-2 /hexes
        public $fireControl = array(2, 4, 0); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 1;
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
            $this->data["Special"] = "Ignores armor, no overkill (Ballistic+Matter weapon).";
            $this->data["Ammunition"] = $this->ammunition;
        }

        public function getDamage($fireOrder){
            $dmg = 1;
            return $dmg;
       }

        public function setAmmo($firingMode, $amount){
            $this->ammunition = $amount;
        }
       public function fire($gamedata, $fireOrder){ //note ammo usage
            parent::fire($gamedata, $fireOrder);
            $this->ammunition--;
            Manager::updateAmmoInfo($fireOrder->shooterid, $this->id, $gamedata->id, $this->firingMode, $this->ammunition, $gamedata->turn);
        }


        public function setMinDamage(){     $this->minDamage = 1 ;      }
        public function setMaxDamage(){     $this->maxDamage = 1 ;      }
}// endof NexusDualSlugCannon



class NexusFletchletteGun extends Pulse{

        public $name = "NexusFletchletteGun";
        public $displayName = "Fletchlette Gun";
        public $iconPath = "NexusFletchletteGun.png"; 		
        public $animation = "trail";
        public $trailLength = 12;
        public $animationWidth = 4;
        public $projectilespeed = 9;
        public $animationExplosionScale = 0.10;
        public $rof = 2;
        public $grouping = 25;
        public $maxpulses = 3;
        
        public $loadingtime = 2;
        public $intercept = 2;
        public $priority = 9; // Matter weapon
        
        public $rangePenalty = 1;
        public $fireControl = array(1, 3, 0); // fighters, <mediums, <capitals
        
        public $damageType = "Matter"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
        public $weaponClass = "Pulse";

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            if ( $maxhealth == 0 ) $maxhealth = 6;
            if ( $powerReq == 0 ) $powerReq = 3;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
        public function getDamage($fireOrder){ return Dice::d(6, 1)+3; }
        public function setMinDamage(){ $this->minDamage = 4 ; }
        public function setMaxDamage(){ $this->maxDamage = 9 ; }		
		
    } // endof NexusFletchletteGun


class NexusHeavyFletchletteGun extends Pulse{

        public $name = "NexusHeavyFletchletteGun";
        public $displayName = "Heavy Fletchlette Gun";
        public $iconPath = "NexusHeavyFletchletteGun.png"; 		
        public $animation = "trail";
        public $trailLength = 12;
        public $animationWidth = 4;
        public $projectilespeed = 9;
        public $animationExplosionScale = 0.10;
        public $rof = 3;
        public $grouping = 25;
        public $maxpulses = 5;
        
        public $loadingtime = 3;
        public $priority = 9; // Matter weapon
        
        public $rangePenalty = 0.50;
        public $fireControl = array(0, 4, 0); // fighters, <mediums, <capitals
        
        public $damageType = "Matter"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
        public $weaponClass = "Pulse";

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            if ( $maxhealth == 0 ) $maxhealth = 7;
            if ( $powerReq == 0 ) $powerReq = 5;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
        public function getDamage($fireOrder){ return Dice::d(6, 1)+6; }
        public function setMinDamage(){ $this->minDamage = 7 ; }
        public function setMaxDamage(){ $this->maxDamage = 12 ; }		
		
    } // endof NexusHeavyFletchletteGun


class NexusImpactor extends Matter{
        public $trailColor = array(226, 26, 20);

        public $name = "NexusImpactor";
        public $displayName = "Impactor";
		public $iconPath = "NexusImpactor.png";
	    
        public $animation = "trail";
        public $animationColor = array(250, 191, 190);
        public $animationExplosionScale = 0.25;
        public $projectilespeed = 20;
        public $animationWidth = 4;
        public $trailLength = 10;
        public $loadingtime = 2;
        public $priority = 9;

        public $rangePenalty = 0.33; //-1/3 hexes
        public $fireControl = array(-3, 1, 2); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 6;
            if ( $powerReq == 0 ) $powerReq = 3;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){ return Dice::d(10, 2)+0;   }
        public function setMinDamage(){     $this->minDamage = 2 ;      }
        public function setMaxDamage(){     $this->maxDamage = 20 ;      }
}// endof NexusImpactor


    class NexusLightGaussCannon extends MatterCannon
    {
        public $name = "NexusLightGaussCannon";
        public $displayName = "Light Gauss Cannon";
		public $iconPath = "NexusLightGaussCannon.png";
        public $animation = "trail";
        public $animationColor = array(250, 250, 190);
        public $projectilespeed = 28;
        public $animationWidth = 4;
        public $animationExplosionScale = 0.20;
        public $trailLength = 8;
        
        public $loadingtime = 1;
        
        public $rangePenalty = 1;
        public $fireControl = array(-2, 2, 1); // fighters, <mediums, <capitals 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 6;
            if ( $powerReq == 0 ) $powerReq = 3;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return Dice::d(10, 1)+3;   }
        public function setMinDamage(){     $this->minDamage = 4 ;      }
        public function setMaxDamage(){     $this->maxDamage = 13 ;      }
		
    }  // endof NexusLightGaussCannon


class NexusLightMatterGun extends Matter{

        public $name = "NexusLightMatterGun";
        public $displayName = "Light Matter Gun";
        public $iconPath = "NexusLightMatterGun.png"; 		
        public $animation = "trail";
        public $trailLength = 12;
        public $animationWidth = 4;
        public $projectilespeed = 9;
        public $animationExplosionScale = 0.10;

        public $loadingtime = 1;
        public $priority = 9; // Matter weapon
        
        public $rangePenalty = 2;
        public $fireControl = array(-2, 4, 0); // fighters, <mediums, <capitals
        
        public $damageType = "Matter"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            if ( $maxhealth == 0 ) $maxhealth = 4;
            if ( $powerReq == 0 ) $powerReq = 1;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
        public function getDamage($fireOrder){ return Dice::d(6, 1)+0; }
        public function setMinDamage(){ $this->minDamage = 1 ; }
        public function setMaxDamage(){ $this->maxDamage = 6 ; }		
		
    } // endof NexusLightMatterGun


class NexusMatterGun extends Matter{

        public $name = "NexusMatterGun";
        public $displayName = "Matter Gun";
        public $iconPath = "NexusMatterGun.png"; 		
        public $animation = "trail";
        public $trailLength = 12;
        public $animationWidth = 4;
        public $projectilespeed = 9;
        public $animationExplosionScale = 0.10;

        public $loadingtime = 2;
        public $priority = 9; // Matter weapon
        
        public $rangePenalty = 1;
        public $fireControl = array(-3, 4, 0); // fighters, <mediums, <capitals
        
        public $damageType = "Matter"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            if ( $maxhealth == 0 ) $maxhealth = 5;
            if ( $powerReq == 0 ) $powerReq = 3;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
        public function getDamage($fireOrder){ return Dice::d(6, 1)+8; }
        public function setMinDamage(){ $this->minDamage = 9 ; }
        public function setMaxDamage(){ $this->maxDamage = 17 ; }		
		
    } // endof NexusMatterGun




	/*custom Orieni fighter weapon*/
    class LightGatlingGun extends LinkedWeapon{
        public $name = "LightGatlingGun";
        public $displayName = "Light Gatling Guns";
	    public $iconPath = 'pairedGatlingGun.png';
        public $animation = "trail";
        public $animationColor = array(250, 250, 190);
        public $projectilespeed = 24;
        public $animationWidth = 2;
        public $trailLength = 12;
        public $animationExplosionScale = 0.15;
        public $shots = 2;
        public $defaultShots = 2;
        public $ammunition = 6;
	    
        
        public $loadingtime = 1;

        public $intercept = 2;
        public $ballisticIntercept = true;

        public $rangePenalty = 2;
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals
        private $damagebonus = 0;
	    
	    public $noOverkill = true;
	    
	    public $priority = 3;//equivalent of d6+4, due to Matter properties 
		
	    
	    public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    	public $weaponClass = "Matter"; //MANDATORY (first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!  
	 

        function __construct($startArc, $endArc){
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }

        public function stripForJson() {
            $strippedSystem = parent::stripForJson();
    
            $strippedSystem->ammunition = $this->ammunition;
           
            return $strippedSystem;
        }
        
	    
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            $this->data["Special"] = "Ignores armor, does not overkill.";
            $this->data["Special"] .= "<br>Can intercept ballistic weapons only.";
            $this->data["Ammunition"] = $this->ammunition;
        }


        public function getSystemArmourStandard($target, $system, $gamedata, $fireOrder, $pos=null){
            return 0; //Matter ignores armor!
        }



        public function setAmmo($firingMode, $amount){
            $this->ammunition = $amount;
        }


       public function fire($gamedata, $fireOrder){ //note ammo usage
        	//debug::log("fire function");
            parent::fire($gamedata, $fireOrder);
            $this->ammunition--;
            Manager::updateAmmoInfo($fireOrder->shooterid, $this->id, $gamedata->id, $this->firingMode, $this->ammunition, $gamedata->turn);
        }
    
        public function getDamage($fireOrder){
            $dmg = Dice::d(3, 1);
            return $dmg;
        }
        public function setMinDamage(){     $this->minDamage = 1;      }
        public function setMaxDamage(){     $this->maxDamage = 3;      }

    }






// END OF MATTER WEAPONS


// DUAL-MODE WEAPONS



    class NexusLightAssaultCannon extends Particle{
        public $trailColor = array(190, 75, 20);

        public $name = "NexusLightAssaultCannon";
        public $displayName = "Light Assault Cannon";
		public $iconPath = "NexusLightAssaultCannon.png";
	    
        public $animation = "trail";
        public $animationColor = array(255, 11, 11);
        public $animationExplosionScale = 0.3;
        public $projectilespeed = 12;
        public $animationWidth = 2;
        public $trailLength = 10;

        public $loadingtime = 2;
        public $priority = 5;

        public $rangePenalty = 0.5; 
        public $fireControl = array(null, 1, 2); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 6;
            if ( $powerReq == 0 ) $powerReq = 3;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){ return Dice::d(10, 2)+7;   }
        public function setMinDamage(){     $this->minDamage = 9 ;      }
        public function setMaxDamage(){     $this->maxDamage = 27 ;      }
    }



//class NexusLightAssaultCannon extends Weapon{ 
/*Dual mode weapon based off the EA Heavy Laser-Pulse Array code*/	
/*        public $name = "NexusLightAssaultCannon";
        public $displayName = "Light Assault Cannon";
	    public $iconPath = "NexusLightAssaultCannon.png";
	
	//Unlike EA Heavy Laser-Pulse Array, the Light Assault Cannon is just
	//firing a shell with different warheads. Therefore it uses the same trail
	//animation. However, I will use different animation colors for the 
	//different modes to give visual distinction.
	public $animationArray = array(1=>'trail', 2=>'trail');
        public $animationColorArray = array(1=>array(255, 11, 11), 2=>array(124, 219, 226));
        public $animationWidthArray = array(1=>2, 2=>2);
	public $trailColor = array(190, 75, 20); //not used for Laser animation?...
        public $trailLength = 10;//not used for Laser animation?...
        public $projectilespeed = 12;//not used for Laser animation?...
        public $animationExplosionScaleArray = array(1=>0.3, 2=>2);//not used for Laser animation?...
	
	
	//actual weapons data
    public $priorityArray = array(1=>7, 2=>1);
	public $uninterceptableArray = array(1=>false, 2=>false);
	public $defaultShotsArray = array(1=>1, 2=>1); 
	
        public $loadingtimeArray = array(1=>3, 2=>3); //mode 1 should be the one with longest loading time
        public $rangePenaltyArray = array(1=>0.50, 2=>0.50);
        public $fireControlArray = array( 1=>array(null, 1, 2), 2=>array(null,1,2) ); // fighters, <mediums, <capitals 
	
	public $firingModes = array(1=>'Standard', 2=>'Flash');
	public $damageTypeArray = array(1=>'Standard', 2=>'Flash'); //indicates that this weapon does damage in Pulse mode
    	public $weaponClassArray = array(1=>'Particle', 2=>'Plasma'); //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!	
	
	
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
		if ( $maxhealth == 0 ){
		    $maxhealth = 6;
		}
		if ( $powerReq == 0 ){
		    $powerReq = 3;
		}
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
	
        public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
			$this->data["Special"] = 'Can fire as either Standard or High-Explosive (Flash) shot. ';
        }

	
        public function getDamage($fireOrder){ 
		switch($this->firingMode){
			case 1:
				return Dice::d(10, 2)+7; //Standard shot
				break;
			case 2:
				return Dice::d(10, 2)+2; //High Explosive shot
				break;	
		}
	}
        public function setMinDamage(){ 
		switch($this->firingMode){
			case 1:
				$this->minDamage = 9; //Standard shot
				break;
			case 2:
				$this->minDamage = 4; //High Explosive shot
				break;	
		}
		$this->minDamageArray[$this->firingMode] = $this->minDamage;
	}
        public function setMaxDamage(){
		switch($this->firingMode){
			case 1:
				$this->maxDamage = 27; //Standard shot
				break;
			case 2:
				$this->maxDamage = 22; //High Explosive shot
				break;	
		}
		$this->maxDamageArray[$this->firingMode] = $this->maxDamage;
	}
	
	
} //endof class NexusLightAssaultCannon
*/

class NexusLightAssaultCannonBattery extends Weapon{ 
/*Dual mode weapon based off the EA Heavy Laser-Pulse Array code*/	
        public $name = "NexusLightAssaultCannonBattery";
        public $displayName = "Light Assault Cannon Battery";
	    public $iconPath = "NexusLightAssaultCannonBattery.png";
	
	//Unlike EA Heavy Laser-Pulse Array, the Light Assault Cannon is just
	//firing a shell with different warheads. Therefore it uses the same trail
	//animation. However, I will use different animation colors for the 
	//different modes to give visual distinction.
	public $animationArray = array(1=>'trail', 2=>'trail');
        public $animationColorArray = array(1=>array(255, 11, 11), 2=>array(124, 219, 226));
        public $animationWidthArray = array(1=>2, 2=>2);
	public $trailColor = array(190, 75, 20); //not used for Laser animation?...
        public $trailLength = 10;//not used for Laser animation?...
        public $projectilespeed = 12;//not used for Laser animation?...
        public $animationExplosionScaleArray = array(1=>0.3, 2=>2);//not used for Laser animation?...
	
	
	//actual weapons data
    public $priorityArray = array(1=>7, 2=>1);
	public $uninterceptableArray = array(1=>false, 2=>false);
	public $defaultShotsArray = array(1=>1, 2=>1); 
	
        public $loadingtimeArray = array(1=>2, 2=>2); //mode 1 should be the one with longest loading time
        public $rangePenaltyArray = array(1=>0.50, 2=>0.50);
        public $fireControlArray = array( 1=>array(null, 1, 2), 2=>array(null,1,2) ); // fighters, <mediums, <capitals 
	
	public $firingModes = array(1=>'Standard', 2=>'Flash');
	public $damageTypeArray = array(1=>'Standard', 2=>'Flash'); //indicates that this weapon does damage in Pulse mode
    	public $weaponClassArray = array(1=>'Particle', 2=>'Plasma'); //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!	
	
	
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
		if ( $maxhealth == 0 ){
		    $maxhealth = 8;
		}
		if ( $powerReq == 0 ){
		    $powerReq = 6;
		}
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
	
        public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
			$this->data["Special"] = 'Can fire as either Standard or High-Explosive (Flash) shot. ';
        }

	
        public function getDamage($fireOrder){ 
		switch($this->firingMode){
			case 1:
				return Dice::d(10, 2)+7; //Standard shot
				break;
			case 2:
				return Dice::d(10, 2)+2; //High Explosive shot
				break;	
		}
	}
        public function setMinDamage(){ 
		switch($this->firingMode){
			case 1:
				$this->minDamage = 9; //Standard shot
				break;
			case 2:
				$this->minDamage = 4; //High Explosive shot
				break;	
		}
		$this->minDamageArray[$this->firingMode] = $this->minDamage;
	}
        public function setMaxDamage(){
		switch($this->firingMode){
			case 1:
				$this->maxDamage = 27; //Standard shot
				break;
			case 2:
				$this->maxDamage = 22; //High Explosive shot
				break;	
		}
		$this->maxDamageArray[$this->firingMode] = $this->maxDamage;
	}
	
	
} //endof class NexusLightAssaultCannonBattery



    class NexusAssaultCannon extends Particle{
        public $trailColor = array(190, 75, 20);

        public $name = "NexusAssaultCannon";
        public $displayName = "Assault Cannon";
		public $iconPath = "NexusAssaultCannon.png";
	    
        public $animation = "trail";
        public $animationColor = array(255, 11, 11);
        public $animationExplosionScale = 0.3;
        public $projectilespeed = 12;
        public $animationWidth = 2;
        public $trailLength = 10;

        public $loadingtime = 4;
        public $priority = 2;  //Piercing shots go early

        public $firingModes = array(
            1 => "Piercing"
        );
        public $damageType = 'Piercing';
        public $weaponClass = "Particle"; //MANDATORY (first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!

        public $rangePenalty = 0.33; 
        public $fireControl = array(null, 1, 2); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 8;
            if ( $powerReq == 0 ) $powerReq = 5;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){ return Dice::d(10, 2)+24;   }
        public function setMinDamage(){     $this->minDamage = 26 ;      }
        public function setMaxDamage(){     $this->maxDamage = 44 ;      }
    }



//class NexusAssaultCannon extends Weapon{ 
/*Dual mode weapon based off the EA Heavy Laser-Pulse Array code*/	
/*        public $name = "NexusAssaultCannon";
        public $displayName = "Assault Cannon";
	    public $iconPath = "NexusAssaultCannon.png";
	
	//Unlike EA Heavy Laser-Pulse Array, the Assault Cannon is just
	//firing a shell with different warheads. Therefore it uses the same trail
	//animation. However, I will use different animation colors for the 
	//different modes to give visual distinction.
	public $animationArray = array(1=>'trail', 2=>'trail');
        public $animationColorArray = array(1=>array(255, 11, 11), 2=>array(124, 219, 226));
        public $animationWidthArray = array(1=>2, 2=>2);
	public $trailColor = array(190, 75, 20); //not used for Laser animation?...
        public $trailLength = 10;//not used for Laser animation?...
        public $projectilespeed = 12;//not used for Laser animation?...
        public $animationExplosionScaleArray = array(1=>0.3, 2=>2);//not used for Laser animation?...
	
	
	//actual weapons data
        public $priorityArray = array(1=>7, 2=>1);
	public $uninterceptableArray = array(1=>false, 2=>false);
	public $defaultShotsArray = array(1=>1, 2=>1); 
	
        public $loadingtimeArray = array(1=>4, 2=>4); //mode 1 should be the one with longest loading time
        public $rangePenaltyArray = array(1=>0.33, 2=>0.33);
        public $fireControlArray = array( 1=>array(null, 1, 2), 2=>array(null,1,2) ); // fighters, <mediums, <capitals 
	
	public $firingModes = array(1=>'Standard', 2=>'Flash');
	public $damageTypeArray = array(1=>'Standard', 2=>'Flash'); //indicates that this weapon does damage in Pulse mode
    	public $weaponClassArray = array(1=>'Particle', 2=>'Plasma'); //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!	
	
	
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
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
			$this->data["Special"] = 'Can fire as either Standard or High-Explosive (Flash) shot. ';
        }

	
        public function getDamage($fireOrder){ 
		switch($this->firingMode){
			case 1:
				return Dice::d(10, 2)+12; //Standard shot
				break;
			case 2:
				return Dice::d(10, 2)+6; //High Explosive shot
				break;	
		}
	}
        public function setMinDamage(){ 
		switch($this->firingMode){
			case 1:
				$this->minDamage = 14; //Standard shot
				break;
			case 2:
				$this->minDamage = 8; //High Explosive shot
				break;	
		}
		$this->minDamageArray[$this->firingMode] = $this->minDamage;
	}
        public function setMaxDamage(){
		switch($this->firingMode){
			case 1:
				$this->maxDamage = 32; //Standard shot
				break;
			case 2:
				$this->maxDamage = 26; //High Explosive shot
				break;	
		}
		$this->maxDamageArray[$this->firingMode] = $this->maxDamage;
	}
	
	
} //endof class NexusAssaultCannon
*/




    class NexusMedAssaultCannon extends Particle{
        public $trailColor = array(190, 75, 20);

        public $name = "NexusMedAssaultCannon";
        public $displayName = "Medium Assault Cannon";
		public $iconPath = "NexusMedAssaultCannon.png";
	    
        public $animation = "trail";
        public $animationColor = array(255, 11, 11);
        public $animationExplosionScale = 0.3;
        public $projectilespeed = 12;
        public $animationWidth = 2;
        public $trailLength = 8;

        public $loadingtime = 3;
        public $priority = 2;  //Piercing shots go early

        public $firingModes = array(
            1 => "Piercing"
        );
        public $damageType = 'Piercing';
        public $weaponClass = "Particle"; //MANDATORY (first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!

        public $rangePenalty = 0.5; 
        public $fireControl = array(null, 1, 2); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 7;
            if ( $powerReq == 0 ) $powerReq = 4;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){ return Dice::d(10, 2)+20;   }
        public function setMinDamage(){     $this->minDamage = 22 ;      }
        public function setMaxDamage(){     $this->maxDamage = 40 ;      }

    } //endof class NexusMedAssaultCannon








class NexusHeavyAssaultCannon extends Weapon{ 
/*Dual mode weapon based off the EA Heavy Laser-Pulse Array code*/	
        public $name = "NexusHeavyAssaultCannon";
        public $displayName = "Heavy Assault Cannon";
	    public $iconPath = "NexusHeavyAssaultCannon.png";
	
	//Unlike EA Heavy Laser-Pulse Array, the Assault Cannon is just
	//firing a shell with different warheads. Therefore it uses the same trail
	//animation. However, I will use different animation colors for the 
	//different modes to give visual distinction.
	public $animationArray = array(1=>'trail', 2=>'trail');
        public $animationColorArray = array(1=>array(255, 11, 11), 2=>array(124, 219, 226));
        public $animationWidthArray = array(1=>3, 2=>3);
	public $trailColor = array(190, 75, 20); //not used for Laser animation?...
        public $trailLength = 15;//not used for Laser animation?...
        public $projectilespeed = 12;//not used for Laser animation?...
        public $animationExplosionScaleArray = array(1=>0.3, 2=>2);//not used for Laser animation?...
	
	
	//actual weapons data
        public $priorityArray = array(1=>7, 2=>1);
	public $uninterceptableArray = array(1=>false, 2=>false);
	public $defaultShotsArray = array(1=>1, 2=>1); 
	
        public $loadingtimeArray = array(1=>4, 2=>4); //mode 1 should be the one with longest loading time
        public $rangePenaltyArray = array(1=>0.33, 2=>0.33);
        public $fireControlArray = array( 1=>array(null, 1, 2), 2=>array(null,1,2) ); // fighters, <mediums, <capitals 
	
	public $firingModes = array(1=>'Standard', 2=>'Flash');
	public $damageTypeArray = array(1=>'Standard', 2=>'Flash'); //indicates that this weapon does damage in Pulse mode
    	public $weaponClassArray = array(1=>'Particle', 2=>'Plasma'); //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!	
	
	
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
		if ( $maxhealth == 0 ){
		    $maxhealth = 10;
		}
		if ( $powerReq == 0 ){
		    $powerReq = 6;
		}
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
	
        public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
			$this->data["Special"] = 'Can fire as either Standard or High-Explosive (Flash) shot. ';
        }

	
        public function getDamage($fireOrder){ 
		switch($this->firingMode){
			case 1:
				return Dice::d(10, 3)+12; //Standard shot
				break;
			case 2:
				return Dice::d(10, 3)+6; //High Explosive shot
				break;	
		}
	}
        public function setMinDamage(){ 
		switch($this->firingMode){
			case 1:
				$this->minDamage = 15; //Standard shot
				break;
			case 2:
				$this->minDamage = 9; //High Explosive shot
				break;	
		}
		$this->minDamageArray[$this->firingMode] = $this->minDamage;
	}
        public function setMaxDamage(){
		switch($this->firingMode){
			case 1:
				$this->maxDamage = 42; //Standard shot
				break;
			case 2:
				$this->maxDamage = 36; //High Explosive shot
				break;	
		}
		$this->maxDamageArray[$this->firingMode] = $this->maxDamage;
	}
	
	
} //endof class NexusHeavyAssaultCannon


class NexusHeavyAssaultCannonBattery extends Weapon{ 
/*Dual mode weapon based off the EA Heavy Laser-Pulse Array code*/	
        public $name = "NexusHeavyAssaultCannonBattery";
        public $displayName = "Heavy Assault Cannon Battery";
	    public $iconPath = "NexusHeavyAssaultCannonBattery.png";
	
	//Unlike EA Heavy Laser-Pulse Array, the Assault Cannon is just
	//firing a shell with different warheads. Therefore it uses the same trail
	//animation. However, I will use different animation colors for the 
	//different modes to give visual distinction.
	public $animationArray = array(1=>'trail', 2=>'trail');
        public $animationColorArray = array(1=>array(255, 11, 11), 2=>array(124, 219, 226));
        public $animationWidthArray = array(1=>3, 2=>3);
	public $trailColor = array(190, 75, 20); //not used for Laser animation?...
        public $trailLength = 15;//not used for Laser animation?...
        public $projectilespeed = 12;//not used for Laser animation?...
        public $animationExplosionScaleArray = array(1=>0.3, 2=>2);//not used for Laser animation?...
	
	
	//actual weapons data
        public $priorityArray = array(1=>7, 2=>1);
	public $uninterceptableArray = array(1=>false, 2=>false);
	public $defaultShotsArray = array(1=>1, 2=>1); 
	
        public $loadingtimeArray = array(1=>3, 2=>3); //mode 1 should be the one with longest loading time
        public $rangePenaltyArray = array(1=>0.33, 2=>0.33);
        public $fireControlArray = array( 1=>array(null, 1, 2), 2=>array(null,1,2) ); // fighters, <mediums, <capitals 
	
	public $firingModes = array(1=>'Standard', 2=>'Flash');
	public $damageTypeArray = array(1=>'Standard', 2=>'Flash'); //indicates that this weapon does damage in Pulse mode
    	public $weaponClassArray = array(1=>'Particle', 2=>'Plasma'); //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!	
	
	
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
		if ( $maxhealth == 0 ){
		    $maxhealth = 15;
		}
		if ( $powerReq == 0 ){
		    $powerReq = 10;
		}
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
	
        public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
			$this->data["Special"] = 'Can fire as either Standard or High-Explosive (Flash) shot. ';
        }

	
        public function getDamage($fireOrder){ 
		switch($this->firingMode){
			case 1:
				return Dice::d(10, 3)+12; //Standard shot
				break;
			case 2:
				return Dice::d(10, 3)+6; //High Explosive shot
				break;	
		}
	}
        public function setMinDamage(){ 
		switch($this->firingMode){
			case 1:
				$this->minDamage = 15; //Standard shot
				break;
			case 2:
				$this->minDamage = 9; //High Explosive shot
				break;	
		}
		$this->minDamageArray[$this->firingMode] = $this->minDamage;
	}
        public function setMaxDamage(){
		switch($this->firingMode){
			case 1:
				$this->maxDamage = 42; //Standard shot
				break;
			case 2:
				$this->maxDamage = 36; //High Explosive shot
				break;	
		}
		$this->maxDamageArray[$this->firingMode] = $this->maxDamage;
	}
	
} //endof class NexusHeavyAssaultCannonBattery






// END OF DUAL-MODE WEAPONS



// LASER WEAPONS


    class NexusHeavyMaser extends Laser{
        public $trailColor = array(140, 210, 255);

        public $name = "NexusHeavyMaser";
        public $displayName = "Heavy Maser";
		public $iconPath = "NexusHeavyMaser.png";
		
        public $animation = "laser";
        //public $animationColor = array(240, 90, 90);
        public $animationColor = array(100, 30, 15);
//        public $animationExplosionScale = 0.16;
//        public $animationWidth = 3;
//        public $animationWidth2 = 0.3;
        public $priority = 8;

        public $loadingtime = 3;

        public $rangePenalty = 0.5;
        public $fireControl = array(-1, 3, 3); // fighters, <mediums, <capitals

        public $damageType = "Standard"; 
        public $weaponClass = "Laser";
        public $firingModes = array( 1 => "Standard");

        public $noOverkill = true;		

        public function getSystemArmourBase($target, $system, $gamedata, $fireOrder, $pos=null){ //Maser treats armor as doubled
            $armour = parent::getSystemArmourBase($target, $system, $gamedata, $fireOrder, $pos);
            $armour = $armour * 2;
            $armour = max(0,$armour);
            return $armour;
        }

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 8;
            }
            if ( $powerReq == 0 ){
                $powerReq = 7;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
            //$this->data["Special"] = '<br>Armor counts as double.';
            $this->data["Special"] = '<br>Armor is doubled, and damage doubled for criticals.';
            parent::setSystemDataWindow($turn);
        }

	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){ //really no matter what exactly was hit!
		parent::onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder);
		if (WeaponEM::isTargetEMResistant($ship,$system)) return; //no effect on Advanced Armor
		$system->critRollMod+=max(0, ($damage-$armour)); //+twice damage to all critical/dropout rolls on system hit this turn
		$system->forceCriticalRoll = true;
	} //endof function onDamagedSystem

        public function getDamage($fireOrder){ return Dice::d(10, 3)+4;   }
        public function setMinDamage(){     $this->minDamage = 7 ;      }
        public function setMaxDamage(){     $this->maxDamage = 34 ;      }  
		
    }  //endof NexusHeavyMaser
	

    class NexusPointDefenseLaser extends Weapon{
        public $name = "NexusPointDefenseLaser";
        public $displayName = "Point Defense Laser";
        public $iconPath = "NexusPointDefenseLaser.png"; 
        public $animationColor = array(255, 30, 30);
        public $animation = "bolt"; //a bolt, not beam
        public $animationExplosionScale = 0.45;
        public $projectilespeed = 17;
        public $animationWidth = 25;
        public $trailLength = 25;
        public $priority = 3; //light Standard weapons
        public $uninterceptable = true; // This is a laser

        public $loadingtime = 1;

        public $intercept = 1;

        public $rangePenalty = 2;
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals

        public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
        public $weaponClass = "Laser";

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            if ( $maxhealth == 0 ) $maxhealth = 1;
            if ( $powerReq == 0 ) $powerReq = 1;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
		public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
			$this->data["Special"] = 'Uninterceptable and can intercept.'; 
		}

        public function getDamage($fireOrder){ return Dice::d(10, 1)+0; }
        public function setMinDamage(){ $this->minDamage = 1 ; }
        public function setMaxDamage(){ $this->maxDamage = 10 ; }

    } //endof class NexusPointDefenseLaser


    class NexusTwinXRayLaser extends Weapon{
        public $name = "NexusTwinXRayLaser";
        public $displayName = "Twin X-Ray Laser";
        public $iconPath = "NexusTwinXRayLaser.png"; 
        public $animationColor = array(255, 30, 30);
        public $animation = "bolt"; //a bolt, not beam
        public $animationExplosionScale = 0.45;
        public $projectilespeed = 17;
        public $animationWidth = 25;
        public $trailLength = 25;
        public $priority = 5; //light Standard weapons
		public $guns = 2;
		public $uninterceptable = true; // This is a laser

        public $loadingtime = 1;

        public $rangePenalty = 1;
        public $fireControl = array(0, 2, 3); // fighters, <mediums, <capitals

        public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
        public $weaponClass = "Laser";

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            if ( $maxhealth == 0 ) $maxhealth = 8;
            if ( $powerReq == 0 ) $powerReq = 4;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
		public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
			$this->data["Special"] = 'Uninterceptable.'; 
		}

        public function getDamage($fireOrder){ return Dice::d(6, 2)+4; }
        public function setMinDamage(){ $this->minDamage = 6 ; }
        public function setMaxDamage(){ $this->maxDamage = 16 ; }

    } //endof class NexusTwinXRayLaser


    class NexusDualXRayLaser extends Weapon{
        public $name = "NexusDualXRayLaser";
        public $displayName = "Dual X-Ray Laser";
        public $iconPath = "NexusDualXRayLaser.png"; 
        public $animationColor = array(255, 30, 30);
        public $animation = "bolt"; //a bolt, not beam
        public $animationExplosionScale = 0.45;
        public $projectilespeed = 17;
        public $animationWidth = 25;
        public $trailLength = 25;
        public $priority = 4; //light Standard weapons
		public $guns = 2;
        public $uninterceptable = true; // This is a laser

        public $loadingtime = 1;


        public $rangePenalty = 2;
        public $fireControl = array(3, 3, 3); // fighters, <mediums, <capitals

        public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
        public $weaponClass = "Laser";

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            if ( $maxhealth == 0 ) $maxhealth = 6;
            if ( $powerReq == 0 ) $powerReq = 2;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
		public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
			$this->data["Special"] = 'Uninterceptable.'; 
		}

        public function getDamage($fireOrder){ return Dice::d(6, 1)+6; }
        public function setMinDamage(){ $this->minDamage = 7 ; }
        public function setMaxDamage(){ $this->maxDamage = 13 ; }

    } //endof class NexusDualXRayLaser


    class NexusLightXRayLaser extends Weapon{
        public $name = "NexusLightXRayLaser";
        public $displayName = "Light X-Ray Laser";
        public $iconPath = "NexusLightXRayLaser.png"; 
        public $animationColor = array(255, 30, 30);
        public $animation = "bolt"; //a bolt, not beam
        public $animationExplosionScale = 0.45;
        public $projectilespeed = 17;
        public $animationWidth = 25;
        public $trailLength = 25;
        public $priority = 4; 
        public $uninterceptable = true; // This is a laser

        public $loadingtime = 1;

        public $rangePenalty = 2;
        public $fireControl = array(2, 2, 2); // fighters, <mediums, <capitals

        public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
        public $weaponClass = "Laser";

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            if ( $maxhealth == 0 ) $maxhealth = 3;
            if ( $powerReq == 0 ) $powerReq = 1;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
		public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
			$this->data["Special"] = 'Uninterceptable.'; 
		}

        public function getDamage($fireOrder){ return Dice::d(6, 1)+6; }
        public function setMinDamage(){ $this->minDamage = 7 ; }
        public function setMaxDamage(){ $this->maxDamage = 12 ; }

    } //endof class NexusLightXRayLaser
	
	
    class NexusXRayLaser extends Weapon{
        public $name = "NexusXRayLaser";
        public $displayName = "X-Ray Laser";
        public $iconPath = "NexusXRayLaser.png"; 
        public $animationColor = array(255, 30, 30);
        public $animation = "bolt"; //a bolt, not beam
        public $animationExplosionScale = 0.45;
        public $projectilespeed = 17;
        public $animationWidth = 25;
        public $trailLength = 25;
        public $priority = 5; 
        public $uninterceptable = true; // This is a laser

        public $loadingtime = 1;

        public $rangePenalty = 1;
        public $fireControl = array(-1, 1, 3); // fighters, <mediums, <capitals

        public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
        public $weaponClass = "Laser";

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            if ( $maxhealth == 0 ) $maxhealth = 5;
            if ( $powerReq == 0 ) $powerReq = 2;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
		public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
			$this->data["Special"] = 'Uninterceptable.'; 
		}

        public function getDamage($fireOrder){ return Dice::d(6, 1)+10; }
        public function setMinDamage(){ $this->minDamage = 11 ; }
        public function setMaxDamage(){ $this->maxDamage = 16 ; }

    } //endof class NexusXRayLaser	
	

    class NexusHeavyXRayLaser extends Weapon{
        public $name = "NexusHeavyXRayLaser";
        public $displayName = "Heavy X-Ray Laser";
        public $iconPath = "NexusHeavyXRayLaser.png"; 
        public $animationColor = array(255, 30, 30);
        public $animation = "bolt"; //a bolt, not beam
        public $animationExplosionScale = 0.45;
        public $projectilespeed = 17;
        public $animationWidth = 25;
        public $trailLength = 25;
        public $priority = 6;
        public $uninterceptable = true; // This is a laser

        public $loadingtime = 2;

        public $rangePenalty = 0.50;
        public $fireControl = array(-3, 2, 3); // fighters, <mediums, <capitals

        public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
        public $weaponClass = "Laser";

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            if ( $maxhealth == 0 ) $maxhealth = 7;
            if ( $powerReq == 0 ) $powerReq = 4;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
		public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
			$this->data["Special"] = 'Uninterceptable.'; 
		}

        public function getDamage($fireOrder){ return Dice::d(6, 2)+12; }
        public function setMinDamage(){ $this->minDamage = 14 ; }
        public function setMaxDamage(){ $this->maxDamage = 24 ; }

    } //endof class NexusHeavyXRayLaser

	

    class NexusLightChemicalLaser extends Laser{
        public $name = "NexusLightChemicalLaser";
        public $displayName = "Light Chemical Laser";
        public $iconPath = "NexusLightChemicalLaser.png"; 		
        public $animation = "laser";
        public $animationColor = array(220, 60, 120);
//        public $animationWidth = 5;
//        public $animationWidth2 = 0.5;
        public $priority = 8;

        public $loadingtime = 2;

        public $raking = 8;

        public $rangePenalty = 1;
        public $fireControl = array(-2, 1, 1); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            if ( $maxhealth == 0 ) $maxhealth = 5;
            if ( $powerReq == 0 ) $powerReq = 1;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
        public function getDamage($fireOrder){ return Dice::d(10, 2)+3; }
        public function setMinDamage(){ $this->minDamage = 5 ; }
        public function setMaxDamage(){ $this->maxDamage = 23 ; }
    } // endof NexusLightChemicalLaser
	
	
	class NexusMediumChemicalLaser extends Laser{
        public $name = "NexusMediumChemicalLaser";
        public $displayName = "Medium Chemical Laser";
        public $iconPath = "NexusMediumChemicalLaser.png"; 		
        public $animation = "laser";
        public $animationColor = array(220, 60, 120);
//        public $animationWidth = 5;
//        public $animationWidth2 = 0.5;
        public $priority = 8;

        public $loadingtime = 3;

        public $raking = 8;

        public $rangePenalty = 0.66;
        public $fireControl = array(-4, 2, 2); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            if ( $maxhealth == 0 ) $maxhealth = 7;
            if ( $powerReq == 0 ) $powerReq = 2;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
        public function getDamage($fireOrder){ return Dice::d(10, 3)+4; }
        public function setMinDamage(){ $this->minDamage = 7 ; }
        public function setMaxDamage(){ $this->maxDamage = 34 ; }

    } // endof NexusMediumChemicalLaser


    class NexusHeavyChemicalLaser extends Laser{
        public $name = "NexusHeavyChemicalLaser";
        public $displayName = "Heavy Chemical Laser";
        public $iconPath = "NexusHeavyChemicalLaser.png"; 		
        public $animation = "laser";
        public $animationColor = array(220, 60, 120);
//        public $animationWidth = 5;
//        public $animationWidth2 = 0.5;
        public $priority = 8;

        public $loadingtime = 4;

        public $raking = 8;

        public $rangePenalty = 0.50;
        public $fireControl = array(-6, 2, 2); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            if ( $maxhealth == 0 ) $maxhealth = 8;
            if ( $powerReq == 0 ) $powerReq = 4;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
        public function getDamage($fireOrder){ return Dice::d(10, 4)+6; }
        public function setMinDamage(){ $this->minDamage = 10 ; }
        public function setMaxDamage(){ $this->maxDamage = 46 ; }
    } // endof NexusHeavyChemicalLaser


    class NexusLightLaserCutter extends Laser{
     
        public $name = "NexusLightLaserCutter";
        public $displayName = "Light Laser Cutter";  
	    public $iconPath = "NexusLightLaserCutter.png";
	    
        public $animation = "laser";
        public $animationColor = array(255, 91, 91);
//        public $animationExplosionScale = 0.16;
//        public $animationWidth = 3;
//        public $animationWidth2 = 0.3;
        public $priority = 8;
        
        public $loadingtime = 2;
        
        public $raking = 6;
        
        public $rangePenalty = 1; //-1 / hexes
        public $fireControl = array(1, 1, 1); // fighters, <mediums, <capitals 
    
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
        
        public function getDamage($fireOrder){        return Dice::d(10, 2)+4;   }
        public function setMinDamage(){     $this->minDamage = 6 ;      }
        public function setMaxDamage(){     $this->maxDamage = 24 ;      }
    } //endof NexusLightLaserCutter


    class NexusHeavyLaserCutter extends Laser{
     
        public $name = "NexusHeavyLaserCutter";
        public $displayName = "Heavy Laser Cutter";  
	    public $iconPath = "NexusHeavyLaserCutter.png";
	    
        public $animation = "laser";
        public $animationColor = array(255, 91, 91);
//        public $animationExplosionScale = 0.16;
//        public $animationWidth = 3;
//        public $animationWidth2 = 0.3;
        public $priority = 8;
        
        public $loadingtime = 4;
        
        public $raking = 6;
        
        public $rangePenalty = 0.5; //-1/2 hexes
        public $fireControl = array(-2, 1, 3); // fighters, <mediums, <capitals 
    
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
        
        public function getDamage($fireOrder){        return Dice::d(10, 5)+4;   }
        public function setMinDamage(){     $this->minDamage = 9 ;      }
        public function setMaxDamage(){     $this->maxDamage = 54 ;      }
    } //endof NexusHeavyLaserCutter



    class NexusInterceptLaser extends Weapon{
        public $name = "NexusInterceptLaser";
        public $displayName = "Intercept Laser";
        public $iconPath = "NexusInterceptLaser.png"; 
        public $animationColor = array(255, 30, 30);
        public $animation = "bolt"; //a bolt, not beam
        public $animationExplosionScale = 0.45;
        public $projectilespeed = 17;
        public $animationWidth = 25;
        public $trailLength = 25;
        public $priority = 3; //light Standard weapons
        public $intercept = 2; 
        public $loadingtime = 1;
        public $uninterceptable = true; // This is a laser

        public $rangePenalty = 2;
        public $fireControl = array(3, 0, 0); // fighters, <mediums, <capitals

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

        public function getDamage($fireOrder){ return Dice::d(10, 1)+1; }
        public function setMinDamage(){ $this->minDamage = 2 ; }
        public function setMaxDamage(){ $this->maxDamage = 11 ; }

    } //endof class NexusInterceptLaser


    class NexusDualInterceptLaser extends Weapon{
        public $name = "NexusDualInterceptLaser";
        public $displayName = "Dual Intercept Laser";
        public $iconPath = "NexusDualInterceptLaser.png"; 
        public $animationColor = array(255, 30, 30);
        public $animation = "bolt"; //a bolt, not beam
        public $animationExplosionScale = 0.45;
        public $projectilespeed = 17;
        public $animationWidth = 25;
        public $trailLength = 25;
        public $priority = 3; //light Standard weapons
        public $intercept = 2; 
        public $loadingtime = 1;
		public $guns = 2;
        public $uninterceptable = true; // This is a laser

        public $rangePenalty = 2;
        public $fireControl = array(3, 0, 0); // fighters, <mediums, <capitals

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

        public function getDamage($fireOrder){ return Dice::d(10, 1)+1; }
        public function setMinDamage(){ $this->minDamage = 2 ; }
        public function setMaxDamage(){ $this->maxDamage = 11 ; }

    } //endof class NexusDualInterceptLaser


    class NexusLargeDefenseLaser extends Laser{ 
        public $name = "NexusLargeDefenseLaser";
        public $displayName = "Large Defense Laser";
        public $animation = "laser";
        public $animationExplosionScale = 0.1;
        public $animationColor = array(255, 58, 31);
        public $animationWidth = 3;
        public $animationWidth2 = 0.2;
	    public $iconPath = "NexusLargeDefenseLaser.png";

        public $intercept = 2;
        public $freeintercept = true; //can intercept fire directed at different unit
        public $freeinterceptspecial = true; //has own custom routine for deciding whether third party interception is legal
        public $loadingtime = 2;

        public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
        public $weaponClass = "Laser";

        public $rangePenalty = 1; //-1/hex
        public $fireControl = array(3, 2, 2); // fighters, <mediums, <capitals
        public $priority = 4; //light Standard 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
			if ( $maxhealth == 0 ) $maxhealth = 4;
            if ( $powerReq == 0 ) $powerReq = 2;            
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
			$this->data["Special"] = "Uninterceptable. May intercept for friendly units. Must have friendly and enemy unit in arc and have friendly unit within 3 hexes.";
		}
		
		public function canFreeInterceptShot($gamedata, $fireOrder, $shooter, $target, $interceptingShip, $firingWeapon){
			//target must be within 3 hexes
			$distance = mathlib::getDistanceHex($interceptingShip, $target);
			if ($distance > 3) return false;
			
			//both source and target of fire must be in arc
			//first check target
			$targetBearing = $interceptingShip->getBearingOnUnit($target);
			if (!mathlib::isInArc($targetBearing, $this->startArc, $this->endArc)) return false;
			//check on source - launch hex for ballistics, current position for direct fire
			if ($firingWeapon->ballistic){
				$movement = $shooter->getLastTurnMovement($fireOrder->turn);
				$pos = mathlib::hexCoToPixel($movement->position); //launch hex
				$sourceBearing = $interceptingShip->getBearingOnPos($pos);				
			}else{ //direct fire
				$sourceBearing = $interceptingShip->getBearingOnUnit($shooter);
			}
			if (!mathlib::isInArc($sourceBearing, $this->startArc, $this->endArc)) return false;
						
			return true;
		}

        public function getDamage($fireOrder){        return Dice::d(10, 1)+3;   }
        public function setMinDamage(){     $this->minDamage = 4 ;      }
        public function setMaxDamage(){     $this->maxDamage = 13 ;      }
    }	//endof class NexusLargeDefenseLaser





    class NexusLaserSpear extends LaserLance{

        public $name = "NexusLaserSpear";
        public $displayName = "Laser Spear";
	    public $iconPath = "NexusLaserSpear.png";
        public $animation = "laser";
        public $animationColor = array(64, 123, 168);
//        public $animationWidth = 3;
//        public $animationWidth2 = 0.3;
        public $priority = 8;
        public $priorityArray = array(1=>8, 2=>2); //Piercing shots go early, to do damage while sections aren't detroyed yet!

        public $loadingtime = 3;
        public $overloadable = false;

        public $raking = 10;

            public $firingModes = array(
                1 => "Raking",
                2 => "Piercing"
            );
        
            public $damageTypeArray=array(1=>'Raking', 2=>'Piercing');
            public $fireControlArray = array( 1=>array(-5, 2, 3), 2=>array(null,-1,-1) ); //Raking and Piercing mode

        public $rangePenalty = 0.66;

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
			if ( $maxhealth == 0 ) $maxhealth = 5;
            if ( $powerReq == 0 ) $powerReq = 3;            
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){ return Dice::d(10, 3)+3; }
        public function setMinDamage(){ $this->minDamage = 6 ; }
        public function setMaxDamage(){ $this->maxDamage = 33 ; }
		
    }  // endof class NexusLaserSpear

    class NexusHeavyLaserSpear extends LaserLance{

        public $name = "NexusHeavyLaserSpear";
        public $displayName = "Heavy Laser Spear";
	    public $iconPath = "NexusHeavyLaserSpear.png";
        public $animation = "laser";
        public $animationColor = array(64, 123, 168);
//        public $animationWidth = 3;
//        public $animationWidth2 = 0.3;
        public $priority = 8;
        public $priorityArray = array(1=>8, 2=>2); //Piercing shots go early, to do damage while sections aren't detroyed yet!

        public $loadingtime = 4;
        public $overloadable = false;

        public $raking = 10;

            public $firingModes = array(
                1 => "Raking",
                2 => "Piercing"
            );
        
            public $damageTypeArray=array(1=>'Raking', 2=>'Piercing');
            public $fireControlArray = array( 1=>array(-5, 2, 3), 2=>array(null,-1,-1) ); //Raking and Piercing mode

        public $rangePenalty = 0.66;

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
			if ( $maxhealth == 0 ) $maxhealth = 6;
            if ( $powerReq == 0 ) $powerReq = 4;            
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){ return Dice::d(10, 4)+4; }
        public function setMinDamage(){ $this->minDamage = 8 ; }
        public function setMaxDamage(){ $this->maxDamage = 44 ; }
		
    }  // endof class NexusHeavyLaserSpear


    class NexusIndustrialLaser extends Laser{
     
        public $name = "NexusIndustrialLaser";
        public $displayName = "Industrial Laser";  
	    public $iconPath = "NexusIndustrialLaser.png";
	    
        public $animation = "laser";
        public $animationColor = array(255, 91, 91);
//        public $animationExplosionScale = 0.16;
//        public $animationWidth = 3;
//        public $animationWidth2 = 0.3;
        public $priority = 7;
        
        public $loadingtime = 3;
        
        public $raking = 6;
        
        public $rangePenalty = 0.66; //-2 / 3 hexes
        public $fireControl = array(-3, 0, 2); // fighters, <mediums, <capitals 
    
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
        
        public function getDamage($fireOrder){        return Dice::d(10, 3)+6;   }
        public function setMinDamage(){     $this->minDamage = 9 ;      }
        public function setMaxDamage(){     $this->maxDamage = 36 ;      }
    } //endof NexusIndustrialLaser



    class NexusLightIndustrialLaser extends Laser{
     
        public $name = "NexusLightIndustrialLaser";
        public $displayName = "Light Industrial Laser";  
	    public $iconPath = "NexusLightIndustrialLaser.png";
	    
        public $animation = "laser";
        public $animationColor = array(255, 91, 91);
//        public $animationExplosionScale = 0.16;
//        public $animationWidth = 3;
//        public $animationWidth2 = 0.3;
        public $priority = 7;
        
        public $loadingtime = 2;
        
        public $raking = 4;
        
        public $rangePenalty = 1.0; //-1 / hex
        public $fireControl = array(-3, 0, 2); // fighters, <mediums, <capitals 
    
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
        
        public function getDamage($fireOrder){        return Dice::d(10, 2)+4;   }
        public function setMinDamage(){     $this->minDamage = 6 ;      }
        public function setMaxDamage(){     $this->maxDamage = 24 ;      }

    } //endof NexusLightIndustrialLaser




// END OF LASER WEAPONS



// PLASMA WEAPONS

class NexusLightPlasmaGun extends Plasma{
    	public $name = "NexusLightPlasmaGun";
        public $displayName = "Light Plasma Gun";
		public $iconPath = "NexusLightPlasmaaGun.png";
        public $animation = "trail";
        public $animationColor = array(75, 250, 90);
    	public $trailColor = array(75, 250, 90);
    	public $projectilespeed = 15;
        public $animationWidth = 5;
    	public $animationExplosionScale = 0.30;
    	public $trailLength = 20;
    	public $rangeDamagePenalty = 0.5;
    		        
        public $loadingtime = 1;
			
        public $rangePenalty = 2;
        public $fireControl = array(-3, 1, 2); // fighters, <=mediums, <=capitals 


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
		
		
    	public function getDamage($fireOrder){        return Dice::d(6, 1)+6;   }
        public function setMinDamage(){     $this->minDamage = 7 /*- $this->dp*/;      }
        public function setMaxDamage(){     $this->maxDamage = 12 /*- $this->dp*/;      }

}  // endof NexusLightPlasmaGun


class NexusMediumPlasmaGun extends Plasma{
    	public $name = "NexusMediumPlasmaGun";
        public $displayName = "Medium Plasma Gun";
		public $iconPath = "NexusLightPlasmaaGun.png";
        public $animation = "trail";
        public $animationColor = array(75, 250, 90);
    	public $trailColor = array(75, 250, 90);
    	public $projectilespeed = 15;
        public $animationWidth = 5;
    	public $animationExplosionScale = 0.30;
    	public $trailLength = 20;
    	public $rangeDamagePenalty = 0.5;
    		        
        public $loadingtime = 2;
			
        public $rangePenalty = 1.5;
        public $fireControl = array(-4, 1, 2); // fighters, <=mediums, <=capitals 


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
		
		
    	public function getDamage($fireOrder){        return Dice::d(10, 2)+2;   }
        public function setMinDamage(){     $this->minDamage = 4 /*- $this->dp*/;      }
        public function setMaxDamage(){     $this->maxDamage = 22 /*- $this->dp*/;      }

}  // endof NexusMediumPlasmaGun



class NexusHeavyPlasmaGun extends Plasma{
    	public $name = "NexusHeavyPlasmaGun";
        public $displayName = "Heavy Plasma Gun";
		public $iconPath = "NexusHeavyPlasmaaGun.png";
        public $animation = "trail";
        public $animationColor = array(75, 250, 90);
    	public $trailColor = array(75, 250, 90);
    	public $projectilespeed = 15;
        public $animationWidth = 5;
    	public $animationExplosionScale = 0.30;
    	public $trailLength = 20;
    	public $rangeDamagePenalty = 0.5;
    		        
        public $loadingtime = 3;
			
        public $rangePenalty = 1;
        public $fireControl = array(-5, 1, 2); // fighters, <=mediums, <=capitals 


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
		
		
    	public function getDamage($fireOrder){        return Dice::d(10,3)+4;   }
        public function setMinDamage(){     $this->minDamage = 7 /*- $this->dp*/;      }
        public function setMaxDamage(){     $this->maxDamage = 34 /*- $this->dp*/;      }

}  // endof NexusHeavyPlasmaGun




class NexusChargedPlasmaGun extends Plasma{
    	public $name = "NexusChargedPlasmaGun";
        public $displayName = "Charged Plasma Gun";
		public $iconPath = "NexusChargedPlasmaGun.png";
		
    	public $trailColor = array(75, 250, 90);
        public $animation = "ball";
        public $animationColor = array(75, 250, 90);
    	public $projectilespeed = 10;
        public $animationWidth = 4;
    	public $animationExplosionScale = 0.9;
    	public $trailLength = 8;
    	public $rangeDamagePenalty = 0.33;
		public $intercept = 0;

		public $rangeDamagePenaltyHCPG = 0.33;  // -1 / 3 hexes, but only after 6 hexes!

		public $priority = 1; //Flash weapon
		
		public $addedDice;

        public $loadingtime = 2;

		public $boostable = true;
        public $boostEfficiency = 3;
        public $maxBoostLevel = 1;
			
        public $rangePenalty = 0.5;
        public $fireControl = array(null, 2, 3); // fighters, <=mediums, <=capitals 
		
		public $damageType = "Flash"; 
		public $weaponClass = "Plasma"; 		

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		        //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 5;
            if ( $powerReq == 0 ) $powerReq = 3;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

// Code using Gravitic Pulsar to obtain cooldown due to boosting weapon
	public function setSystemDataWindow($turn){
            $boost = $this->getExtraDicebyBoostlevel($turn);            
            if (!isset($this->data["Special"])) {
                $this->data["Special"] = '';
            }else{
//original                $this->data["Special"] .= '<br>';
                $this->data["Special"] = '<br>';
            } 
		$this->data["Special"] .= "Plasma weapon. Armor treated as half.";
		$this->data["Special"] .= "<br>Loses -1 damage per 3 hexes after the first 10 hexes.";
		$this->data["Special"] .= "<br>+3 power = +2d10 damage and 1 turn cooldown.";
//		$this->data["Special"] .= "<br>+4 power = +d10 damage and 2 turns cooldown.";
		$this->data["Boostlevel"] = $boost;
		$this->normalload = $this->loadingtime;
		parent::setSystemDataWindow($turn);		
	}

        private function getExtraDicebyBoostlevel($turn){
            $add = 0;
            switch($this->getBoostLevel($turn)){
                case 1:
                    $add = 2;
                    break;
//                case 2:
//                    $add = 2;
//                    break;
                default:
                    break;
            }
            return $add;
        }

	protected function applyCooldown($gamedata){
		$currBoostlevel = $this->getBoostLevel($gamedata->turn);
		//if boosted, cooldown (1 or 2 tuns)
		if($currBoostlevel > 0){
			$cooldownLength = $currBoostlevel ;
			$finalTurn = $gamedata->turn + $cooldownLength;
			$crit = new ForcedOfflineForTurns(-1, $this->unit->id, $this->id, "ForcedOfflineForTurns", $gamedata->turn, $finalTurn);
			$crit->updated = true;
			$this->criticals[] =  $crit;
		} 
	}

         private function getBoostLevel($turn){
            $boostLevel = 0;
            foreach ($this->power as $i){
                if ($i->turn != $turn){
                   continue;
                }
//                if ($i->type == 2){
//                    $boostLevel += $i->amount;
//                }
            }
            return $boostLevel;
        }

// Variable damage reduction with range from the Descari Plasma Bolter

	//skip first 10 hexes when calculating the damage modifier
	protected function getDamageMod($damage, $shooter, $target, $pos, $gamedata)
	{
		parent::getDamageMod($damage, $shooter, $target, $pos, $gamedata);
					if ($pos != null) {
					$sourcePos = $pos;
					} 
					else {
					$sourcePos = $shooter->getHexPos();
					}
			$dis = mathlib::getDistanceHex($sourcePos, $target);				
			if ($dis <= 10) {
				$damage -= 0;
				}
			else {
				$damage -= round(($dis - 10) * $this->rangeDamagePenaltyHCPG);
			}	
		        $damage = max(0, $damage); //at least 0	    
        		$damage = floor($damage); //drop fractions, if any were generated
      			 return $damage;
	}		

// Code for adding extra dice of damage. Based on Fusion Agitator
        public function getDamage($fireOrder){
            $add = $this->getExtraDicebyBoostlevel($fireOrder->turn);
            $dmg = Dice::d(10, (2 + $add))+4;
            return $dmg;
        }

		public function fire($gamedata, $fireOrder){
			parent::fire($gamedata, $fireOrder);
			$this->applyCooldown($gamedata);
		}

        public function getAvgDamage(){
            $this->setMinDamage();
            $this->setMaxDamage();

            $min = $this->minDamage;
            $max = $this->maxDamage;
            $avg = round(($min+$max)/2);
            return $avg;
        }
		
        public function setMinDamage(){
            $turn = TacGamedata::$currentTurn;
            $boost = $this->getBoostLevel($turn);
            $this->minDamage = 2 + ($boost * 1) + 4;
        }   

        public function setMaxDamage(){
            $turn = TacGamedata::$currentTurn;
            $boost = $this->getBoostLevel($turn);
            $this->maxDamage = 20 + ($boost * 10) + 4;
        }  

}  // endof NexusChargedPlasmaGun




class NexusHeavyChargedPlasmaGun extends Plasma{
    	public $name = "NexusHeavyChargedPlasmaGun";
        public $displayName = "Heavy Charged Plasma Gun";
		public $iconPath = "NexusHeavyChargedPlasmaGun.png";
		
    	public $trailColor = array(75, 250, 90);
        public $animation = "ball";
        public $animationColor = array(75, 250, 90);
    	public $projectilespeed = 10;
        public $animationWidth = 5;
    	public $animationExplosionScale = 1;
    	public $trailLength = 10;
    	public $rangeDamagePenalty = 0.5;
		public $intercept = 0;

		public $rangeDamagePenaltyHCPG = 0.5;  // -1 / 2 hexes, but only after 10 hexes!

		public $priority = 1; //Flash weapon
		
		public $addedDice;

        public $loadingtime = 3;

		public $boostable = true;
        public $boostEfficiency = 3;
        public $maxBoostLevel = 2;
			
        public $rangePenalty = 0.5;
        public $fireControl = array(null, 2, 3); // fighters, <=mediums, <=capitals 
		
		public $damageType = "Flash"; 
		public $weaponClass = "Plasma"; 		

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		        //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 7;
            if ( $powerReq == 0 ) $powerReq = 3;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

// Code using Gravitic Pulsar to obtain cooldown due to boosting weapon
	public function setSystemDataWindow($turn){
            $boost = $this->getExtraDicebyBoostlevel($turn);            
            if (!isset($this->data["Special"])) {
                $this->data["Special"] = '';
            }else{
//original                $this->data["Special"] .= '<br>';
                $this->data["Special"] = '<br>';
            } 
		$this->data["Special"] .= "Plasma weapon. Armor treated as half.";
		$this->data["Special"] .= "<br>Loses -1 damage per 2 hexes after the first 10 hexes.";
		$this->data["Special"] .= "<br>+3 power = +1d10 damage and 1 turn cooldown.";
		$this->data["Special"] .= "<br>+6 power = +2d10 damage and 2 turns cooldown.";
		$this->data["Boostlevel"] = $boost;
		$this->normalload = $this->loadingtime;
		parent::setSystemDataWindow($turn);		
	}

        private function getExtraDicebyBoostlevel($turn){
            $add = 0;
            switch($this->getBoostLevel($turn)){
                case 1:
                    $add = 1;
                    break;
                case 2:
                    $add = 2;
                    break;
                default:
                    break;
            }
            return $add;
        }

	protected function applyCooldown($gamedata){
		$currBoostlevel = $this->getBoostLevel($gamedata->turn);
		//if boosted, cooldown (1 or 2 tuns)
		if($currBoostlevel > 0){
			$cooldownLength = $currBoostlevel ;
			$finalTurn = $gamedata->turn + $cooldownLength;
			$crit = new ForcedOfflineForTurns(-1, $this->unit->id, $this->id, "ForcedOfflineForTurns", $gamedata->turn, $finalTurn);
			$crit->updated = true;
			$this->criticals[] =  $crit;
		} 
	}

         private function getBoostLevel($turn){
            $boostLevel = 0;
            foreach ($this->power as $i){
                if ($i->turn != $turn){
                   continue;
                }
                if ($i->type == 2){
                    $boostLevel += $i->amount;
                }
            }
            return $boostLevel;
        }

// Variable damage reduction with range from the Descari Plasma Bolter

	//skip first 10 hexes when calculating the damage modifier
	protected function getDamageMod($damage, $shooter, $target, $pos, $gamedata)
	{
		parent::getDamageMod($damage, $shooter, $target, $pos, $gamedata);
					if ($pos != null) {
					$sourcePos = $pos;
					} 
					else {
					$sourcePos = $shooter->getHexPos();
					}
			$dis = mathlib::getDistanceHex($sourcePos, $target);				
			if ($dis <= 10) {
				$damage -= 0;
				}
			else {
				$damage -= round(($dis - 10) * $this->rangeDamagePenaltyHCPG);
			}	
		        $damage = max(0, $damage); //at least 0	    
        		$damage = floor($damage); //drop fractions, if any were generated
      			 return $damage;
	}		

// Code for adding extra dice of damage. Based on Fusion Agitator
        public function getDamage($fireOrder){
            $add = $this->getExtraDicebyBoostlevel($fireOrder->turn);
            $dmg = Dice::d(10, (2 + $add))+4;
            return $dmg;
        }

		public function fire($gamedata, $fireOrder){
			parent::fire($gamedata, $fireOrder);
			$this->applyCooldown($gamedata);
		}

        public function getAvgDamage(){
            $this->setMinDamage();
            $this->setMaxDamage();

            $min = $this->minDamage;
            $max = $this->maxDamage;
            $avg = round(($min+$max)/2);
            return $avg;
        }
		
        public function setMinDamage(){
            $turn = TacGamedata::$currentTurn;
            $boost = $this->getBoostLevel($turn);
            $this->minDamage = 2 + ($boost * 1) + 4;
        }   

        public function setMaxDamage(){
            $turn = TacGamedata::$currentTurn;
            $boost = $this->getBoostLevel($turn);
            $this->maxDamage = 20 + ($boost * 10) + 4;
        }  

}  // endof NexusHeavyChargedPlasmaGun



    class NexusEarlyPlasmaWave extends Torpedo{
        public $name = "NexusEarlyPlasmaWave";
        public $displayName = "Early Plasma Wave";
        public $iconPath = "plasmaWaveTorpedo.png";
        public $range = 20;
        public $loadingtime = 3;
        
        public $weaponClass = "Plasma"; //deals Plasma, not Ballistic, damage. Should be Ballistic(Plasma), but I had to choose ;)
        public $damageType = "Flash"; 
        
        public $fireControl = array(null, 0, 2); // fighters, <mediums, <capitals 
        
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
                $maxhealth = 7;
            }
            if ( $powerReq == 0 ){
                $powerReq = 4;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
	    //ignores half armor (as a Plasma weapon should!) - now handled by standard routines
    	
		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
			if (!isset($this->data["Special"])) {
				$this->data["Special"] = '';
			}else{
				$this->data["Special"] .= '<br>';
			}
			$this->data["Special"] .= "Ignores half of armor.";
		}
        
        
        public function getDamage($fireOrder){        return Dice::d(10, 2);   }
        public function setMinDamage(){     $this->minDamage = 2;      }
        public function setMaxDamage(){     $this->maxDamage = 20;      }
    
    }//endof class NexusEarlyPlasmaWave




    class NexusRangedEarlyPlasmaWave extends Torpedo{
		// for bases and OSATs
        public $name = "NexusRangedEarlyPlasmaWave";
        public $displayName = "Ranged Early Plasma Wave";
        public $iconPath = "plasmaWaveTorpedo.png";
        public $range = 40;
        public $loadingtime = 3;
        
        public $weaponClass = "Plasma"; //deals Plasma, not Ballistic, damage. Should be Ballistic(Plasma), but I had to choose ;)
        public $damageType = "Flash"; 
        
        public $fireControl = array(null, 0, 2); // fighters, <mediums, <capitals 
        
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
                $maxhealth = 7;
            }
            if ( $powerReq == 0 ){
                $powerReq = 4;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
	    //ignores half armor (as a Plasma weapon should!) - now handled by standard routines
    	
		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
			if (!isset($this->data["Special"])) {
				$this->data["Special"] = '';
			}else{
				$this->data["Special"] .= '<br>';
			}
			$this->data["Special"] .= "Ignores half of armor.";
		}
        
        
        public function getDamage($fireOrder){        return Dice::d(10, 2);   }
        public function setMinDamage(){     $this->minDamage = 2;      }
        public function setMaxDamage(){     $this->maxDamage = 20;      }
    
    }//endof class NexusRangedEarlyPlasmaWave



    class NexusRangedPlasmaWave extends Torpedo{
        public $name = "NexusRangedPlasmaWave";
        public $displayName = "Ranged Plasma Wave";
        public $iconPath = "plasmaWaveTorpedo.png";
        public $range = 60;
        public $loadingtime = 3;
        
        public $weaponClass = "Plasma"; //deals Plasma, not Ballistic, damage. Should be Ballistic(Plasma), but I had to choose ;)
        public $damageType = "Flash"; 
        
        public $fireControl = array(null, 0, 2); // fighters, <mediums, <capitals 
        
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
                $maxhealth = 7;
            }
            if ( $powerReq == 0 ){
                $powerReq = 4;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
	    //ignores half armor (as a Plasma weapon should!) - now handled by standard routines
    	
		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
			if (!isset($this->data["Special"])) {
				$this->data["Special"] = '';
			}else{
				$this->data["Special"] .= '<br>';
			}
			$this->data["Special"] .= "Ignores half of armor.";
		}
        
        public function getDamage($fireOrder){        return Dice::d(10, 3);   }
        public function setMinDamage(){     $this->minDamage = 3;      }
        public function setMaxDamage(){     $this->maxDamage = 30;      }
    
    }//endof class NexusRangedPlasmaWave




// END OF PLASMA WEAPONS



// ION WEAPONS

class NexusIonBeam extends Raking{
    public $trailColor = array(30, 170, 255);

    public $name = "NexusIonBeam";
    public $displayName = "Ion Beam";
    //public $animation = "trail";
	public $iconPath = "NexusIonBeam.png";
    public $animation = "laser"; //more fitting for Raking, and faster at long range
    public $animationColor = array(127, 0, 255);
//    public $animationExplosionScale = 0.25;
//    public $animationWidth = 3;//4;
//    public $animationWidth2 = 0.3;
    public $trailLength = 24;
    public $priority = 8;
    
    public $loadingtime = 2;
    public $shots = 1;

    public $rangePenalty = 0.33;
    public $fireControl = array(-2, 2, 2); // fighters, <mediums, <capitals 
    
    public $damageType = "Raking"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    public $weaponClass = "Ion"; //MANDATORY (first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!

        public $firingModes = array( 1 => "Raking");

    
    
    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
    {
        parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
    }

    public function getDamage($fireOrder){        return Dice::d(10, 2)+5;  }
    public function setMinDamage(){     $this->minDamage = 7 ;      }
    public function setMaxDamage(){     $this->maxDamage = 25 ;      }

} // endof NexusIonBeam



    class NexusIonGun extends Weapon{
        public $name = "NexusIonGun";
        public $displayName = "Ion Gun";
        public $iconPath = "NexusIonGun.png";
        
        public $animation = "trail";
        public $animationColor = array(127, 0, 255);
        public $animationExplosionScale = 0.2;
        public $projectilespeed = 16;
        public $animationWidth = 3;
        public $trailLength = 3;
        
        public $priority = 3; //damage output 8 is very light
        public $loadingtime = 1;
        public $intercept = 2;
        public $rangePenalty = 1;
        
        public $damageType = "Standard"; 
        public $weaponClass = "Ion"; 
        
        public $fireControl = array(2, 2, 2); // fighters, <mediums, <capitals
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 2;
            if ( $powerReq == 0 ) $powerReq = 2;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){        return 6;   }
        public function setMinDamage(){     $this->minDamage = 6 ;      }
        public function setMaxDamage(){     $this->maxDamage = 6 ;      }
		
    } // endof NexusIonGun


    class NexusTwinIonGun extends Weapon{
        public $name = "NexusTwinIonGun";
        public $displayName = "Twin Ion Gun";
        public $iconPath = "dualIonBolter.png";
        
        public $animation = "trail";
        public $animationColor = array(127, 0, 255);
        public $animationExplosionScale = 0.2;
        public $projectilespeed = 16;
        public $animationWidth = 3;
        public $trailLength = 3;
        
        public $priority = 3; //damage output 8 is very light
        public $loadingtime = 1;
        public $intercept = 2;
        public $rangePenalty = 1;
        public $guns = 2;
        
        public $damageType = "Standard"; 
        public $weaponClass = "Ion"; 
        
        public $fireControl = array(2, 2, 2); // fighters, <mediums, <capitals
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 4;
            if ( $powerReq == 0 ) $powerReq = 4;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){        return 6;   }
        public function setMinDamage(){     $this->minDamage = 6 ;      }
        public function setMaxDamage(){     $this->maxDamage = 6 ;      }
		
    } // endof NexusTwinIonGun


    class NexusLightIonGun extends Weapon{
        public $name = "NexusLightIonGun";
        public $displayName = "Light Ion Gun";
        public $iconPath = "NexusFtrTwinIon.png";
        
        public $animation = "trail";
        public $animationColor = array(127, 0, 255);
        public $animationExplosionScale = 0.2;
        public $projectilespeed = 16;
        public $animationWidth = 3;
        public $trailLength = 3;
        
        public $priority = 3; //damage output 8 is very light
        public $loadingtime = 1;
        public $intercept = 2;
		public $shots = 2;
		public $defaultShots = 2;
        public $rangePenalty = 2;
        
        public $damageType = "Standard"; 
        public $weaponClass = "Ion"; 
        

        function __construct($startArc, $endArc, $damagebonus, $nrOfShots = 2){
            $this->damagebonus = $damagebonus;
            $this->defaultShots = $nrOfShots;
            $this->shots = $nrOfShots;
            $this->intercept = $nrOfShots;		

            if($nrOfShots === 1){
                $this->iconPath = "particleGun.png";
            }

            parent::__construct(0, 1, 0, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
        }

        
        public function getDamage($fireOrder){        return 5;   }
        public function setMinDamage(){     $this->minDamage = 5 ;      }
        public function setMaxDamage(){     $this->maxDamage = 5 ;      }
		
    } // endof NexusLightIonGun	


    class NexusIonBolter extends Weapon{
        public $name = "NexusIonBolter";
        public $displayName = "Ion Bolter";
        public $iconPath = "NexusIonBolter.png";
        
        public $animation = "trail";
        public $animationColor = array(127, 0, 255);
        public $animationExplosionScale = 0.2;
        public $projectilespeed = 16;
        public $animationWidth = 3;
        public $trailLength = 3;
        
        public $priority = 3; //damage output 8 is very light
        public $loadingtime = 1;
        public $intercept = 2;
        public $rangePenalty = 1;
        
        public $damageType = "Standard"; 
        public $weaponClass = "Ion"; 
        
        public $fireControl = array(2, 2, 2); // fighters, <mediums, <capitals
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 2;
            if ( $powerReq == 0 ) $powerReq = 2;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){        return 8;   }
        public function setMinDamage(){     $this->minDamage = 8 ;      }
        public function setMaxDamage(){     $this->maxDamage = 8 ;      }
		
    } // endof NexusIonBolter
	
	
    class NexusLightIonBolter extends Weapon{
        public $name = "NexusLightIonBolter";
        public $displayName = "Light Ion Bolter";
        public $iconPath = "NexusFtrTwinIon.png";
        
        public $animation = "trail";
        public $animationColor = array(127, 0, 255);
        public $animationExplosionScale = 0.2;
        public $projectilespeed = 16;
        public $animationWidth = 3;
        public $trailLength = 3;
        
        public $priority = 3; //damage output 8 is very light
        public $loadingtime = 1;
        public $intercept = 2;
		public $shots = 2;
		public $defaultShots = 2;
        public $rangePenalty = 2;
        
        public $damageType = "Standard"; 
        public $weaponClass = "Ion"; 
        

        function __construct($startArc, $endArc, $damagebonus, $nrOfShots = 2){
            $this->damagebonus = $damagebonus;
            $this->defaultShots = $nrOfShots;
            $this->shots = $nrOfShots;
            $this->intercept = $nrOfShots;		

            if($nrOfShots === 1){
                $this->iconPath = "particleGun.png";
            }

            parent::__construct(0, 1, 0, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
        }

        
        public function getDamage($fireOrder){        return 7;   }
        public function setMinDamage(){     $this->minDamage = 7 ;      }
        public function setMaxDamage(){     $this->maxDamage = 7 ;      }
		
    } // endof NexusLightIonBolter	
	


// END OF ION WEAPONS



// ELECTROMAGNETIC WEAPONS



class NexusLightBurstBeam extends Weapon{
	public $name = "NexusLightBurstBeam";
	public $displayName = "Light Burst Beam";
	public $iconPath = "NexusLightBurstBeam.png";	
	public $animation = "laser";
	public $animationColor = array(158, 240, 255);
	public $trailColor = array(158, 240, 255);
	public $projectilespeed = 15;
	public $animationWidth = 2;
	public $animationWidth2 = 0.2;
	public $animationExplosionScale = 0.10;
	public $trailLength = 30;
	public $noOverkill = true;
		        
	public $loadingtime = 1;
	public $priority = 10; //as antiship weapon, going last
	public $priorityAFArray = array(1=>3); //as antifighter weapon, going early
			
	public $rangePenalty = 2;
	public $fireControl = array(4, null, null); // fighters, <=mediums, <=capitals 
	
	public $damageType = "Standard"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	public $weaponClass = "Electromagnetic"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!

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
       
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}	      
		$this->data["Special"] .= "Targets fighters only.";    
		$this->data["Special"] .= "<br>Causes immediate dropout (excluding superheavy)."; 
		$this->data["Special"] .= "<br>Automatically hits EM shield if interposed.";
		$this->data["Special"] .= "<br>Does not affect units protected by Advanced Armor.";  	
	}
	
	//Burst Beams ignore armor; advanced armor halves effect (due to weapon being Electromagnetic)
	public function getSystemArmourBase($target, $system, $gamedata, $fireOrder, $pos = null){
		if (WeaponEM::isTargetEMResistant($target,$system)){
			$returnArmour = parent::getSystemArmourBase($target, $system, $gamedata, $fireOrder, $pos);
			$returnArmour = floor($returnArmour/2);
			return $returnArmour;
		}else{
			return 0;
		}
	}
	
	protected function beforeDamage($target, $shooter, $fireOrder, $pos, $gamedata){ //if target is protected by EM shield, that shield is hit automatically
		if($target instanceof FighterFlight){ //for fighters - regular allocation
			parent::beforeDamage($target, $shooter, $fireOrder, $pos, $gamedata);
			return;
		}
		
		//first - find bearing from target to firing ship (needed to determine whether shield interacts with incoming shot)
		$relativeBearing = $target->getBearingOnUnit($shooter);
		//are there any active EM shields affecting shot?
		$affectingShields = array();
		foreach($target->systems as $shield){
			if( ($shield instanceOf EMShield)  //this is an actual shield!
				&& (!$shield->isDestroyed()) //not destroyed
				&& (!$shield->isOfflineOnTurn($gamedata->turn)) //powered up
			   	&& (mathlib::isInArc($relativeBearing, $shield->startArc, $shield->endArc)) //actually in arc to affect
			) {
				$affectingShields[] = $shield;
			}
		}
		$countShields = count($affectingShields);
		if($countShields > 0){ //hit shield if active in arc and not destroyed (proceed to onDamagedSystem directly)
			//choose randomly from relevant shields
			$chosenID = Dice::d($countShields,1)-1; //array elements numeration starts at 0
			$shield = $affectingShields[$chosenID];			
			$this->onDamagedSystem($target, $shield, 0, 0, $gamedata, $fireOrder);
		} else { //otherwise hit normally (parent beforeDamage) (...for 0 damage...) , actual effect handled in onDamagedSystem 
			parent::beforeDamage($target, $shooter, $fireOrder, $pos, $gamedata);
			return;
		}
	}//endof function beforeDamage
		
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
				$crit = new OutputReduced1(-1, $ship->id, $reactor->id, "OutputReduced1", $gamedata->turn);
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
		
	public function getDamage($fireOrder){        return 0;   }
	public function setMinDamage(){     $this->minDamage = 0;      }
	public function setMaxDamage(){     $this->maxDamage = 0;      }
}//endof class NexusLightBurstBeam


    class NexusLightChargeCannon extends Weapon{
        public $trailColor = array(30, 170, 255);

        public $name = "NexusLightChargeCannon";
        public $displayName = "Light Charge Cannon";
		public $iconPath = "NexusLightChargeCannon.png";
		public $animation = "bolt";
        public $animationColor = array(255, 250, 230);
        public $animationExplosionScale = 0.15;
        public $animationWidth = 2;
        public $animationWidth2 = 0.2;

        public $intercept = 1;
        public $loadingtime = 1;
        public $priority = 3;

        public $rangePenalty = 2;
        public $fireControl = array(3, 2, 2); // fighters, <mediums, <capitals

        public $damageType = "Standard"; 
        public $weaponClass = "Electromagnetic";
        public $firingModes = array( 1 => "Standard");
        
        
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

        public function setSystemDataWindow($turn){
            $this->data["Special"] = 'Treats armor as if it was 1 point lower.';
            $this->data["Special"] = '<br>+2 to all critical/dropout rolls made by system hit this turn.';
            parent::setSystemDataWindow($turn);
        }

		//ignores 1 point of armor; advanced armor ignores this effect
        public function getSystemArmourBase($target, $system, $gamedata, $fireOrder, $pos=null){ //standard part of armor - reduce by 1!
			if (WeaponEM::isTargetEMResistant($target,$system)){
				return $armor;
			}else{
				$armour = parent::getSystemArmourBase($target, $system, $gamedata, $fireOrder, $pos);
				$armour = $armour - 1;
				$armour = max(0,$armour);
				return $armour;
			}
        }

	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){ //really no matter what exactly was hit!
		parent::onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder);
		if (WeaponEM::isTargetEMResistant($ship,$system)) return; //no effect on Advanced Armor
		$system->critRollMod+=2; //+2 to all critical/dropout rolls on system hit this turn
	} //endof function onDamagedSystem

        public function getDamage($fireOrder){ return Dice::d(6, 2)+0;   }
        public function setMinDamage(){     $this->minDamage = 2 ;      }
        public function setMaxDamage(){     $this->maxDamage = 12 ;      }  
		
    }  //endof NexusLightChargeCannon



    class NexusChargeCannon extends Weapon{
        public $trailColor = array(30, 170, 255);

        public $name = "NexusChargeCannon";
        public $displayName = "Charge Cannon";
		public $iconPath = "NexusChargeCannon.png";
		public $animation = "bolt";
        public $animationColor = array(255, 250, 230);
        public $animationExplosionScale = 0.15;
        public $animationWidth = 2;
        public $animationWidth2 = 0.2;

        public $intercept = 2;
        public $loadingtime = 1;
        public $priority = 5;

        public $rangePenalty = 1;
        public $fireControl = array(5, 2, 2); // fighters, <mediums, <capitals

        public $damageType = "Standard"; 
        public $weaponClass = "Electromagnetic";
        public $firingModes = array( 1 => "Standard");
        
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 6;
            }
            if ( $powerReq == 0 ){
                $powerReq = 2;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
            $this->data["Special"] = 'Treats armor as if it was 1 point lower.';
            $this->data["Special"] = '<br>+3 to all critical/dropout rolls made by system hit this turn.';
            parent::setSystemDataWindow($turn);
        }

		//ignores 1 point of armor; advanced armor ignores this effect
        public function getSystemArmourBase($target, $system, $gamedata, $fireOrder, $pos=null){ //standard part of armor - reduce by 1!
			if (WeaponEM::isTargetEMResistant($target,$system)){
				return $armor;
			}else{
				$armour = parent::getSystemArmourBase($target, $system, $gamedata, $fireOrder, $pos);
				$armour = $armour - 1;
				$armour = max(0,$armour);
				return $armour;
			}
        }

	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){ //really no matter what exactly was hit!
		parent::onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder);
		if (WeaponEM::isTargetEMResistant($ship,$system)) return; //no effect on Advanced Armor
		$system->critRollMod+=3; //+3 to all critical/dropout rolls on system hit this turn
	} //endof function onDamagedSystem

        public function getDamage($fireOrder){ return Dice::d(10, 1)+4;   }
        public function setMinDamage(){     $this->minDamage = 5 ;      }
        public function setMaxDamage(){     $this->maxDamage = 14 ;      } 
		
    }  //endof NexusChargeCannon







// END OF ELECTROMAGNETIC WEAPONS




// PULSE WEAPONS


class NexusSwarmTorpedo extends Pulse{

        public $name = "NexusSwarmTorpedo";
        public $displayName = "Swarm Torpedo";
		public $iconPath = "NexusSwarmTorpedo.png";
        public $useOEW = true; //torpedo
        public $ballistic = true; //missile
		public $range = 25;
//		public $distanceRange = 35;		
        public $animation = "trail";
        public $animationColor = array(192, 192, 192);
    	public $trailColor = array(215, 126, 111);
        public $trailLength = 45;
        public $animationWidth = 2;
        public $projectilespeed = 5;
        public $animationExplosionScale = 0.15;
        public $rof = 2;
        public $grouping = 25;
        public $maxpulses = 4;
        public $priority = 6;
	protected $useDie = 2; //die used for base number of hits	
        
        public $loadingtime = 2;
        
        public $rangePenalty = 0;
        public $fireControl = array(-1, 2, 2); // fighters, <mediums, <capitals 

		public $firingMode = "Ballistic";
        public $damageType = "Pulse"; 
        public $weaponClass = "Pulse"; 
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 5;
            }
            if ( $powerReq == 0 ){
                $powerReq = 2;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){            
            parent::setSystemDataWindow($turn);
//            $this->data["Pulses"] = 'D2';            
//			$this->data["Special"] .= '<br>Max 4 pulse. +1 per 25%';			
			$this->data["Special"] .= '<br>Benefits from offensive EW.';			
        }

	
        public function getDamage($fireOrder){        return 8;   }
    }  // endof NexusSwarmTorpedo



class NexusRangedSwarmTorpedo extends Pulse{

        public $name = "NexusRangedSwarmTorpedo";
        public $displayName = "Ranged Swarm Torpedo";
		public $iconPath = "NexusSwarmTorpedo.png";
        public $useOEW = true; //torpedo
        public $ballistic = true; //missile
		public $range = 50;
//		public $distanceRange = 35;		
        public $animation = "trail";
        public $animationColor = array(192, 192, 192);
    	public $trailColor = array(215, 126, 111);
        public $trailLength = 45;
        public $animationWidth = 2;
        public $projectilespeed = 5;
        public $animationExplosionScale = 0.15;
        public $rof = 2;
        public $grouping = 25;
        public $maxpulses = 4;
        public $priority = 6;
	protected $useDie = 2; //die used for base number of hits	
        
        public $loadingtime = 2;
        
        public $rangePenalty = 0;
        public $fireControl = array(-1, 2, 2); // fighters, <mediums, <capitals 

		public $firingMode = "Ballistic";
        public $damageType = "Pulse"; 
        public $weaponClass = "Pulse"; 
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 5;
            }
            if ( $powerReq == 0 ){
                $powerReq = 2;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){            
            parent::setSystemDataWindow($turn);
//            $this->data["Pulses"] = 'D2';            
//			$this->data["Special"] .= '<br>Max 4 pulse. +1 per 25%';			
			$this->data["Special"] .= '<br>Benefits from offensive EW.';			
        }
	
        public function getDamage($fireOrder){        return 8;   }
    }  

// endof NexusRangedSwarmTorpedo


class NexusHeavySwarmTorpedo extends Pulse{

        public $name = "NexusHeavySwarmTorpedo";
        public $displayName = "Heavy Swarm Torpedo";
		public $iconPath = "NexusHeavySwarmTorpedo.png";
        public $useOEW = true; //torpedo
        public $ballistic = true; //missile
		public $range = 25;
//		public $distanceRange = 35;		
        public $animation = "trail";
        public $animationColor = array(192, 192, 192);
    	public $trailColor = array(215, 126, 111);
        public $trailLength = 45;
        public $animationWidth = 2;
        public $projectilespeed = 5;
        public $animationExplosionScale = 0.15;
        public $rof = 2;
        public $grouping = 25;
        public $maxpulses = 6;
        public $priority = 6;
	protected $useDie = 3; //die used for base number of hits	
        
        public $loadingtime = 2;
        
        public $rangePenalty = 0;
        public $fireControl = array(0, 3, 3); // fighters, <mediums, <capitals 

		public $firingMode = "Ballistic";
        public $damageType = "Pulse"; 
        public $weaponClass = "Pulse"; 
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 7;
            }
            if ( $powerReq == 0 ){
                $powerReq = 4;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){            
            parent::setSystemDataWindow($turn);
            $this->data["Pulses"] = 'D 3';   
			$this->data["Special"] = '<br>Benefits from offensive EW.';						
        }

	
        public function getDamage($fireOrder){        return 10;   }
    }  // endof NexusHeavySwarmTorpedo



class NexusDefensePulsar extends Pulse{

        public $name = "NexusDefensePulsar";
        public $displayName = "Defense Pulsar";
		public $iconPath = "NexusDefensePulsar.png";
		
        public $animation = "trail";
        public $trailLength = 12;
        public $animationWidth = 4;
        public $projectilespeed = 9;
        public $animationExplosionScale = 0.10;
		
		protected $useDie = 2; //die used for the number of hits
        public $rof = 3;
        public $grouping = 25;
        public $maxpulses = 3;
        
        public $loadingtime = 1;
        public $intercept = 2;
        public $priority = 3;
        
        public $rangePenalty = 2;
        public $fireControl = array(2, 2, 2); // fighters, <mediums, <capitals
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            if ( $maxhealth == 0 ) $maxhealth = 4;
            if ( $powerReq == 0 ) $powerReq = 2;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
        public function getDamage($fireOrder){ return 6; }
        public function setMinDamage(){ $this->minDamage = 6 ; }
        public function setMaxDamage(){ $this->maxDamage = 6 ; }		
		
    }  // endof NexusDefensePulsar



// END OF PULSE WEAPONS








/*Chaff Launcher
intercepts all weapon fire (directed at self) from HEX (including uninterceptable weapons).
Done as: kind of offensive mode - player needs to pick hex to fire at. Animated as kind of EMine. 
All appropriate fire orders will get an interception set up before other intercepts are declared.
If weapon is left to its own devices it will simply provide a single interception (...if game allows non-1-per-turn weapon to be intercepting in the first place!)
*/
class PlasmaWeb extends Weapon implements DefensiveSystem{
        public $name = "PlasmaWeb";
        public $displayName = "Plasma Web";
		public $iconPath = "PlasmaWeb.png";
	
        public $trailColor = array(192,192,192);
        public $animation = "ball";
        public $animationColor = array(192,192,192);
        public $animationExplosionScale = 0.5;
        public $animationExplosionType = "AoE";
        public $explosionColor = array(235,235,235);
        public $projectilespeed = 12;
        public $animationWidth = 10;
        public $trailLength = 10;

        public $ballistic = false;
        public $hextarget = false; //for technical reasons this proved hard to do
        public $hidetarget = false;
        public $priority = 1; //to show effect quickly
        public $uninterceptable = true; //just so nothing tries to actually intercept this weapon
        public $doNotIntercept = true; //do not intercept this weapon, period
		public $canInterceptUninterceptable = true; //able to intercept shots that are normally uninterceptable, eg. Lasers
	
        public $useOEW = false; //not important, really	    
        
        public $loadingtime = 1;
		public $range = 100; //let's put maximum range here, but generous one
        public $rangePenalty = 0;
        public $fireControl = array(100, 100, 100); // fighters, <mediums, <capitals; just so the weapon is targetable
		public $intercept = 2; //intercept rating -2	    
	    
		public $firingMode = 'Intercept'; //firing mode - just a name essentially
		public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    	public $weaponClass = "Particle"; //not important really
	 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		        //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 4;
            if ( $powerReq == 0 ) $powerReq = 2;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
		public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            $this->data["Special"] = "Fired at hex (although You technically have to pick an unit). Will apply interception to all fire from target hex to Chaff-protected ship.";
            $this->data["Special"] .= "<br>Will affect uninterceptable weapons.";
        }


	//Defensive system functions

    public function getDefensiveType()
    {
       return "Interceptor";
    }

    public function getDefensiveHitChangeMod($target, $shooter, $pos, $turn, $weapon){ //no defensive hit chance change
            return 0;
    }

    public function getDefensiveDamageMod($target, $shooter, $pos, $turn, $weapon){
		$output = 0;
		//Affects only Antimatter, Laser, and Particle weapons
		if($weapon->weaponClass == 'Laser' || $weapon->weaponClass == 'Particle' || $weapon->weaponClass == 'Antimatter') $output = 2;
        return $output;
    }

	//end Defensive system functions


	        
	//hit chance always 100 - so it always hits and is correctly animated
	public function calculateHitBase($gamedata, $fireOrder)
	{
		$fireOrder->needed = 100; //auto hit!
		$fireOrder->updated = true;
		
		//while we're at it - we may add appropriate interception orders!		
		$targetShip = $gamedata->getShipById($fireOrder->targetid);
		
		$shipsInRange = $gamedata->getShipsInDistance($targetShip); //all units on target hex
		foreach ($shipsInRange as $affectedShip) {
			$allOrders = $affectedShip->getAllFireOrders($gamedata->turn);
			foreach($allOrders as $subOrder) {
				if (($subOrder->type == 'normal') && ($subOrder->targetid == $fireOrder->shooterid) ){ //something is firing at protected unit - and is affected!
					//uninterceptable are affected all right, just those that outright cannot be intercepted - like ramming or mass driver - will not be affected
					$subWeapon = $affectedShip->getSystemById($subOrder->weaponid);
					if( $subWeapon->doNotIntercept != true ){
						//apply interception! Note that this weapon is technically not marked as firing defensively - it is marked as firing offensively though! (already)
						//like firing.php addToInterceptionTotal
						$subOrder->totalIntercept += $this->getInterceptionMod($gamedata, $subOrder);
        				$subOrder->numInterceptors++;
					}
				}
			}
		}
		
		//retarget at hex - this will affect how the weapon is animated/displayed in firing log!
		    //insert correct target coordinates: CURRENT target position
		    $pos = $targetShip->getHexPos();
		    $fireOrder->x = $pos->q;
		    $fireOrder->y = $pos->r;
		    $fireOrder->targetid = -1; //correct the error

	}//endof function calculateHitBase
	   
	public function fire($gamedata, $fireOrder)
	{ //sadly here it really has to be completely redefined... or at least I see no option to avoid this
		$this->changeFiringMode($fireOrder->firingMode);//changing firing mode may cause other changes, too!
		$shooter = $gamedata->getShipById($fireOrder->shooterid);
		/** @var MovementOrder $movement */
		$movement = $shooter->getLastTurnMovement($fireOrder->turn);
		$posLaunch = $movement->position;//at moment of launch!!!		
		//$this->calculateHit($gamedata, $fireOrder); //already calculated!
		$rolled = Dice::d(100);
		$fireOrder->rolled = $rolled; ///and auto-hit ;)
		$fireOrder->shotshit++;
		$fireOrder->pubnotes .= "Interception applied to all weapons at target hex that are firing at Chaff-launching ship. "; //just information for player, actual applying was done in calculateHitBase method

		$fireOrder->rolled = max(1, $fireOrder->rolled);//Marks that fire order has been handled, just in case it wasn't marked yet!
		TacGamedata::$lastFiringResolutionNo++;    //note for further shots
		$fireOrder->resolutionOrder = TacGamedata::$lastFiringResolutionNo;//mark order in which firing was handled!
	} //endof function fire

        public function getDamage($fireOrder){
            return 0; //this weapon does no damage, in case it actually hits something!
        }
        public function setMinDamage(){     $this->minDamage = 0;      }
        public function setMaxDamage(){     $this->maxDamage = 0;      }
		
}//endof PlasmaWeb	





/* moving to official Plasma weapons, plasma.php file!
class PlasmaBlast extends Weapon{
        public $name = "PlasmaBlast";
        public $displayName = "Plasma Blast";
		public $iconPath = "PlasmaWeb.png";
		
		public $range = 3;
		public $loadingtime = 1;
//		public $hextarget = true;
		
		public $flashDamage = true;
		public $priority = 1;
			
        public $animation = "ball";
        public $trailColor = array(30, 140, 60);
        public $animationColor = array(30, 140, 60);
        public $animationExplosionScale = 1;
		public $animationExplosionType = "AoE";
        public $projectilespeed = 12;
        public $animationWidth = 10;
        public $trailLength = 10;    

		public $firingMode = 'AoE'; //firing mode - just a name essentially
		public $damageType = "Flash"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    	public $weaponClass = "Plasma"; //should be Ballistic and Matter, but FV does not allow that. Instead decrease advanced armor encountered by 2 points (if any) (usually system does that, but it will account for Ballistic and not Matter)

        public $rangePenalty = 0; //none
        public $fireControl = array(50, null, null); // fighters, <mediums, <capitals




public function calculateHitBase($gamedata, $fireOrder)
    {
        $fireOrder->needed = 100; //100% chance of hitting everything on target hex
        $fireOrder->updated = true;
    } 

 public function fire($gamedata, $fireOrder){
        $this->changeFiringMode($fireOrder->firingMode);//changing firing mode may cause other changes, too!
        $shooter = $gamedata->getShipById($fireOrder->shooterid); //so we know which ship is firing, this is useful

		if ($fireOrder->targetid != -1) { //make weapon target hex rather than unit
            $targetship = $gamedata->getShipById($fireOrder->targetid);
            //insert correct target coordinates: CURRENT  target position
            $position = $targetship->getCoPos(); 
            $fireOrder->x = $position["x"];
            $fireOrder->y = $position["y"];
            $fireOrder->targetid = -1; 
        }

		//roll to hit - we'll make a regular roll (irrelevant as hit is automatic, but we need to mark SOME number anyway):
		$rolled = Dice::d(100);
		$fireOrder->rolled = $rolled;

		//deal damage!
        $target = new OffsetCoordinate($fireOrder->x, $fireOrder->y);
        $ships1 = $gamedata->getShipsInDistance($target); //all ships on target hex
        foreach ($ships1 as $targetShip) if ($targetShip instanceOf FighterFlight) {

            $this->AOEdamage($targetShip, $shooter, $fireOrder, $gamedata);
        }
    }
	
//and now actual damage dealing - and we already know fighter is hit (fire()) doesn't pass anything else)
//source hex will be taken from firing unit, damage will be individually rolled for each fighter hit
 public function AOEdamage($target, $shooter, $fireOrder, $gamedata)
    {
        if ($target->isDestroyed()) return; //no point allocating
            foreach ($target->systems as $fighter) {
                if ($fighter == null || $fighter->isDestroyed()) {
                    continue;
                }
         //roll (and modify as appropriate) damage for this particular fighter:
        $damage = $this->getDamage();
//        $damage = $this->getDamageMod($damage, $shooter, $target, null, $gamedata);
//        $damage -= $target->getDamageMod($shooter, null, $gamedata->turn, $this);

                $this->doDamage($target, $shooter, $fighter, $damage, $fireOrder, null, $gamedata, false);

		}
	}






        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		        //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 4;
            if ( $powerReq == 0 ) $powerReq = 2;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
//    	public function getDamage($fireOrder){        return Dice::d(6, 1)+2;   }
    	public function getDamage($fireOrder){        return 12;   }
        public function setMinDamage(){     $this->minDamage = 12;      }
        public function setMaxDamage(){     $this->maxDamage = 12;      }
}//endof PlasmaBlast
*/






class TestGun extends Particle{
        public $trailColor = array(30, 170, 255);

        public $name = "TestGun";
        public $displayName = "Test Gun";
		public $iconPath = "tacLaser.png";
	    
        public $animation = "trail";
        public $animationColor = array(255, 250, 230);
        public $animationExplosionScale = 0.15;
        public $projectilespeed = 15;
        public $animationWidth = 4;
        public $trailLength = 10;
        public $loadingtime = 1;
        public $priority = 5;
        public $intercept = 2;

        public $rangePenalty = 0.25; //-1/4 hexes
        public $fireControl = array(3, 3, 3); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 4;
            if ( $powerReq == 0 ) $powerReq = 1;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){ return 6;   }
        public function setMinDamage(){     $this->minDamage = 6 ;      }
        public function setMaxDamage(){     $this->maxDamage = 6 ;      }
}// endof TestGun



/*fighter-mounted variant*/
    class LightParticleBeamFtr extends Particle{
//        public $trailColor = array(30, 170, 255);

        public $name = "LightParticleBeamFtr";
        public $displayName = "Light Particle Beam";
		public $iconPath = "lightParticleBeamShip.png";
	    
        public $animation = "beam";
        public $animationColor = array(255, 250, 230);
        public $animationExplosionScale = 0.12;
        public $projectilespeed = 12;
        public $animationWidth = 3;
        public $trailLength = 8;

        public $intercept = 2;
        public $loadingtime = 1;
        public $priority = 4;

        public $rangePenalty = 2 ; //-2 / hex
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals

	function __construct($startArc, $endArc, $nrOfShots = 1){
            $this->defaultShots = $nrOfShots;
            $this->shots = $nrOfShots;
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }

        public function getDamage($fireOrder){ return Dice::d(10, 1)+4;   }
        public function setMinDamage(){     $this->minDamage = 5 ;      }
        public function setMaxDamage(){     $this->maxDamage = 14 ;      }
	}// endof LightParticleBeamFtr
	

/*fighter-mounted variant*/
    class NexusParticleGridFtr extends Particle{
//        public $trailColor = array(30, 170, 255);

        public $name = "NexusParticleGridFtr";
        public $displayName = "Particle Grid";
		public $iconPath = "NexusParticleGrid.png";
	    
        public $animation = "beam";
        public $animationColor = array(255, 250, 230);
        public $animationExplosionScale = 0.12;
        public $projectilespeed = 12;
        public $animationWidth = 3;
        public $trailLength = 8;

        public $intercept = 2;
        public $loadingtime = 1;
        public $priority = 4;

        public $rangePenalty = 2 ; //-2 / hex
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals

	function __construct($startArc, $endArc, $nrOfShots = 1){
            $this->defaultShots = $nrOfShots;
            $this->shots = $nrOfShots;
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }

        public function getDamage($fireOrder){ return Dice::d(10, 1)+1;   }
        public function setMinDamage(){     $this->minDamage = 2 ;      }
        public function setMaxDamage(){     $this->maxDamage = 11 ;      }
	}// endof NexusParticleGridFtr


/*fighter-mounted variant*/
    class StdParticleBeamFtr extends Particle{
//        public $trailColor = array(30, 170, 255);

        public $name = "StdParticleBeamFtr";
        public $displayName = "Standard Particle Beam";
		public $iconPath = "stdParticleBeam.png";
	    
        public $animation = "bolt";
        public $animationColor = array(255, 102, 0);
        public $animationExplosionScale = 0.15;
        public $projectilespeed = 12;
        public $animationWidth = 3;
        public $trailLength = 10;

        public $intercept = 2;
        public $loadingtime = 1;
        public $priority = 5;

        public $rangePenalty = 1 ; 
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals

	function __construct($startArc, $endArc, $nrOfShots = 1){
            $this->defaultShots = $nrOfShots;
            $this->shots = $nrOfShots;
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }

        public function getDamage($fireOrder){ return Dice::d(10, 1)+6;   }
        public function setMinDamage(){     $this->minDamage = 7 ;      }
        public function setMaxDamage(){     $this->maxDamage = 16 ;      }
	}// endof StdParticleBeamFtr


/*fighter-mounted variant*/
    class TwinArrayFtr extends Particle{
//        public $trailColor = array(30, 170, 255);

        public $name = "TwinArrayFtr";
        public $displayName = "Twin Array";
		public $iconPath = "twinArray.png";
	    
        public $animation = "bolt";
        public $animationColor = array(255, 163, 26);
        public $animationExplosionScale = 0.15;
        public $projectilespeed = 12;
        public $animationWidth = 3;
        public $trailLength = 10;

        public $intercept = 2;
        public $loadingtime = 1;
		public $guns = 2;
        public $priority = 4;

        public $rangePenalty = 2 ; 
        public $fireControl = array(6, 5, 4); // fighters, <mediums, <capitals

	function __construct($startArc, $endArc, $nrOfShots = 1){
            $this->defaultShots = $nrOfShots;
            $this->shots = $nrOfShots;
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }

        public function getDamage($fireOrder){ return Dice::d(10, 1)+4;   }
        public function setMinDamage(){     $this->minDamage = 5 ;      }
        public function setMaxDamage(){     $this->maxDamage = 14 ;      }
	}// endof TwinArrayFtr


/*fighter-mounted variant*/
class MedPlasmaCannonFtr extends LinkedWeapon{
	/*dedicated anti-ship weapon for mines*/
        public $name = "MedPlasmaCannonFtr";
        public $displayName = "Medium Plasma Cannon";
	public $iconPath = "mediumPlasma.png";
    public $animationColor = array(75, 250, 90); //...it's not inheriting from Plasma, so needs to have proper color declared
	/*
        public $animation = "trail";
        public $animationColor = array(75, 250, 90);
        public $trailColor = array(75, 250, 90);
        public $projectilespeed = 10;
        public $animationWidth = 4;
        public $trailLength = 13;
        public $animationExplosionScale = 0.23;
*/

        public $intercept = 0; //no interception for this weapon!
        public $loadingtime = 3;
        public $shots = 1;
        public $defaultShots = 1;
        public $rangePenalty = 1;
        public $fireControl = array(-5, 0, 0); // fighters, <mediums, <capitals
        public $rangeDamagePenalty = 0.5; //-1/2 hexes!
		public $priority = 6;

    	public $damageType = "Standard"; 
    	public $weaponClass = "Plasma"; 

        function __construct($startArc, $endArc, $shots = 1){
            $this->shots = $shots;
            $this->defaultShots = $shots;
            
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){    
            parent::setSystemDataWindow($turn);
			if (!isset($this->data["Special"])) {
				$this->data["Special"] = '';
			}else{
				$this->data["Special"] .= '<br>';
			}
			$this->data["Special"] .= "Does less damage over distance (".$this->rangeDamagePenalty." per hex)";
			$this->data["Special"] .= "<br>Ignores half of armor."; //handled by standard routines for Plasma weapons now
        }


        public function getDamage($fireOrder){        return Dice::d(10,3)+4;   }
        public function setMinDamage(){     $this->minDamage = 7 ;      }
        public function setMaxDamage(){     $this->maxDamage = 34 ;      }
    } //endof MedPlasmaCannonFtr


class MatterCannonFtr extends Matter {
    /*fighter version of Matter Cannon*/
    /*NOT done as linked weapon!*/
    /*limited ammo*/
        public $name = "MatterCannonFtr";
        public $displayName = "Matter Cannon";
	public $iconPath = "matterCannon.png";
        public $animation = "bolt";
        public $animationColor = array(250, 250, 190);
        public $priority = 9; //Matter weapon
        
        public $loadingtime = 2;
        public $exclusive = false; //this is not an exclusive weapon!
        
        public $rangePenalty = 0.5;
        public $fireControl = array(-2, 0, 0); // fighters, <mediums, <capitals 

        function __construct($startArc, $endArc){
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }
	
        public function getDamage($fireOrder){        return Dice::d(10, 2)+2;   }
        public function setMinDamage(){   return  $this->minDamage = 4 ;      }
        public function setMaxDamage(){   return  $this->maxDamage = 22 ;      }
}//MatterCannonFtr




/*fighter-mounted variant*/
    class GatlingGunFtr extends LinkedWeapon{
        public $trailColor = array(30, 170, 255);

        public $name = "GatlingGunFtr";
        public $displayName = "Gatling Gun"; //it's not 'paired' in any way, except being usually mounted twin linked - like most fighter weapons...
        public $iconPath = "pairedParticleGun.png";
        public $animation = "trail";
        public $animationColor = array(30, 170, 255);
        public $animationExplosionScale = 0.10;
        public $projectilespeed = 12;
        public $animationWidth = 2;
        public $trailLength = 10;
        public $intercept = 2;
        public $ballisticIntercept = true;
        public $ammunition = 6;
		
        public $loadingtime = 1;
        public $shots = 2;
        public $defaultShots = 2;
		public $priority = 2; //correct for d6+2 and lighter

        public $rangePenalty = 3;
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals
        private $damagebonus = 0;
        
        public $damageType = "Standard"; 
        public $weaponClass = "Particle";         

        function __construct($startArc, $endArc, $damagebonus, $nrOfShots = 2){
            $this->damagebonus = $damagebonus;
            $this->defaultShots = $nrOfShots;
            $this->shots = $nrOfShots;
            $this->intercept = $nrOfShots;		

            if ($damagebonus >= 3) $this->priority++; //heavier varieties fire later in the queue
            if ($damagebonus >= 5) $this->priority++;
            if ($damagebonus >= 7) $this->priority++;

            if($nrOfShots === 1){
                $this->iconPath = "particleGun.png";
            }
            if($nrOfShots >2){//no special icon for more than 3 linked weapons
                $this->iconPath = "pairedParticleGun3.png";
            }

            parent::__construct(0, 1, 0, $startArc, $endArc);
        }

        public function stripForJson() {
            $strippedSystem = parent::stripForJson();
    
            $strippedSystem->ammunition = $this->ammunition;
           
            return $strippedSystem;
        }
	    
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
//            $this->data["Special"] = "Ignores armor, does not overkill.";
            $this->data["Special"] = "Can intercept ballistic weapons only.";
            $this->data["Ammunition"] = $this->ammunition;
        }

        public function setAmmo($firingMode, $amount){
            $this->ammunition = $amount;
        }

       public function fire($gamedata, $fireOrder){ //note ammo usage
        	//debug::log("fire function");
            parent::fire($gamedata, $fireOrder);
            $this->ammunition--;
            Manager::updateAmmoInfo($fireOrder->shooterid, $this->id, $gamedata->id, $this->firingMode, $this->ammunition, $gamedata->turn);
        }
		
        public function getDamage($fireOrder){        return Dice::d(6)+$this->damagebonus;   }
        public function setMinDamage(){     $this->minDamage = 1+$this->damagebonus ;      }
        public function setMaxDamage(){     $this->maxDamage = 6+$this->damagebonus ;      }
    } //endof GatlingGunFtr






class NexusTestBlaster extends Weapon{
        public $trailColor = array(30, 170, 255);

        public $name = "NexusTestBlaster";
        public $displayName = "Test Blaster";
		public $iconPath = "NexusParticleBolt.png";
	    
        public $animation = "bolt";
        public $animationColor = array(255, 250, 230);
        public $animationExplosionScale = 0.15;
        public $projectilespeed = 15;
        public $animationWidth = 4;
        public $trailLength = 10;
        public $loadingtime = 1;
        public $priority = 5;
        public $intercept = 2;

        public $rangePenalty = 2; //-1/hex
        public $fireControl = array(3, 2, 1); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 4;
            if ( $powerReq == 0 ) $powerReq = 1;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }




        public $damageType = "Standard"; 
        public $weaponClass = "Particle";
        public $firingModes = array( 1 => "Standard");


        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            $this->data["Special"] = "Does up to three pulses of 1 damage, combined into a single hit.";
        }


        public $grouping = 25;
        public $maxpulses = 3;
		protected $useDie = 1; //die used for base number of hits	

        protected function getPulses($turn)
        {
            return Dice::d($this->useDie) + $this->fixedBonusPulses;
        }
	
        protected function getExtraPulses($needed, $rolled)
        {
            return floor(($needed - $rolled) / ($this->grouping));
        }
	
		public function rollPulses($turn, $needed, $rolled){
			$pulses = $this->getPulses($turn);
			$pulses+= $this->getExtraPulses($needed, $rolled);
			$pulses=min($pulses,$this->maxpulses);
			return $pulses;
		}

        public function getDamage($fireOrder){
			$damage = 0;
			$damage+=(($this->rollPulses($turn, $needed, $rolled)) * 1);
			return $damage;
		}

        public function setMinDamage(){     $this->minDamage = 1 ;      }
        public function setMaxDamage(){     $this->maxDamage = 3 ;      }
		
}// endof NexusTestBlaster


class Enveloper extends Weapon{
    /*Testing the creation of an enveloping weapon that does not damage the primary section.*/
	/*Using the Ipsha Resonance Generator as the template*/
	public $name = "Enveloper";
	public $displayName = "Enveloper";
	public $iconPath = "ResonanceGenerator.png";
	
	public $animation = "laser"; //described as beam in nature, standard damage is resonance effect and not direct
	public $animationColor = array(125, 125, 230);
	public $animationExplosionScale = 0.6; //make it look really large - while singular damage is low, it's repeated on every structure block - eg. all-encompassing

	public $loadingtime = 1;
	
	public $rangePenalty = 1; //-1/hex
	public $fireControl = array(null, 2, 2); // fighters, <mediums, <capitals 
	
	public $intercept = 0;
	public $priority = 1;// as it attacks every section, should go first!
	
	public $noPrimaryHits = true; //outer section hit will NOT be able to roll PRIMARY result!
	
	public $damageType = "Standard"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	public $weaponClass = "Electromagnetic"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!
	
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $aftFacing=false)
	{
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
		if ( $maxhealth == 0 ){
			$maxhealth = 8;
		}
		if ( $powerReq == 0 ){
			$powerReq = 6;
		}
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
	}	
	
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);  
		$this->data["Special"] = "Attacks all sections (so a capital ship will sufer 5 attacks, while MCV only 1).";  //MCV should suffer 2, but for technical reasons I opted for going for Section = Structure block		    
		$this->data["Special"] .= "<br>Ignores armor."; 
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
	
	public function isTargetAmbiguous($gamedata, $fireOrder){//targat always ambiguous - just so enveloping weapon is not used to decide target section!
		return true;
	}
	
	/*attacks every not destroyed (as of NOW!) ship section*/
	protected function beforeDamage($target, $shooter, $fireOrder, $pos, $gamedata){
		//fighters are untargetable, so we know it's a ship
		if ($target->isDestroyed()) return; //no point allocating
		$activeStructures = $target->getSystemsByName("Structure",false);//list of non-destroyed Structure blocks
		foreach($activeStructures as $struct){
			$fireOrder->chosenLocation = $struct->location;			
			$damage = $this->getFinalDamage($shooter, $target, $pos, $gamedata, $fireOrder);
			$this->damage($target, $shooter, $fireOrder,  $gamedata, $damage, false);//force PRIMARY location!
		}
	}//endof function beforeDamage
		
	public function getDamage($fireOrder){       return 10;   }
	public function setMinDamage(){     $this->minDamage = 10 ;      }
	public function setMaxDamage(){     $this->maxDamage = 10 ;      }
	
} //endof class Enveloper











class LimpetBoreTorp extends Weapon{
        public $name = "LimpetBoreTorp";
        public $displayName = "Limpet Bore Torpedo";
		    public $iconPath = "LimpetBoreTorpedo.png";
        public $animation = "trail";
        public $trailColor = array(141, 240, 255);
        public $animationColor = array(50, 50, 50);
        public $animationExplosionScale = 0.2;
        public $projectilespeed = 10;
        public $animationWidth = 4;
        public $trailLength = 100;    

		public $overrideCallingRestricions = true; //flag looked for in weaponManager.js to allow a ballistic called shot

        public $useOEW = false; //missile
        public $ballistic = true; //missile
        public $range = 30;
        public $distanceRange = 60;
        public $ammunition = 5; //limited number of shots
		public $noInterceptDegradation = true; //if true, this weapon will be intercepted without degradation!
		public $noPrimaryHits = true; //cannot hit PRIMARY from outer table

        public $calledShotMod = 0; //instead of usual -8
        
        public $loadingtime = 2; // 1/2 turns
        public $rangePenalty = 0;
        public $fireControl = array(null, 2, 4); // fighters, <mediums, <capitals; INCLUDES BOTH LAUNCHER AND MISSILE DATA!
	    
	public $noOverkill = true; //Matter weapon
	public $priority = 9; //Matter weapon
	    
//	public $firingMode = 'Called Shot'; //firing mode - just a name essentially
	public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    	public $weaponClass = "Matter"; //should be Ballistic and Matter, but FV does not allow that. Instead decrease advanced armor encountered by 2 points (if any) (usually system does that, but it will account for Ballistic and not Matter)
	 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		        //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 5;
            if ( $powerReq == 0 ) $powerReq = 3;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function stripForJson() {
            $strippedSystem = parent::stripForJson();    
            $strippedSystem->ammunition = $this->ammunition;           
            return $strippedSystem;
        }
        
	    
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            $this->data["Special"] = "Ignores armor, no overkill (Ballistic+Matter weapon).";
            $this->data["Ammunition"] = $this->ammunition;
        }
        
        public function getDamage($fireOrder){
            return Dice::d(10, 2)+10;
       }
        public function setAmmo($firingMode, $amount){
            $this->ammunition = $amount;
        }
       public function fire($gamedata, $fireOrder){ //note ammo usage
            parent::fire($gamedata, $fireOrder);
            $this->ammunition--;
            Manager::updateAmmoInfo($fireOrder->shooterid, $this->id, $gamedata->id, $this->firingMode, $this->ammunition, $gamedata->turn);
        }
    
        public function setMinDamage(){     $this->minDamage = 12;      }
        public function setMaxDamage(){     $this->maxDamage = 30;      }
		
}//endof LimpetBoreTorp



class LimpetBoreBase extends LimpetBoreTorp{
        public $name = "LimpetBoreBase";
        public $displayName = "Base Limpet Bore Torpedo";
		    public $iconPath = "LimpetBoreTorpedo.png";
        public $animation = "trail";
        public $trailColor = array(141, 240, 255);
        public $animationColor = array(50, 50, 50);
        public $animationExplosionScale = 0.2;
        public $projectilespeed = 10;
        public $animationWidth = 4;
        public $trailLength = 100;    

        public $range = 60;
        public $distanceRange = 60;
        public $ammunition = 7; //limited number of shots
		public $noInterceptDegradation = true; //if true, this weapon will be intercepted without degradation!
		public $noPrimaryHits = true; //cannot hit PRIMARY from outer table

        public $calledShotMod = 0; //instead of usual -8
        
        public $loadingtime = 1; 
        public $rangePenalty = 0;
        public $fireControl = array(null, 2, 4); // fighters, <mediums, <capitals; INCLUDES BOTH LAUNCHER AND MISSILE DATA!
	    		
}//endof LimpetBoreBase





class ProximityLaser extends Laser{
        public $name = "ProximityLaser";
        public $displayName = "Proximity Laser";
	    public $iconPath = "ProximityLaser.png";
        public $animation = 'laser';
        public $animationColor = array(220, 60, 120);
		
        public $useOEW = false; //missile
        public $ballistic = true; //missile
        public $range = 30;
//        public $distanceRangeArray = array(1=>30, 2=>15);
        public $ammunition = 10; //limited number of shots
		public $hidetarget = true;
	    
        public $loadingtime = 3; // 1/3 turns
        public $rangePenalty = 0;
        public $fireControl = array(null, 0, 0); // fighters, <mediums, <capitals; INCLUDES BOTH LAUNCHER AND MISSILE DATA!
	    
        public $raking = 10;
		public $priority = 8; 
	    
		public $firingMode = 'Ballistic'; //firing mode - just a name essentially
		public $damageType = 'Raking'; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
//    	public $weaponClass = "Ballistic"; //should be Ballistic and Matter, but FV does not allow that. Instead decrease advanced armor encountered by 2 points (if any) (usually system does that, but it will account for Ballistic and not Matter)
	 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		        //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 6;
            if ( $powerReq == 0 ) $powerReq = 6;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function stripForJson() {
            $strippedSystem = parent::stripForJson();    
            $strippedSystem->ammunition = $this->ammunition;           
            return $strippedSystem;
        }
	    
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            $this->data["Special"] = "Bomb-pumped laser. Ballistic weapon that scores raking (8) damage.";
            $this->data["Special"] .= "<br>Long-range: 20 hex launch and 30 hex max range, 2d10+2 damage.";
            $this->data["Special"] .= "<br>Short-range: 10 hex launch and 15 hex max range, 3d10+4 damage.";
            $this->data["Ammunition"] = $this->ammunition;
        }
        
        public function setAmmo($firingMode, $amount){
            $this->ammunition = $amount;
        }
       public function fire($gamedata, $fireOrder){ //note ammo usage
            parent::fire($gamedata, $fireOrder);
            $this->ammunition--;
            Manager::updateAmmoInfo($fireOrder->shooterid, $this->id, $gamedata->id, $this->firingMode, $this->ammunition, $gamedata->turn);
        }

        public function getDamage($fireOrder){ return Dice::d(10, 3)+8;   }
        public function setMinDamage(){     $this->minDamage = 11;      }
        public function setMaxDamage(){     $this->maxDamage = 38;      }
		
}//endof ProximityLaser





class FMissileRack extends Weapon {
		public $name = "FMissileRack";
        public $displayName = "Class-F Missile Rack";
        public $iconPath = "ClassFMissileRack.png";

		public $useOEW = false;
		public $ballistic = true;
	
        public $animation = "trail";
        public $animationColor = array(50, 50, 50);

        public $intercept = 0;
		public $priority = 6; 		
		
        public $loadingtime = 1;
		public $normalload = 2;

		public $range = 20;
		public $distanceRange = 60;
		public $rangeMod = 0;
		public $hits = array();
		
        public $rangePenalty = 0;
        public $fireControlArray = array(1=>array(6, 6, 6), 2=>array(4, 4, 4));
		public $firingModes = array(1=>'Standard', 2=>'Long-range'); //equals to available missiles; data is basic - if launcher is special, constructor will modify it
		public $damageTypeArray = array(1=>'Standard', 2=>'Standard'); //indicates that this weapon does damage in Pulse mode

		public $rangeArray = array(1=>20, 2=>35); //, 3=>15); 
		public $distanceRangeArray = array(1=>60, 2=>75); //, 3=>45); 

        public $damageType = "Standard";
		public $weaponClass = "Ballistic";

		function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){ //maxhealth and power reqirement are fixed; left option to override with hand-written values
			if ( $maxhealth == 0 ) $maxhealth = 6;
			if ( $powerReq == 0 ) $powerReq = 0;
			if ($this->turnsloaded == 1) {
				$basicFC = $this->fireControlArray[1]; //get current default values
				$basicFC[0] = $basicFC[0] -2; //antifighter FC
				$basicFC[1] = $basicFC[1] -2; //antimedium FC
				$basicFC[2] = $basicFC[2] -2; //antiheavy FC 
				$this->fireControlArray[1] = $basicFC;
				$longFC = array(null, null, null); //LR FC is nullified as cannot fire in rapid mode immediately after long-range mode
				$this->fireControlArray[2] = $longFC;
				$this->changeFiringMode(1);    //recompile current values from arrays
			}

            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);




//        	switch($this->turnsloaded){
//				case 1:
					/*
	                foreach ($this->fireControlArray as $key=>$FCarray){  //Rapid fire reduces the FC by 2
                    $this->fireControlArray[$key][0] -= 2; //fighter
                    $this->fireControlArray[$key][1] -= 2; //medium
                    $this->fireControlArray[$key][2] -= 2; //Cap
					}
					*/
//					$this->fireControlArray = array(1=>array(4, 4, 4), 2=>array(null, null, null));
//				$this->range[$key][0] -= 5; //Rapid fire reduces the range by 5
//                }
        }

        public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);   
			$this->data["Special"] = $this->fireControlArray;  
//			$this->data["Special"] = "Can fire accelerated ROF with -5 range and -10 FC:";  
//			$this->data["Special"] .= "<br> - 1 turn: 1d10+6, intercept -10"; 
		}
	
	public function getDamage($fireOrder){ 
		switch($this->firingMode){
			case 1: //Standard
				return 20; 
				break;
			case 2: //Long-Range
				return 20; 
				break;
			default: //most missiles do the same damage
				return 20; 
				break;	
		}
	}
	
	public function setMinDamage(){ 
		switch($this->firingMode){
			case 1: //Standard
				$this->minDamage = 20; 
				break;
			case 2: //Long-Range
				$this->minDamage = 20; 
				break;
			default: //most missiles do the same damage
				$this->minDamage = 20; 
				break;	
		}
		$this->minDamageArray[$this->firingMode] = $this->minDamage;
	}
	
	public function setMaxDamage(){
		switch($this->firingMode){
			case 1: //Standard
				$this->maxDamage = 20; 
				break;
			case 2: //Long-Range
				$this->maxDamage = 20; 
				break;
			default: //most missiles do the same damage
				$this->maxDamage = 20; 
				break;	
		}
		$this->maxDamageArray[$this->firingMode] = $this->maxDamage;
	}


		public function stripForJson(){
			$strippedSystem = parent::stripForJson();
			$strippedSystem->data = $this->data;
			$strippedSystem->minDamage = $this->minDamage;
			$strippedSystem->minDamageArray = $this->minDamageArray;
			$strippedSystem->maxDamage = $this->maxDamage;
			$strippedSystem->maxDamageArray = $this->maxDamageArray;		
			$strippedSystem->fireControl = $this->fireControl;
			$strippedSystem->fireControlArray = $this->fireControlArray;
//			$strippedSystem->range = $this->range;
			return $strippedSystem;
		}

}//end of class FMissileRack
	








class ChaffMissile extends Weapon{
	public $name = "ChaffMissile";
    public $displayName = "Chaff Missile";
    public $useOEW = false;
    public $ballistic = true;
    public $animation = "trail";
    public $animationColor = array(50, 50, 50);

    public $range = 20;
    public $distanceRange = 60;
    public $rangeMod = 0;
    public $priority = 1;
    public $loadingtime = 1;
    public $iconPath = "ClassFMissileRack.png";    

	private static $alreadyEngaged = array(); //units that were already engaged by a Chaff Missile this turn (multiple Chaff Missiles do not stack).

    public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    public $weaponClass = "Ballistic"; //MANDATORY (first letter upcase) weapon class - overrides $this->data["Weapon type"] if set! 
	
	public $firingMode = 'Chaff';
    public $fireControl = array(6, 6, 6); // fighters, <mediums, <capitals ; INCLUDES MISSILE WARHEAD (and FC if present)! as effectively it is the same and simpler

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		        //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 6;
            if ( $powerReq == 0 ) $powerReq = 0;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }




	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){ //really no matter what exactly was hit!
//		if (WeaponEM::isTargetEMResistant($ship,$system)) return; //no effect on Advanced Armor

//Need to check if fired weapons are ballistics or not

        if (isset(ChaffMissile::$alreadyEngaged[$ship->id])) return; //target already engaged by a previous Chaff Missile

		$effectHit = 3; 
		$effectHit5 = $effectHit * 5;
		$fireOrder->pubnotes .= "<br> All non-ballistic weapon's fire by target reduced by $effectHit5 percent.";

		$allFire = $ship->getAllFireOrders($gamedata->turn);
		foreach($allFire as $fireOrder) {
			if ($fireOrder->type == 'normal') {
				if ($fireOrder->rolled > 0) {
				}else{
					$fireOrder->needed -= 3 *5; //$needed works on d100
					$fireOrder->pubnotes .= "; Chaff Missile impact, -15% to hit."; //note why hit chance does not match
					ChaffMissile::$alreadyEngaged[$ship->id] = true;
				}
			}
		}







//		$effectIni = Dice::d(6,1);//strength of effect: 1d6
//		$effectSensors = Dice::d(6,1);//strength of effect: 1d6
//		$effectIni5 = $effectIni * 5;
		
		if ($ship instanceof FighterFlight){  //place effect on first fighter, even if it's already destroyed!
			$firstFighter = $ship->getSampleFighter();
			ChaffMissile::$alreadyEngaged[$ship->id] = true;//mark engaged        
			if($firstFighter){
				for($i=1; $i<=$effectHit;$i++){
					$crit = new tmphitreduction(-1, $ship->id, $firstFighter->id, 'tmphitreduction', $gamedata->turn, $gamedata->turn); 
					$crit->updated = true;
			        	$firstFighter->criticals[] =  $crit;
				}
			}
		}else{ //ship - place effcet on C&C!   */
			$CnC = $ship->getSystemByName("CnC");
			ChaffMissile::$alreadyEngaged[$ship->id] = true;//mark engaged        
			if($CnC){
				for($i=0; $i<=$effectHit;$i++){
					$crit = new tmphitreduction(-1, $ship->id, $CnC->id, 'tmphitreduction', $gamedata->turn, $gamedata->turn); 
//					$crit->inEffect = true;
					$crit->updated = true;
			        	$CnC->criticals[] =  $crit;
				}
			}
		}
	} //endof function onDamagedSystem


    
        public function getDamage($fireOrder){ return 0;   }
        public function setMinDamage(){     $this->minDamage = 0;      }
        public function setMaxDamage(){     $this->maxDamage = 0;      }
    
	public function setSystemDataWindow($turn){
		$this->data["Range"] = $this->range . '/' . $this->distanceRange;
		$this->data["Special"] = 'Causes a -3 to hit on target on turn it is fired.';
		parent::setSystemDataWindow($turn);
        }
    
 
} //endof class ChaffMissile  




class StealthMissile extends Weapon{
	public $name = "StealthMissile";
    public $displayName = "Stealth Missile";
    public $useOEW = false;
    public $ballistic = true;
    public $animation = "trail";
    public $animationColor = array(50, 50, 50);

    public $range = 20;
    public $distanceRange = 60;
    public $rangeMod = 0;
    public $priority = 1;
    public $loadingtime = 1;
    public $iconPath = "ClassFMissileRack.png";    

	public $hidetarget = true;

    public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    public $weaponClass = "Ballistic"; //MANDATORY (first letter upcase) weapon class - overrides $this->data["Weapon type"] if set! 
	
	public $firingMode = 'Ballistic';
    public $fireControl = array(6, 6, 6); // fighters, <mediums, <capitals ; INCLUDES MISSILE WARHEAD (and FC if present)! as effectively it is the same and simpler

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		        //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 6;
            if ( $powerReq == 0 ) $powerReq = 0;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

    
        public function getDamage($fireOrder){ return 10;   }
        public function setMinDamage(){     $this->minDamage = 10;      }
        public function setMaxDamage(){     $this->maxDamage = 10;      }
    
	public function setSystemDataWindow($turn){
		$this->data["Range"] = $this->range . '/' . $this->distanceRange;
		$this->data["Special"] = 'Causes a -3 to hit on target on turn it is fired.';
		parent::setSystemDataWindow($turn);
        }
    
 
} //endof class StealthMissile  





class TestMissile extends Laser{
        public $name = "TestMissile";
        public $displayName = "Test Missile";
	    public $iconPath = "NexusLaserMissile.png";

        public $animationArray = array(1=>'trail', 2=>'trail');
        public $trailColor = array(141, 240, 255);
        public $animationColorArray = array(1=>array(220, 60, 120), array(220, 60, 120));
        public $projectilespeed = 10;
        public $trailLength = 100;    
        public $uninterceptableArray = array(1=>false, 2=>false); 
		
        public $ballistic = true; //missile
        public $rangeArray = array(1=>20, 2=>20);
        public $distanceRangeArray = array(1=>30, 2=>30);
        public $ammunition = 20; //limited number of shots
	    
        public $loadingtimeArray = array(1=>1, 2=>1); // 1/2 turns
        public $rangePenaltyArray = array(1=>0, 2=>0);
        public $fireControlArray = array(1=>array(null, 2, 2), 2=>array(null, 2, 2)); // fighters, <mediums, <capitals; INCLUDES BOTH LAUNCHER AND MISSILE DATA!
		public $hidetargetArray = array(1=>false, 2=>true);
		public $priorityArray = array(1=>8, 2=>8); 
	    
		public $firingModes = array(1=>'Regular', 2=>'Stealth'); //firing mode - just a name essentially
		public $damageTypeArray = array(1=>'Standard', 2=>'Standard'); //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		        //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 6;
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
            $this->data["Special"] = "Bomb-pumped laser. Ballistic weapon that scores raking (8) damage.";
            $this->data["Special"] .= "<br>Long-range: 20 hex launch and 30 hex max range, 2d10+2 damage.";
            $this->data["Special"] .= "<br>Short-range: 10 hex launch and 15 hex max range, 3d10+4 damage.";
            $this->data["Ammunition"] = $this->ammunition;
        }
        
        public function setAmmo($firingMode, $amount){
            $this->ammunition = $amount;
        }
       public function fire($gamedata, $fireOrder){ //note ammo usage
            parent::fire($gamedata, $fireOrder);
            $this->ammunition--;
            Manager::updateAmmoInfo($fireOrder->shooterid, $this->id, $gamedata->id, $this->firingMode, $this->ammunition, $gamedata->turn);
        }

        public function getDamage($fireOrder){ 
		switch($this->firingMode){
			case 1:
				return 5; //Light Chemical Laser
				break;
			case 2:
				return 6; //Medium Chemical Laser
				break;	
		}
	}
        public function setMinDamage(){ 
		switch($this->firingMode){
			case 1:
				$this->minDamage = 5; //Light Chemical Laser
				break;
			case 2:
				$this->minDamage = 6; //Medium Chemical Laser
				break;	
		}
		$this->minDamageArray[$this->firingMode] = $this->minDamage;
	}
        public function setMaxDamage(){
		switch($this->firingMode){
			case 1:
				$this->maxDamage = 5; //Light Chemical Laser
				break;
			case 2:
				$this->maxDamage = 6; //Medium Chemical Laser
				break;	
		}
		$this->maxDamageArray[$this->firingMode] = $this->maxDamage;
	}
    
//        public function getDamage($fireOrder){ return Dice::d(10, 2)+2;   }
//        public function setMinDamage(){     $this->minDamage = 5;      }
//        public function setMaxDamage(){     $this->maxDamage = 23;      }
		
} //endof TestMissile


class TestMissile2 extends Weapon{
        public $name = "TestMissile2";
        public $displayName = "Test Missile 2";
		    public $iconPath = "NexusKineticBoxLauncher.png";
        public $animation = "trail";
        public $trailColor = array(141, 240, 255);
        public $animationColor = array(50, 50, 50);
        public $animationExplosionScale = 0.2;
        public $projectilespeed = 10;
        public $animationWidth = 4;
        public $trailLength = 100;    

        public $useOEW = false; //missile
        public $ballistic = true; //missile
        public $range = 60;
        public $distanceRange = 80;
        public $ammunition = 4; //limited number of shots
	    
        
        public $loadingtime = 1; // 1/2 turns
        public $rangePenalty = 0;
        public $fireControl = array(3, 3, 3); // fighters, <mediums, <capitals; INCLUDES BOTH LAUNCHER AND MISSILE DATA!
	    
	public $priority = 6; 
	    
	public $firingMode = 'Ballistic'; //firing mode - just a name essentially
	public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    	public $weaponClass = "Matter"; //should be Ballistic and Matter, but FV does not allow that. Instead decrease advanced armor encountered by 2 points (if any) (usually system does that, but it will account for Ballistic and not Matter)

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		        //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 4;
            if ( $powerReq == 0 ) $powerReq = 0;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function stripForJson() {
            $strippedSystem = parent::stripForJson();    
            $strippedSystem->ammunition = $this->ammunition;           
            return $strippedSystem;
        }
        
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            $this->data["Special"] = "Ignores armor, no overkill (Ballistic+Matter weapon).";
            $this->data["Ammunition"] = $this->ammunition;
        }
        
        public function getDamage($fireOrder){
            $dmg = 5;
            return $dmg;
       }
        public function setAmmo($firingMode, $amount){
            $this->ammunition = $amount;
        }
       public function fire($gamedata, $fireOrder){ //note ammo usage
            parent::fire($gamedata, $fireOrder);
            $this->ammunition--;
            Manager::updateAmmoInfo($fireOrder->shooterid, $this->id, $gamedata->id, $this->firingMode, $this->ammunition, $gamedata->turn);
        }
    
        public function setMinDamage(){     $this->minDamage = 5;      }
        public function setMaxDamage(){     $this->maxDamage = 5;      }
}//endof TestMissile2







// Uses MultiMissileLauncher code

class MultiDefenseLauncher extends Weapon {
	public $name = "MultiDefenseLauncher";
    public $displayName = "ToBeSetInConstructor";
    public $useOEW = false;
    public $ballistic = true;
    public $animation = "trail";
    public $animationColor = array(50, 50, 50);
	/*
    public $trailColor = array(141, 240, 255);
    public $animationExplosionScale = 0.4;
    public $projectilespeed = 8;
    public $animationWidth = 4;
    public $trailLength = 100;
	*/
    public $range = 20;
    public $distanceRange = 60;
    public $firingMode = 1;
    public $rangeMod = 0;
    public $priority = 6;
	public $hits = array();
    public $loadingtime = 1;
    public $iconPath = "ClassDMissileRack.png";    
//    public $loadingtimeArray = array(1=>1, 2=>1, 3=>1); //mode 1 should be the one with longest loading time

    public $priorityArray = array(1=>1, 2=>1, 3=>4);
    public $intercept = 6;
    public $ballisticIntercept = true;

    private $rackExplosionDamage = 50; //how much damage will this weapon do in case of catastrophic explosion
    private $rackExplosionThreshold = 20; //how high roll is needed for rack explosion    

	private static $alreadyEngaged = array(); //units that were already engaged by a Chaff Missile	this turn (multiple Webs do not stack).

    public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    public $weaponClass = "Ballistic"; //MANDATORY (first letter upcase) weapon class - overrides $this->data["Weapon type"] if set! 

//NOTE: The 'Intercceptor' mode, while only serving as an anti-missile misile if you do nothing
//needs to be listed as its own mode. Otherwise, it takes out the defensive shield characteristic
//of the chaff missile and allows it to provider intercept against more than one incoming missile.	
	public $firingModes = array(1=>'Interceptor', 2=>'Chaff', 3=>'AntiFighter'); //equals to available missiles; data is basic - if launcher is special, constructor will modify it
	public $damageTypeArray = array(1=>'Standard', 2=>'Standard', 3=>'Standard'); //indicates that this weapon does damage in Pulse mode
    public $fireControlArray = array( 1=>array(null, null, null), 2=>array(6, 6, 6), 3=>array(9, 6, 6) ); // fighters, <mediums, <capitals ; INCLUDES MISSILE WARHEAD (and FC if present)! as effectively it is the same and simpler
    public $rangeArray = array(1=>20, 2=>20, 3=>15); //distanceRange remains fixed, as it's improbable that anyone gets out of missile range and this would need more coding
	public $distanceRangeArray = array(1=>60, 2=>60, 3=>45);
    //typical (class-S) launcher is FC 3/3/3 and warhead +3 - which would mean 6/6/6!

    /*ATYPICAL constructor: doesn't take health and power usage, but takes desired launcher type - and does appropriate modifications*/
        function __construct($armour, $launcherType, $startArc, $endArc, $base=false)
        {
		switch($launcherType){ //modifications dependent on launcher type...
			default: 
				$this->displayName = "Class-D Missile Rack";
				$maxhealth = 6;
				$this->iconPath = "ClassDMissileRack.png";
//				$this->loadingtime = 1; //fires every turn
				$this->rackExplosionThreshold = 20; //how high roll is needed for rack explosion 
				break;	
		}

		if ($base){ //mounted on base (or other stable platform): +40 launch range (launch range = distance range)
			foreach ($this->rangeArray as $key=>$rng) {
		    		$this->rangeArray[$key] += 40; 
			}         
		}

		$this->range = $this->rangeArray[1]; //base range = first range

		parent::__construct($armour, $maxhealth, 0, $startArc, $endArc);
        }

	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){ //really no matter what exactly was hit!
//		if (WeaponEM::isTargetEMResistant($ship,$system)) return; //no effect on Advanced Armor

//		$allFire = $ship->getAllFireOrders($gamedata->turn);

		$this->changeFiringMode($fireOrder->firingMode);  //needs to be outside the switch routine

		switch($this->firingMode){

		case 1:

			break;

		case 2:

			if (isset(MultiDefenseLauncher::$alreadyEngaged[$ship->id])) return; //target already engaged by a previous Chaff Missile

			$effectHit = 3; 
			$effectHit5 = $effectHit * 5;
			$fireOrder->pubnotes .= "<br> All non-ballistic weapon's fire by target reduced by $effectHit5 percent.";

			$allFire = $ship->getAllFireOrders($gamedata->turn);
			foreach($allFire as $fireOrder) {
				if ($fireOrder->type == 'normal') {
					if ($fireOrder->rolled > 0) {
					}else{
						$fireOrder->needed -= 3 *5; //$needed works on d100
						$fireOrder->pubnotes .= "; Chaff Missile impact, -15% to hit."; //note why hit chance does not match
						MultiDefenseLauncher::$alreadyEngaged[$ship->id] = true;
					}
				}
			}

			if ($ship instanceof FighterFlight){  //place effect on first fighter, even if it's already destroyed!
				$firstFighter = $ship->getSampleFighter();
				MultiDefenseLauncher::$alreadyEngaged[$ship->id] = true;//mark engaged        
				if($firstFighter){
					for($i=1; $i<=$effectHit;$i++){
						$crit = new tmphitreduction(-1, $ship->id, $firstFighter->id, 'tmphitreduction', $gamedata->turn, $gamedata->turn); 
						$crit->updated = true;
							$firstFighter->criticals[] =  $crit;
					}
				}
			}else{ //ship - place effcet on C&C!   */
				$CnC = $ship->getSystemByName("CnC");
				MultiDefenseLauncher::$alreadyEngaged[$ship->id] = true;//mark engaged        
				if($CnC){
					for($i=0; $i<=$effectHit;$i++){
						$crit = new tmphitreduction(-1, $ship->id, $CnC->id, 'tmphitreduction', $gamedata->turn, $gamedata->turn); 
						$crit->updated = true;
							$CnC->criticals[] =  $crit;
					}
				}
			}

			break;

		case 3:

			break;
		}

	} //endof function onDamagedSystem

	public function getDamage($fireOrder){ 
		switch($this->firingMode){
			case 1: //Anti-missile
				return 0; 
				break;
			case 2: //Chaff
				return 0; 
				break;
			case 3: //Anti-Fighter
				return 15; 
				break;
			default: 
				return 0; 
				break;	
		}
	}
	public function setMinDamage(){ 
		switch($this->firingMode){
			case 1: //Anti-missile
				$this->minDamage = 0; 
				break;
			case 2: //Chaff
				$this->minDamage = 0; 
				break;
			case 3: //Anti-Fighter
				$this->minDamage = 15; 
				break;

			default: 
				$this->minDamage = 0; 
				break;	
		}
		$this->minDamageArray[$this->firingMode] = $this->minDamage;
	}
	public function setMaxDamage(){
		switch($this->firingMode){
			case 1: //Anti-missile
				$this->maxDamage = 0; 
				break;
			case 2: //Chaff
				$this->maxDamage = 0; 
				break;
			case 3: //Anti-Fighter
				$this->maxDamage = 15; 
				break;

			default: 
				$this->maxDamage = 0; 
				break;	
		}
		$this->maxDamageArray[$this->firingMode] = $this->maxDamage;
	}

	public function setSystemDataWindow($turn){
		$this->data["Range"] = $this->range . '/' . $this->distanceRange;
		$this->data["Special"] = 'Multiple munitions available. This weapon may explode when damaged.';
		$this->data["Special"] .= '<br>Available munitions:';
		$this->data["Special"] .= '<br> - Anti-missile: Default mode, -30 intercept against ballistics.';
		$this->data["Special"] .= '<br> - Chaff: Range 20, Target has -15% to hit for all non-ballistics. Not cumulative.';
		$this->data["Special"] .= '<br> - Anti-Fighter: range 15, +15 FC vs fighters, dmg 15';
		parent::setSystemDataWindow($turn);
        }

    public function isInDistanceRange($shooter, $target, $fireOrder){
        $movement = $shooter->getLastTurnMovement($fireOrder->turn);    
        if(mathlib::getDistanceHex($movement->position,  $target) > $this->distanceRange)
        {
            $fireOrder->pubnotes .= " FIRING SHOT: Target moved out of distance range.";
            return false;
        }
        return true;
    }

    public function testCritical($ship, $gamedata, $crits, $add = 0){ //add testing for ammo explosion!
        $explodes = false;
        $roll = Dice::d(20);
        if ($roll >= $this->rackExplosionThreshold) $explodes = true;

        if($explodes){
            $this->ammoExplosion($ship, $gamedata, $this->rackExplosionDamage);            
            $this->addMissileCritOnSelf($ship->id, "AmmoExplosion", $gamedata);
        }else{
            $crits = parent::testCritical($ship, $gamedata, $crits, $add);
        }

        return $crits;
    } //endof function testCritical

    public function ammoExplosion($ship, $gamedata, $damage){
        //first, destroy self if not yet done...
        if (!$this->isDestroyed()){
            $this->noOverkill = true;
            $fireOrder =  new FireOrder(-1, "ammoExplosion", $ship->id,  $ship->id, $this->id, -1, 
                    $gamedata->turn, 'standard', 100, 1, 1, 1, 0, null, null, 'ballistic');
            $dmgToSelf = 1000; //rely on $noOverkill instead of counting exact amount left - 1000 should be more than enough...
            $this->doDamage($ship, $ship, $this, $dmgToSelf, $fireOrder, $pos, $gamedata, true, $this->location);
        }

        //then apply damage potential as a hit...
        if($damage>0){
            $this->noOverkill = false;
            $this->damageType = 'Flash'; //should be Raking by the rules, but Flash is much easier to do - and very fitting for explosion!
            $fireOrder =  new FireOrder(-1, "ammoExplosion", $ship->id,  $ship->id, $this->id, -1, 
                    $gamedata->turn, 'flash', 100, 1, 1, 1, 0, null, null, 'ballistic');
            $this->doDamage($ship, $ship, $this, $damage, $fireOrder, null, $gamedata, false, $this->location); //show $this as target system - this will ensure its destruction, and Flash mode will take care of the rest
        }
    }

    public function addMissileCritOnSelf($shipid, $phpclass, $gamedata){
        $crit = new $phpclass(-1, $shipid, $this->id, $phpclass, $gamedata->turn);
        $crit->updated = true;
        $this->criticals[] =  $crit;
    }        

} //endof class MultiDefenseLauncher






?>
