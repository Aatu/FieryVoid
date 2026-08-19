<?php
/**
 * Universal Output Compression Handler for Fiery Void
 * 
 * This script is registered as a shutdown function to automatically
 * apply Brotli or Gzip compression to the output buffer if supported.
 */

function fv_compress_output() {
    $content = ob_get_clean();
    if ($content === false) return;
    
    $acceptEncoding = $_SERVER['HTTP_ACCEPT_ENCODING'] ?? '';
    $etag = md5($content);

    /*
     * Weak ETag. (Weak because a proxy that recompresses changes the bytes but not the
     * meaning, which is what a strong ETag would be asserting.)
     *
     * KNOWN DEAD ON LIVE — kept deliberately. Confirmed 2026-04-04 (commit 02a09181,
     * "confirm ETag not available on our shared hosting"): the shared host's proxy
     * STRIPS ETag, so no browser ever receives one, never sends If-None-Match, and the
     * 304 branch below cannot fire. The logic stays so it starts working by itself if
     * the hosting config ever changes. This note was lost in a comment tidy-up a week
     * later (69f0280f) and re-derived from scratch in 2026-08; leave it in place.
     *
     * Cost of keeping it: md5() over the whole response, measured at 0.36 ms for a
     * 250 KB page and 4.3 ms for 3 MB. Small, and the alternative — deleting it — would
     * silently turn any 304 into a full transfer the day the proxy behaviour changes.
     */
    header("Etag: W/\"$etag\"");

    /*
     * Supply a DEFAULT Cache-Control — never overwrite one the page set for itself.
     *
     * header() replaces a header of the same name, and this function runs as a shutdown
     * handler: after every line of the page that registered it. An unconditional header()
     * here is therefore always the last word, and it silently undid two deliberate — and
     * opposite — decisions made elsewhere in the app:
     *
     *   - game.php, games.php and gamelobby.php send "no-store" so that a personalised,
     *     per-player HTML document is never written to the browser's disk cache. That is
     *     the root fix for stale pages reappearing on session restore after a browser or
     *     machine restart (see the polling/cache notes). Replacing it with a header that
     *     PERMITS storage, and that carries no max-age or Expires to judge staleness by,
     *     hands the decision back to per-browser heuristics — precisely what that fix
     *     existed to take away.
     *
     *   - gamelobbyloader.php sends "public, max-age=31536000, immutable" for VERSIONED
     *     ship-data URLs, which cannot go stale by construction (the ?v= changes when the
     *     data does). Replacing it with must-revalidate forces a revalidation round trip
     *     on every lobby load for data specifically designed to be served straight from
     *     disk — the opposite of that file's stated intent.
     *
     * Pages that express no opinion are unaffected and still get the default below.
     */
    $hasCacheControl = false;
    foreach (headers_list() as $sentHeader) {
        if (stripos($sentHeader, 'Cache-Control:') === 0) { $hasCacheControl = true; break; }
    }
    if (!$hasCacheControl) {
        header("Cache-Control: private, must-revalidate");
    }

    header('X-Accel-Buffering: no');

    // Handle 304 Not Modified
    $ifNoneMatch = $_SERVER['HTTP_IF_NONE_MATCH'] ?? '';
    if ($ifNoneMatch && (trim($ifNoneMatch) === "\"$etag\"" || trim($ifNoneMatch) === "W/\"$etag\"")) {
        header("HTTP/1.1 304 Not Modified");
        exit;
    }

    // Check if we already sent a compression header or if content is too small
    $existingHeaders = headers_list();
    $alreadyCompressed = false;
    $isJson = false;

    foreach ($existingHeaders as $header) {
        if (stripos($header, 'Content-Encoding') !== false) $alreadyCompressed = true;
        if (stripos($header, 'application/json') !== false) $isJson = true;
    }

    $threshold = $isJson ? 256 : 1024;

    if ($alreadyCompressed || strlen($content) < $threshold || headers_sent()) {
        echo $content;
        return;
    }

    // BROTLI (Highest Priority)
    if (strpos($acceptEncoding, 'br') !== false && function_exists('brotli_compress')) {
        header('Content-Encoding: br');
        header('Vary: Accept-Encoding');
        
        // Defensive headers for specific server setups
        header('X-LiteSpeed-No-Gzip: 1');
        header('X-LSCompress: 0');
        if (function_exists('apache_setenv')) {
            apache_setenv('no-gzip', '1');
        }
        
        $compressed = brotli_compress($content, 4); 
        header('Content-Length: ' . strlen($compressed));
        echo $compressed;
        return;
    }

    // GZIP (Fallback)
    if (strpos($acceptEncoding, 'gzip') !== false && function_exists('gzencode')) {
        header('Content-Encoding: gzip');
        header('Vary: Accept-Encoding');
        $compressed = gzencode($content, 6);
        header('Content-Length: ' . strlen($compressed));
        echo $compressed;
        return;
    }

    // No compression fallback
    echo $content;
}
?>
