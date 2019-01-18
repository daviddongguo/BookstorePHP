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
if (false) {
    DB::$user = 'bootstore';
    DB::$dbName = 'bootstore';
    DB::$password = 'vuxunjqTbm5S7sAq';
    DB::$port = 3333;
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
// <editor-fold defaultstate="collapsed" desc="Run admin/item/add Page (GET POST)">
$app->get('/admin/item/add', function() use ($app, $log) {
    // fetch the first step of book classification
    $classes = DB::query("SELECT * FROM classes WHERE code LIKE '%00'");
    // stage 1 get form
    $app->render('item_addedit.html.twig', array(
        'Classification' => array(
            '1' => $classes
        )
    ));
});
$app->post('/admin/item/add', function() use ($app, $log) {
    // -----------------debugging --------------------
//    var_dump($_SESSION['user']);
    print_r($_FILES);
    echo '<hr />';
    var_dump($_POST);
    echo '<hr />';
    // -----------------debugging --------------------
    // 
    $id = $app->request()->post('id');
    $title = $app->request()->post('title');
    $author = $app->request()->post('author');
    $price = $app->request()->post('price');
    $description = $app->request()->post('condition');
    $condition = $app->request()->post('condition');

    $valueList = array('id' => $id, 'title' => $title, 'author' => $author, 'price' => $price, 'description' => $description, 'condition' => $condition);
    //
    $errorList = array();
    if (strlen($title) < 2 || strlen($title) > 200) {
        array_push($errorList, "Number must be 8-12 characters long");
    }

    // 
    $image = $_FILES['image'];
    $imageInfo = getimagesize($image['tmp_name']);
    if (!$imageInfo) {
        array_push($errorList, "File does not look like a valid image");
    } else {
        // never allow '..' in the file name
        if (strstr($image['name'], '..')) {
            array_push($errorList, "File name invalid");
        }
        // only allow select extensions
        $ext = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, array('jpg', 'jpeg', 'gif', 'png'))) {
            array_push($errorList, "File extension invalid");
        }
        // check mime-type submitted
        //$mimeType = $image['type']; // TODO: use getimagesize result mime-type instead
        $mimeType = $imageInfo['mime'];
        if (!in_array($mimeType, array('image/gif', 'image/jpeg', 'image/png'))) {
            array_push($errorList, "File type invalid");
        }

        //
        if ($errorList) {
            $app->render('item_addedit.html.twig', array(
                'v' => $valueList, 'errorList' => $errorList));
        } else {
            $imageData = file_get_contents($image['tmp_name']);
            DB::insert('items', array(
                'title' => $title,
                'image' => $imageData,
                'mimeType' => $mimeType
            ));
            $itemId = DB::insertId();
            $app->render('item_add_success.html.twig', array('itemId' => $itemId));
        }
    }
});
// <editor-fold defaultstate="collapsed" desc="Run /item/:id/image (GET)">
$app->get('/item/:id/image', function($id) use ($app, $log) {
    $item = DB::queryFirstRow("SELECT image, mimeType FROM items WHERE id=%i", $id);
    if (!$item) {
        $app->notFound();
        return;
    }
    $app->response()->header('content-type', $item['mimeType']);
    echo $item['image'];
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

