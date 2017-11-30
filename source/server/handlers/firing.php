<?php

/*Marcin Sawicki problems during debug: copuying Firing class method after method*/
/*old version moved to .old file*/

class Firing{
    public $gamedata;
	
    public static function validateFireOrders($fireOrders, $gamedata){
            return true;
    }
	

    //compares weapons' capability as interceptor
    //if intercept rating is the same, faster-firing weapon would go first
    public static function compareInterceptAbility($weaponA, $weaponB){	    
        if ($weaponA->intercept > $weaponB->intercept){
            return 1;
        }else if ($weaponA->intercept < $weaponB->intercept){
            return -1;
        }else if ($weaponA->loadingtime < $weaponB->loadingtime){
            return 1;
        }else if ($weaponA->loadingtime > $weaponB->loadingtime){
            return -1;
        }else{
            return 0;
        }   
    } //endof function compareInterceptAbility
	
	
	
	
	
} //endof class Firing




?>
