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

    /**
     * A short token identifying the currently-deployed code version.
     *
     * Derived from the mtime of the game JS bundle, which is rebuilt on every
     * patch/deploy. Use it to scope server-side caches (e.g. APCu gamedata JSON)
     * so a deploy automatically orphans entries produced by the previous code —
     * otherwise stale, old-shape JSON can survive a patch and be served to
     * clients running the new bundle (mismatched system ids/counts, etc.).
     *
     * Cached in a static so repeated calls within a request don't re-stat.
     *
     * @return string e.g. "v1718900000" (or "v0" if the bundle isn't found)
     */
    public static function getDeployVersion() {
        static $deployVersion = null;
        if ($deployVersion !== null) {
            return $deployVersion;
        }

        $publicDir = dirname(__DIR__, 2) . '/public/';
        // Prefer the game bundle; fall back to the lobby bundle if absent.
        $candidates = [
            'client/game.legacy.bundle.js',
            'client/gamelobby.legacy.bundle.js',
        ];

        $mtime = 0;
        foreach ($candidates as $rel) {
            $full = $publicDir . $rel;
            if (file_exists($full)) {
                $mtime = filemtime($full);
                break;
            }
        }

        $deployVersion = 'v' . $mtime;
        return $deployVersion;
    }
}
