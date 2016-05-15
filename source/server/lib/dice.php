<?php
require_once dirname(__DIR__) . '/server/lib/random_compat-2.0.2/lib/random.php';
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
