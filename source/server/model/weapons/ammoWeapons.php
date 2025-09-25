<?php

class AmmoDirectWeapon extends Weapon{        
	public $name = "AmmoDirectWeapon";
	public $displayName = 'SOMEONE DID NOT OVERLOAD TEMPLATE WEAPON FULLY!'; //should never be shown ;)   
			
	public $checkAmmoMagazine = true;
    public $animation = "bolt";
    public $animationArray = array();
    public $animationExplosionScale = 0; //0 means it will be set automatically by standard constructor, based on average damage yield
    public $animationExplosionScaleArray = array();    
    
    public $animationColor = array(50, 50, 50);
    public $firingMode = 1;
    public $priority = 6;
    
    public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    public $weaponClass = "Particle"; //MANDATORY (first letter upcase) weapon class - overrides $this->data["Weapon type"] if set! 
	
	//basic weapon data, before being modified by actual ammo.
	protected $basicFC=array(0,0,0);
	private $dpArray = array(); //array of damage penalties for all modes! - filled automatically
	
	public $firingModes = array(); //equals to available ammo
	public $damageTypeArray = array(); //indicates that this weapon does damage in Pulse mode
    public $fireControlArray = array(); // fighters, <mediums, <capitals ; INCLUDES MISSILE WARHEAD (and FC if present)! as effectively it is the same and simpler

	protected $ammoClassesArray = array();//FILLED IN CONSTRUCTOR! classes representing POTENTIALLY available ammo - so firing modes are always shown in the same order

	protected $availableAmmoAlreadySet = false;	
	
	private $ammoMagazine; //reference to ammo magazine
	private $ammoClassesUsed = array();

//Matter variables
    public $noOverkill = false; 
    protected $noOverkillArray = array();	
//Adding Pulse variables
	public $maxpulses = 0;    
	public $maxpulsesArray = array();
    public $grouping = 0;
    public $groupingArray = array();		   		  
//Extra variables Long Range
	public $rangePenalty = 0;	
	public $rangePenaltyArray = array(); 		
		
