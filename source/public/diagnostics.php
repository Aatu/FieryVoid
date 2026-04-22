<?php
/**
 * Server Diagnostics: JSON Size & Compression Check
 * Upload to your public/ folder, visit in browser.
 * Leave file on local server, do not delete.
 * Usage: diagnostics.php              (general info)
 *        diagnostics.php?gameid=XXXX  (measure actual gamedata for a game)
 */

// Diagnostics for Fiery Void Universal Compression
require_once 'global.php';

session_start();
header('Content-Type: text/html; charset=utf-8');

// --- Auth check: only logged-in users ---
if (!isset($_SESSION['user'])) {
    die('<h2 style="color:red">You must be logged in to use this diagnostic.</h2>');
}
$userid = $_SESSION['user'];

echo '<!DOCTYPE html><html><head><title>FV Server Diagnostics</title>';
echo '<style>
    body { background: #1a1a2e; color: #e0e0e0; font-family: monospace; padding: 20px; line-height: 1.6; }
    h2 { color: #00d4ff; border-bottom: 1px solid #333; padding-bottom: 5px; }
    .good { color: #00ff88; } .bad { color: #ff4444; } .warn { color: #ffaa00; }
    table { border-collapse: collapse; margin: 10px 0; }
    td, th { border: 1px solid #444; padding: 6px 12px; text-align: left; }
    th { background: #2a2a4e; }
    .size { font-size: 1.2em; font-weight: bold; }
</style></head><body>';
echo '<h1>🔍 FieryVoid Server Diagnostics</h1>';

// ==============================
// SECTION 1: PHP & Extension Info
// ==============================
echo '<h2>1. PHP Environment</h2>';
echo '<table>';
echo '<tr><td>PHP Version</td><td>' . phpversion() . '</td></tr>';
echo '<tr><td>Server Software</td><td>' . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . '</td></tr>';
echo '<tr><td>SAPI</td><td>' . php_sapi_name() . '</td></tr>';

// APCu
$apcuAvailable = function_exists('apcu_fetch');
echo '<tr><td>APCu Extension</td><td class="' . ($apcuAvailable ? 'good' : 'bad') . '">' 
     . ($apcuAvailable ? '✅ Available' : '❌ Not Available') . '</td></tr>';

// Brotli extension
$brotliExt = extension_loaded('brotli');
$brotliFunc = function_exists('brotli_compress');
echo '<tr><td>Brotli PHP Extension</td><td class="' . ($brotliExt ? 'good' : 'warn') . '">' 
     . ($brotliExt ? '✅ Loaded' : '⚠️ Not loaded') . '</td></tr>';
echo '<tr><td>brotli_compress() function</td><td class="' . ($brotliFunc ? 'good' : 'warn') . '">' 
     . ($brotliFunc ? '✅ Available' : '⚠️ Not available') . '</td></tr>';

// Gzip
$gzipFunc = function_exists('gzencode');
echo '<tr><td>gzencode() function</td><td class="' . ($gzipFunc ? 'good' : 'bad') . '">' 
     . ($gzipFunc ? '✅ Available' : '❌ Not available') . '</td></tr>';

// Check loaded Apache modules (may not work on all configs)
$apacheModules = function_exists('apache_get_modules') ? apache_get_modules() : [];
$hasModDeflate = in_array('mod_deflate', $apacheModules);
$hasModBrotli = in_array('mod_brotli', $apacheModules);
if (!empty($apacheModules)) {
    echo '<tr><td>mod_deflate (Apache)</td><td class="' . ($hasModDeflate ? 'good' : 'warn') . '">' 
         . ($hasModDeflate ? '✅ Loaded' : '⚠️ Not detected') . '</td></tr>';
    echo '<tr><td>mod_brotli (Apache)</td><td class="' . ($hasModBrotli ? 'good' : 'warn') . '">' 
         . ($hasModBrotli ? '✅ Loaded' : '⚠️ Not detected') . '</td></tr>';
} else {
    echo '<tr><td>Apache Modules</td><td class="warn">⚠️ Cannot enumerate (apache_get_modules not available)</td></tr>';
}

echo '</table>';

// ==============================
// SECTION 2: Compression Test on Dynamic Content
// ==============================
echo '<h2>2. Compression Test (Dynamic PHP Output)</h2>';
echo '<p>Testing what <code>Accept-Encoding</code> your browser sent with THIS request:</p>';

$acceptEnc = $_SERVER['HTTP_ACCEPT_ENCODING'] ?? 'none';
echo '<table>';
echo '<tr><td>Accept-Encoding header</td><td><code>' . htmlspecialchars($acceptEnc) . '</code></td></tr>';

$supportsBr = (strpos($acceptEnc, 'br') !== false);
$supportsGzip = (strpos($acceptEnc, 'gzip') !== false);
echo '<tr><td>Browser supports Brotli?</td><td class="' . ($supportsBr ? 'good' : 'warn') . '">' 
     . ($supportsBr ? '✅ Yes' : '⚠️ No') . '</td></tr>';
echo '<tr><td>Browser supports Gzip?</td><td class="' . ($supportsGzip ? 'good' : 'warn') . '">' 
     . ($supportsGzip ? '✅ Yes' : '⚠️ No') . '</td></tr>';
echo '</table>';

echo '<p><strong>To check if Apache is actually compressing this dynamic PHP response:</strong></p>';
echo '<ol>';
echo '<li>Open DevTools (F12) → Network tab</li>';
echo '<li>Refresh this page</li>';
echo '<li>Click on the <code>diagnostics.php</code> request</li>';
echo '<li>Check the <strong>Response Headers</strong> for: <code>Content-Encoding: br</code> or <code>Content-Encoding: gzip</code></li>';
echo '<li>Compare <strong>"transferred"</strong> size vs <strong>"size"</strong> in the Network list</li>';
echo '</ol>';

// ==============================
// SECTION 3: Actual Gamedata Size Test
// ==============================
$gameid = isset($_GET['gameid']) ? (int)$_GET['gameid'] : 0;

if ($gameid > 0) {
    echo '<h2>3. Gamedata JSON Size Analysis — Game #' . $gameid . '</h2>';
    
    // Load the Manager
    try {
        require_once dirname(__DIR__) . '/server/varconfig.php';
        require_once 'global.php';
        
        $json = Manager::getTacGamedataJSON($gameid, $userid, 0, 0, -1, true);
        
        if (!$json || $json === '{}') {
            echo '<p class="bad">❌ No gamedata returned (empty or no access to game #' . $gameid . ')</p>';
} else {
            $rawBytes = strlen($json);
            
            // Decode to analyze structure
            $data = json_decode($json);
            $isError = isset($data->error);
            
            if ($isError) {
                echo '<p class="bad">❌ Server returned error: ' . htmlspecialchars($data->error) . '</p>';
    } else {
                // Compression tests
                $gzipped = $gzipFunc ? gzencode($json, 9) : null;
                $brotlied = $brotliFunc ? brotli_compress($json, 11) : null;
                $gzipBytes = $gzipped ? strlen($gzipped) : null;
                $brotliBytes = $brotlied ? strlen($brotlied) : null;
                
                echo '<table>';
                echo '<tr><th>Metric</th><th>Value</th></tr>';
                echo '<tr><td>Raw JSON size</td><td class="size">' . number_format($rawBytes) . ' bytes (' . round($rawBytes/1024, 1) . ' KB)</td></tr>';
                if ($gzipBytes) {
                    $gzipRatio = round((1 - $gzipBytes/$rawBytes) * 100, 1);
                    echo '<tr><td>Gzip compressed (level 9)</td><td class="size">' . number_format($gzipBytes) . ' bytes (' . round($gzipBytes/1024, 1) . ' KB) — ' . $gzipRatio . '% reduction</td></tr>';
                }
                if ($brotliBytes) {
                    $brRatio = round((1 - $brotliBytes/$rawBytes) * 100, 1);
                    echo '<tr><td>Brotli compressed (level 11)</td><td class="size">' . number_format($brotliBytes) . ' bytes (' . round($brotliBytes/1024, 1) . ' KB) — ' . $brRatio . '% reduction</td></tr>';
                }
                echo '</table>';
                
                // Breakdown by component
                echo '<h3>Payload Breakdown</h3>';
                echo '<table>';
                echo '<tr><th>Component</th><th>Size (bytes)</th><th>% of Total</th></tr>';
                
                $shipCount = 0;
                $totalShipBytes = 0;
                $totalMovementBytes = 0;
                $totalSystemBytes = 0;
                $totalDamageBytes = 0;
                $totalFireOrderBytes = 0;
                $totalEWBytes = 0;
                
                if (isset($data->ships) && is_array($data->ships)) {
                    $shipCount = count($data->ships);
                    foreach ($data->ships as $ship) {
                        $shipJson = json_encode($ship, JSON_NUMERIC_CHECK);
                        $totalShipBytes += strlen($shipJson);
                        
                        if (isset($ship->movement)) {
                            $totalMovementBytes += strlen(json_encode($ship->movement, JSON_NUMERIC_CHECK));
                        }
                        if (isset($ship->EW)) {
                            $totalEWBytes += strlen(json_encode($ship->EW, JSON_NUMERIC_CHECK));
                        }
                        if (isset($ship->systems) && is_array($ship->systems)) {
                            foreach ($ship->systems as $sys) {
                                $sysJson = json_encode($sys, JSON_NUMERIC_CHECK);
                                $totalSystemBytes += strlen($sysJson);
                                
                                if (isset($sys->damage)) {
                                    $totalDamageBytes += strlen(json_encode($sys->damage, JSON_NUMERIC_CHECK));
                                }
                                if (isset($sys->fireOrders)) {
                                    $totalFireOrderBytes += strlen(json_encode($sys->fireOrders, JSON_NUMERIC_CHECK));
                                }
                            }
                        }
                    }
                }
                
                $nonShipBytes = $rawBytes - $totalShipBytes;
                
                $pct = function($bytes) use ($rawBytes) {
                    return $rawBytes > 0 ? round(($bytes / $rawBytes) * 100, 1) : 0;
                };
                
                echo '<tr><td>Ships (' . $shipCount . ' total)</td><td>' . number_format($totalShipBytes) . '</td><td>' . $pct($totalShipBytes) . '%</td></tr>';
                echo '<tr><td>&nbsp;&nbsp;└─ Systems (all ships)</td><td>' . number_format($totalSystemBytes) . '</td><td>' . $pct($totalSystemBytes) . '%</td></tr>';
                echo '<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;└─ Fire Orders</td><td>' . number_format($totalFireOrderBytes) . '</td><td>' . $pct($totalFireOrderBytes) . '%</td></tr>';
                echo '<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;└─ Damage Entries</td><td>' . number_format($totalDamageBytes) . '</td><td>' . $pct($totalDamageBytes) . '%</td></tr>';
                echo '<tr><td>&nbsp;&nbsp;└─ Movement</td><td>' . number_format($totalMovementBytes) . '</td><td>' . $pct($totalMovementBytes) . '%</td></tr>';
                echo '<tr><td>&nbsp;&nbsp;└─ EW</td><td>' . number_format($totalEWBytes) . '</td><td>' . $pct($totalEWBytes) . '%</td></tr>';
                echo '<tr><td>Game metadata (slots, rules, etc.)</td><td>' . number_format($nonShipBytes) . '</td><td>' . $pct($nonShipBytes) . '%</td></tr>';
                echo '<tr style="background:#2a2a4e"><td><strong>TOTAL</strong></td><td><strong>' . number_format($rawBytes) . '</strong></td><td><strong>100%</strong></td></tr>';
                echo '</table>';
                
                // Empty array analysis
                echo '<h3>Empty Array Analysis</h3>';
                $emptyArrayCount = 0;
                $emptyArrayBytes = 0;
                $emptyStringCount = 0;
                $emptyArrayKeys = [];
                $emptyStringKeys = [];
                
                $countProps = function($obj) use (&$emptyArrayCount, &$emptyArrayBytes, &$emptyStringCount, &$emptyArrayKeys, &$emptyStringKeys, &$countProps) {
                    if (is_array($obj)) {
                        if (empty($obj)) { 
                            $emptyArrayCount++; 
                            $emptyArrayBytes += 2; 
                        }
                        foreach ($obj as $v) $countProps($v);
                    } elseif (is_object($obj)) {
                        foreach (get_object_vars($obj) as $k => $v) {
                            if (is_array($v) && empty($v)) {
                                $emptyArrayCount++; 
                                $emptyArrayBytes += strlen('"' . $k . '":[]') + 1;
                                if (!isset($emptyArrayKeys[$k])) $emptyArrayKeys[$k] = 0;
                                $emptyArrayKeys[$k]++;
                            }
                            if (is_string($v) && $v === '') {
                                $emptyStringCount++;
                                if (!isset($emptyStringKeys[$k])) $emptyStringKeys[$k] = 0;
                                $emptyStringKeys[$k]++;
                            }
                            $countProps($v);
                        }
                    }
                };
                
                $countProps($data);
                echo '<p>Found <strong>' . $emptyArrayCount . '</strong> empty arrays taking ~<strong>' . number_format($emptyArrayBytes) . ' bytes</strong> of JSON space.</p>';
                
                if (!empty($emptyArrayKeys)) {
                    arsort($emptyArrayKeys);
                    echo '<h4>Empty Array Details</h4>';
                    echo '<table><tr><th>Key Name</th><th>Occurrence Count</th></tr>';
                    foreach ($emptyArrayKeys as $k => $count) {
                        echo "<tr><td>$k</td><td>$count</td></tr>";
                    }
                    echo '</table>';
                }

                echo '<p>Found <strong>' . $emptyStringCount . '</strong> empty strings (<code>""</code>).</p>';
                if (!empty($emptyStringKeys)) {
                    arsort($emptyStringKeys);
                    echo '<h4>Empty String Details</h4>';
                    echo '<table><tr><th>Key Name</th><th>Occurrence Count</th></tr>';
                    foreach ($emptyStringKeys as $k => $count) {
                        echo "<tr><td>$k</td><td>$count</td></tr>";
                    }
                    echo '</table>';
                }
            }
        }
    } catch (Exception $e) {
        echo '<p class="bad">❌ Error loading gamedata: ' . htmlspecialchars($e->getMessage()) . '</p>';
    }
} else {
    echo '<h2>3. Gamedata Size Test</h2>';
    echo '<p>Add <code>?gameid=XXXX</code> to the URL to measure a specific game\'s JSON payload.</p>';
    echo '<p>Example: <code>diagnostics.php?gameid=3750</code></p>';
}

// ==============================
// SECTION 4: JavaScript test snippet
// ==============================
echo '<h2>4. Browser Console Test (Copy & Paste)</h2>';
echo '<p>Paste this in your browser console while on a game page to see live transfer sizes:</p>';
echo '<pre style="background:#0d0d1a; padding:15px; border:1px solid #444; overflow-x:auto; font-size:12px;">';
echo htmlspecialchars('
// Paste in browser console on any game page
(function() {
    const url = `gamedata.php?gameid=${gamedata.gameid}&turn=${gamedata.turn}&phase=${gamedata.gamephase}&activeship=${gamedata.activeship}&playerid=${gamedata.thisplayer}&last_time=0&time=${Date.now()}`;
    
    fetch(url).then(r => {
        const encoding = r.headers.get("content-encoding") || "none";
        const transferSize = performance.getEntriesByType("resource")
            .filter(e => e.name.includes("gamedata.php"))
            .pop();
        
        return r.text().then(text => {
            console.log("=== GAMEDATA SIZE REPORT ===");
            console.log("Content-Encoding:", encoding);
            console.log("Raw JSON size:", (text.length / 1024).toFixed(1), "KB");
            console.log("Transfer size:", transferSize ? (transferSize.transferSize / 1024).toFixed(1) + " KB" : "check Network tab");
            console.log("Compression ratio:", transferSize ? ((1 - transferSize.transferSize / text.length) * 100).toFixed(1) + "%" : "N/A");
            
            const data = JSON.parse(text);
            if (data.ships) {
                console.log("Ship count:", data.ships.length);
                let totalSystems = 0;
                data.ships.forEach(s => { if(s.systems) totalSystems += s.systems.length; });
                console.log("Total systems:", totalSystems);
            }
            console.log("Full JSON:", text.length, "chars");
        });
    });
})();
');
echo '</pre>';

echo '<h2>5. curl Test Commands</h2>';
echo '<p>Run these from your local terminal to test compression on the live server:</p>';
echo '<pre style="background:#0d0d1a; padding:15px; border:1px solid #444; overflow-x:auto; font-size:12px;">';
echo htmlspecialchars('
# Test WITHOUT compression (raw size)
curl -s -o /dev/null -w "Raw size: %{size_download} bytes\\n" "https://YOURSITE/gamedata.php?gameid=XXXX&turn=1&phase=0&activeship=-1&playerid=1&last_time=0" --cookie "PHPSESSID=YOUR_SESSION_ID"

# Test WITH gzip
curl -s -H "Accept-Encoding: gzip" -o /dev/null -w "Gzip transfer: %{size_download} bytes\\n" "https://YOURSITE/gamedata.php?gameid=XXXX&turn=1&phase=0&activeship=-1&playerid=1&last_time=0" --cookie "PHPSESSID=YOUR_SESSION_ID"

# Test WITH brotli (requires curl 7.57+)
curl -s -H "Accept-Encoding: br" -o /dev/null -w "Brotli transfer: %{size_download} bytes\\n" "https://YOURSITE/gamedata.php?gameid=XXXX&turn=1&phase=0&activeship=-1&playerid=1&last_time=0" --cookie "PHPSESSID=YOUR_SESSION_ID"

# Show response headers (check for Content-Encoding)
curl -s -D - -o /dev/null -H "Accept-Encoding: br, gzip" "https://YOURSITE/gamedata.php?gameid=XXXX&turn=1&phase=0&activeship=-1&playerid=1&last_time=0" --cookie "PHPSESSID=YOUR_SESSION_ID"
');
echo '</pre>';

echo '<hr><p class="warn">⚠️ <strong>IMPORTANT:</strong> Delete this file from your server after testing! It exposes server configuration details.</p>';
echo '</body></html>';
?>
