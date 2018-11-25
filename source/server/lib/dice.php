<?php

class Dice
{
    private static $forced = [];

    public static function d($max, $times = 1)
    {
        if (count(self::$forced) > 0) {
            return array_pop(self::$forced);
        }

        $total = 0;

        for ($i = 0; $i < $times; $i++) {
            $total += mt_rand(1, $max);
        }

        return $total;
    }

    public static function forceForTest($list)
    {
        self::$forced = $list;
    }

}