    //ATYPICAL constructor: takes ammo magazine class and (optionally) information about being fitted to stable platform
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $magazine, $base=false)
	{
		//VERY IMPORTANT: fill $ammoClassesArray (cannot be done as constants!
		//classes representing POTENTIALLY available ammo - so firing modes are always shown in the same order
		//remember that appropriate enhancements need to be enabled on ship itself, too!
		
		if(!$this->availableAmmoAlreadySet){ //This weapon should never be used, so just list all ammo in game.
			//GROME SHELLS
			$this->ammoClassesArray[] =  new AmmoHShellBasic();
			$this->ammoClassesArray[] =  new AmmoMShellBasic();
			$this->ammoClassesArray[] =  new AmmoLShellBasic();
			$this->ammoClassesArray[] =  new AmmoHShellFlash();
			$this->ammoClassesArray[] =  new AmmoMShellFlash();
			$this->ammoClassesArray[] =  new AmmoLShellFlash();
			$this->ammoClassesArray[] =  new AmmoHShellScatter();
			$this->ammoClassesArray[] =  new AmmoMShellScatter();			
			$this->ammoClassesArray[] =  new AmmoLShellScatter();
			$this->ammoClassesArray[] =  new AmmoHShellHeavy();
			$this->ammoClassesArray[] =  new AmmoMShellHeavy();
			$this->ammoClassesArray[] =  new AmmoLShellHeavy();
			$this->ammoClassesArray[] =  new AmmoHShellLRange();
			$this->ammoClassesArray[] =  new AmmoMShellLRange();
			$this->ammoClassesArray[] =  new AmmoHShellULRange();									
			$this->availableAmmoAlreadySet = true;
		}
	
		$this->ammoMagazine = $magazine;
		$this->recompileFiringModes();
		
		if ( $maxhealth == 0 ) $maxhealth = 6;         	
		if ( $powerReq == 0 ) $powerReq = 0;
			
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc); //class-S launcher: structure 6, power usage 0
		$magazine->subscribe($this); //subscribe to any further changes in ammo availability
	}
    

    protected function getAmmoMagazine(){
        return $this->ammoMagazine;
    }	
	
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		
		$this->data["Special"] = 'Available firing modes depend on ammo bought as unit enhancements. Ammunition available is tracked by central Ammunition Magazine system.';
	}
	
	//prepare firing modes - in order as indicated by $ammoCLassesArray (so every time the order is the same and classes aren't mixed), but use only classes actually held by magazine (no matter the count - 0 rounds is fine) if magazine holds no ammo - still list first entry on the list (weapon has to have SOME data!)
	
 	public function recompileFiringModes(){
		//clear existing arrays
		$this->firingModes = array(); //equals to available missiles
		$this->damageTypeArray = array(); //indicates that this weapon does damage in Pulse mode
		$this->weaponClassArray = array();
    		$this->fireControlArray = array(); // fighters, <mediums, <capitals ; INCLUDES MISSILE WARHEAD (and FC if present)! as effectively it is the same and simpler
		$this->priorityArray = array();
		$this->dpArray = array();
		$this->damageTypeArray = array();
		$this->weaponClassArray = array();
		$this->noOverkillArray = array();
		$this->minDamageArray = array();
		$this->maxDamageArray = array();
		$this->ammoClassesUsed = array();
		$this->maxpulsesArray = array();//Adding Pulse functions
		$this->groupingArray = array();//Adding Pulse functions	
		$this->rangePenaltyArray = array();
		$this->animationArray = array();											
		$this->animationExplosionScaleArray = array();							
		//add data for all modes to arrays
		$currMode = 0;
		foreach ($this->ammoClassesArray as $currAmmo){
			$isPresent = $this->ammoMagazine->getAmmoPresence($currAmmo->modeName);//does such ammo exist in magazine?
			if($isPresent){
				$currMode++;
				//fill all arrays for indicated mode
				$this->ammoClassesUsed[$currMode] = $currAmmo;
				$this->firingModes[$currMode] = $currAmmo->modeName;
				$this->damageTypeArray[$currMode] = $currAmmo->damageType; 
				$this->weaponClassArray[$currMode] = $currAmmo->weaponClass; 	
				
				$fc0 = 0;
				if(($this->basicFC[0] === null) || ($currAmmo->fireControlMod[0]===null)) {
					$fc0 = null;
				}else{
					$fc0 = $this->basicFC[0] + $currAmmo->fireControlMod[0];
				}
				$fc1 = $this->basicFC[1];
				if(($this->basicFC[1] === null) || ($currAmmo->fireControlMod[1]===null)) {
					$fc1 = null;
				}else{
					$fc1 = $this->basicFC[1] + $currAmmo->fireControlMod[1];
				}
				$fc2 = $this->basicFC[2];
				if(($this->basicFC[2] === null) || ($currAmmo->fireControlMod[2]===null)) {
					$fc2 = null;
				}else{
					$fc2 = $this->basicFC[2] + $currAmmo->fireControlMod[2];
				}
				$this->fireControlArray[$currMode] = array($fc0, $fc1, $fc2); // fighters, <mediums, <capitals ; INCLUDES MISSILE WARHEAD (and FC if present)! as effectively it is the same and simpler
				

				$this->priorityArray[$currMode] = $currAmmo->priority;
				$this->noOverkillArray[$currMode] = $currAmmo->noOverkill;
				$this->minDamageArray[$currMode] = $currAmmo->minDamage;
				$this->maxDamageArray[$currMode] = $currAmmo->maxDamage;							
				$this->maxpulsesArray[$currMode] = $currAmmo->maxpulses;//Adding Pulse functions
				$this->groupingArray[$currMode] = $currAmmo->grouping;//Adding Pulse functions				
				$this->rangePenaltyArray[$currMode] = $currAmmo->rangePenalty;
				$this->animationArray[$currMode] = $currAmmo->animation;
				$this->animationExplosionScaleArray[$currMode] = $currAmmo->animationExplosionScale;									
			}
		}
			
		//if there is no ammo available - add entry for first ammo on the list... or don't, just fill firingModes (this one is necessary) - assume basic weapons data resemble something like basic desired mode
		if ($currMode < 1){
			$this->FiringModes[1] = 'NoAmmunitionAvailable';
		}
			
		//change mode to 1, to call all appropriate routines connected with mode change
		$this->changeFiringMode(1);		
		//remember about effecting criticals, too!
	//	$this->effectCriticals(); //This was applying criticals twice! DK			
	}//endof function recompileFiringModes
	
	
	
	
 	public function stripForJson(){
		$strippedSystem = parent::stripForJson();
		$strippedSystem->firingModes = $this->firingModes; 
		$strippedSystem->damageTypeArray = $this->damageTypeArray; 
		$strippedSystem->weaponClassArray = $this->weaponClassArray; 
		$strippedSystem->fireControlArray = $this->fireControlArray; 
		$strippedSystem->priorityArray = $this->priorityArray; 
		$strippedSystem->dpArray = $this->dpArray; 
		$strippedSystem->noOverkillArray = $this->noOverkillArray; 
		$strippedSystem->minDamageArray = $this->minDamageArray; 
		$strippedSystem->maxDamageArray = $this->maxDamageArray; 			
		$strippedSystem->maxpulsesArray = $this->maxpulsesArray;//Adding Pulse functions
		$strippedSystem->groupingArray = $this->groupingArray;//Adding Pulse functions				
		$strippedSystem->rangePenaltyArray = $this->rangePenaltyArray; //Adding Range and No Lock variables for KK Missiles
		$strippedSystem->animationArray = $this->animationArray;
		$strippedSystem->animationExplosionScaleArray = $this->animationExplosionScaleArray;				
		return $strippedSystem;
	} 

    public function effectCriticalDamgeReductions($dp, $repeat = false){
		if($repeat) return; //Damage Reduced crit has already been applied in onConstructed() for this type of weapon, don't apply again!

        //damage penalty: 20% of variance or straight 2, whichever is bigger; hold that as a fraction, however! - low rolls should be affected lefss than high ones, after all        
        foreach ($this->firingModes as $dmgMode => $modeName) {
            $variance = $this->maxDamageArray[$dmgMode] - $this->minDamageArray[$dmgMode];
            $mod = $dp * max(2, 0.2 * $variance);
        
            $avgDmg = ($this->maxDamageArray[$dmgMode] + $this->minDamageArray[$dmgMode]) / 2;
        
            if ($avgDmg > 0) {
                $this->dpArray[$dmgMode] = $mod / $avgDmg;
            } else {
                $this->dpArray[$dmgMode] = 1;
            }
        
            $this->dpArray[$dmgMode] = min(0.9, $this->dpArray[$dmgMode]);
        
            $this->minDamageArray[$dmgMode] = floor($this->minDamageArray[$dmgMode] * (1 - $this->dpArray[$dmgMode]));
            $this->maxDamageArray[$dmgMode] = floor($this->maxDamageArray[$dmgMode] * (1 - $this->dpArray[$dmgMode]));
        }
    }	


	//actually use getDamage() method of ammo!
    public function getDamage($fireOrder)
    {
		$currAmmo = null;
        //find appropriate ammo
		if (array_key_exists($this->firingMode,$this->ammoClassesUsed)){
			$currAmmo = $this->ammoClassesUsed[$this->firingMode];
		}
	    
		//execute getDamage()
		if($currAmmo){
			return $currAmmo->getDamage($fireOrder);
		}else{
			return 0;	
		}
    }

     public function fire($gamedata, $fireOrder)	//For Multiwarhead & Jammer missiles
    {		
        parent::fire($gamedata, $fireOrder);
        
		$magazine =  $this->getAmmoMagazine();
    	$modeName = $this->firingModes[$this->firingMode];
    			
		if($magazine){ //else something is wrong - weapon is put on a ship without Ammo Magazine!
			$magazine->doDrawAmmo($gamedata,$modeName);
		}
		
	}//endof function fire	


	
    //Adding Pulse functions
    protected function getPulses($turn)
        {
           $currAmmo = null;
        //find appropriate ammo
		if (array_key_exists($this->firingMode,$this->ammoClassesUsed)){
			$currAmmo = $this->ammoClassesUsed[$this->firingMode];
		} 
		//execute getPulses()
		if($currAmmo){
			return $currAmmo->getPulses($turn);
		}else{
			return 0;	
			}
    	 }
    	 
    protected function getExtraPulses($needed, $rolled)
   		 {
		$currAmmo = null;
        //find appropriate ammo
		if (array_key_exists($this->firingMode,$this->ammoClassesUsed)){
			$currAmmo = $this->ammoClassesUsed[$this->firingMode];
		}
		//execute getExtraPulses()
		if($currAmmo){
			return $currAmmo->getExtraPulses($needed, $rolled);
		}else{
			return 0;	
			}
	    }	    
	public function rollPulses($turn, $needed, $rolled)
	    {
			$currAmmo = null;
	        //find appropriate ammo
			if (array_key_exists($this->firingMode,$this->ammoClassesUsed)){
				$currAmmo = $this->ammoClassesUsed[$this->firingMode];
			}
			//execute rollPulses()
			if($currAmmo){
				return $currAmmo->rollPulses($turn, $needed, $rolled);
			}else{
				return 0;	
			}
	    }

}//endof AmmoDirectTemplate



