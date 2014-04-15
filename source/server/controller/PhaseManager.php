<?php
/**
 * Description of PhaseManager
 *
 * @author jvanrosmalen
 */
class PhaseManager {
    const PRE_BATTLE = 0.0;
    
    const INITIAL_ACTION = 1.0;
    
    const MOVEMENT = 2.0;
    
    const WEAPONS_FIRE = 3.0;
    const WEAPONS_BALLISTIC = 4.0;
    const WEAPONS_SHIPS = 5.0;
    const WEAPONS_FIGHTER_TO_FIGHTER = 6.0;
    const WEAPONS_FIGHTER_TO_SHIPS = 7.0;
    
    const POST_TURN_ACTIONS = 8.0;
    
    private static $phase_sequence = [
        PRE_BATTLE => [INITIAL_ACTION, true],
        INITIAL_ACTION => [MOVEMENT, true],
        MOVEMENT => [WEAPONS_FIRE, false],
        WEAPONS_FIRE => [POST_TURN_ACTIONS, true],
        POST_TURN_ACTIONS => [INITIAL_ACTIONS, false],
    ];
    
    private static $current_phase = PRE_BATTLE;
    
    public static function advancePhase(){
        $current_phase = $phase_sequence[$current_phase];
    }
    
    public static function getCurrentPhase(){
        return self::$current_phase;
    }
}

?>
