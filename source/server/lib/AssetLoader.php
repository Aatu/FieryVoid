<?php
class AssetLoader {
    /**
     * Appends a filemtime-based version string to the asset URL
     * to ensure immediate updates and enable long-term caching.
     * 
     * @param string $path The path relative to the 'public' directory
     * @return string The versioned URL
     */
    public static function getAssetUrl($path) {
        // Find the public directory relative to this file
        // This file: server/lib/AssetLoader.php
        // Root: server/lib/../../
        $publicDir = dirname(__DIR__, 2) . '/public/';
        $fullPath = $publicDir . $path;

        if (file_exists($fullPath)) {
            $version = filemtime($fullPath);
            return $path . '?v=' . $version;
        }

        return $path;
    }
}
