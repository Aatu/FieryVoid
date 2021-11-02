<?php

	//common  functionality of Antimatter weapons
	class AntimatterWeapon extends Weapon{   
        public $animation = "beam";     
		public $animationColor = array(0, 184, 230);
        public $projectilespeed = 10;
        public $animationWidth = 1;
        public $animationExplosionScale = 0.20;
        public $trailLength = 5;
		
		public $doubleRangeIfNoLock = true; //if no lock-on is achieved - double the range instead of range penalty
		public $specialRangeCalculation = true; //to inform front end that it should use weapon-specific range penalty calculation - such a method should be present in .js!
		
		public $damageType = "Standard";
    	public $weaponClass = "Antimatter"; 
		public $firingMode = "Standard";
		
		public $rngNoPenalty = 1; //maximum range at which weapon suffers no penalty
		public $rngNoPenaltyArray = array();
		public $rngNormalPenalty = 2;//maximum range at which weapon suffers regular penalty
		public $rngNormalPenaltyArray = array();
		public $maxX = 10; //maximum value of X
		public $maxXArray = array(); //For Antimatter shredder	
		public $dmgEquation = '2X+5'; //to be able to automatically incorporate this into weapon description
		public $dmgEquationArray = array(); //For AntimatterShredder	
		//effect: 
		// - for range up to $rngNoPenalty weapon suffers no penalty
		// - for ranges higher than $rngNoPenalty up to $rngNormalPenalty weapon suffers regular range penalty
		// - for ranges above $rngNormalPenalty weapon suffers double range penalty
		
		//Antimatter weapons suffer distinct versions of criticals
		public $possibleCriticals = array(14 => "ReducedRangeAntimatter", 19 => "ReducedDamageAntimatter", 25 => array("ReducedRangeAntimatter", "ReducedDamageAntimatter"));
		
		
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
			$this->data["Range brackets"] = 'No penalty up to ' . $this->rngNoPenalty . ' / Normal penalty up to ' . $this->rngNormalPenalty . ' / Double therafter ' ;
			$this->data["X-dependent damage"] = $this->dmgEquation . ' ( max X = ' . $this->maxX . ')';
            $this->data["Special"] = "Damage is dependent on how good a hit is - it's not randomized. Quality of hit is called X, and equals difference between actual and needed to-hit roll divided by 5.";
			//$this->data["Special"] .= "<br>This weapon does " . $this->dmgEquation .' damage, with maximum X being ' . $this->maxX . '.';
			//$this->data["Special"] .= '<br><br>This weapon suffers no range penalty up to ' . $this->rngNoPenalty . ' hexes, regular penalty up to ' . $this->rngNormalPenalty . ' hexes, and double penalty for remaining distance.';
			$this->data["Special"] .= "<br>In case of no lock-on the range itself is doubled, not calculated penalty.";
        }
		
		public function getX($fireOrder){
			$X = floor(($fireOrder->needed - $fireOrder->rolled)/5);
			$X = min($this->maxX, $X);
			//Damage Reduced: reduces X by 2 (no less than 0)			
			$X -= 2*$this->hasCritical('ReducedDamageAntimatter');//account for range reduced critical(s)
			$X = max(0, $X);		
			return $X;
		}
		
		public function calculateRangePenalty($distance){
			$distanceEffective = $distance + 3*$this->hasCritical('ReducedRangeAntimatter');//account for range reduced critical(s) - increase effective range by 3
			$rangePenalty = 0;//base penalty	
			$rangePenalty += $this->rangePenalty * max(0,$distanceEffective-$this->rngNoPenalty); //regular range penalty
			$rangePenalty += $this->rangePenalty * max(0,$distanceEffective-$this->rngNormalPenalty); //regular range penalty again (for effective double penalty)
			return $rangePenalty;
		}		
		
		//changing of Antimatter-specific attributes
		public function changeFiringMode($newMode)
		{ //change parameters with mode change
			parent::changeFiringMode($newMode);
			$i = $this->firingMode;
			
			if (isset($this->rngNoPenaltyArray[$i])) $this->rngNoPenalty = $this->rngNoPenaltyArray[$i];
			if (isset($this->rngNormalPenaltyArray[$i])) $this->rngNormalPenalty = $this->rngNormalPenaltyArray[$i];
			if (isset($this->dmgEquationArray[$i])) $this->dmgEquation = $this->dmgEquationArray[$i];
			if (isset($this->maxXArray[$i])) $this->maxX = $this->maxXArray[$i];
		}//endof function changeFiringMode
		
	} //endof class AntimatterWeapon