class AmmoHeavyRailGun extends AmmoDirectWeapon{
	public $name = "AmmoHeavyRailGun";
    public $displayName = "Heavy Railgun";
    public $iconPath = "HeavyRailgun.png";    
    public $animationColor = array(250, 250, 190);
	
    public $priority = 9;
    public $loadingtime = 4;

    public $rangePenalty = 0.33;
	protected $basicFC = array(-3, 2, 2); // fighters, <mediums, <capitals
	
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $magazine)
	{
		if ( $maxhealth == 0 ) $maxhealth = 12;
            	if ( $powerReq == 0 ) $powerReq = 9;
            		
		//reset shell availability! (Parent sets way too much)
		if(!$this->availableAmmoAlreadySet){
			$this->ammoClassesArray = array();
			$this->ammoClassesArray[] =  new AmmoHShellBasic();
			$this->ammoClassesArray[] =  new AmmoHShellFlash();
			$this->ammoClassesArray[] =  new AmmoHShellScatter();
			$this->ammoClassesArray[] =  new AmmoHShellHeavy();
			$this->ammoClassesArray[] =  new AmmoHShellLRange();
			$this->ammoClassesArray[] =  new AmmoHShellULRange();								
			$this->availableAmmoAlreadySet = true;
		}            						
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $magazine); //Parent routines take care of the rest
	}
	
        public function setSystemDataWindow($turn){		
		parent::setSystemDataWindow($turn);	
			$this->data["Special"] .= '<br>Can fire different shells, effects are outlined below:';
			$this->data["Special"] .= "<br>  - Flash: Deals Plasma damage in Flash mode."; 
			$this->data["Special"] .= "<br>  - Scatter: Pulse mode with 25% grouping "; 
			$this->data["Special"] .= "<br>  - Heavy: Deals +15 damage"; 
			$this->data["Special"] .= "<br>  - Long Range: Range penalty -5% per 4 hex, damage 3d10+3.";
			$this->data["Special"] .= "<br>  - Ultra Long Range: Range penalty -5% per 5 hex, damage 1d10+5.";
		}	
	
} //endof class AmmoHeavyRailGun

class AmmoMediumRailGun extends AmmoDirectWeapon{
	public $name = "AmmoMediumRailGun";
    public $displayName = "Medium Railgun";
    public $iconPath = "Railgun.png";    
    public $animationColor = array(250, 250, 190);
    	
    public $priority = 9;
    public $loadingtime = 3;

