<?php
    class Particle extends Weapon{
        public $damageType = "Standard"; 
        public $weaponClass = "Particle"; 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
        }

        public $priority = 6;

    }



    class StdParticleBeam extends Particle{ 

        public $trailColor = array(30, 170, 255);

        public $name = "stdParticleBeam";
        public $displayName = "Standard Particle Beam";
        public $animation = "beam";
        public $animationColor = array(255, 250, 230);
        public $animationExplosionScale = 0.15;
        public $projectilespeed = 15;
        public $animationWidth = 4;
        public $trailLength = 10;

        public $intercept = 2;
        public $loadingtime = 1;


        public $rangePenalty = 1;
        public $fireControl = array(4, 4, 4); // fighters, <mediums, <capitals
        public $priority = 5;

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return Dice::d(10)+6;   }
        public function setMinDamage(){     $this->minDamage = 7 ;      }
        public function setMaxDamage(){     $this->maxDamage = 16 ;      }

    }

    class QuadParticleBeam extends StdParticleBeam {
        public $name = "quadParticleBeam";
        public $displayName = "Quad Particle Beam";
        public $guns = 4;
    }

    class ParticleBlaster extends Particle{
        public $trailColor = array(30, 170, 255);

        public $name = "particleBlaster";
        public $displayName = "Particle Blaster";
        public $animation = "trail";
        public $animationColor = array(255, 250, 230);
        public $animationExplosionScale = 0.25;
        public $projectilespeed = 15;
        public $animationWidth = 5;
        public $trailLength = 10;
        public $priority = 6;

        public $loadingtime = 2;


        public $rangePenalty = 0.5;
        public $fireControl = array(0, 4, 4); // fighters, <mediums, <capitals


        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return Dice::d(10)+12;   }
        public function setMinDamage(){     $this->minDamage = 13 ;      }
        public function setMaxDamage(){     $this->maxDamage = 22 ;      }

    }
    

