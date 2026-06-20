"use strict";

window.AssetManager = {
    _isWebpSupported: null,
    _isLocal: null,

    /**
     * Checks if we are running in a local developmental environment.
     */
    isLocal: function() {
        if (this._isLocal !== null) return this._isLocal;
        var host = window.location.hostname;
        this._isLocal = (host === "localhost" || host === "127.0.0.1" || host.indexOf("192.168.") === 0);
        return this._isLocal;
    },

    /**
     * Checks if the browser supports WebP.
     * Caches the result after the first run.
     */
    isWebpSupported: function() {
        if (this._isWebpSupported !== null) return this._isWebpSupported;

        // Skip WebP conversion on local environments to prevent 404 noise 
        // unless explicitly forced by the developer.
        var forceWebp = window.forceWebp || window.location.search.toLowerCase().indexOf('forcewebp') !== -1;
        if (this.isLocal() && !forceWebp) {
            this._isWebpSupported = false;
            return false;
        }

        try {
            var elem = document.createElement('canvas');
            if (!!(elem.getContext && elem.getContext('2d'))) {
                this._isWebpSupported = elem.toDataURL('image/webp').indexOf('data:image/webp') === 0;
            } else {
                this._isWebpSupported = false;
            }
        } catch (e) {
            this._isWebpSupported = false;
        }

        // Initialize global error listener once support is known
        if (this._isWebpSupported && !window._assetManagerInitialized) {
            window.addEventListener('error', function(e) {
                if (e.target.tagName === 'IMG' && e.target.src.indexOf('.webp') !== -1) {
                    console.warn("WebP Load failed, reverting to original:", e.target.src);
                    e.target.src = e.target.src.replace('.webp', '.png');
                }
            }, true);
            window._assetManagerInitialized = true;
        }

        return this._isWebpSupported;
    },

    /**
     * Appends the deploy-version cache-buster to an image URL.
     *
     * Image files are served with a 1-year immutable cache (see public/.htaccess),
     * so when artwork is swapped under the SAME filename (e.g. re-skinned ship
     * images) clients keep serving the stale copy until the cache expires. The JS
     * bundles avoid this via a ?v=<mtime> query; images had no such buster.
     *
     * window.assetVersion is injected by game.php / gamelobby.php from
     * AssetLoader::getDeployVersion() (the game-bundle mtime, which changes on every
     * deploy). Appending it makes every image URL unique per deploy, so a redeploy
     * forces a refetch while still allowing the year-long cache between deploys.
     * Falls back to leaving the path untouched if no version is available.
     */
    appendVersion: function(path) {
        var v = window.assetVersion;
        if (!v) return path;
        return path + (path.indexOf('?') === -1 ? '?' : '&') + 'v=' + v;
    },

    /**
     * Given an image path, returns the WebP version if supported.
     */
    getSmartImagePath: function(path) {
        if (!path || typeof path !== 'string') return path;

        var cleanPath = path.split('?')[0];

        if (this.isWebpSupported()) {
            if (cleanPath.toLowerCase().endsWith('.png')) {
                return this.appendVersion(path.replace('.png', '.webp'));
            } else if (cleanPath.toLowerCase().endsWith('.jpg')) {
                return this.appendVersion(path.replace('.jpg', '.webp'));
            } else if (cleanPath.toLowerCase().endsWith('.jpeg')) {
                return this.appendVersion(path.replace('.jpeg', '.webp'));
            }
        }

        return this.appendVersion(path);
    }
};