    public $rangePenalty = 0.5;
	protected $basicFC = array(-3, 2, 2); // fighters, <mediums, <capitals
	
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $magazine)
	{
		if ( $maxhealth == 0 ) $maxhealth = 9;
            	if ( $powerReq == 0 ) $powerReq = 6;
            		
		//reset shell availability! (Parent sets way too much)
		if(!$this->availableAmmoAlreadySet){
			$this->ammoClassesArray = array();
			$this->ammoClassesArray[] =  new AmmoMShellBasic();
			$this->ammoClassesArray[] =  new AmmoMShellFlash();
			$this->ammoClassesArray[] =  new AmmoMShellScatter();			
			$this->ammoClassesArray[] =  new AmmoMShellHeavy();
			$this->ammoClassesArray[] =  new AmmoMShellLRange();					
			$this->availableAmmoAlreadySet = true;
		}              						
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $magazine); //Parent routines take care of the rest
	}
	
        public function setSystemDataWindow($turn){		
		parent::setSystemDataWindow($turn);	
			$this->data["Special"] .= '<br>Can fire different shells, effects are outlined below:';
			$this->data["Special"] .= "<br>  - Flash: Deals Plasma damage in Flash mode."; 
			$this->data["Special"] .= "<br>  - Scatter: Pulse mode with 25% grouping "; 
			$this->data["Special"] .= "<br>  - Heavy: Deals +10 damage"; 
			$this->data["Special"] .= "<br>  - Long Range: Range penalty -5 per 3 hex, damage 3d10+3.";
		}		
	
	
} //endof class AmmoMediumRailGun

class AmmoLightRailGun extends AmmoDirectWeapon{
	public $name = "AmmoLightRailGun";
    public $displayName = "Light Railgun";
    public $iconPath = "LightRailgun.png";    
    public $animationColor = array(250, 250, 190);	
    
    public $priority = 5;
    public $loadingtime = 2;

    public $rangePenalty = 1;
	protected $basicFC = array(3, 2, 0); // fighters, <mediums, <capitals
	
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $magazine)
	{
		if ( $maxhealth == 0 ) $maxhealth = 6;
            	if ( $powerReq == 0 ) $powerReq = 3;
            		
		//reset shell availability! (Parent sets way too much)
		if(!$this->availableAmmoAlreadySet){
			$this->ammoClassesArray = array();
			$this->ammoClassesArray[] =  new AmmoLShellBasic();
			$this->ammoClassesArray[] =  new AmmoLShellFlash();
			$this->ammoClassesArray[] =  new AmmoLShellScatter();	
			$this->ammoClassesArray[] =  new AmmoLShellHeavy();	
												
			$this->availableAmmoAlreadySet = true;
		}                						
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $magazine); //Parent routines take care of the rest
	}
	
        public function setSystemDataWindow($turn){		
		parent::setSystemDataWindow($turn);	
			$this->data["Special"] .= '<br>Can fire different shells, effects are outlined below:';
			$this->data["Special"] .= "<br>  - Flash: Deals Plasma damage in Flash mode."; 
			$this->data["Special"] .= "<br>  - Scatter: Pulse mode with 25% grouping "; 
			$this->data["Special"] .= "<br>  - Heavy: Deals +5 damage"; 
		}		
	
} //endof class AmmoLightRailGun




//Ammunition Types for Direct Fire Weapons below
//ammunition for AmmoMagazine - template; using template assures that all variables are filled even if a particular missile does not need them
class AmmoTemplateDirectWeapons{	
	public $name = 'AmmoTemplateDirectWeapons';
	public $displayName = 'SOMEONE DID NOT OVERLOAD TEMPLATE FULLY!'; //should never be shown ;)
	public $modeName = 'Template';
	public $size = 1; //how many store slots are required for a single round
	public $enhancementName = 'AMMO_DDDD'; //enhancement name to be enabled
	public $enhancementDescription = '(ammo) TEMPLATE'; //enhancement description
	public $enhancementPrice = 1;//price per missile
	
	public $fireControlMod = array(0, 0, 0); //MODIFIER for weapon fire control!
	public $minDamage = 0;
	public $maxDamage = 0;	
	public $damageType = 'Standard';//mode of dealing damage
	public $weaponClass = 'Particle';//weapon class
	public $priority = 1;
	public $noOverkill = false;

    //Adding Pulse variables
	public $maxpulses = 0;
    public $grouping = 0;		
   
    public $animation = "bolt";
    public $animationExplosionScale = 0; //0 means it will be set automatically by standard constructor, based on average damage yield    
	public $rangePenalty = 0;
	
    function __construct(){}
	
    public function getDamage($fireOrder) //actual function to be called, as with weapon!
    {
        return 0;
    }		
	
	function getPrice($unit) //some ammo might have different price depending on unit being fitted!
	{
		return $this->enhancementPrice;
	}

    
    //Adding Pulse functions
    protected function getPulses($turn)
        {
            return 0;
        }//endof function getPulses
	
    protected function getExtraPulses($needed, $rolled)
        {
            return 0;
        }//endof function getExtraPulses
	
	public function rollPulses($turn, $needed, $rolled)
		{
		return 0;
		}//endof function rollPulses
	 
		    
} //endof class AmmoTemplateDirectWeapons


//Basic Ammo for Heavy Railgun
class AmmoHShellBasic extends AmmoTemplateDirectWeapons{	
	public $name = 'AmmoHShellBasic';
	public $displayName = 'Heavy Shell'; //should never be shown ;)
	public $modeName = 'Heavy Shell';
	public $size = 1; //how many store slots are required for a single round
	public $enhancementName = 'SHELL_HBSC'; //enhancement name to be enabled
	public $enhancementDescription = '(HEAVY AMMO) Basic Shell'; //enhancement description
	public $enhancementPrice = 1;//officially 0, but if it was 0 then there would be no reason not to load it
	
