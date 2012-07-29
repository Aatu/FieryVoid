<?php

    class Torpedo extends Weapon{
    
        public $ballistic = true;

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

    }
    
    class IonTorpedo extends Torpedo{
    
        public $name = "ionTorpedo";
        public $displayName = "Ion Torpedo";
        public $range = 50;
        public $loadingtime = 2;
        
        public $fireControl = array(-4, 1, 3); // fighters, <mediums, <capitals 
        
        public $trailColor = array(141, 240, 255);
        public $animation = "torpedo";
        public $animationColor = array(30, 170, 255);
        public $animationExplosionScale = 0.25;
        public $projectilespeed = 12;
        public $animationWidth = 10;
        public $trailLength = 10;
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){        return 15;   }
        public function setMinDamage(){     $this->minDamage = 15 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 15 - $this->dp;      }
    
    }
    
    class BallisticTorpedo extends Torpedo{
    
        public $name = "ballisticTorpedo";
        public $displayName = "Ballistic Torpedo";
        public $range = 25;
        public $loadingtime = 1;
        public $shots = 6;
        public $defaultShots = 1;
        public $normalload = 6;
        
        public $fireControl = array(0, 3, 4); // fighters, <mediums, <capitals 
        
        public $trailColor = array(141, 240, 255);
        public $animation = "trail";
        public $animationColor = array(227, 148, 55);
        public $animationExplosionScale = 0.25;
        public $projectilespeed = 12;
        public $animationWidth = 4;
        public $trailLength = 40;
        
        public $grouping = 20;
        
        public $canChangeShots = true;
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
            
        }
        
        public function firedOnTurn($turn){
            
            foreach ($this->fireOrders as $fire){
                if ($fire->weaponid == $this->id && $fire->turn == $turn){
                    return  $fire->shots;
                }
            }
            return 0;
        }
        
        public function calculateLoading( $gameid, $phase, $ship, $turn )
        {
            $loading = null;
            $shotsfired = $this->firedOnTurn($turn);
            if ($phase === 2)
            {
                if  
                ( $this->isOfflineOnTurn($turn) )
                {
                    $loading = new WeaponLoading($this->id, 0, $gameid, $ship->id, 0, 0, 0, 0);
                }
                else if ($shotsfired)
                {
                    $newloading = $this->turnsloaded-$shotsfired;
                    if ($newloading < 0)
                        $newloading = 0;

                $loading = new WeaponLoading($this->id, 0, $gameid, $ship->id, $newloading, 0, 0, 0);
                }
            }
            else if ($phase === 1)
            {
                $newloading = $this->turnsloaded+1;
                if ($newloading > $this->getNormalLoad())
                    $newloading = $this->getNormalLoad();

                $loading = new WeaponLoading($this->id, 0, $gameid, $ship->id, $newloading, 0, 0, 0);
            }

            return $loading;
        }
        
        /*
        public function setLoading($ship, $turn, $phase){
            $turnsloaded = 0;
        
        
            for ($i = 0;$i<=$turn;$i++){
                $step = 1;
                $off = $this->isOfflineOnTurn($i);
                $overload = $this->isOverloadingOnTurn($i-1);
                $nowoverloading = $this->isOverloadingOnTurn($i);
                if ($i == $turn && $phase == 1 && $overload){
                    $nowoverloading = true;
                }           
                $fired = $this->firedOnTurn($ship, $i);
                
                        
                if ($i == 0){
                    if (!$off){
                        $turnsloaded = $this->getNormalLoad();
                        if ($overload){
                            $turnsloaded = $this->overloadturns;
                        }
                    }
                    continue;
                }
                
                if ($off){
                    $turnsloaded = 0;
                    continue;
                }
                
                if ($overload){
                    $step = 2;
                }
                
                $turnsloaded += $step;
                
                if (!$overload && $turnsloaded > $this->getNormalLoad()){
                    $turnsloaded = $this->getNormalLoad();
                }
        
                if ($turnsloaded > $this->getNormalLoad() && !$nowoverloading){
                    $turnsloaded = $this->getNormalLoad();
                }else if ($turnsloaded > $this->overloadturns && $nowoverloading){
                    $turnsloaded = $this->overloadturns;
                }
                                
               
                
                        
                if ($fired){
                    $turnsloaded -= $fired;
                    if ($turnsloaded < 0)
                        $turnsloaded = 0;
                    
                }
                
            }
            
            $this->turnsloaded = $turnsloaded;
        }
        */
        public function onConstructed($ship, $turn, $phase){
            parent::onConstructed($ship, $turn, $phase);
            $this->shots = $this->turnsloaded;
        }
        
        public function getDamage($fireOrder){        return Dice::d(10,2);   }
        public function setMinDamage(){     $this->minDamage = 2 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 20 - $this->dp;      }
    
    }



?>
