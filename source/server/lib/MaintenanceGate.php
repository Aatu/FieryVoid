<?php
/**
 * MaintenanceGate — one shared access check for the web-runnable maintenance tools.
 *
 * WHY THIS FILE EXISTS
 * generateStaticShipFileWeb.php and mass_optimizer.php are both reachable as plain URLs on
 * the live site, and until 2026-08 neither checked anything at all. Anyone — or any crawler
 * that found the URL — could start a multi-minute job that instantiates ~2700 ships or
 * re-encodes ~2500 images. That is a free DoS against a shared-hosting account, and it is
 * exactly the traffic pattern that gets a host's abuse detection to start returning 403 to
 * the whole IP.
 *
 * It lives in ONE file rather than being pasted into each tool for the same reason
 * ShipCompactor does: two copies of a rule drift, and a gate that drifts is a gate that is
 * open somewhere. Not autoloaded — source/autoload.php is a generated classmap marked "do
 * not edit" — so callers require_once it explicitly, the same way global.php does with
 * AssetLoader.
 *
 * CONTRACT FOR CALLERS
 * varconfig.php must already be loaded when requireAccess() runs, because that is where the
 * key lives. This class deliberately does NOT include varconfig itself: a require/require_once
 * of it from inside a function would register it as "already included", and global.php's own
 * require_once would then silently skip it, leaving $database_name and friends undefined and
 * breaking the entire app. So either call this AFTER global.php (which loads varconfig), or
 * require varconfig at file scope first (what mass_optimizer.php does — it has no global.php).
 */
class MaintenanceGate
{
    /** Session flag set once a valid key has been presented, so follow-up AJAX calls
     *  (which cannot carry the ?key= that was on the original page URL) still pass. */
    const SESSION_FLAG = 'fv_maintenance_ok';

    /**
     * The session flag is namespaced PER INSTALL, not shared.
     *
     * /game/ and /testInstance/ are two separate deployments on ONE domain, so they share a
     * session cookie (session.cookie_path defaults to '/') and, on this host, very likely the
     * same session store. With a single flat flag, unlocking one would silently unlock the
     * other — and setting a different key on each server, which is the whole point of having
     * two, would buy nothing. Same path-based isolation idiom server_load_guard.php uses for
     * its APCu keys.
     *
     * __DIR__ is <root>/source/server/lib, so three levels up is the install root.
     */
    private static function sessionFlag(): string
    {
        return self::SESSION_FLAG . '_' . substr(md5(dirname(__DIR__, 3)), 0, 8);
    }

    /**
     * Allow the request through, or emit a refusal page and exit.
     *
     * CLI is always allowed: these tools are run from the console by fvbuild.ps1 and by
     * `docker exec ... php generateStaticShipFile.php`, and a console script is not the
     * thing being defended against. Checked first so that nothing below can ever break
     * the local build.
     *
     * @param string $toolName Human-readable name, shown on the refusal page.
     */
    public static function requireAccess(string $toolName): void
    {
        if (PHP_SAPI === 'cli') {
            return;
        }

        $configured = isset($GLOBALS['maintenance_key']) ? trim((string)$GLOBALS['maintenance_key']) : '';

        // No key configured => refuse. This is deliberately secure-by-default rather than
        // open-by-default: a gate that falls open when its key goes missing reopens the
        // exact hole it exists to close, and it would do so silently on the first deploy
        // that shipped a varconfig without the setting. The refusal page below says
        // precisely how to fix it, so the cost of getting this wrong is one edit, not a
        // mystery.
        if ($configured === '') {
            self::refuse($toolName, true);
        }

        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }

        if (!empty($_SESSION[self::sessionFlag()])) {
            return;
        }

        $supplied = isset($_GET['key']) ? (string)$_GET['key'] : '';
        if ($supplied !== '' && hash_equals($configured, $supplied)) {
            $_SESSION[self::sessionFlag()] = true;
            return;
        }

        // Log only a genuine wrong-key ATTEMPT, never a bare anonymous hit — otherwise a
        // single crawler sweeping the directory fills the error log.
        if ($supplied !== '') {
            error_log('MaintenanceGate: rejected bad key for ' . $toolName
                . ' from ' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
        }

        self::refuse($toolName, false);
    }

    /**
     * 404, not 403.
     *
     * Two reasons. First, the usual one: an unauthenticated visitor should not be able to
     * tell a protected endpoint from a nonexistent one. Second, a local one — the live
     * server's own intermittent 403s (LiteSpeed throttling / host WAF) are a thing we
     * actively diagnose, and adding a second, unrelated source of 403 to the same URLs
     * would make that diagnosis ambiguous. A 404 from this gate is unmistakably ours.
     */
    private static function refuse(string $toolName, bool $notConfigured): void
    {
        if (!headers_sent()) {
            header('HTTP/1.1 404 Not Found');
            header('Content-Type: text/html; charset=utf-8');
            header('Cache-Control: no-store'); // never let a proxy cache the refusal
        }

        echo "<!DOCTYPE html><html><head><title>Not Found</title></head>"
           . "<body style=\"font-family:sans-serif;background:#0a161c;color:#eee;padding:30px\">";
        echo '<h2>' . htmlspecialchars($toolName) . '</h2>';

        if ($notConfigured) {
            echo "<p>This tool is locked because no maintenance key is configured on this server.</p>"
               . "<p>Add a line like this to <code>source/server/varconfig.php</code> "
               . "<strong>on this server</strong> (pick your own long random value, and do not "
               . "commit it to the public repo):</p>"
               . "<pre style=\"background:#162a33;padding:12px;border-radius:6px\">"
               . "\$maintenance_key = 'some-long-random-string';</pre>"
               . "<p>Then load this page as "
               . "<code>?key=some-long-random-string</code>.</p>";
        } else {
            echo "<p>Not found.</p>";
        }

        echo "</body></html>";
        exit;
    }
}
