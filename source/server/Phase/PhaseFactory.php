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
            case -1:
                return new DeploymentGamePhase();
            case 1:
                return new InitialOrdersGamePhase();
            case 2:
                return new MovementGamePhase();
            default:
                throw new Exception("Unrecognized phase '$phase'");
        }
    }
}