/*fighter-mounted variant*/
    class ParticleBlasterFtr extends Particle{
        public $trailColor = array(30, 170, 255);

        public $name = "particleBlasterFtr";
        public $displayName = "Particle Blaster";
        public $iconPath = "particleBlaster.png";
        public $animation = "trail";
        public $animationColor = array(255, 250, 230);
        public $animationExplosionScale = 0.25;
        public $projectilespeed = 12;
        public $animationWidth = 5;
        public $trailLength = 10;
        public $priority = 6;

        public $loadingtime = 3;


        public $rangePenalty = 1;
        public $fireControl = array(-4, 0, 0); // fighters, <mediums, <capitals


	function __construct($startArc, $endArc, $nrOfShots = 1){
            $this->defaultShots = $nrOfShots;
            $this->shots = $nrOfShots;
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return Dice::d(10)+12;   }
        public function setMinDamage(){     $this->minDamage = 13 ;      }
        public function setMaxDamage(){     $this->maxDamage = 22 ;      }

    }


    class AdvParticleBeam extends Particle{

        public $trailColor = array(30, 170, 255);

        public $name = "advParticleBeam";
        public $displayName = "Advanced Particle Beam";
        public $animation = "beam";
        public $animationColor = array(255, 250, 230);
        public $animationExplosionScale = 0.20;
        public $projectilespeed = 14;
        public $animationWidth = 5;
        public $trailLength = 10;
        public $iconPath = "stdParticleBeam.png";
        public $priority = 5;

        public $intercept = 2;
        public $loadingtime = 1;
        public $rangePenalty = 0.66;
        public $fireControl = array(5, 5, 5); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return Dice::d(10)+8;   }
        public function setMinDamage(){     $this->minDamage = 9 ;      }
        public function setMaxDamage(){     $this->maxDamage = 18 ;      }

    }



    class TwinArray extends Particle{
        public $trailColor = array(30, 170, 255);

        public $name = "twinArray";
        public $displayName = "Twin Array";
        public $animation = "trail";
        public $animationColor = array(30, 170, 255);
        public $animationExplosionScale = 0.15;
        public $projectilespeed = 12;
        public $animationWidth = 3;
        public $trailLength = 10;

        public $intercept = 2;

        public $loadingtime = 1;
        public $guns = 2;
        public $priority = 4;

        public $rangePenalty = 2;
        public $fireControl = array(6, 5, 4); // fighters, <mediums, <capitals


        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return Dice::d(10)+4;   }
        public function setMinDamage(){     $this->minDamage = 5 ;      }
        public function setMaxDamage(){     $this->maxDamage = 14 ;      }

    }

    class HeavyArray extends Particle{

        public $trailColor = array(30, 170, 255);

        public $name = "heavyArray";
        public $displayName = "Heavy array";
        public $animation = "trail";
        public $animationColor = array(30, 170, 255);
        public $animationExplosionScale = 0.25;
        public $projectilespeed = 20;
        public $animationWidth = 4;
        public $trailLength = 15;

        public $intercept = 2;

        public $loadingtime = 1;
        public $guns = 2;
        public $priority = 6;

        public $rangePenalty = 1;
        public $fireControl = array(2, 3, 4); // fighters, <mediums, <capitals


        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return Dice::d(10, 2)+6;   }
        public function setMinDamage(){     $this->minDamage = 8 ;      }
        public function setMaxDamage(){     $this->maxDamage = 26 ;      }

    }



    class ParticleCannon extends Raking{

        public $trailColor = array(30, 170, 255);

        public $name = "particleCannon";
        public $displayName = "Particle Cannon";
        public $animation = "beam";
        public $animationColor = array(255, 250, 230);
        public $animationExplosionScale = 0.35;
        public $projectilespeed = 15;
        public $animationWidth = 8;
        public $trailLength = 24;

        public $intercept = 1;
        public $loadingtime = 2;
        public $priority = 8;

        public $rangePenalty = 0.5;
        public $fireControl = array(2, 4, 5); // fighters, <mediums, <capitals

        public $damageType = "Raking"; 
        public $weaponClass = "Particle";
        public $firingModes = array( 1 => "Raking");
        
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
            //$this->data["Weapon type"] = "Particle";
            //$this->data["Damage type"] = "Raking";

            parent::setSystemDataWindow($turn);
        }

        public function getDamage($fireOrder){ return Dice::d(10, 2)+15;   }
        public function setMinDamage(){     $this->minDamage = 17 ;      }
        public function setMaxDamage(){     $this->maxDamage = 35 ;      }
    }



    class LightParticleCannon extends Raking{

        public $trailColor = array(30, 170, 255);

        public $name = "lightParticleCannon";
        public $displayName = "Light Particle Cannon";
        public $animation = "beam";
        public $animationColor = array(255, 250, 230);
        public $animationExplosionScale = 0.25;
        public $projectilespeed = 13;
        public $animationWidth = 5;
        public $trailLength = 24;

        public $intercept = 2;
        public $loadingtime = 2;
        public $priority = 8;

        public $rangePenalty = 1;
        public $fireControl = array(0, 2, 4); // fighters, <mediums, <capitals

        public $damageType = "Raking"; 
        public $weaponClass = "Particle";
        public $firingModes = array( 1 => "Raking");
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
           // $this->data["Weapon type"] = "Particle";
            //$this->data["Damage type"] = "Raking";

            parent::setSystemDataWindow($turn);
        }

        public function getDamage($fireOrder){ return Dice::d(10, 2)+8;   }
        public function setMinDamage(){     $this->minDamage = 10 ;      }
        public function setMaxDamage(){     $this->maxDamage = 28 ;      }
    }
    


    class HvyParticleCannon extends Raking{

        public $trailColor = array(252, 252, 252);

        public $name = "hvyParticleCannon";
        public $displayName = "Heavy Particle Cannon";
        public $animation = "laser";
        public $animationColor = array(255, 230, 100);
        public $animationColor2 = array(255, 255, 255);
        public $animationExplosionScale = 0.45;
        public $animationWidth = 5;
        public $priority = 7;

        public $loadingtime = 6;

        public $rangePenalty = 0.33;
        public $fireControl = array(0, 4, 6); // fighters, <mediums, <capitals

        public $damageType = "Raking"; 
        public $weaponClass = "Particle";
        public $firingModes = array( 1 => "Raking");
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
            //$this->data["Weapon type"] = "Particle";
            //$this->data["Damage type"] = "Raking";

            parent::setSystemDataWindow($turn);
        }

        public function getDamage($fireOrder){ return Dice::d(10, 6)+60;   }
        public function setMinDamage(){     $this->minDamage = 66 ;      }
        public function setMaxDamage(){     $this->maxDamage = 120 ;      }
    }



    class ParticleCutter extends Raking{
        public $trailColor = array(252, 252, 252);

        public $name = "particleCutter";
        public $displayName = "Particle Cutter";
        public $animation = "beam";
        public $animationColor = array(252, 252, 252);
        public $animationExplosionScale = 0.35;
        public $projectilespeed = 30;
        public $animationWidth = 5;
        public $trailLength = 1500;
        public $firingModes = array( 1 => "Sustained");
        
        public $damageType = "Raking"; 
        public $weaponClass = "Particle";
        
        // Set to make the weapon start already overloaded.
        public $alwaysoverloading = true;
        public $overloadturns = 2;
        public $extraoverloadshots = 2;
        public $overloadshots = 2;
        public $loadingtime = 2;
        public $priority = 8;

        public $rangePenalty = 0.5;
        public $fireControl = array(2, 3, 4); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
            $this->data["Special"] .= "This weapon is always in sustained mode.";

            parent::setSystemDataWindow($turn);
        }

        public function isOverloadingOnTurn($turn = null){
            return true;
        }
        
        public function getDamage($fireOrder){ return Dice::d(10, 2)+12;   }
        public function setMinDamage(){     $this->minDamage = 14 ;      }
        public function setMaxDamage(){     $this->maxDamage = 32 ;      }
    }



    class ParticleRepeater extends Particle{
        public $trailColor = array(252, 252, 252);
        public $name = "particleRepeater";
        public $displayName = "Particle Repeater";
        public $animation = "trail";
        public $animationColor = array(252, 252, 252);
        public $animationExplosionScale = 0.40;
        public $projectilespeed = 40;
        public $animationWidth = 4;
        public $trailLength = 30;
        
        public $loadingtime = 1;
        public $boostable = true;
        public $boostEfficiency = 1;
        public $priority = 5;
	public $intercept = 1;
        public $rangePenalty = 1;
        public $fireControl = array(4, 2, 2); // fighters, <mediums, <capitals
        
        private $hitChanceMod = 0;
        private $previousHit = true;
        
       
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
	    $this->data["Special"] = "Standard power: 1 shot, intercept -5.";
	    $this->data["Special"] .= "<br>Each additional +1 Power adds -5 intercept or 1 shot in offensive mode.";
	    $this->data["Special"] .= "<br>Each pair of shots above 2 forces a turn of cooldown.";
	    $this->data["Special"] .= "<br>All shots hit the same target. If a shot misses, further ones miss automatically. Otherwise they have cumulative to hit penalty.";
            //$this->defaultShots = 1+$this->getBoostLevel(TacGamedata::$currentTurn); //default shots is 1, so interception is correct!
            
            parent::setSystemDataWindow($turn);
        } 
	    
	protected function applyCooldown($gamedata){
		$currBoostlevel = $this->getBoostLevel($gamedata->turn);
		//if boosted, cooldown (1 per 2 extra shots above first 2)
		$turnToAdd = 0;
		 $cooldownLength = ceil(($currBoostlevel-2)/2);//actual numbers of turns of cooldown
		 while($cooldownLength > 0){ 		     
			$crit = new ForcedOfflineOneTurn(-1, $this->unit->id, $this->id, "ForcedOfflineOneTurn", $gamedata->turn+$turnToAdd);
			$crit->updated = true;
			$crit->newCrit = true; //force save even if crit is not for current turn
			$this->criticals[] =  $crit;
			$turnToAdd++;    
			$cooldownLength--;
		 }
	}
        
        public function fire($gamedata, $fireOrder){ 
			$currBoostlevel = $this->getBoostLevel($gamedata->turn);
				$this->hitChanceMod = 0;
				$fireOrder->shots = 1 + $currBoostlevel;
				parent::fire($gamedata, $fireOrder);
			$this->applyCooldown($gamedata);			
        }
	    
    /* applying cooldown when firing defensively, too
    */
    public function fireDefensively($gamedata, $interceptedWeapon)
    {
    	if ($this->firedDefensivelyAlready==0){ //in case of multiple interceptions during one turn - suffer backlash only once
		$this->applyCooldown($gamedata);	
	}
	parent::fireDefensively($gamedata, $interceptedWeapon);
    }
        
        /*if previous shot missed, next one misses automatically*/
        /*so if current mod is not equal to one of previous shot, then it's clearly a miss - return suitably high mod*/
        public function getShotHitChanceMod($shotInSequence){ 
            $prevExpectedChance = $this->getPrevShotHitChanceMod($shotInSequence-1);
            if($prevExpectedChance != $this->hitChanceMod){ //something missed in between
                $this->hitChanceMod = 10000; //clear miss!!!
            }else{
                $this->hitChanceMod = $this->getPrevShotHitChanceMod($shotInSequence);
            }
            return $this->hitChanceMod;
        }
        
        public function getPrevShotHitChanceMod($shotInSequence){ //just finds hit chance for a given shot - what it should be
            if($shotInSequence <=0) return 0;
            if($shotInSequence ==1) return 5;
            $mod= 5+10*($shotInSequence-2);
            return $mod;
        }                
        
        protected function getWeaponHitChanceMod($turn){
            return $this->hitChanceMod;
        }
        
        protected function doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata, $damageWasDealt, $location = null){ 
            //if target is fighter flight, ensure that the same fighter is hit every time!
            if($target instanceof FighterFlight){
                $fireOrder->linkedHit = $system;
            }            
            parent::doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata, $damageWasDealt, $location);
        }        
        
        protected function getBoostLevel($turn){
            $boostLevel = 0;
            foreach ($this->power as $i){
                    if ($i->turn != $turn)
                            continue;
                    if ($i->type == 2){
                            $boostLevel += $i->amount;
                    }
            }
            return $boostLevel;
        }
        protected function getMaxShots($turn){
            return 1 + $this->getBoostLevel($turn);
        }
        public function getInterceptRating($turn){
            return 1 + $this->getBoostLevel($turn);            
        }
        public function getDamage($fireOrder){ return Dice::d(10, 2);   }
        public function setMinDamage(){     $this->minDamage = 2 ;      }
        public function setMaxDamage(){     $this->maxDamage = 20 ;      }
    } //endof class ParticleRepeater
    
	
    class RepeaterGun extends Particle{
        public $trailColor = array(252, 252, 252);
        public $name = "repeaterGun";
        public $displayName = "Repeater Gun";
        public $animation = "trail";
        public $animationColor = array(252, 252, 252);
        public $animationExplosionScale = 0.30;
        public $projectilespeed = 20;
        public $animationWidth = 4;
        public $trailLength = 30;
        
        public $loadingtime = 1;
        public $boostable = true;
        public $boostEfficiency = 2;
        public $priority = 4;
	public $intercept = 1;
        public $rangePenalty = 0.5; //-1/2 hexes
        public $fireControl = array(2, 2, 2); // fighters, <mediums, <capitals
        
        private $hitChanceMod = 0;
        private $previousHit = true;
        
       
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
	    $this->data["Special"] = "Standard power: 1 shot, intercept -5.";
	    $this->data["Special"] .= "<br>Each additional +2 Power adds -5 intercept or 1 shot in offensive mode.";
	    $this->data["Special"] .= "<br>Each additional shot forces a turn of cooldown.";
	    $this->data["Special"] .= "<br>All shots hit the same target. If a shot misses, further ones miss automatically. Otherwise they have cumulative to hit penalty.";
            //$this->defaultShots = 1+$this->getBoostLevel(TacGamedata::$currentTurn); //default shots is 1, so interception is correct!
            
            parent::setSystemDataWindow($turn);
        } 
        
	protected function applyCooldown($gamedata){
		$currBoostlevel = $this->getBoostLevel($gamedata->turn);
		//if boosted, cooldown (1 per extra shot)
		$turnToAdd = 0;
		 $cooldownLength = $currBoostlevel;//actual numbers of turns of cooldown
		 while($cooldownLength > 0){ 		     
			$crit = new ForcedOfflineOneTurn(-1, $this->unit->id, $this->id, "ForcedOfflineOneTurn", $gamedata->turn+$turnToAdd);
			$crit->updated = true;
			$crit->newCrit = true; //force save even if crit is not for current turn
			$this->criticals[] =  $crit;
			$turnToAdd++;    
			$cooldownLength--;
		 }
	}
	    
        public function fire($gamedata, $fireOrder){ 
	    $currBoostlevel = $this->getBoostLevel($gamedata->turn);
            $this->hitChanceMod = 0;
            $fireOrder->shots = 1 + $currBoostlevel;
            parent::fire($gamedata, $fireOrder);
		
			$this->applyCooldown($gamedata);
        }
        
    /* applying cooldown when firing defensively, too
    */
    public function fireDefensively($gamedata, $interceptedWeapon)
    {
    	if ($this->firedDefensivelyAlready==0){ //in case of multiple interceptions during one turn - suffer backlash only once
			$this->applyCooldown($gamedata);	
		}
	parent::fireDefensively($gamedata, $interceptedWeapon);
    }
	    
        /*if previous shot missed, next one misses automatically*/
        /*so if current mod is not equal to one of previous shot, then it's clearly a miss - return suitably high mod*/
        public function getShotHitChanceMod($shotInSequence){ 
            $prevExpectedChance = $this->getPrevShotHitChanceMod($shotInSequence-1);
            if($prevExpectedChance != $this->hitChanceMod){ //something missed in between
                $this->hitChanceMod = 10000; //clear miss!!!
            }else{
                $this->hitChanceMod = $this->getPrevShotHitChanceMod($shotInSequence);
            }
            return $this->hitChanceMod;
        }
        
        public function getPrevShotHitChanceMod($shotInSequence){ //just finds hit chance for a given shot - what it should be
            if($shotInSequence <=0) return 0;
            if($shotInSequence ==1) return 5;
            $mod= 5+10*($shotInSequence-2);
            return $mod;
        }                
        
        protected function getWeaponHitChanceMod($turn){
            return $this->hitChanceMod;
        }
        
        protected function doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata, $damageWasDealt, $location = null){ 
            //if target is fighter flight, ensure that the same fighter is hit every time!
            if($target instanceof FighterFlight){
                $fireOrder->linkedHit = $system;
            }            
            parent::doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata, $damageWasDealt, $location);
        }        
        
        protected function getBoostLevel($turn){
            $boostLevel = 0;
            foreach ($this->power as $i){
                    if ($i->turn != $turn)
                            continue;
                    if ($i->type == 2){
                            $boostLevel += $i->amount;
                    }
            }
            return $boostLevel;
        }
        protected function getMaxShots($turn){
            return 1 + $this->getBoostLevel($turn);
        }
        public function getInterceptRating($turn){
            return 1 + $this->getBoostLevel($turn);            
        }
        public function getDamage($fireOrder){ return Dice::d(10, 1)+3;   }
        public function setMinDamage(){     $this->minDamage = 4 ;      }
        public function setMaxDamage(){     $this->maxDamage = 13 ;      }
    } //endof class RepeaterGun
    /*old Repeater Gun
    class RepeaterGun extends ParticleRepeater{
        public $name = "repeaterGun";
        public $displayName = "Repeater Gun";
        public $animation = "trail";
        public $animationExplosionScale = 0.30;
        public $projectilespeed = 30;
        public $animationWidth = 4;
        public $trailLength = 25;
        
        public $boostEfficiency = 2;
        public $rangePenalty = 0.5; //-1/2 hexes
        public $fireControl = array(2, 2, 2); // fighters, <mediums, <capitals
        public $priority = 4;
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        //appropriate redefinitions mostly done in ParticleRepeater class!
        
        public function getDamage($fireOrder){ return Dice::d(10)+3;   }
        public function setMinDamage(){     $this->minDamage = 4 ;      }
        public function setMaxDamage(){     $this->maxDamage = 13 ;      }
    }
*/




    class PairedParticleGun extends LinkedWeapon{

        public $trailColor = array(30, 170, 255);

        public $name = "pairedParticleGun";
        public $displayName = "Paired Particle guns";
        public $animation = "trail";
        public $animationColor = array(30, 170, 255);
        public $animationExplosionScale = 0.10;
        public $projectilespeed = 12;
        public $animationWidth = 2;
        public $trailLength = 10;
        public $intercept;


        public $loadingtime = 1;
        public $shots = 2;
        public $defaultShots = 2;
	public $priority = 2;

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
		

            if ($damagebonus > 2) $this->priority++;            
            if ($damagebonus > 4) $this->priority++;                      
            if ($damagebonus > 6) $this->priority++;

            if($nrOfShots === 1){
                $this->iconPath = "particleGun.png";
            }

            if($nrOfShots === 3){
                $this->iconPath = "pairedParticleGun3.png";
            }

            parent::__construct(0, 1, 0, $startArc, $endArc);

        }

        public function setSystemDataWindow($turn){

            //$this->data["Weapon type"] = "Particle";
            //$this->data["Damage type"] = "Standard";

            parent::setSystemDataWindow($turn);
        }

        public function getDamage($fireOrder){        return Dice::d(6)+$this->damagebonus;   }
        public function setMinDamage(){     $this->minDamage = 1+$this->damagebonus ;      }
        public function setMaxDamage(){     $this->maxDamage = 6+$this->damagebonus ;      }

    }



    class SolarCannon extends Particle{
        public $name = "solarCannon";
        public $displayName = "Solar Cannon";
        public $animation = "beam";
        public $animationColor = array(0, 250, 0);
        public $animationExplosionScale = 0.45;
        public $projectilespeed = 15;
        public $animationWidth = 8;
        public $trailLength = 14;
        public $priority = 6;

        public $loadingtime = 3;

        public $rangePenalty = 0.5;
        public $fireControl = array(0, 3, 5); // fighters, <mediums, <capitals

        public $damageType = "Standard"; 
        public $weaponClass = "Particle"; 
        public $noOverkill = true; // The damage of a solar cannon does not overkill.
        public $firingModes = array( 1 => "Special"); 
        
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        
        protected function doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata, $damageWasDealt, $location = null){
            /*repeat damage on structure (ignoring armor); 
              system hit will have its armor reduced by 2
              for non-fighter targets
              */

            parent::doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata, $damageWasDealt, $location);
            if(!$target instanceof FighterFlight){
                $damageWasDealt=true; //if structure is already destroyed, no further overkill will happen
                $struct = $target->getStructureSystem($system->location);
                //reduce damage by armor of system hit - as it would be (was!) during actual damage-dealing procedure
                $damage = $damage - $this->getSystemArmourStandard($target, $system, $gamedata, $fireOrder) - $this->getSystemArmourInvulnerable($target, $system, $gamedata, $fireOrder);
                //reduce armor of system hit
                $crit = new ArmorReduced(-1, $target->id, $system->id, "ArmorReduced", $gamedata->turn);
                $crit->updated = true;
                $crit->inEffect = false;
                if ( $system != null ){
                    $system->criticals[] = $crit;
                    $system->criticals[] = $crit;
                }
                //repeat damage on structure this system is mounted to; instead of ignoring armor, damage is increased by armor of struture
                //increase damage by armor of structure - to simulate armor-ignoring effect
                $damage = $damage + $this->getSystemArmourStandard($target, $struct, $gamedata, $fireOrder) + $this->getSystemArmourInvulnerable($target, $struct, $gamedata, $fireOrder);
                parent::doDamage($target, $shooter, $struct, $damage, $fireOrder, $pos, $gamedata, $damageWasDealt, $location); 
            }
        } //endof function doDamage
        
        
        
        public function getDamage($fireOrder){        return Dice::d(5)+12;   }
        public function setMinDamage(){     $this->minDamage = 13 ;      }
        public function setMaxDamage(){     $this->maxDamage = 17 ;      }

    }



    class LightParticleBlaster extends LinkedWeapon{

        public $trailColor = array(30, 170, 255);

        public $name = "lightParticleBlaster";
        public $displayName = "Light Particle Blaster";
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
	public $priority = 2;

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

            if ($damagebonus > 4) $this->priority++;            
            if ($damagebonus > 6) $this->priority++;                      
            if ($damagebonus > 7) $this->priority++;

            if($nrOfShots === 3){
                $this->iconPath = "pairedParticleGun3.png";
            }

            parent::__construct(0, 1, 0, $startArc, $endArc);

        }

        public function setSystemDataWindow($turn){

            //$this->data["Weapon type"] = "Particle";
            //$this->data["Damage type"] = "Standard";

            parent::setSystemDataWindow($turn);
        }

        public function getDamage($fireOrder){        return Dice::d(3)+$this->damagebonus;   }
        public function setMinDamage(){     $this->minDamage = 1+$this->damagebonus ;      }
        public function setMaxDamage(){     $this->maxDamage = 3+$this->damagebonus ;      }

    }



    class LightParticleBeam extends LinkedWeapon{

        public $trailColor = array(30, 170, 255);

        public $name = "lightParticleBeam";
        public $displayName = "Light Particle Beam";
        public $animation = "trail";
        public $animationColor = array(30, 170, 255);
        public $animationExplosionScale = 0.10;
        public $projectilespeed = 12;
        public $animationWidth = 2;
        public $trailLength = 10;
        public $priority = 4;

        public $intercept = 2;

        public $loadingtime = 1;
        public $shots = 2;
        public $defaultShots = 2;

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

            parent::__construct(0, 1, 0, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){

            //$this->data["Weapon type"] = "Particle";
            //$this->data["Damage type"] = "Standard";

            parent::setSystemDataWindow($turn);
        }

        public function getDamage($fireOrder){        return Dice::d(6)+$this->damagebonus;   }
        public function setMinDamage(){     $this->minDamage = 1+$this->damagebonus ;      }
        public function setMaxDamage(){     $this->maxDamage = 6+$this->damagebonus ;      }

    }



    class HeavyBolter extends Particle{
        public $name = "heavyBolter";
        public $displayName = "Heavy Bolter";
        public $animation = "trail";
        public $animationColor = array(255, 250, 230);
        public $animationExplosionScale = 0.5;
        public $projectilespeed = 12;
        public $animationWidth = 6;
        public $trailLength = 6;
        public $priority = 6;

        public $loadingtime = 3;


        public $rangePenalty = 0.33;
        public $fireControl = array(-1, 2, 3); // fighters, <mediums, <capitals


        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return 24;   }
        public function setMinDamage(){     $this->minDamage = 24 ;      }
        public function setMaxDamage(){     $this->maxDamage = 24 ;      }
    }
    


    class MediumBolter extends Particle{
        public $name = "mediumBolter";
        public $displayName = "Medium Bolter";
        public $animation = "trail";
        public $animationColor = array(255, 250, 230);
        public $animationExplosionScale = 0.4;
        public $projectilespeed = 14;
        public $animationWidth = 4;
        public $trailLength = 4;
        public $priority = 6;

        public $loadingtime = 2;

        public $intercept = 1;

        public $rangePenalty = 0.5;
        public $fireControl = array(1, 2, 3); // fighters, <mediums, <capitals


        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return 18;   }
        public function setMinDamage(){     $this->minDamage = 18 ;      }
        public function setMaxDamage(){     $this->maxDamage = 18 ;      }
    }
    
    class LightBolter extends Particle{

        public $name = "lightBolter";
        public $displayName = "Light Bolter";
        public $animation = "trail";
        public $animationColor = array(255, 250, 230);
        public $animationExplosionScale = 0.3;
        public $projectilespeed = 16;
        public $animationWidth = 3;
        public $trailLength = 3;
        public $priority = 5;

        public $loadingtime = 1;

        public $intercept = 1;

        public $rangePenalty = 1;
        public $fireControl = array(3, 2, 2); // fighters, <mediums, <capitals


        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return 12;   }
        public function setMinDamage(){     $this->minDamage = 12 ;      }
        public function setMaxDamage(){     $this->maxDamage = 12 ;      }
    }
    

    class LightParticleBeamShip extends StdParticleBeam{

        public $trailColor = array(30, 170, 255);

        public $name = "lightParticleBeamShip";
        public $displayName = "Light Particle Beam";
        public $animation = "beam";
        public $animationColor = array(255, 250, 230);
        public $animationExplosionScale = 0.12;
        public $projectilespeed = 12;
        public $animationWidth = 3;
        public $trailLength = 8;

        public $intercept = 2;
        public $loadingtime = 1;
        public $priority = 4;

        public $rangePenalty = 2;
        public $fireControl = array(3, 3, 3); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return Dice::d(10)+4;   }
        public function setMinDamage(){     $this->minDamage = 5 ;      }
        public function setMaxDamage(){     $this->maxDamage = 14 ;      }
    }


