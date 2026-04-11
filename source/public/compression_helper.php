<?php
/**
 * Universal Output Compression Handler for Fiery Void
 * 
 * This script is registered as a shutdown function to automatically
 * apply Brotli or Gzip compression to the output buffer if supported.
 * 
 * Triggered via global.php
 */

function fv_compress_output() {
    // Only capture if buffering was started
    $content = ob_get_clean();
    if ($content === false) return;

    // Apply Weak ETag for caching (Nginx often strips strong ETags when compression is active)
    // NOTE: On this specific server, ETags are stripped by the proxy, but we keep the logic 
    // for compatibility if the server config changes in the future.
    $etag = md5($content);
    
    header_remove('Pragma');
    header_remove('Expires');
    
    header("Etag: W/\"$etag\"");
    header("Cache-Control: private, must-revalidate");

    $ifNoneMatch = $_SERVER['HTTP_IF_NONE_MATCH'] ?? '';
    if ($ifNoneMatch && (trim($ifNoneMatch) === "\"$etag\"" || trim($ifNoneMatch) === "W/\"$etag\"")) {
        header("HTTP/1.1 304 Not Modified");
        exit;
    }

    // Check if we already sent a compression header or if it's too small to bother
    $existingHeaders = headers_list();
    $alreadyCompressed = false;
    $isJson = false;

    foreach ($existingHeaders as $header) {
        if (stripos($header, 'Content-Encoding') !== false) $alreadyCompressed = true;
        if (stripos($header, 'X-LiteSpeed-No-Gzip') !== false) $alreadyCompressed = true;
        if (stripos($header, 'application/json') !== false) $isJson = true;
    }

    $threshold = $isJson ? 256 : 1024;

    if ($alreadyCompressed || strlen($content) < $threshold || headers_sent()) {
        echo $content;
        return;
    }

    $acceptEncoding = $_SERVER['HTTP_ACCEPT_ENCODING'] ?? '';

    // BROTLI (Highest Priority)
    if (strpos($acceptEncoding, 'br') !== false && function_exists('brotli_compress')) {
        header('X-Debug-Method: Brotli-Universal');
        header('X-LiteSpeed-No-Gzip: 1');
        header('X-LSCompress: 0');
        
        if (function_exists('apache_setenv')) {
            apache_setenv('no-gzip', '1');
        }
        
        header('Content-Encoding: br');
        header('Vary: Accept-Encoding');
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
