<?php

class Debug
{
    /**
     * Per-request memo of exceptions already logged, mapping the exception object to
     * the log id it was given.
     *
     * WHY THIS EXISTS
     * ---------------
     * The *Manager classes latch a failed database connect and rethrow the identical
     * exception object for every later call in the same request (see
     * Manager::initDBManager). chatdata.php calls into ChatManager once per requested
     * chat -- up to 8 -- so without this, one dead-database poll writes up to eight
     * copies of the same trace, each with the full REQUEST and SESSION context, to
     * fieryvoid.log. That is disk I/O amplification at exactly the moment the server
     * is already in trouble.
     *
     * Deduping here rather than at the ~25 individual catch sites keeps the change in
     * one place, and every caller still gets a usable log id back -- the same one, so
     * the several error responses a client receives all point at the single logged
     * frame instead of at seven redundant ones.
     *
     * Keyed on object identity, so two genuinely separate failures that happen to
     * carry the same message are still logged separately. A PHP static, so it dies
     * with the request, like the latch it serves.
     */
    private static $loggedExceptions = null;

    public static function log($msg)
    {
        return self::doLog($msg);
    }

    /**
     * Log an exception or error
     * @param Throwable|Exception $e
     */
    public static function error($e)
    {
        if (!$e instanceof Throwable && !$e instanceof Exception) {
            return self::log("Debug::error called with non-exception: " . var_export($e, true));
        }

        if (self::$loggedExceptions === null) {
            self::$loggedExceptions = new SplObjectStorage();
        }

        // offsetExists/offsetGet rather than contains()/[] — contains() is deprecated as
        // of PHP 8.5, and the array syntax on an object key trips static analysers.
        if (self::$loggedExceptions->offsetExists($e)) {
            return self::$loggedExceptions->offsetGet($e);
        }

        $msg = "\nEXCEPTION: " . get_class($e);
        $msg .= "\nMESSAGE: " .$e->getMessage();
        $msg .= "\nCODE: " . $e->getCode();
        $msg .= "\nFILE: " . $e->getFile() . " (" . $e->getLine() . ")";
        $msg .= "\nTRACE: " . $e->getTraceAsString();

        // Walk the cause chain. Without this, wrapping an exception to give it a stable
        // code — as ChatManager/Manager/HelpManager do to restore DBManager's code 300
        // "database unreachable" marker — would replace the only record of what actually
        // went wrong with a generic label. The wrapper carries the code; the chain
        // carries the diagnosis, e.g. the real "(HY000/1040): Too many connections".
        // Bounded, because a cyclic or absurdly deep chain must not be able to fill a log.
        $previous = $e->getPrevious();
        for ($depth = 1; $previous !== null && $depth <= 5; $depth++) {
            $msg .= "\nCAUSED BY [$depth]: " . get_class($previous) . ": " . $previous->getMessage()
                 .  " @ " . $previous->getFile() . " (" . $previous->getLine() . ")";
            $previous = $previous->getPrevious();
        }

        $logid = self::doLog($msg);
        self::$loggedExceptions->attach($e, $logid);

        return $logid;
    }
    
    private static function doLog($msg)
    {
        try {
            $date = date('Y-m-d H:i:s');
            $UID = uniqid();
            
            // Gather context
            $method = $_SERVER['REQUEST_METHOD'] ?? 'CLI';
            $uri = $_SERVER['REQUEST_URI'] ?? 'N/A';
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            
            $context = "\n--- CONTEXT ---";
            $context .= "\nMETHOD: $method";
            $context .= "\nURI: $uri";
            $context .= "\nIP: $ip";
            
            if (!empty($_REQUEST)) {
                $context .= "\nREQUEST: " . json_encode($_REQUEST, JSON_PARTIAL_OUTPUT_ON_ERROR);
            }
            
            if (isset($_SESSION) && !empty($_SESSION)) {
                $context .= "\nSESSION: " . json_encode($_SESSION, JSON_PARTIAL_OUTPUT_ON_ERROR);
            }
            $context .= "\n------------\n";

            $fullMsg = "[$UID][$date] $msg$context\n";
            
            // 1. Output to System Log (Docker captures this)
            // We use a more concise format for the console to keep it readable
            $consoleMsg = "FV_DEBUG: [$UID] " . str_replace(array("\n", "\r"), " ", $msg);
            @error_log($consoleMsg);

            // 2. Output to Project Log File (Full context)
            $logDir = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'logs';
            if (!is_dir($logDir)) {
                @mkdir($logDir, 0777, true);
            }
            
            $logFile = $logDir . DIRECTORY_SEPARATOR . 'fieryvoid.log';
            
            if (is_writable($logDir) || (!file_exists($logFile) && is_writable($logDir)) || (file_exists($logFile) && is_writable($logFile))) {
                @file_put_contents($logFile, $fullMsg, FILE_APPEND);
            }
            
            return $UID;
        } catch (Throwable $t) {
            @error_log("Debug::doLog fatal failure: " . $t->getMessage());
            return "LOG_ERROR";
        }
    }
}