	public $fireControlMod = array(0, 0, 0); //MODIFIER for weapon fire control!
	public $minDamage = 12;
	public $maxDamage = 57;	
	public $damageType = 'Standard';//mode of dealing damage
	public $weaponClass = 'Matter';//weapon class
	public $priority = 9;
	public $noOverkill = true;  
    public $rangePenalty = 0.33;
    public $animation = "bolt";

	
    function __construct(){}
	
    public function getDamage($fireOrder) //actual function to be called, as with weapon!
    {
        return Dice::d(10, 5)+7; 
    }		
	
	function getPrice($unit) //some ammo might have different price depending on unit being fitted!
	{
		return $this->enhancementPrice;
	} 
		    
} //endof class AmmoHShellBasic

//Basic Ammo for Medium Railgun
class AmmoMShellBasic extends AmmoTemplateDirectWeapons{	
	public $name = 'AmmoMShellBasic';
	public $displayName = 'Medium Shell'; //should never be shown ;)
	public $modeName = 'Medium Shell';
	public $size = 1; //how many store slots are required for a single round
	public $enhancementName = 'SHELL_MBSC'; //enhancement name to be enabled
	public $enhancementDescription = '(MEDIUM AMMO) Basic Shell'; //enhancement description
	public $enhancementPrice = 1;//officially 0, but if it was 0 then there would be no reason not to load it
	
	public $fireControlMod = array(0, 0, 0); //MODIFIER for weapon fire control!
	public $minDamage = 6;
	public $maxDamage = 33;	
	public $damageType = 'Standard';//mode of dealing damage
	public $weaponClass = 'Matter';//weapon class
	public $priority = 9;
	public $noOverkill = true;  
    public $rangePenalty = 0.5;
    public $animation = "bolt";

	
    function __construct(){}
	
    public function getDamage($fireOrder) //actual function to be called, as with weapon!
    {
        return Dice::d(10, 3)+3; 
    }		
	
	function getPrice($unit) //some ammo might have different price depending on unit being fitted!
	{
		return $this->enhancementPrice;
	} 
		    
} //endof class AmmoMShellBasic

//Basic Ammo for Light Railgun
class AmmoLShellBasic extends AmmoTemplateDirectWeapons{	
	public $name = 'AmmoLShellBasic';
	public $displayName = 'Light Shell'; //should never be shown ;)
	public $modeName = 'Light Shell';
	public $size = 1; //how many store slots are required for a single round
	public $enhancementName = 'SHELL_LBSC'; //enhancement name to be enabled
	public $enhancementDescription = '(LIGHT AMMO) Basic Shell'; //enhancement description
	public $enhancementPrice = 1;//officially 0, but if it was 0 then there would be no reason not to load it
	
	public $fireControlMod = array(0, 0, 0); //MODIFIER for weapon fire control!
	public $minDamage = 6;
	public $maxDamage = 15;	
	public $damageType = 'Standard';//mode of dealing damage
	public $weaponClass = 'Matter';//weapon class
	public $priority = 5;
	public $noOverkill = true;  
    public $rangePenalty = 1;
    public $animation = "bolt";

	
    function __construct(){}
	
    public function getDamage($fireOrder) //actual function to be called, as with weapon!
    {
        return Dice::d(10, 1)+5; 
    }		
	
	function getPrice($unit) //some ammo might have different price depending on unit being fitted!
	{
		return $this->enhancementPrice;
	} 
		    
} //endof class AmmoLShellBasic

//Flash Ammo for Heavy Railgun
class AmmoHShellFlash extends AmmoTemplateDirectWeapons{	
	public $name = 'AmmoHShellFlash';
	public $displayName = 'Heavy Flash Shell'; //should never be shown ;)
	public $modeName = 'Heavy Flash Shell';
	public $size = 1; //how many store slots are required for a single round
	public $enhancementName = 'SHELL_HFLH'; //enhancement name to be enabled
	public $enhancementDescription = '(HEAVY AMMO) Flash Shell'; //enhancement description
	public $enhancementPrice = 10;
	
	public $fireControlMod = array(0, 0, 0); //MODIFIER for weapon fire control!
	public $minDamage = 12;
	public $maxDamage = 57;	
	public $damageType = 'Flash';//mode of dealing damage
	public $weaponClass = 'Plasma';//weapon class
	public $priority = 9;
	public $noOverkill = false;  
    public $rangePenalty = 0.33;
    public $animation = "bolt";

	
    function __construct(){}
	
    public function getDamage($fireOrder) //actual function to be called, as with weapon!
    {
        return Dice::d(10, 5)+7; 
    }		
	
	function getPrice($unit) //some ammo might have different price depending on unit being fitted!
	{
		return $this->enhancementPrice;
	} 
		    
} //endof class AmmoHShellFlash

//Flash Ammo for Medium Railgun
class AmmoMShellFlash extends AmmoTemplateDirectWeapons{	
	public $name = 'AmmoMShellFlash';
	public $displayName = 'Medium Flash Shell'; //should never be shown ;)
	public $modeName = 'Medium Flash Shell';
	public $size = 1; //how many store slots are required for a single round
	public $enhancementName = 'SHELL_MFLH'; //enhancement name to be enabled
	public $enhancementDescription = '(MEDIUM AMMO) Flash Shell'; //enhancement description
	public $enhancementPrice = 6;
	
