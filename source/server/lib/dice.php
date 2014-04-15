<?php
    
    class Dice{

        public static function d($max, $times = 1){
            
            $total = 0;
            
            for ($i=0;$i<$times;$i++){
                $total += mt_rand ( 1 , $max );
            }
        
        
            return $total;
        }
    
    }
?>
