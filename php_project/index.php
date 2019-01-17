<?php

// <editor-fold defaultstate="collapsed" desc="Setup Session & Logger">
session_start();

require_once 'vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// create a log channel
$log = new Logger('main');
$log->pushHandler(new StreamHandler('logs/everything.log', Logger::DEBUG));
$log->pushHandler(new StreamHandler('logs/errors.log', Logger::ERROR));
// </editor-fold>
// <editor-fold defaultstate="collapsed" desc="Configure Database Connection">
DB::$user = 'bootstore';
DB::$dbName = 'bootstore';
DB::$password = 'vuxunjqTbm5S7sAq';
DB::$port = 3333;
DB::$host = 'localhost';
DB::$encoding = 'utf8';
DB::$error_handler = 'db_error_handler';

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
// <editor-fold defaultstate="collapsed" desc="Run Index Page (GET)">
$app->get('/', function() use ($app, $log) {
    $sessionID = session_id();

    //Get all todos from DB
    $books = DB::query("SELECT * FROM items");

    // -----------------debugging --------------------
    var_dump($sessionID); // debugging
    echo '<hr />';
    var_dump($books);
    echo '<hr />';
    // -----------------debugging --------------------
    //Pass todos to index HTML as array of todos
    $app->render('index.html.twig', array(
        'sessionID' => $sessionID, 
        'books' => $books));
});
// </editor-fold>
// <editor-fold defaultstate="collapsed" desc="Run addedit item Page (GET POST)">
$app->get('/add', function() use ($app, $log) {
    //Pass todos to index HTML as array of todos
    $errorList = array();
    $item = array();
    $app->render('addedititem.html.twig', array(
        'errorList' => $errorList,
        'v' => $item));
});
$app->post('/add', function() use ($app, $log) {
    var_dump($_POST);
});
// <editor-fold defaultstate="collapsed" desc="user-description">
// </editor-fold>
// <editor-fold defaultstate="collapsed" desc="user-description">
// </editor-fold>
// <editor-fold defaultstate="collapsed" desc="user-description">
// </editor-fold>
// <editor-fold defaultstate="collapsed" desc="Research Notes">
//BOOTSTRAP - Basic Navbar (Top)
//https://www.w3schools.com/booTsTrap/tryit.asp?filename=trybs_navbar&stacked=h
//BOOTSTRAP - Sticky Navbar (Top)
//https://www.w3schools.com/booTsTrap/tryit.asp?filename=trybs_affix&stacked=h
//Responsive Navigation Bar
//https://www.w3schools.com/howto/howto_js_topnav_responsive.asp
//Top Navigation Bar (CSS)
//https://www.w3schools.com/howto/howto_js_topnav.asp
//Item Added to Card Modal Pop-up
//https://www.w3schools.com/howto/howto_css_modals.asp
//Fixed Sidebar
//https://www.w3schools.com/howto/howto_css_fixed_sidebar.asp
//Rounded Images
//https://www.w3schools.com/howto/howto_css_rounded_images.asp
//Columation
//https://www.w3schools.com/howto/howto_css_two_columns.asp
//BOOTSTRAP DEMO
//https://www.w3schools.com/howto/tryit.asp?filename=tryhow_website_bootstrap4
//Style Cards
//https://www.w3schools.com/howto/howto_css_cards.asp
//How To Create an Icon Bar
//https://www.w3schools.com/howto/howto_css_icon_bar.asp#
//Responsive Top Nav
//https://www.w3schools.com/howto/tryit.asp?filename=tryhow_js_topnav
// </editor-fold>





$app->run();