	public $fireControlMod = array(0, 0, 0); //MODIFIER for weapon fire control!
	public $minDamage = 6;
	public $maxDamage = 33;	
	public $damageType = 'Flash';//mode of dealing damage
	public $weaponClass = 'Plasma';//weapon class
	public $priority = 9;
	public $noOverkill = false;  
    public $rangePenalty = 0.5;
    public $animation = "bolt";

	
    function __construct(){}
	
    public function getDamage($fireOrder) //actual function to be called, as with weapon!
    {
        return Dice::d(10, 3)+3; 
    }		
	
	function getPrice($unit) //some ammo might have different price depending on unit being fitted!
	{
		return $this->enhancementPrice;
	} 
		    
} //endof class AmmoMShellFlash

//Flash Ammo for Light Railgun
class AmmoLShellFlash extends AmmoTemplateDirectWeapons{	
	public $name = 'AmmoLShellFlash';
	public $displayName = 'Light Flash Shell'; //should never be shown ;)
	public $modeName = 'Light Flash Shell';
	public $size = 1; //how many store slots are required for a single round
	public $enhancementName = 'SHELL_LFLH'; //enhancement name to be enabled
	public $enhancementDescription = '(LIGHT AMMO) Flash Shell'; //enhancement description
	public $enhancementPrice = 3;
	
	public $fireControlMod = array(0, 0, 0); //MODIFIER for weapon fire control!
	public $minDamage = 6;
	public $maxDamage = 15;	
	public $damageType = 'Flash';//mode of dealing damage
	public $weaponClass = 'Plasma';//weapon class
	public $priority = 5;
	public $noOverkill = false;  
    public $rangePenalty = 1;
    public $animation = "bolt";

	
    function __construct(){}
	
    public function getDamage($fireOrder) //actual function to be called, as with weapon!
    {
        return Dice::d(10, 1)+5; 
    }		
	
	function getPrice($unit) //some ammo might have different price depending on unit being fitted!
	{
		return $this->enhancementPrice;
	} 
		    
} //endof class AmmoLShellFlash

//Scatter Ammo for Heavy Railgun
class AmmoHShellScatter extends AmmoTemplateDirectWeapons{	
	public $name = 'AmmoHShellScatter';
	public $displayName = 'Heavy Scatter Shell'; //should never be shown ;)
	public $modeName = 'Heavy Scatter Shell';
	public $size = 1; //how many store slots are required for a single round
	public $enhancementName = 'SHELL_HSCT'; //enhancement name to be enabled
	public $enhancementDescription = '(HEAVY AMMO) Scatter Shell'; //enhancement description
	public $enhancementPrice = 10;
	
	public $fireControlMod = array(-2, -2, -2); //MODIFIER for weapon fire control!
	public $minDamage = 12;
	public $maxDamage = 57;	
	public $damageType = 'Pulse';//mode of dealing damage
	public $weaponClass = 'Matter';//weapon class
	public $priority = 9;
	public $noOverkill = true;  
    public $rangePenalty = 0.33;
    public $animation = "bolt";
    public $animationExplosionScale = 0.4; //0 means it will be set automatically by standard constructor, based on average damage yield    

    //Adding Pulse variables
	public $maxpulses = 6; //
    public $grouping = 25;
	
    function __construct(){}
	
    public function getDamage($fireOrder) //actual function to be called, as with weapon!
    {
        return Dice::d(10, 5)+7; 
    }		
	
	function getPrice($unit) //some ammo might have different price depending on unit being fitted!
	{
		return $this->enhancementPrice;
	} 
	    
    //Adding Pulse functions
    protected function getPulses($turn)
    {
        return 1;
    }//endof function getPulses
	
    protected function getExtraPulses($needed, $rolled)
    {
        return floor(($needed - $rolled) / ($this->grouping));
    }
	
	public function rollPulses($turn, $needed, $rolled)
	{
		$pulses = $this->getPulses($turn);
		$pulses+= $this->getExtraPulses($needed, $rolled);
		$pulses=min($pulses,$this->maxpulses);
		return $pulses;
	}
			    
} //endof class AmmoHShellScatter

//Scatter Ammo for Medium Railgun
class AmmoMShellScatter extends AmmoTemplateDirectWeapons{	
	public $name = 'AmmoMShellScatter';
	public $displayName = 'Medium Scatter Shell'; //should never be shown ;)
	public $modeName = 'Medium Scatter Shell';
	public $size = 1; //how many store slots are required for a single round
	public $enhancementName = 'SHELL_MSCT'; //enhancement name to be enabled
	public $enhancementDescription = '(MEDIUM AMMO) Scatter Shell'; //enhancement description
	public $enhancementPrice = 5;
	
	public $fireControlMod = array(-2, -2, -2); //MODIFIER for weapon fire control!
	public $minDamage = 6;
	public $maxDamage = 33;	
	public $damageType = 'Pulse';//mode of dealing damage
	public $weaponClass = 'Matter';//weapon class
	public $priority = 9;
	public $noOverkill = true;  
    public $rangePenalty = 0.5;
    public $animation = "bolt";
    public $animationExplosionScale = 0.3; //0 means it will be set automatically by standard constructor, based on average damage yield    

    //Adding Pulse variables
	public $maxpulses = 6; //
    public $grouping = 25;
	
    function __construct(){}
	
