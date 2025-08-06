<?php 

/* //SAFER VERSION DEPENDING ON APACHE SETTINGS
declare(strict_types=1);

// --- Output buffering with gzip if available ---
if (!headers_sent() && !ini_get('zlib.output_compression')) {
    ob_start('ob_gzhandler');
} else {
    ob_start();
}
header('Content-Type: application/json; charset=utf-8');
*/

if (!headers_sent() && !ini_get('zlib.output_compression')) {
    ob_start();
}

header('Content-Type: application/json; charset=utf-8');


// --- Includes ---
require_once 'global.php';
require_once __DIR__ . '/server/lib/Debug.php'; // Ensure debug logging still works

// --- Validate required parameters ---
$gameid = isset($_GET['gameid']) ? (int)$_GET['gameid'] : 0;
$shipid = isset($_GET['shipid']) ? (int)$_GET['shipid'] : 0;
$turn   = isset($_GET['turn'])   ? (int)$_GET['turn']   : 0;

if ($gameid <= 0 || $shipid <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing or invalid gameid/shipid.']);
    ob_end_flush();
    exit;
}

try {
    // --- Database connection ---
    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', 
                   $database_host ?? 'localhost', 
                   $database_name ?? 'B5CGM');

    $link = new PDO($dsn, $database_user ?? 'root', $database_pass ?? '', [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    Debug::log('DB connection successful');

    // --- Prepare and execute query ---
    $stmt = $link->prepare("
        SELECT *
        FROM tac_adaptivearmour
        WHERE gameid = :gameid
          AND shipid = :shipid
    ");
    $stmt->execute([
        ':gameid' => $gameid,
        ':shipid' => $shipid,
    ]);

    $row = $stmt->fetch();

    if ($row) {
        // Optional: Log for debugging
        Debug::log('Adaptive armour row found for game '.$gameid.', ship '.$shipid);
        echo json_encode($row, JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE);
    } else {
        Debug::log('No adaptive armour found for game '.$gameid.', ship '.$shipid);
        echo json_encode(['status' => 'empty']);
    }

} catch (Throwable $e) {
    $logid = Debug::error($e);
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage(),
        'logid' => $logid
    ]);
}

ob_end_flush();
exit;

/* //OLD VERSION


ob_start("ob_gzhandler"); 
    include_once 'global.php';


	$gameid = $_GET["gameid"];
	$shipid = $_GET["shipid"];
	$turn = $_GET["turn"];

	try {
		$link = new PDO("mysql:host=" . ($database_host ?? 'localhost') . ";dbname=B5CGM","root","",
						array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
	}
	catch(PDOException $ex){
	    die(json_encode(array('outcome' => false, 'message' => 'Unable to connect')));
	}

	if ($link){
		debug::log("alive connection");
		$stmt = $link->prepare(
				            "SELECT 
				                *
				            FROM 
				                tac_adaptivearmour
				            WHERE 
				                gameid = :gameid
				            AND
				                shipid = :shipid"
			            );

		$stmt->execute(array(
			":gameid" =>$gameid,
			":shipid" => $shipid
			));


		$row = $stmt->fetch();

		if ($row){
			foreach ($row as $result){
				debug::log($result);
			}
		}
		else {				
			debug::log("ERROR");
		}
	}
	else 
		debug::log("no connection");

*/
/*

        while($stmt->fetch()){
            $value["particle"] = array($particlepoints, $particlealloc);
            $value["laser"] = array($laserpoints, $laseralloc);
            $value["molecular"] = array($molecularpoints, $molecularalloc);
            $value["matter"] = array($matterpoints, $matteralloc);
            $value["plasma"] = array($plasmapoints, $plasmaalloc);
            $value["electromagnetic"] = array($electromagneticpoints, $electromagneticalloc);
            $value["antimatter"] = array($antimatterpoints, $antimatteralloc);
            $value["ion"] = array($ionpoints, $ionalloc);
            $value["gravitic"] = array($graviticpoints, $graviticalloc);
            $value["ballistic"] = array($ballisticpoints, $ballisticalloc);
        }

*/

	
//	print(json_encode($ret));
/*    
?>
*/