/*converting to AntimatterWeapon class!
    class AntimatterConverter extends Weapon{ //deliberately NOT extending AntimatterWeapon class, AMConverter mostly uses regular calculations        
        public $name = "antimatterConverter";
        public $displayName = "Antimatter Converter";
        public $animation = "beam";
		public $animationColor = array(0, 184, 230); //let's make it the same as Antimatter weapons!
        //public $animationColor = array(175, 225, 175);
        public $projectilespeed = 10;
        public $animationWidth = 4;
        public $animationExplosionScale = 0.90;
        public $trailLength = 20;
        public $priority = 2; //fire early due to potential Flash damage
        public $loadingtime = 3;
        public $rangePenalty = 1;
        public $fireControl = array(-6, 4, 4); // fighters, <=mediums, <=capitals 


	    public $damageType = 'Flash'; 
    	public $weaponClass = "Antimatter"; 
	    
	public $firingModes = array( 
		1 => "Flash"
	);	    
        
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            $this->data["Special"] = "Damage is dependent on how good a hit is - it's not randomized (actual damage done is 4X+2).<br>There is no actual maximum, with exceptional hit chance damage may be exceptional as well.";
        }
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
       	public function getDamage($fireOrder){
                return $damage = 2 + 4*floor(($fireOrder->needed - $fireOrder->rolled)/5);
            }

        public function setMinDamage(){     $this->minDamage = 2;      }
        public function setMaxDamage(){     $this->maxDamage = 82;      }
    }
*/

    class AntimatterConverter extends AntimatterWeapon{ 
        public $name = "antimatterConverter";
        public $displayName = "Antimatter Converter";
        public $animation = "beam";
        public $projectilespeed = 10;
        public $animationWidth = 4;
        public $animationExplosionScale = 0.90;
        public $trailLength = 20;
		
        public $priority = 2; //fire early due to potential Flash damage
        public $loadingtime = 3;
        public $fireControl = array(-6, 4, 4); // fighters, <=mediums, <=capitals 
		
        public $rangePenalty = 1; //-1/hex
		public $rngNoPenalty = 0; //maximum range at which weapon suffers no penalty
		public $rngNormalPenalty = 999;//maximum range at which weapon suffers regular penalty
		//Antimatter Converter uses regular range penalty at all ranges - which is translated to AntimatterWeapon rules as no penalty up to 0 hexes and double penalty after 999 hexes (eg. at infinity)
		
		public $maxX = 999; //maximum value of X - UNLIMITED for this weapon
		public $dmgEquation = '4X+2'; //to be able to automatically incorporate this into weapon description
		

	    public $damageType = 'Flash'; 
    	public $weaponClass = "Antimatter"; 
    	public $firingMode = "Flash"; 	    
		public $firingModes = array( 
			1 => "Flash"
		);	    
        
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
       	public function getDamage($fireOrder){
			$X = $this->getX($fireOrder);
			$damage = 4*$X + 2;
			return $damage ;
		}

        public function setMinDamage(){     $this->minDamage = 2;      }
        public function setMaxDamage(){     $this->maxDamage = 82;      } //actually this isn't maximum, but game needs _something_ to use in calculations - and this is good estimation of reasonable maximum (for X = 20)
    } //endof class AntimatterConverter


	class AntiprotonGun extends AntimatterWeapon{        
        public $name = "AntiprotonGun";
        public $displayName = "Antiproton Gun";
		public $iconPath = "AntiprotonGun.png";
        public $priority = 6; //X+12 easily qualifies for heavy Standard weapon

        public $intercept = 2;
        public $loadingtime = 1;
		
		public $rangePenalty = 1; //-1/hex base penalty

		public $rngNoPenalty = 5; //maximum range at which weapon suffers no penalty
		public $rngNormalPenalty = 10;//maximum range at which weapon suffers regular penalty
		public $maxX = 10; //maximum value of X
		public $dmgEquation = 'X+12'; //to be able to automatically incorporate this into weapon description

        public $fireControl = array(2, 3, 3); // fighters, <mediums, <capitals 
		
	
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
			if ( $maxhealth == 0 ) $maxhealth = 8;
            if ( $powerReq == 0 ) $powerReq = 4;            
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
       	public function getDamage($fireOrder){
                $X = $this->getX($fireOrder);
				$damage = $X + 12;
				return $damage ;
            }

        public function setMinDamage(){     $this->minDamage = 12;      }
        public function setMaxDamage(){     $this->maxDamage = 22;      }
	
	} //end of AntiprotonGun

	
	class AntimatterCannon extends AntimatterWeapon{        
        public $name = "AntimatterCannon";
        public $displayName = "Antimatter Cannon";
		public $iconPath = "AntimatterCannon.png";
        public $animation = "laser";
        //public $animationColor = array(0, 184, 230); //let's inherit from Antimatter...
        public $animationWidth = 4;
        public $animationWidth2 = 0.2;
		
        public $priority = 7; //that's heavy Raking hit!
		public $priorityArray = array(1=>7, 2=>2); //heavy Raking in primary mode, Piercing in alternate mode
        public $raking = 10;
        public $loadingtime = 3;
		public $rangePenalty = 1; //-1/hex base penalty
        public $intercept = 1;
        
        public $firingModes = array(
            1 => "Raking",
            2 => "Piercing"
        );
        
        public $damageTypeArray = array(1=>'Raking', 2=>'Piercing');

		public $rngNoPenalty = 10; //maximum range at which weapon suffers no penalty
		public $rngNormalPenalty = 20;//maximum range at which weapon suffers regular penalty
		public $maxX = 20; //maximum value of X
		public $dmgEquation = '2X+16'; //to be able to automatically incorporate this into weapon description

        public $fireControlArray = array( 1=>array(-2, 3, 5), 2=>array(null,-1, 1) ); // fighters, <mediums, <capitals 
		
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
			if ( $maxhealth == 0 ) $maxhealth = 9;
            if ( $powerReq == 0 ) $powerReq = 8;            
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
       	public function getDamage($fireOrder){
                $X = $this->getX($fireOrder);
				$damage = (2* $X) + 16;
				return $damage ;
            }

        public function setMinDamage(){     $this->minDamage = 16;      }
        public function setMaxDamage(){     $this->maxDamage = 56;      }
	
	} //end of class AntimatterCannon
	
	
	class AntiprotonDefender extends AntimatterWeapon{        
        public $name = "AntiprotonDefender";
        public $displayName = "Antiproton Defender";
		public $iconPath = "AntiprotonDefender.png";
        public $priority = 5; //that's Standard Heavy hit!

        public $intercept = 3;
        public $loadingtime = 1;
		
		public $rangePenalty = 1; //-1/hex base penalty

		public $rngNoPenalty = 3; //maximum range at which weapon suffers no penalty
		public $rngNormalPenalty = 6;//maximum range at which weapon suffers regular penalty
		public $maxX = 10; //maximum value of X
		public $dmgEquation = 'X+8'; //to be able to automatically incorporate this into weapon description

        public $fireControl = array(4, 2, 2); // fighters, <mediums, <capitals 
		
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
			if ( $maxhealth == 0 ) $maxhealth = 4;
            if ( $powerReq == 0 ) $powerReq = 3;            
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
       	public function getDamage($fireOrder){
                $X = $this->getX($fireOrder);
				$damage = $X + 8;
				return $damage ;
            }

        public function setMinDamage(){     $this->minDamage = 8;      }
        public function setMaxDamage(){     $this->maxDamage = 18;      }
	
	} //end of class AntiprotonDefender


	class AntimatterTorpedo extends AntimatterWeapon{        
        public $name = "AntimatterTorpedo";
        public $displayName = "Antimatter Torpedo";
		public $iconPath = "AntimatterTorpedo.png";
        public $trailColor = array(0, 184, 230);
        public $animation = "torpedo";
        public $animationColor = array(30, 170, 255);
        public $animationExplosionScale = 0.25;
        public $projectilespeed = 12;
        public $animationWidth = 10;
        public $trailLength = 10;
        public $priority = 5;
        
        public $ballistic = true;
        public $weaponClass = "Ballistic";         

        public $loadingtime = 2;
		
		public $rangePenalty = 1; //-1/hex base penalty

		public $rngNoPenalty = 25; //maximum range at which weapon suffers no penalty
		public $rngNormalPenalty = 50;//maximum range at which weapon suffers regular penalty
		public $maxX = 12; //maximum value of X
		public $dmgEquation = 'X+8'; //to be able to automatically incorporate this into weapon description

        public $fireControl = array(-2, 2, 4); // fighters, <mediums, <capitals 
		
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
			if ( $maxhealth == 0 ) $maxhealth = 6;
            if ( $powerReq == 0 ) $powerReq = 7;            
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
       	public function getDamage($fireOrder){
                $X = $this->getX($fireOrder);
				$damage = $X + 8;
				return $damage ;
            }

        public function setMinDamage(){     $this->minDamage = 8;      }
        public function setMaxDamage(){     $this->maxDamage = 20;      }
	
	} //end of class AntimatterTorpedo

	class LightAntiprotonGun extends LinkedWeapon{  //deliberately NOT extending AntimatterWeapon class, uses regular calculations 
		public $name = "LightAntiprotonGun";
		public $displayName = "Light Antiproton Gun";
		public $animation = "trail";
		public $animationColor = array(0, 184, 230);
		public $animationExplosionScale = 0.10;
		public $projectilespeed = 12;
		public $animationWidth = 2;
		public $trailLength = 10;

		public $priority = 4;

		public $loadingtime = 1;
		public $shots = 2;
		public $defaultShots = 2;

		public $rangePenalty = 2;
		public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals
		public $rangeDamagePenalty = 1;

		public $damageType = "Standard"; 
		public $weaponClass = "Antimatter"; 

		function __construct($startArc, $endArc, $nrOfShots = 2){ 
			$this->shots = $nrOfShots;
			$this->defaultShots = $nrOfShots;        
		
			if($nrOfShots === 1){
				$this->iconPath = "LightAntiprotonGun.png";
			}
			if($nrOfShots === 2){
				$this->iconPath = "LightAntiprotonGun2.png";
			}
		
			parent::__construct(0, 1, 0, $startArc, $endArc);
		}

		public function getDamage($fireOrder){        return Dice::d(6, 2) - 1;   }
		public function setMinDamage(){     $this->minDamage = 1 ;      }
		public function setMaxDamage(){     $this->maxDamage = 11 ;      }

	}// end of class LightAntiprotonGun


	class LtAntimatterCannon extends Weapon{  //deliberately NOT extending AntimatterWeapon class uses regular calculations 
		public $iconPath = "LightAntimatterCannon.png";
        public $name = "LtAntimatterCannon";
        public $displayName = "Light Antimatter Cannon";
        public $animation = "trail";
        public $animationColor = array(0, 184, 230);
        public $trailColor = array(0, 184, 230);
        public $projectilespeed = 11;
        public $animationWidth = 4;
        public $trailLength = 12;
        public $animationExplosionScale = 0.25;
                
        public $loadingtime = 2;
		public $priority = 6;
		public $shots = 1;		
            
        public $rangePenalty = 2;
        public $fireControl = array(0, 0, 0); // fighters, <=mediums, <=capitals 

		public $damageType = "Standard"; 
		public $weaponClass = "Antimatter"; 

        function __construct($startArc, $endArc, $shots = 1){
            $this->shots = $shots;
            $this->defaultShots = $shots;
            
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }   

        
        public function getDamage($fireOrder){        return (Dice::d(10, 1) + 4) * 2;   }
        public function setMinDamage(){     $this->minDamage = 10 ;      }
        public function setMaxDamage(){     $this->maxDamage = 28 ;      }

	}//end of class LtAntimatterCannon





	class AntimatterShredder extends AntimatterWeapon{        
        public $name = "AntimatterShredder";
        public $displayName = "Antimatter Shredder";
		public $iconPath = "AntimatterShredder.png";
        public $animation = "trail";
        public $animationArray = array(1=>"trail", 2=>"laser", 3=>"laser");
        public $projectilespeed = 13;
        public $animationWidth = 3;
        public $trailLength = 9;
        //public $animationColor = array(0, 184, 230); //let's inherit from Antimatter...
        public $animationWidth2 = 0.2;
		public $animationExplosionScale = 0.4;                   
		
        public $priority = 2; 
		public $priorityArray = array(1=>2, 2=>7, 3=>2); //Shredder affects every unit in range, while Piercing affects all sections in its path - both should be fired very early
        public $raking = 10;
        public $loadingtime = 3;
		public $rangePenalty = 1; //-1/hex base penalty
        public $intercept = 1;        
		
		public $firingMode = "Shredder";
        public $firingModes = array(
            1 => "Shredder",
			2 => "Raking",
            3 => "Piercing"			
        );
		
		
		//Range Reduced on Shredder would be quite awkward OR require additional custom coding - I went for givit it only ReducedDamage critical instead
		public $possibleCriticals = array(14 => "ReducedDamageAntimatter", 25 => array("ReducedDamageAntimatter", "ReducedDamageAntimatter"));
		
       
        public $damageTypeArray = array(1=> 'Standard', 2=>'Raking', 3=>'Piercing');
		
		public $rngNoPenalty = 11; //maximum range at which weapon suffers no penalty
		public $rngNoPenaltyArray = array(1=>11,2=>10,3=>10); //Shredder can be targeted up to 10 hexes away, but can actually reach a hex further away
		public $rngNormalPenalty = 11;//maximum range at which weapon suffers regular penalty
		public $rngNormalPenaltyArray = array(1=>11,2=>20,3=>20);
		public $maxX = 10; //maximum value of X
		public $maxXArray = array(1=>10, 2=>20, 3=>20); //maximum value of X
		public $dmgEquation = '2X+6'; //to be able to automatically incorporate this into weapon description
		public $dmgEquationArray = array(1=>'2X+6', 2=>'2X+16', 3=>'2X+16'); //to be able to automatically incorporate this into weapon description


        public $ignoreAllEW = true; //Shredder completely ignores EW (and lock-ons)
		public $ignoreAllEWArray = array(1=>true, 2=>false, 3=>false); 
        public $ignoreJinking = true; //Shredder completely ignores jinking when calculating hit chance 
		public $ignoreJinkingArray = array(1=>true, 2=>false, 3=>false); 
		
        public $fireControlArray = array( 1=>array(0,0,0), 2=>array(-2, 3, 5), 3=>array(null,-1, 1) ); // fighters, <mediums, <capitals 
		
		
		public $range = 10;
        public $rangeArray = array(1=>10, 2=>0, 3=>0); //range is unlimited for AMCannon, but limited for Shredder
		public $hextarget = true;
		public $hextargetArray = array(1=>true, 2=>false, 3=>false);
		
		
		public $calledShotMod = 0; //Shredder shots vs fighters are technically called shots, but shouldn't be penalized as such; other modes don't allow called shots
		
		private static $alreadyEngaged = array(); //units that were already engaged by Shredder this turn (multiple Shredders do not stack)
		
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
			if (!isset($this->data["Special"])) {
				$this->data["Special"] = '';
			}else{
				$this->data["Special"] .= '<br>';
			}
			$this->data["Special"] .= 'Shredder mode is aimed at hex, and affects all units within 1 hex of target point (not beyond 10 hexes though, and will never hit firing unit itself).';
			$this->data["Special"] .= '<br>Shredder will roll to hit separately for each attack. Attack will suffer no range penalty of any kind, will ignore any EW and jinking.';
			$this->data["Special"] .= '<br>Each fighter will suffer 1 attack, LCVs/MCVs/HCVs d3 attacks, Capital ships d6 and Enormous units d6+3.';
			$this->data["Special"] .= '<br>Multiple Shredders are NOT cumulative. Shredder fire is not interceptable.'; //uninterceptability is due to technical reasons - with no fire order ID, interception will not be applied properly
			$this->data["Special"] .= '<br>Remaining two modes are equal to Antimatter Cannon, without special features.';
        }
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
			if ( $maxhealth == 0 ) $maxhealth = 10;
            if ( $powerReq == 0 ) $powerReq = 8;            
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
       	public function getDamage($fireOrder){
			$damage = 0;
	        $X = $this->getX($fireOrder);
			if($this->firingMode ==1){
					$damage = (2*$X) + 6; //Shredder
			}
			else{
					$damage = (2*$X) + 16;
					return $damage ; //AMCannon	
			}		
			return $damage ;
		}

        public function setMinDamage(){     
			switch($this->firingMode){
				case 1:
					$this->minDamage = 6; //Shredder
					break;
				default:
					$this->minDamage = 16; //AMCannon
					break;	
			}     
		}
        public function setMaxDamage(){    
			switch($this->firingMode){
				case 1:
					$this->maxDamage = 26; //Shredder
					break;
				default:
					$this->maxDamage = 56; //AMCannon
					break;	
			}
		}
		
		
		
		//find units in range, create attacks vs them - if this weapon is being fired in Shredder mode, that is!
		public function beforeFiringOrderResolution($gamedata){
			$firingOrders = $this->getFireOrders($gamedata->turn);
			foreach($firingOrders as $fireOrder){
				if (($fireOrder->type == 'normal') && ($fireOrder->firingMode == 1) ) { //exists!
					//if it's targeted on unit - retarget on hex
					if ($fireOrder->targetid != -1) {
						$targetship = $gamedata->getShipById($fireOrder->targetid);
						//insert correct target coordinates: last turns' target position
						$targetPos = $targetship->getHexPos();
						$fireOrder->x = $targetPos->q;
						$fireOrder->y = $targetPos->r;
						$fireOrder->targetid = -1; //correct the error
						$fireOrder->calledid = -1; //just in case
					}			
						
					//find all units in target area, declare appropriate number of firing orders vs them...
					$shooter = $gamedata->getShipById($fireOrder->shooterid);
					$targetLocation = new OffsetCoordinate($fireOrder->x, $fireOrder->y);
			
					$unitsInRange = $gamedata->getShipsInDistance($targetLocation, 1);
					foreach ($unitsInRange as $targetUnit) {
						//just for debugging purposes - range to target					
						$dist = mathlib::getDistanceHex($shooter, $targetUnit);
						$fireOrder->notes .= $targetUnit->phpclass . ": $dist;";		
						$fireOrder->updated = true;						
						
						if ($targetUnit === $shooter) continue; //do not target self
						if ($targetUnit->isDestroyed()) continue; //no point engaging dead ships
						if (isset(AntimatterShredder::$alreadyEngaged[$targetUnit->id])) continue; //unit already engaged
						$relativeBearing = $shooter->getBearingOnUnit($targetUnit);
						if (mathlib::getDistance($shooter->getCoPos(), $targetUnit->getCoPos()) > 0){ //check arc only if target  is not on the same hex!
							if (!(mathlib::isInArc($relativeBearing, $this->startArc, $this->endArc))) continue; //must be in arc
						}
						AntimatterShredder::$alreadyEngaged[$targetUnit->id] = true;//mark engaged
						$this->prepareShredderOrders($shooter, $targetUnit, $gamedata, $fireOrder); //actually declare appropriate number of attacks!				
					}					
				}
			}
		} //endof function beforeFiringOrderResolution
		

		//1 attack on every fighter, d3 on MCV/HCVs, d6 on Caps, d6+3 on Enormous
		private function prepareShredderOrders($shooter, $target, $gamedata, $fireOrder){
			$numberOfAttacks = 1;
			if ($target instanceOf FighterFlight){ //fighter
				$numberOfAttacks = 1;
			} else if ($target->shipSizeClass < 3){//MCV/HCV
				$numberOfAttacks = Dice::d(3);
			} else if ($target->Enormous){ //Enormous
				$numberOfAttacks = Dice::d(6)+3;
			}else{ //Capital
				$numberOfAttacks = Dice::d(6);
			}
			
			for($i = 1; $i<=$numberOfAttacks;$i++){ //actually declare as appropriate
				if($target instanceOf FighterFlight){ //one attack against every fighter!
					foreach ($target->systems as $fighter) {
						if ($fighter == null || $fighter->isDestroyed()) {
							continue;
						}
						$newFireOrder = new FireOrder(
							-1, "normal", $shooter->id, $target->id,
							$this->id, $fighter->id, $gamedata->turn, 1, 
							0, 0, 1, 0, 0, //needed, rolled, shots, shotshit, intercepted
							0,0,$this->weaponClass,-1 //X, Y, damageclass, resolutionorder
						);		
						$newFireOrder->addToDB = true;
						$this->fireOrders[] = $newFireOrder;
					}
				}else{
					$newFireOrder = new FireOrder(
						-1, "normal", $shooter->id, $target->id,
						$this->id, -1, $gamedata->turn, 1, 
						0, 0, 1, 0, 0, //needed, rolled, shots, shotshit, intercepted
						0,0,$this->weaponClass,-1 //X, Y, damageclass, resolutionorder
					);		
					$newFireOrder->addToDB = true;
					$this->fireOrders[] = $newFireOrder;
				}
			}			
		}//endof function prepareShredderOrders

		
		public function calculateHitBase($gamedata, $fireOrder)
		{
			$this->changeFiringMode($fireOrder->firingMode);
			if($this->firingMode ==1) { //Shredder
				//against hex - it shouldn't even be resolved
				if ($fireOrder->targetid == -1) {				
					$fireOrder->needed = 0;	//just so no one tries to intercept it				
					$fireOrder->updated = true;
					$fireOrder->notes .= 'Antimatter Shredder aiming shot, not resolved.';
					return;
				} 
				//set range to 11 - so targets out of nominal range aren't missed!
				$this->range = 11;
				$this->rangeArray[1] = 11;
				$this->hextarget = false; //otherwise it won't get intercepted - temporarily make it unit-targeted!
				$this->hextargetArray[1] = false;
			} 
			//Antimatter Cannon or Shredder direct shot - default routine will do!
			parent::calculateHitBase($gamedata, $fireOrder);
		}		
			
		public function fire($gamedata, $fireOrder){
			if(($this->firingMode == 1) && ($fireOrder->targetid == -1)) { //initial "tareting location" Shredder shot should not actually be resolved
				return;
			} else if(($this->firingMode ==1) &&($fireOrder->calledid >= 0)){ //in case of direct Shredder attack - skip it if target fighter is already destroyed!
				$target = $gamedata->getShipById($fireOrder->targetid);
				if($target instanceOf FighterFlight) { //it should be so, check is just in case
					$craft = $target->getFighterBySystem($fireOrder->calledid);
					if($craft && $craft->isDestroyed()) return;//the shot is dedicated to this one fighter, cannot be retargeted on another - skip resolution
				}
			}
			//Antimatter Cannon OR direct Shredder attack - default routine will do!
			parent::fire($gamedata, $fireOrder);
		}
	
	} //end of class AntimatterShredder
		
?>
