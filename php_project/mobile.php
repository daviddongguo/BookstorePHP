<?php


session_start();

require_once 'vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// create a log channel
$log = new Logger('main');
$log->pushHandler(new StreamHandler('logs/everything.log', Logger::DEBUG));
$log->pushHandler(new StreamHandler('logs/errors.log', Logger::ERROR));


DB::debugMode();            //Replace before submitting (SCOTT)
DB::debugMode();

if (false) {
    DB::$user = 'bootstore';
    DB::$dbName = 'bootstore';
    DB::$password = 'vuxunjqTbm5S7sAq';
    DB::$port = 3333;
    DB::$host = 'localhost';
    DB::$encoding = 'utf8';
    DB::$error_handler = 'db_error_handler';
} else {
    DB::$user = 'cp4907_mobile2';
    DB::$dbName = 'cp4907_mobile2';
    DB::$password = 'cxIOBbXCroUR7RNG';
    DB::$port = 3306;
    DB::$host = 'localhost';
    DB::$encoding = 'utf8';
    DB::$error_handler = 'db_error_handler';
}

// </editor-fold>
// <editor-fold defaultstate="collapsed" desc="Configure Error-Handler">
function db_error_handler($params) {
    global $app, $log;
    $log->error("SQL error: " . $params['error']);
    $log->error("SQL query: " . $params['query']);
    http_response_code(500);
    $app->render('fatal_error.html.twig');
    die; // don't want to keep going if a query broke
}

// </editor-fold>
// <editor-fold defaultstate="collapsed" desc="Slim creation and setup">
$app = new \Slim\Slim(array(
    'view' => new \Slim\Views\Twig()
        ));

$view = $app->view();
$view->parserOptions = array(
    'debug' => true,
    'cache' => dirname(__FILE__) . '/cache'
);
$view->setTemplatesDirectory(dirname(__FILE__) . '/templates');
// </editor-fold>
// <editor-fold defaultstate="collapsed" desc="Add User and Session to superglobals">
if (!isset($_SESSION['userId'])) {
    $_SESSION['userId'] = array();
}
if (!isset($_SESSION['sessionId'])) {
    $_SESSION['sessionId'] = session_id();
}
$twig = $app->view()->getEnvironment();
$twig->addGlobal('global_userId', $_SESSION['userId']);
$twig->addGlobal('global_sessionId', $_SESSION['sessionId']);
// </editor-fold>

$app->get('/mobile', function() use ($app, $log) {
    

    var_dump(DB::query("SELECT * FROM items")) ;
});


$app->run();

