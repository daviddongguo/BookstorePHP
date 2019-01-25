<?php

session_start();

require_once 'vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// create a log channel
$log = new Logger('main');
$log->pushHandler(new StreamHandler('logs/everything.log', Logger::DEBUG));
$log->pushHandler(new StreamHandler('logs/errors.log', Logger::ERROR));

//DB::debugMode();
if (false) {
    DB::$user = 'bootstore';
    DB::$dbName = 'bootstore';
    DB::$password = 'vuxunjqTbm5S7sAq';
    DB::$port = 3333;
    DB::$host = 'localhost';
    DB::$encoding = 'utf8';
    DB::$error_handler = 'db_error_handler';
} else {
    if (true) {
        DB::$user = 'u673935962_book';
        DB::$dbName = 'u673935962_book';
        DB::$password = 'vuxunjqTbm5S7sAq';
        DB::$port = 3306;
        DB::$host = 'localhost';
        DB::$encoding = 'utf8';
        DB::$error_handler = 'db_error_handler';
    } else {
        DB::$user = 'bootstore';
        DB::$dbName = 'bootstore';
        DB::$password = 'vuxunjqTbm5S7sAq';
        DB::$port = 3306;
        DB::$host = 'localhost';
        DB::$encoding = 'utf8';
        DB::$error_handler = 'db_error_handler';
    }
}

function db_error_handler($params) {
    global $app, $log;
    $log->error("SQL error: " . $params['error']);
    $log->error("SQL query: " . $params['query']);
    http_response_code(500);
    $app->render('fatal_error.html.twig');
    die; // don't want to keep going if a query broke
}

$app = new \Slim\Slim();
// not use twig
//$app->response()->header('content-type', 'application/json');
$app->response()->header('content-type', 'application/json');
\Slim\Route::setDefaultConditions(array('id' => '\d+'));


$app->get('/', function() use($app, $log) {

    $todoList = DB::query("SELECT * FROM items");
    echo json_encode($todoList, JSON_PRETTY_PRINT);
});



$app->get('/api/list/:currentPage/:currentBookClass',
        function($currentPage = 1, $currentBookClass = 'xxx')
        use ($app, $log) {
    $pagesize = 5;
    // Totalpages
    if ($currentBookClass == 'xxx') {   // for all book classes
        DB::query("SELECT id FROM items");
    } else {                            // for special classes
        DB::query("SELECT id FROM items "
                . " WHERE DeweyDecimalClass LIKE %s"
                . " ", substr($currentBookClass, 0, 1) . '%%');
    }
    $TotalItems = DB::count();
    $totalpages = (int) (($TotalItems - 1) / $pagesize) + 1;

    // Validate currentPage
    if ($currentPage > $totalpages) {   // Invalid
        $currentPage = $totalpages;
    }


    // Books List
    $offsetItmes = ($pagesize * ($currentPage - 1));                //0:1, 5:2, 10:3
    if ($currentBookClass == 'xxx') {   // for all book classes
        $booksList = DB::query("SELECT * FROM items "
                        . " LIMIT $pagesize OFFSET $offsetItmes");  //0:1, 5:2, 10:3
    } else {                            // for special classes
        $booksList = DB::query("SELECT * FROM items "
                        . " WHERE DeweyDecimalClass LIKE %s"
                        . " LIMIT $pagesize OFFSET $offsetItmes", substr($currentBookClass, 0, 1) . '%%');
    }


    // All DeweyDecimalClass 
    $querStr = "SELECT DISTINCT c.code, c.name "
            . " FROM classes as c "
            . " INNER JOIN items as i ON c.code=i.DeweyDecimalClass";
    $classCodes = DB::query($querStr);

    // CurrentBookClass
    if (is_numeric($currentBookClass) && strlen($currentBookClass) > 0) {
        $currentBookClass = substr($currentBookClass, 0, 3);
    } else {
        $currentBookClass = 'xxx';
    }
    // Render
    $result = array();
    array_push($result, array(
        'TotalItems' => $TotalItems,
        'totalpages' => $totalpages,
        'currentPage' => $currentPage,
        'currentBookClass' => $currentBookClass,
        'DeweyDecimalClass' => $classCodes,
        '$booksList' => $booksList
    ));

    echo json_encode($result, JSON_PRETTY_PRINT);
});


$app->run();

