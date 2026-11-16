<?php
/**
 * Feature flags: is a part-built feature reachable on THIS server?
 *
 * Read from $feature_flags in varconfig.php (per environment, like the DB settings):
 *
 *     $feature_flags = array(
 *         'cpuOpponent' => true,                        // on for everyone
 *         'campaign'    => false,                       // off (the same as leaving it out)
 *     );
 *     $feature_beta_testers = array(
 *         'cpuOpponent' => array(12, 345),              // player ids who see it while the flag is off
 *     );
 *
 * ABSENT OR FALSE = OFF. A server whose varconfig has never heard of a flag has it off, so the
 * live server stays dark with no action needed and code for an unfinished feature can ride along
 * in routine patches (TACTICAL_AI_PLAN.md section 2, CAMPAIGN_PLAN.md section 8).
 *
 * Two questions, deliberately separate:
 *   enabled($name)            - is the flag on for the whole server?
 *   enabledFor($name, $id)    - is it on for this player (the flag, or the beta list)?
 *   reachable($name)          - is it on for anybody at all (the flag, or a non-empty beta list)?
 *                               What server-side machinery asks, where there is no "viewer".
 */
class Features
{
    /* Harness-only overrides, keyed by flag name. CLI harnesses do not load varconfig.php (global.php
       does, and the load guard makes that unusable on the CLI), so they say what they want here. */
    private static $overrides = array();

    public static function enabled($name)
    {
        if (array_key_exists($name, self::$overrides)) return self::$overrides[$name];

        global $feature_flags;
        return isset($feature_flags) && is_array($feature_flags) && !empty($feature_flags[$name]);
    }

    public static function enabledFor($name, $playerid)
    {
        if (self::enabled($name)) return true;
        if (array_key_exists($name, self::$overrides)) return false; //an override of false means off, beta list or not
        if ($playerid === null || !is_numeric($playerid)) return false;

        return in_array((int)$playerid, self::betaTesters($name), true);
    }

    public static function reachable($name)
    {
        if (self::enabled($name)) return true;
        if (array_key_exists($name, self::$overrides)) return false;

        return count(self::betaTesters($name)) > 0;
    }

    private static function betaTesters($name)
    {
        global $feature_beta_testers;
        if (!isset($feature_beta_testers) || !is_array($feature_beta_testers)) return array();
        if (empty($feature_beta_testers[$name]) || !is_array($feature_beta_testers[$name])) return array();

        return array_map('intval', $feature_beta_testers[$name]);
    }

    /** Harness use only. Pass null to remove the override. */
    public static function override($name, $value)
    {
        if ($value === null) unset(self::$overrides[$name]);
        else self::$overrides[$name] = (bool)$value;
    }
}
