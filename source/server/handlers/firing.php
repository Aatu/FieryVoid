<?php

/*Marcin Sawicki problems during debug: copuying Firing class method after method*/
/*old version moved to .old file*/

class Firing{
    public $gamedata;
	
    public static function validateFireOrders($fireOrders, $gamedata)){
throw new Exception("firing debug 10 validateFireOrders");
            return true;
    }
	
    public static function firingExists(){	//just a test function
	return true;    
    }	
	
	
	
	
	
} //endof class Firing




?>
