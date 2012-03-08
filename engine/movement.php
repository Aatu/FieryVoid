<?php
    class Movement{

        public static function validateMovement($gamedata, $ships){
            $ship = $gamedata->ships[$gamedata->activeship];
            $moves = $ships[$gamedata->activeship]->movement;
        }
        
        public static function isPivoting($ship, $turn){
			if ($ship->agile)
				return 0;
				
            $pivoting = 0; // 0: false, 1: left, 2:right
            $lastmove = $ship->getLastMovement();
            $movements = array();
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
                
            }
            
            return $pivoting;
        }
        
        public static function isRolling($ship, $turn){
			if ($ship->agile)
				return false;
				
            $rolling = false;
            $lastmove = $ship->getLastMovement();
            $movements = array();
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
        
       
        public static function getTurnDelay($ship){
            $turndelay = 0;
			if (!is_array($ship->movement))
				return 0;
				
            foreach ($ship->movement as $move){
           
                if (($move->type == "move" || $move->type == "slipright" || $move->type == "slipleft" ) && $turndelay > 0){
                    $turndelay--;
                }
                                   
                if ($move->type == "turnleft" || $move->type == "turnright"){
                    $turndelay += self::calculateTurndelay($ship, $move);
                }
            
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
            
            
            if ($move->type == "pivotright" || $move->type == "pivotleft"){
            
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
					
				$direction = $ship->systems[$i]->direction;
                $assignedarray[$direction] += $value;
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
                $movements[] = new MovementOrder(null, "isPivotingLeft", $lastmove->x, $lastmove->y, 0,0, $lastmove->speed, $lastmove->heading, $lastmove->facing, true, ($turn+1));
                //$movements[] = new MovementOrder(null, "pivotleft", $lastmove->x, $lastmove->y, $lastmove->speed, $lastmove->heading, MathLib::addToHexFacing($lastmove->facing , -1), true, ($turn+1));
            }else if ($pivoting == 2){
                $movements[] = new MovementOrder(null, "isPivotingRight", $lastmove->x, $lastmove->y, 0,0, $lastmove->speed, $lastmove->heading, $lastmove->facing, true, ($turn+1));
                //$movements[] = new MovementOrder(null, "pivotright", $lastmove->x, $lastmove->y, $lastmove->speed, $lastmove->heading, MathLib::addToHexFacing($lastmove->facing , 1), true, ($turn+1));
            }
            
            if ($rolling){
                $movements[] = new MovementOrder(null, "isRolling", $lastmove->x, $lastmove->y, 0,0, $lastmove->speed, $lastmove->heading, $lastmove->facing, true, ($turn+1));
                $rolled = !$rolled;
            }
            if ($rolled){
                $movements[] = new MovementOrder(null, "isRolled", $lastmove->x, $lastmove->y, 0,0, $lastmove->speed, $lastmove->heading, $lastmove->facing, true, ($turn+1));
            }
            
            
            return $movements;
        
        }
        
         public static function isRolled($ship, $turn = -1){
            $ret = false;
			$turnwas = 0;
            $lastmove = $ship->getLastMovement();
            $movements = array();
            if ($ship->agile){
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
					
					if ($move->type == "roll")
						$ret = !$ret;
				
					
				}
			}else{
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
				
			$crits = $system->hasCritical("HalfEfficiency", $turn);
			$used = round($used/($crits+1));
			
			return $used;
		
		}
		
		
    

    }
?>
