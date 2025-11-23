<?php
    class Movement{
        
        private static function checkIsNewMove($gamedata, $ship, $move){
            
            
            
            $newship = $gamedata->getShipById($ship->id);
   
            $newmove = $newship->getMovementById($move->id);

            if (!$newmove)
                return true;
          
            return false;
            
        }

        public static function validateMovement($gamedata, $ship){
            
            if ($gamedata->phase == 3){

                if (!($ship instanceof FighterFlight))
                    $ship->movement = array();

                for ($i=count($ship->movement)-1;$i>=0;$i--){
                    $move = $ship->movement[$i];

                    if (($move->type != "pivotright" && $move->type != "pivotleft" )
						||( $move->turn != $gamedata->turn )
						||(!self::checkIsNewMove($gamedata, $ship, $move))){
                        unset($ship->movement[$i]);
                       
                    }

                }
                
            }else{
                
            //These parts are never used since validateMovement is only called during FireGamePhase, and it has a mistake in it anyway... DK - Nov 2025    
                //$ship = $gamedata->ships[$gamedata->activeship];
                //$moves = $ships[$gamedata->activeship]->movement;     
                
            }
            
            return true;
            
        }

     
        public static function isPivoting($ship, $turn){
			if ($ship->agile || $ship instanceof FighterFlight)
				return 0;
				
            $pivoting = 0; // 0: false, 1: left, 2:right
            foreach ($ship->movement as $move){
                if ($move->turn != $turn || $turn == null)
                    continue;

                if ($move->type == "isPivotingLeft")
                    $pivoting = 1;
                    
                if ($move->type == "isPivotingRight")
                    $pivoting = 2;
                

                if ($move->type == "pivotright" && !$move->preturn){
                    if ($pivoting == 1){
                        $pivoting = 0;
                    }else if ($pivoting == 0){
                        $pivoting = 2;
                    }
                }
                
                if ($move->type == "pivotleft" && !$move->preturn){
                    if ($pivoting == 2){
                        $pivoting = 0;
                    }else if ($pivoting == 0){
                        $pivoting = 1;
                    }
                }

                if ($move->value == "turnIntoPivot" && !$move->preturn){
  				   $pivoting = 0;
				}                 
            }
            
            return $pivoting;
        }
        
        public static function hasPivoted($ship, $turn){
            foreach ($ship->movement as $move){
                if ($move->turn != $turn)
                    continue;

                if ($move->type == "pivotleft" || $move->type == "pivotright" )
                    return true;
                
                if ($move->type == "isPivotingRight" || $move->type == "isPivotingLeft" )
                    return true;
            }
            return false;
        }

        public static function hasTurned($ship, $turn){
            foreach ($ship->movement as $move){
                if ($move->turn != $turn)
                    continue;
                if ($move->type == "turneleft" || $move->type == "turnright" )
                    return true;
           }
            return false;
        }
        
        public static function hasRolled($ship, $turn){
            foreach ($ship->movement as $move){
                if ($move->turn != $turn)
                    continue;

                if ($move->type == "roll" || $move->type == "isRolling" )
                    return true;
            }

            return false;
        }
        
        public static function isRolling($ship, $turn){
			if ($ship->agile || $ship instanceof FighterFlight)
				return false;
				
            $rolling = false;
            foreach ($ship->movement as $move){
                if ($move->turn != $turn || $turn == null)
                    continue;
                    
                if ($move->type == "isRolling")
                    $rolling = true;
                
                if ($move->type == "roll"){
                    $rolling = !$rolling;
                }
                
            }
            
            return $rolling;
        }
        		
        public static function isHalfPhased($ship, $turn){
            foreach ($ship->movement as $move){
                if ($move->turn != $turn) continue;
                if ($move->type == "halfPhase" ) return true;
            }
            return false;
        }
		
        public static function getJinking($ship, $turn){
			$jinking = 0;
			
			foreach ($ship->movement as $move){
           
                if ($move->type == "jink" && $move->turn == $turn){
                    $jinking += (int)$move->value;
                }
                                   
                           
            }
          
            return $jinking;
		}    
		
		public static function getCombatPivots($ship, $turn){
			$pivots = 0;
			
			foreach ($ship->movement as $move){
           
                if (($move->type == "pivotleft" || $move->type == "pivotright" ) && $move->value == "combatpivot" && $move->turn == $turn){
					$pivots++;
                }
                                   
                           
            }
           
            return $pivots;
		}        
       
        public static function getTurnDelay($ship){
            $turndelay = 0;
			if (!is_array($ship->movement))
				return 0;
			
            $last = null;
            
            foreach ($ship->movement as $move){
                
                if ($move->turn < TacGamedata::$currentTurn -1)
                    continue;
           
                if (($move->type == "move" || $move->type == "slipright" || $move->type == "slipleft" ) && $turndelay > 0){
                    $turndelay--;
                }
                                   
                if ($move->type == "turnleft" || $move->type == "turnright"){
                    if (!$ship->agile || !$last 
                        || ($last->type != "turnleft" && $last->type != "turnright"))
                    {
                        $turndelay += self::calculateTurndelay($ship, $move);
                    }
                }
                $last = $move;
            }
        
            if ($turndelay < 0)
                $turndelay = 0;
        
            return $turndelay;
        }
        
        public static function calculateTurndelay($ship, $move){
            $speed = $move->speed;
			if ($speed == 0)
				return 0;
				
            $turndelay = ceil($speed * $ship->turndelaycost);
            
            if ($ship instanceof FighterFlight)
				return $turndelay;
            
            $turndelay -= self::calculateExtraThrustSpent($ship, $move);
            if ($turndelay < 1)
                $turndelay = 1;
                
            return $turndelay;
        }
        
        public static function calculateExtraThrustSpent($ship, $move){
            $reg = self::calculateThrustStillReq($ship, $move);

            $extra = 0 - $reg[0];
            
            if ( $extra < 0)
                $extra = 0;
                
            return $extra;

        }
        
        public static function calculateThrustStillReq($ship, $move){
            $assignedarray = self::calculateAssignedThrust($ship, $move);
            $requiredThrust = $move->requiredThrust;
            $stillReq = $requiredThrust;
            $any = 0;
        
            foreach ($requiredThrust as $i=>$req){
                $ass = $assignedarray[$i];
                
                if ( $ass>$req){
                    $stillReq[$i] = 0;
                    $any += $ass-$req;
                }else{
                    $stillReq[$i] -= $ass;
                }   
            }
            
            $stillReq[0] -= $any;
            
            
            if ($move->type == "pivotright" || $move->type == "pivotleft" && !($ship instanceof FighterFlight)) {
            
                $reversed = self::hasSidesReversedForMovement($ship);
                $right = ($move->type == "pivotright");
                if ($reversed){
                    $right = !$right;
                }
                
                if ($right){
                    if ($assignedarray[1]>0 || $assignedarray[3]>0){
                        $stillReq[2] = -1;
                        $stillReq[4] = -1;
                    }
                    if ($assignedarray[2]>0 || $assignedarray[4]>0){
                        $stillReq[1] = -1;
                        $stillReq[3] = -1;
                    }
                
                }else{
                    if ($assignedarray[1]>0 || $assignedarray[4]>0){
                        $stillReq[2] = -1;
                        $stillReq[3] = -1;
                    }
                    if ($assignedarray[2]>0 || $assignedarray[3]>0){
                        $stillReq[1] = -1;
                        $stillReq[4] = -1;
                    }
                }
                
                
            }
            
            
            return $stillReq;
            
        
        
        }
        
        public static function calculateAssignedThrust($ship, $move){
            $assignedarray = array(0,0,0,0,0);
            
             
            
            foreach ($move->assignedThrust as $i=>$value){
				if (empty($value))
					continue;
					
				 if ($ship instanceof FighterFlight){
									
					$assignedarray[0] += $value;

				}else{
					$direction = $ship->systems[$i]->direction;
					$assignedarray[$direction] += $value;
				}
            }

            return $assignedarray;
        }
        
        public static function hasSidesReversedForMovement($ship){
            $back = self::isGoingBackwards($ship);
            
            $reversed = (($back || self::isRolled($ship)) && !($back && self::isRolled($ship)));
            
            return $reversed;
        }
        

        public static function setPreturnMovementStatusForShip($ship, $turn){
            $turn = $turn -1;
            $rolled = self::isRolled($ship, $turn);
            $rolling = self::isRolling($ship, $turn);
            $pivoting = self::isPivoting($ship, $turn); // 0: false, 1: left, 2:right
            $lastmove = $ship->getLastMovement();
            $movements = array();            
            
            if ($pivoting == 1){
                $movements[] = new MovementOrder(null, "isPivotingLeft", $lastmove->position, 0,0, $lastmove->speed, $lastmove->heading, $lastmove->facing, true, ($turn+1), 0, $ship->iniative);
                //$movements[] = new MovementOrder(null, "pivotleft", $lastmove->x, $lastmove->y, $lastmove->speed, $lastmove->heading, MathLib::addToHexFacing($lastmove->facing , -1), true, ($turn+1));
            }else if ($pivoting == 2){
                $movements[] = new MovementOrder(null, "isPivotingRight", $lastmove->position, 0,0, $lastmove->speed, $lastmove->heading, $lastmove->facing, true, ($turn+1), 0, $ship->iniative);
                //$movements[] = new MovementOrder(null, "pivotright", $lastmove->x, $lastmove->y, $lastmove->speed, $lastmove->heading, MathLib::addToHexFacing($lastmove->facing , 1), true, ($turn+1));
            }
            
            if ($rolling){
                $movements[] = new MovementOrder(null, "isRolling", $lastmove->position, 0,0, $lastmove->speed, $lastmove->heading, $lastmove->facing, true, ($turn+1), 0, $ship->iniative);
                $rolled = !$rolled;
            }
            if ($rolled){
                $movements[] = new MovementOrder(null, "isRolled", $lastmove->position, 0,0, $lastmove->speed, $lastmove->heading, $lastmove->facing, true, ($turn+1), 0, $ship->iniative);
            }
			
			//Find Engine on Ship
			$engine = $ship->getSystemByName("Engine");
			
			if($engine){
				//Check Engine Crits, if has the ControlsStuck crit on this turn ending, create new Movement Order.
				foreach ($engine->criticals as $critical) {
					if ($critical->phpclass === "ControlsStuck" && $critical->inEffect) {
						
						
						// Check if it matches the current turn
						if ($critical->turn == $turn) {		                
							// Found a matching "Engine Shorted" critical for this turn
							$movements[] = self::doStuckEngine($ship, $engine, $turn, $lastmove);
							break;
						}
					}
				}
			}
		
            return $movements;
            
        }//endof setPreturnMovementStatusForShip()
 
        
         public static function isRolled($ship, $turn = -1){
             $ret = false;
             $turnwas = 0;
             $lastmove = $ship->getLastMovement();
             $movements = array();
             
             if ($ship->agile){
                 foreach ($ship->movement as $move){
//                     if ($move->turn != $turn && $turn != -1)
//                         continue;
                     
//                     if ($move->turn != $turnwas){
//                         $ret = false;
//                     }

//                     if ($move->type == "isRolled"){
//                         $ret = true;
//                         $turnwas = $move->turn;
//                     }

                     if ($move->type == "roll")
                         $ret = !$ret;
                }
            }
            else
            {
                foreach ($ship->movement as $move){
                    if ($move->turn != $turn && $turn != -1)
                        continue;

                    if ($move->turn != $turnwas){
                        $ret = false;
                    }

                    if ($move->type == "isRolled"){
                        $ret = true;
                        $turnwas = $move->turn;
                    }
                }
            }
            
            return $ret;
        }
		
		private static function isGoingBackwards($ship){
			$lastmove = $ship->getLastMovement();
			$heading = $lastmove->heading;
			$facing = $lastmove->facing;
			
			return (Mathlib::addToHexFacing($heading, 3) == $facing);
				
		}
		
		public static function getAmountChanneled($system, $ship, $turn){
			$used = 0;
			foreach ($ship->movement as $movement){
				if ($movement->turn != $turn)
					continue;
				
				//print(var_dump($movement->assignedThrust));
				//print("Now handling ".$system->id ."\n\n");
				if (!isset($movement->assignedThrust[$system->id]))
					continue;
					
				$assigned = $movement->assignedThrust[$system->id];
				
				if ($assigned != null){
					$used += $assigned;
				}
				
			}
			
			if ($system->hasCritical("FirstThrustIgnored", $turn))
				$used--;
			
			if ($system->hasCritical("FirstThrustIgnoredOneTurn", $turn))
				$used--;			
				
			$crits = $system->hasCritical("HalfEfficiency", $turn);
			$used = round($used/($crits+1));
			
			return $used;
		
		}


	    //Called from canAccelerate() below	- basically going neither ahead nor backwards
        public static function isOutOfAlignment($ship) {
			$lastmove = $ship->getLastMovement();
			$heading = $lastmove->heading;
			$facing = $lastmove->facing;			
			       
	        if ($facing == $heading || Mathlib::addToHexFacing($facing, 3) == $heading || Mathlib::addToHexFacing($facing, -3) == $heading) return false; //in alignment either way
	        return true;
	    }//endof isOutOfAlignment()

	     
		//Called from Engine class function, doEngineShorted()
		public static function hasMainThruster($ship) {
		    $mainThrusters = array_filter($ship->systems, function($system) {
		        return $system instanceof Thruster && $system->direction == 2;
		    });

		    return !empty($mainThrusters);
		}//endof hasMainThrusters()


		//Called from Engine class function, doEngineShorted()
		public static function canAccelerate($ship, $gamedata) {
		    if ($ship->base || $ship->smallBase || $ship->osat || $ship->isDestroyed()) {
		        return false;
		    }

		    // Check if all engines are destroyed
		    $engines = $ship->getSystemsByName('Engine');
		    $allEnginesDestroyed = array_reduce($engines, function($carry, $engine) {
		        return $carry && $engine->isDestroyed();
		    }, true);

		    if ($allEnginesDestroyed) {
		        return false;
		    }

		    // Engine Shorted Crit: Thrust only via main thrusters
		    if (self::isOutOfAlignment($ship)) {
		        return false;
		    }

		    return true;
		}//endof canAccelerate()		

		//Called from doStuckEgine() below, which is called from setPreturnMovementStatus above :)
		public static function sortThrustersAllocations($mainThrusters, $maxThrust) {
		    $remainingThrust = $maxThrust; // Total thrust to allocate
		    $allocations = []; // Array to store final thrust allocation

		    // First, assign maximum thrust to each thruster (up to its output limit)
		    foreach ($mainThrusters as $thruster) {
		        if ($remainingThrust <= 0) {
		            break; // No more thrust to allocate
		        }

		        // Calculate the maximum thrust that can be assigned to this thruster
		        $thrustForThisThruster = min($thruster->output, $remainingThrust);
		        $allocations[$thruster->id] = $thrustForThisThruster;
		        $remainingThrust -= $thrustForThisThruster; // Deduct allocated thrust from remaining

		        // If this thruster is fully allocated, continue to the next one
		    }

		    // If there's any remaining thrust, distribute it evenly across all thrusters
		    if ($remainingThrust > 0) {
		        // Loop through each thruster and add as evenly as possible
		        $thrusterCount = count($mainThrusters);
		        $evenThrust = floor($remainingThrust / $thrusterCount);
		        $leftoverThrust = $remainingThrust % $thrusterCount;

		        foreach ($mainThrusters as $thruster) {
		            if (!isset($allocations[$thruster->id])) {
		                $allocations[$thruster->id] = 0; // Initialize if not allocated
		            }
		            $allocations[$thruster->id] += $evenThrust; // Add even thrust
		        }

		        // Distribute any leftover thrust
		        foreach ($mainThrusters as $thruster) {
		            if ($leftoverThrust > 0) {
		                $allocations[$thruster->id]++;
		                $leftoverThrust--;
		            }
		        }
		    }

		    return $allocations;
		}//endof sortThrustersAllocations()

		
		//Called from setPreturnMovementStatus above if conditions are met.
		public static function doStuckEngine($ship, $engine, $turn, $lastmove) {

		    $newMovement = null;
            
		    // Calculate acceleration from stuck engine
		    $maxThrust = $engine->getOutputWhenOffline();
	        foreach ($engine->criticals as $crit){
	            $maxThrust += $crit->outputMod;//outputMod is negative
	        }
//	        $maxThrust += $engine->getBoostLevel($turn); //Actually this si next turns thrust, where there would be no boost as engine offline.
	        	        		    
    
		    $maxAccel = floor($maxThrust / $ship->accelcost);
			$newHeading	= $lastmove->heading;	 
			    
		    if(self::isGoingBackwards($ship)){ //If main thrusters fire when backwards it SLOWS the ship!
		    	$newSpeed = $lastmove->speed - $maxAccel;
				if($newSpeed < 0 ){
					$newHeading = MathLib::addToHexFacing($lastmove->heading, 3);
            		$newSpeed = $newSpeed * -1;
				}
			}else{			
		    	$newSpeed = $lastmove->speed + $maxAccel;				
			}	
		    $value = $newSpeed - $lastmove->speed;	//Not sure this is needed, but just in case!

		    // Create new movement order with updated speed
		    $newMovement = new MovementOrder(null, "speedchange", $lastmove->position, 0, 0, $newSpeed, $newHeading, $lastmove->facing, true, ($turn+1), $value, $ship->iniative);
		    			
			$mainThrusters = array();
		    $thrusters = $ship->getSystemsByName('Thruster');
		    		    
		    foreach($thrusters as $thruster){
		    	if($thruster->location == 2) $mainThrusters[] = $thruster;
			}		    	   	

			// Allocate thrust to thrusters
			$allocatedThrust = self::sortThrustersAllocations($mainThrusters, $maxThrust);

			// Iterate through the allocated thrust and assign it
			foreach ($allocatedThrust as $systemId => $thrustAllocation) {
			    $newMovement->assignedThrust[$systemId] = $thrustAllocation;  
			}

			return $newMovement;

		}//endof doStuckEngine()
    

    }
?>
