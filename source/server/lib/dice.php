<?php
require_once dirname(__DIR__) . '/lib/random_compat-2.0.2/lib/random.php';
//This is required for random_int support in PHP versions lower than 7.  
//mcrypt needs to be active as well.
    class Dice{

        public static function d($max, $times = 1){
            
            $total = 0;
            
            for ($i=0;$i<$times;$i++){
                $total += random_int(1 , $max);
            }
        
        
            return $total;
        }
    
    }
?>
