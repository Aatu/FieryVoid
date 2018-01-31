<?php
	class EW{
	
	
		public static function validateEW($EW, $gamedata){
			
			$turn = $gamedata->turn;
			$used = 0;
			$ship = null;
			
			foreach ($EW as $entry){
			
				if ($turn != $entry->turn)
					continue;
				

				$ship = $gamedata->getShipById($entry->shipid);
				$used += $entry->amount;
                
                if ($entry->type === "DIST")
                {
                    if ($entry->amount % 3 !== 0)
                        throw new Exception("Validation of EW failed: DIST ew not divisable by 3");
                }
			}
			
			if ($ship == null)
				return false;
				
			$available = self::getScannerOutput($ship, $gamedata->turn);

			if ($available >= $used){
				return true;
			}
			
			return false;
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
                $output -= 2;
			
            
            if ($output < 0)
                $output = 0;
            
			return $output;
				
		}
        
        public static function getBlanketDEW($gamedata, $target)
        {
            $FDEW = 0;
            foreach ($gamedata->ships as $ship)
            {
                if ($ship->team == $target->team && $ship->isElint()
                     && Mathlib::getDistanceHex( $target->getCoPos(), $ship->getCoPos() ) <= 20){
                    $blanket = $ship->getBlanketDEW($gamedata->turn);
                    
                    if ( $blanket > $FDEW )
                        $FDEW = $blanket*0.25;
                }
            }
            
            return $FDEW;
        }
        
        public static function getSupportedOEW($gamedata, $ship, $target)
        {
            $jammer = $target->getSystemByName("jammer");

            if($jammer != null && $jammer->getOutput()> 0 ){
                // Jammer protected ships cannot be targetten for SOEW
                            return 0;
            }
            
            if (Mathlib::getDistanceHex( $target->getCoPos(), $ship->getCoPos() ) > 30)
                return 0;
            
            $amount = 0;
            foreach ($gamedata->ships as $elint)
            {
                if ($elint->id === $ship->id)
                    continue;
                
                if (!$elint->isElint())
                    continue;
                
                if (Mathlib::getDistanceHex( $target->getCoPos(), $elint->getCoPos() ) > 30)
                    continue;
                
                if (Mathlib::getDistanceHex( $ship->getCoPos(), $elint->getCoPos() ) > 30)
                    continue;

                if (!$elint->getEWbyType("SOEW", $gamedata->turn, $ship))
                    continue;

                $foew = $elint->getEWByType("OEW", $gamedata->turn, $target) * 0.5;
                if ($foew > $amount)
                    $amount = $foew;
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
                
                if (Mathlib::getDistanceHex( $ship->getCoPos(), $elint->getCoPos() ) > 50)
                    continue;

                $fdew = $elint->getEWByType("SDEW", $gamedata->turn, $ship)*0.5;

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
                if ($elint->id === $ship->id)
                    continue;
                
                if (!$elint->isElint())
                    continue;
                
                if (Mathlib::getDistanceHex( $ship->getCoPos(), $elint->getCoPos() ) > 50)
                    continue;

                $fdew = $elint->getEWByType("DIST", $gamedata->turn, $ship)*0.25;

                //if (fdew > amount)
                $amount += $fdew;
            }

            if ($num > 0)
                return $amount/$num;
            return $amount;
        }
	}