/*AoG did only Particle Projector. Nexus custom ships use other weights of this line as well.*/
    class ParticleProjector extends Particle{
        public $trailColor = array(30, 170, 255);

        public $name = "particleProjector";
        public $displayName = "Particle Projector";
        public $animation = "beam";
        public $animationColor = array(205, 200, 200);
        public $animationExplosionScale = 0.25;
        public $projectilespeed = 15;
        public $animationWidth = 4;
        public $trailLength = 20;

        public $intercept = 2;
        public $loadingtime = 2;
        public $priority = 4;

        public $rangePenalty = 1;
        public $fireControl = array(1, 2, 2); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){ return Dice::d(10, 1)+4;   }
        public function setMinDamage(){     $this->minDamage = 5 ;      }
        public function setMaxDamage(){     $this->maxDamage = 14 ;      }
    }



    class BAInterceptorMkI extends Particle{
        /*Belt Alliance version of Mk I Interceptor - identical to EA one, but without EWeb*/
        public $trailColor = array(30, 170, 255);
        public $name = "BAInterceptorMkI";
        public $displayName = "BA Interceptor I";
        
        public $animation = "trail";
        public $iconPath = "interceptor.png";
        public $animationColor = array(30, 170, 255);
        public $animationExplosionScale = 0.15;
        public $priority = 4;
        public $animationWidth = 1;
            
        public $intercept = 3;
             
        public $loadingtime = 1;
  
        public $rangePenalty = 2;
        public $fireControl = array(6, null, null); // fighters, <mediums, <capitals 
        
        public $damageType = "Standard"; 
        public $weaponClass = "Particle";
        
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
        
        public function getDamage($fireOrder){        return Dice::d(10)+5;   }
        public function setMinDamage(){     $this->minDamage = 6 ;      }
        public function setMaxDamage(){     $this->maxDamage = 15 ;      }
        
    }



    class QuadArray extends Particle{
        /*Abbai weapon - Twin Array on steroinds and with overheating problems*/
        public $name = "quadArray";
        public $displayName = "Quad Array";
        public $iconPath = "quadParticleBeam.png";//"quadArray.png";
        public $animation = "trail";
        public $animationColor = array(30, 170, 255);
        public $animationExplosionScale = 0.15;
        public $trailColor = array(30, 170, 255);
        public $projectilespeed = 12;
        public $animationWidth = 3;
        public $trailLength = 10;

        public $intercept = 2;

        public $loadingtime = 1;
        public $guns = 4;
        public $priority = 4;
        public $rangePenalty = 2;
        public $fireControl = array(6, 5, 4); // fighters, <mediums, <capitals

        public $firingModes = array(1=>'Quad', 2=>'Triple', 3=>'Dual');
        public $gunsArray = array(1=>4,2=>3,3=>2);
	    
	public $firedThisTurn = false; //to avoid re-rolling criticals!

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
        
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            //$this->output = $this->baseOutput + $this->getBoostLevel($turn); //handled in front end
            $this->data["Special"] = 'If fired offensively at full power, can overheat and shut down.<br>Can be fired at reduced power to avoid this:';    
            $this->data["Special"] .= "<br>Quad shot: 50% chance to shut down for a turn"; 
            $this->data["Special"] .= "<br>Triple shot: 25% chance to shut down for a turn";  
            $this->data["Special"] .= "<br>Dual shot: always safe"; 
        }

        public function fire($gamedata, $fireOrder){
            // If fired, Quad Array might overheat and go in shutdown for 1 turn.
            // Make a crit roll taking into account used firing mode
            parent::fire($gamedata, $fireOrder);
            
	    if ($this->firedThisTurn) return; //crit already accounted for (if necessary)
	    $this->firedThisTurn = true; //to avoid rolling crit for every shot!
		
            $chance = 0;
            if ($this->firingMode==1){//quad
                $chance = 2; //50%
            }else if ($this->firingMode==2){//triple
                $chance = 1; //25%
            }
		
            $roll = Dice::d(4);            
            if($roll <= $chance){ // It has overheated.
                $crit = new ForcedOfflineOneTurn(-1, $fireOrder->shooterid, $this->id, "ForcedOfflineOneTurn", $gamedata->turn);
                $crit->updated = true;
                $this->criticals[] =  $crit;
            }
		
        }
        
        public function getDamage($fireOrder){        return Dice::d(10)+4;   }
        public function setMinDamage(){     $this->minDamage = 5 ;      }
        public function setMaxDamage(){     $this->maxDamage = 14 ;      }

    } //endof class QuadArray




?>

