<?php
	class EW{
	
	
		public static function validateEW($ship, $gamedata){

		    $EW = $ship->EW;
			$turn = $gamedata->turn;
			$used = 0;
      /* //Removed to allow Contrained ELINT vessels to work e.g. Mindriders - DK - 20.7.24  			
			foreach ($EW as $entry){
			
				if ($turn != $entry->turn)
					continue;

				$used += $entry->amount;
				
        
                if ($entry->type === "DIST")
                {
                   if ($entry->amount % 3 !== 0)
                       throw new Exception("Validation of EW failed: DIST ew not divisable by 3");
                }
			}
	*/		
			/* Marcin Sawicki, 27.09.2019: apparently at this point ship enhancements are NOT taken into account (and scanner output can be affected by them)
			hence I disable this check! judging it not to be necessary.
			*/
			/*
			$available = self::getScannerOutput($ship, $gamedata->turn);

			if ($available >= $used){
				return true;
			}

            throw new Exception("EW validation failed: used more than available (shipId: $ship->id). Used $used available $available");
			*/
			return true;
			
		}
		
		public static function getScannerOutput($ship, $turn){
		
            if ($ship->isDisabled())
                return 0;
            
			$output = 0;
			foreach ($ship->systems as $system){
			
				if ($system->outputType == "EW"){
					$output += $system->getOutput($turn);
				}
			}
            		$CnC = $ship->getSystemByName("CnC");
			if ($CnC && $CnC->hasCritical("RestrictedEW"))
				$output -= 2*$CnC->hasCritical("RestrictedEW");	
//GTS 13Jan22
            		$BSGHybrid = $ship->getSystemByName("BSGHybrid");
			if ($BSGHybrid && $BSGHybrid->hasCritical("SensorLoss"))
				$output -= 3*$BSGHybrid->hasCritical("SensorLoss");	

			
			    if ($output < 0)
				$output = 0;
            
			return $output;
				
		}
        
    public static function getBlanketDEW($gamedata, $target)
        {
            $FDEW = 0;
            foreach ($gamedata->ships as $ship)
            {
                if ( ($ship->team == $target->team) 
					&& $ship->isElint()
                    && Mathlib::getDistanceHex($target, $ship) <= 20
				){
                    $blanket = $ship->getBlanketDEW($gamedata->turn);
                    if ( $blanket > $FDEW ) $FDEW = $blanket;
                }
            }

		if($target->hasSpecialAbility("ConstrainedEW")){//Mindrider ships have less efficient ELINT abilities - DK 19.07.24.
            $FDEW = $FDEW * 0.2;			
		}else{
            $FDEW = $FDEW * 0.25;
		}    
			//if(($target->jammerMissile) && $FDEW < 2) $FDEW = 2; //Jammer Missiles provide 2 BDEW to all ships in range, but not in combination with normal BDEW!
			if(isset(AmmoMissileJ::$alreadyJammed[$target->id]) && $FDEW < 2) $FDEW = 2; //Jammer Missiles provide 2 BDEW to all ships in range, but not in combination with normal BDEW!            
				            
            return $FDEW;
        }
        
        public static function getSupportedOEW($gamedata, $ship, $target)
        {
			$jammerValue = $target->getSpecialAbilityValue("Jammer", array("shooter" => $ship, "target" => $target));
			if ($jammerValue>0) {
				return 0; //no lock-on on supported ship negates SOEW, if any
			}
			/*replaced by code above
			if ( ($ship->faction != $target->faction) && (!$ship->hasSpecialAbility("AdvancedSensors")) ){
					$jammer = $target->getSystemByName("jammer");
				if($jammer != null && $jammer->getOutput()> 0 ){ // Jammer protected ships cannot be targetten for SOEW
				return 0;
				}
			}
			*/
			
            $amount = 0;
            foreach ($gamedata->ships as $elint)
            {
                if ($elint->id === $ship->id) 
                    continue;
                
                if (!$elint->isElint())
                    continue;
                
                if (Mathlib::getDistanceHex( $target, $elint ) > 30) 
                    continue;
                
                if (Mathlib::getDistanceHex( $ship, $elint ) > 30) 
                    continue;

                if (!$elint->getEWbyType("SOEW", $gamedata->turn, $ship)) 
                    continue;

                $jammerValue = $target->getSpecialAbilityValue("Jammer", array("shooter" => $elint, "target" => $target));
				if ($jammerValue>0) {
					//return 40; //No sure why we returned 40, seems like an error - DK Nov 2025
					continue; //no lock-on negates SOEW, if any
				}                

                //Check line of sight between ELINT and target/shooter
                $elintPos = $elint->getHexPos();
                $shooterPos = $ship->getHexPos();
                $targetPos = $target->getHexPos();                

                $losBlockedTarget  = $elint->isLoSBlocked($elintPos, $targetPos, $gamedata); //Defaults false e.g. line of sight NOT blocked.
                if($losBlockedTarget) continue; //Line of sight blocked to one of the relevant units, skip.         

                $losBlockedShooter  = $elint->isLoSBlocked($elintPos, $shooterPos, $gamedata); //Defaults false e.g. line of sight NOT blocked.
                if($losBlockedShooter) continue; //Line of sight blocked to one of the relevant units, skip.                                       

				if($elint->hasSpecialAbility("ConstrainedEW")){//Mindrider ships have less efficient ELINT abilities - DK 19.07.24.
				    $foew = $elint->getEWByType("OEW", $gamedata->turn, $target) * 0.33;
				    $foew = round($foew * 3) / 3;		
				}else{
          	      $foew = $elint->getEWByType("OEW", $gamedata->turn, $target) * 0.5;
				}
								
				$dist = EW::getDistruptionEW($gamedata, $elint); //account for ElInt being disrupted
				$foew = $foew-$dist;		
				
                if ($foew > $amount) $amount = $foew;
            }

            if($ship instanceof FighterFlight){
                // Fighter flights only receive half the benefit of OEW
                // Kitchensink rules page 191
                return ($amount * 0.5);
            }else{
                return $amount;
            }
        }

        public static function getSupportedDEW($gamedata, $ship)
        {
            $amount = 0;
            foreach ($gamedata->ships as $elint)
            {
                if ($elint->id === $ship->id)
                    continue;
                
				/* faction should not affect it!
                if($elint->faction != $ship->faction)
                    continue;
                */
		    
                if (!$elint->isElint())
                    continue;
                
                if (Mathlib::getDistanceHex( $ship, $elint ) > 50)
                    continue;

                if($elint->hasSpecialAbility("ConstrainedEW")){//Mindrider ships have less efficient ELINT abilities - DK 19.07.24.
               		$fdew = $elint->getEWByType("SDEW", $gamedata->turn, $ship)*0.33;
				    $fdew = round($fdew * 3) / 3;	               				
				}else{
              	  $fdew = $elint->getEWByType("SDEW", $gamedata->turn, $ship)*0.5;
				}

                if ($fdew > $amount)
                $amount = $fdew;
            }

            return $amount;
        }

      
        public static function getDistruptionEW($gamedata, $ship)
        {
            $num = $ship->getOEWTargetNum($gamedata->turn);
            $amount = 0;

            foreach ($gamedata->ships as $elint)
            {
                if ($elint->id === $ship->id) continue;
                
                if (!$elint->isElint()) continue;
                
                if (Mathlib::getDistanceHex( $ship, $elint ) > 30) continue;               

                $elintPos = $elint->getHexPos();
                $shipPos = $ship->getHexPos();

                $losBlocked  = $elint->isLoSBlocked($elintPos, $shipPos, $gamedata); //Defaults false e.g. line of sight NOT blocked.
                if($losBlocked) continue; //Line of sight blocked to one of the relevant units, skip.                  

                if($elint->hasSpecialAbility("ConstrainedEW")){//Mindrider ships have less efficient ELINT abilities - DK 19.07.24.
        	        $fdew = $elint->getEWByType("DIST", $gamedata->turn, $ship) / 4 ;	
				}else{	
        	        $fdew = $elint->getEWByType("DIST", $gamedata->turn, $ship) / 3 ;//NOT *0.25;

                //if (fdew > amount)
                $amount += $fdew;
            }
            if ($num > 0) return $amount/$num;
            return 0; //NOT $amount;
    		 }
		}
	}	
	
		
