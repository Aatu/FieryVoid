<?php
require_once dirname(__DIR__) . '/lib/random_compat-2.0.2/lib/random.php';
//This is required for random_int support in PHP versions lower than 7.  
//mcrypt needs to be active as well.
    class Dice{

        /* ⭐ ELITE CREW - "ALL WEAPONS DO +1 POINT OF DAMAGE PER DIE, ALTHOUGH NO DIE CAN BE
           GREATER THAN ITS MAXIMUM YIELD (e.g. if you rolled a 9 on a d10 it would be treated as
           a 10, but if a 10 is rolled it would not be improved)."

           THE BONUS HAS TO LIVE HERE, on the die, and not on the damage total. d() returns a SUM -
           Dice::d(6,4) is one number - so a caller cannot tell how many dice went into it, and
           "+1 per die, capped at the die's face" is not expressible downstream: +4 on a 4d6 roll
           overpays every 6 that was already maxed. Every weapon's getDamage() in the tree already
           funnels through this one function, so capping each roll AS IT IS MADE is both the
           correct rule and the only edit that reaches all ~900 getDamage() implementations.

           ⚠ IT IS A STATIC, SO IT MUST BE SET AND CLEARED AROUND ONE CALL AND NOTHING ELSE.
           Weapon::getFinalDamage is the single writer (it saves and restores the previous value in
           a finally block); leave it set and every d() in the request - critical rolls, hangar
           1d6s, escape d20s, the next ship's guns - silently inherits an Elite Crew's bonus. It
           deliberately has no other setter.

           0 on every ordinary request, so an ordinary roll costs one integer comparison. */
        public static $perDieBonus = 0;

        public static function d($max, $times = 1){
            
            $total = 0;
            $bonus = (int)self::$perDieBonus;
            
            for ($i=0;$i<$times;$i++){
                $roll = random_int(1 , $max);
                //min(): the cap is the die's own face value, so a maxed die is never improved.
                if ($bonus > 0) $roll = min($max, $roll + $bonus);
                $total += $roll;
            }
        
        
            return $total;
        }
    
    }
?>
