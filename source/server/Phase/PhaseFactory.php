<?php
/**
 * Created by PhpStorm.
 * User: aatur
 * Date: 09/01/2018
 * Time: 0.25
 */

class PhaseFactory
{
    public static function get($phase) {
        switch($phase) {
            case -2:
                return new BuyingGamePhase();
            default:
                throw new Exception("Unrecognized phase '$phase'");
        }
    }
}