    public function getDamage($fireOrder) //actual function to be called, as with weapon!
    {
        return Dice::d(10, 3)+3; 
    }		
	
	function getPrice($unit) //some ammo might have different price depending on unit being fitted!
	{
		return $this->enhancementPrice;
	} 
	    
    //Adding Pulse functions
    protected function getPulses($turn)
    {
        return 1;
    }//endof function getPulses
	
    protected function getExtraPulses($needed, $rolled)
    {
        return floor(($needed - $rolled) / ($this->grouping));
    }
	
	public function rollPulses($turn, $needed, $rolled)
	{
		$pulses = $this->getPulses($turn);
		$pulses+= $this->getExtraPulses($needed, $rolled);
		$pulses=min($pulses,$this->maxpulses);
		return $pulses;
	}
			    
} //endof class AmmoMShellScatter

//Scatter Ammo for Light Railgun
class AmmoLShellScatter extends AmmoTemplateDirectWeapons{	
	public $name = 'AmmoLShellScatter';
	public $displayName = 'Light Scatter Shell'; //should never be shown ;)
	public $modeName = 'Light Scatter Shell';
	public $size = 1; //how many store slots are required for a single round
	public $enhancementName = 'SHELL_LSCT'; //enhancement name to be enabled
	public $enhancementDescription = '(LIGHT AMMO) Scatter Shell'; //enhancement description
	public $enhancementPrice = 2;
	
	public $fireControlMod = array(-2, -2, -2); //MODIFIER for weapon fire control!
	public $minDamage = 6;
	public $maxDamage = 15;	
	public $damageType = 'Pulse';//mode of dealing damage
	public $weaponClass = 'Matter';//weapon class
	public $priority = 5;
	public $noOverkill = true;  
    public $rangePenalty = 1;
    public $animation = "bolt";
    public $animationExplosionScale = 0.2; //0 means it will be set automatically by standard constructor, based on average damage yield    

    //Adding Pulse variables
	public $maxpulses = 6; //
    public $grouping = 25;
	
    function __construct(){}
	
    public function getDamage($fireOrder) //actual function to be called, as with weapon!
    {
        return Dice::d(10, 1)+5; 
    }		
	
	function getPrice($unit) //some ammo might have different price depending on unit being fitted!
	{
		return $this->enhancementPrice;
	} 
	    
    //Adding Pulse functions
    protected function getPulses($turn)
    {
        return 1;
    }//endof function getPulses
	
    protected function getExtraPulses($needed, $rolled)
    {
        return floor(($needed - $rolled) / ($this->grouping));
    }
	
	public function rollPulses($turn, $needed, $rolled)
	{
		$pulses = $this->getPulses($turn);
		$pulses+= $this->getExtraPulses($needed, $rolled);
		$pulses=min($pulses,$this->maxpulses);
		return $pulses;
	}
			    
} //endof class AmmoLShellScatter


//Heavy Ammo for Heavy Railgun
class AmmoHShellHeavy extends AmmoTemplateDirectWeapons{	
	public $name = 'AmmoHShellHeavy';
	public $displayName = 'Heavy Heavy Shell'; //should never be shown ;)
	public $modeName = 'Heavy Heavy Shell';
	public $size = 1; //how many store slots are required for a single round
	public $enhancementName = 'SHELL_HHVY'; //enhancement name to be enabled
	public $enhancementDescription = '(HEAVY AMMO) Heavy Shell'; //enhancement description
	public $enhancementPrice = 18;
	
	public $fireControlMod = array(0, 0, 0); //MODIFIER for weapon fire control!
	public $minDamage = 27;
	public $maxDamage = 72;	
	public $damageType = 'Standard';//mode of dealing damage
	public $weaponClass = 'Matter';//weapon class
	public $priority = 9;
	public $noOverkill = true;  
    public $rangePenalty = 0.33;
    public $animation = "bolt";

	
    function __construct(){}
	
    public function getDamage($fireOrder) //actual function to be called, as with weapon!
    {
        return Dice::d(10, 5)+ 22; 
    }		
	
	function getPrice($unit) //some ammo might have different price depending on unit being fitted!
	{
		return $this->enhancementPrice;
	} 
		    
} //endof class AmmoHShellHeavy

//Heavy Ammo for Medium Railgun
class AmmoMShellHeavy extends AmmoTemplateDirectWeapons{	
	public $name = 'AmmoMShellHeavy';
	public $displayName = 'Medium Heavy Shell'; //should never be shown ;)
	public $modeName = 'Medium Heavy Shell';
	public $size = 1; //how many store slots are required for a single round
	public $enhancementName = 'SHELL_MHVY'; //enhancement name to be enabled
	public $enhancementDescription = '(MEDIUM AMMO) Heavy Shell'; //enhancement description
	public $enhancementPrice = 12;
	
	public $fireControlMod = array(0, 0, 0); //MODIFIER for weapon fire control!
	public $minDamage = 16;
	public $maxDamage = 43;	
	public $damageType = 'Standard';//mode of dealing damage
	public $weaponClass = 'Matter';//weapon class
	public $priority = 9;
	public $noOverkill = true;  
    public $rangePenalty = 0.5;
    public $animation = "bolt";

	
    function __construct(){}
	
    public function getDamage($fireOrder) //actual function to be called, as with weapon!
    {
        return Dice::d(10, 3)+13; 
    }		
	
	function getPrice($unit) //some ammo might have different price depending on unit being fitted!
	{
		return $this->enhancementPrice;
	} 
		    
} //endof class AmmoMShellHeavy

