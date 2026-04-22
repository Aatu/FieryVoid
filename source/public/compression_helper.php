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

    // Apply Weak ETag for caching
    header("Etag: W/\"$etag\"");
    header("Cache-Control: private, must-revalidate");
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