//Heavy Ammo for Light Railgun
class AmmoLShellHeavy extends AmmoTemplateDirectWeapons{	
	public $name = 'AmmoLShellHeavy';
	public $displayName = 'Light Heavy Shell'; //should never be shown ;)
	public $modeName = 'Light Heavy Shell';
	public $size = 1; //how many store slots are required for a single round
	public $enhancementName = 'SHELL_LHVY'; //enhancement name to be enabled
	public $enhancementDescription = '(LIGHT AMMO) Heavy Shell'; //enhancement description
	public $enhancementPrice = 6;
	
	public $fireControlMod = array(0, 0, 0); //MODIFIER for weapon fire control!
	public $minDamage = 11;
	public $maxDamage = 20;	
	public $damageType = 'Standard';//mode of dealing damage
	public $weaponClass = 'Matter';//weapon class
	public $priority = 6;
	public $noOverkill = true;  
    public $rangePenalty = 1;
    public $animation = "bolt";

	
    function __construct(){}
	
    public function getDamage($fireOrder) //actual function to be called, as with weapon!
    {
        return Dice::d(10, 1)+10; 
    }		
	
	function getPrice($unit) //some ammo might have different price depending on unit being fitted!
	{
		return $this->enhancementPrice;
	} 
		    
} //endof class AmmoLShellHeavy

//Long Range Ammo for Heavy Railgun
class AmmoHShellLRange extends AmmoTemplateDirectWeapons{	
	public $name = 'AmmoHShellLRange';
	public $displayName = 'Heavy Long Range Shell'; //should never be shown ;)
	public $modeName = 'Heavy Long Range Shell';
	public $size = 1; //how many store slots are required for a single round
	public $enhancementName = 'SHELL_HLR'; //enhancement name to be enabled
	public $enhancementDescription = '(HEAVY AMMO) Long Range Shell'; //enhancement description
	public $enhancementPrice = 4;
	
	public $fireControlMod = array(0, 0, 0); //MODIFIER for weapon fire control!
	public $minDamage = 6;
	public $maxDamage = 33;	
	public $damageType = 'Standard';//mode of dealing damage
	public $weaponClass = 'Matter';//weapon class
	public $priority = 9;
	public $noOverkill = true;  
    public $rangePenalty = 0.25;
    public $animation = "bolt";

	
    function __construct(){}
	
    public function getDamage($fireOrder) //actual function to be called, as with weapon!
    {
        return Dice::d(10, 3)+3; 
    }		
	
	function getPrice($unit) //some ammo might have different price depending on unit being fitted!
	{
		return $this->enhancementPrice;
	} 
		    
} //endof class AmmoHShellLRange

//Long Range Ammo for Medium Railgun
class AmmoMShellLRange extends AmmoTemplateDirectWeapons{	
	public $name = 'AmmoMShellLRange';
	public $displayName = 'Medium Long Range Shell'; //should never be shown ;)
	public $modeName = 'Medium Long Range Shell';
	public $size = 1; //how many store slots are required for a single round
	public $enhancementName = 'SHELL_MLR'; //enhancement name to be enabled
	public $enhancementDescription = '(MEDIUM AMMO) Long Range Shell'; //enhancement description
	public $enhancementPrice = 2;
	
	public $fireControlMod = array(0, 0, 0); //MODIFIER for weapon fire control!
	public $minDamage = 6;
	public $maxDamage = 15;	
	public $damageType = 'Standard';//mode of dealing damage
	public $weaponClass = 'Matter';//weapon class
	public $priority = 5;
	public $noOverkill = true;  
    public $rangePenalty = 0.33;
    public $animation = "bolt";

	
    function __construct(){}
	
    public function getDamage($fireOrder) //actual function to be called, as with weapon!
    {
        return Dice::d(10, 1)+5; 
    }		
	
	function getPrice($unit) //some ammo might have different price depending on unit being fitted!
	{
		return $this->enhancementPrice;
	} 
		    
} //endof class AmmoMShellLRange

//Ultra Long Range Ammo for Heavy Railgun
class AmmoHShellULRange extends AmmoTemplateDirectWeapons{	
	public $name = 'AmmoHShellULRange';
	public $displayName = 'Heavy Ultra Long Range Shell'; //should never be shown ;)
	public $modeName = 'Heavy Ultra Long Range Shell';
	public $size = 1; //how many store slots are required for a single round
	public $enhancementName = 'SHELL_HULR'; //enhancement name to be enabled
	public $enhancementDescription = '(HEAVY AMMO) Ultra Long Range Shell'; //enhancement description
	public $enhancementPrice = 6;
	
	public $fireControlMod = array(0, 0, 0); //MODIFIER for weapon fire control!
	public $minDamage = 6;
	public $maxDamage = 15;	
	public $damageType = 'Standard';//mode of dealing damage
	public $weaponClass = 'Matter';//weapon class
	public $priority = 8;
	public $noOverkill = true;  
    public $rangePenalty = 0.2;
    public $animation = "bolt";

	
    function __construct(){}
	
    public function getDamage($fireOrder) //actual function to be called, as with weapon!
    {
        return Dice::d(10, 1)+5; 
    }		
	
	function getPrice($unit) //some ammo might have different price depending on unit being fitted!
	{
		return $this->enhancementPrice;
	} 
		    
} //endof class AmmoHShellULRange

?>
