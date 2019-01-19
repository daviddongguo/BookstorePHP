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
//    var_dump($sessionID); // debugging
//    echo '<hr />';
//    var_dump($books);
//    echo '<hr />';
    // -----------------debugging --------------------
    //Pass todos to index HTML as array of todos
    $app->render('index.html.twig', array(
        'sessionID' => $sessionID,
        'books' => $books));
});
// </editor-fold>
// <editor-fold defaultstate="collapsed" desc="Run admin/item/add Page (GET POST)">
$app->get('/admin/item/add', function() use ($app, $log) {
    // stage 1 get form
    $app->render('item_addedit.html.twig');
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
    $isbn = $app->request()->post('isbn');
    $description = $app->request()->post('description');
    $condition = $app->request()->post('condition');
    $bookclass = $app->request()->post('bookclass');
    $price = $app->request()->post('price');
    $imageData = null;
    $mimeType = null;
    $valueList = array(
        'id' => $id,
        'title' => $title,
        'author' => $author,
        'isbn' => $isbn,
        'description' => $description,
        'condition' => $condition,
        'price' => $price,
        'image' => $imageData,
        'mimeType' => $mimeType
    );

    //
    $errorList = array();
    if (strlen($title) < 2 || strlen($title) > 200) {
        array_push($errorList, "Title($title) must be 2-200 characters long");
    }
    if (strlen($author) < 2 || strlen($author) > 100) {
        array_push($errorList, "Author($author) must be 2-100 characters long");
    }
    if (strlen($isbn) < 2 || strlen($isbn) > 20) {
        array_push($errorList, "ISBN($isbn) invalid");
    }
    if (strlen($description) < 20 || strlen($description) > 200) {
        array_push($errorList, "Description must be 20-2000 characters long");
    }
    if ($condition < 40 || $condition > 100) {
        array_push($errorList, "Condition($condition) must be 40-100");
    }
    if (!is_numeric($price)||$price <= 0 || $price > 999.99) {
        array_push($errorList, "Price($price) invalid");
    }


    // 
    if ($_FILES['image']['size'] != 0) {
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
            $imageData = file_get_contents($image['tmp_name']);
            $valueList['image'] = $imageData;
            $valueList['mimeType'] = $mimeType;
        }
    }

    //
    if ($errorList) {
        $app->render('item_addedit.html.twig', array(
            'v' => $valueList, 'errorList' => $errorList));
    } else {
//        var_dump(array(
//            'id' => $id,
//            'title' => $title,
//            'author' => $author,
//            'ISBN' => $isbn,
//            'description' => $description,
//            'conditionofused' => $condition,
//            'price' => $price,
//            'image' => $imageData,
//            'mimeType' => $mimeType
//        ));
//        return;
        DB::insert('items', array(
            'id' => $id,
            'title' => $title,
            'author' => $author,
            'ISBN' => $isbn,
            'description' => $description,
            'DeweyDecimalClass' => $bookclass,
            'conditionofused' => $condition,
            'price' => $price,
            'image' =>  $imageData,
            'mimeType' => $mimeType
        ));
        $itemId = DB::insertId();
        $app->render('item_add_success.html.twig', array('itemId' => $itemId));
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

// <editor-fold defaultstate="collapsed" desc="Run /item/:code/class (GET)">
$app->get('/item/:code/class', function($code) use ($app, $log) {

    switch (strlen($code)) {
        case 1:
            $codelikeStr = $code . '%0';
            $querStr = "SELECT code, name FROM classes WHERE code LIKE '$codelikeStr' ORDER BY code";
            $results = DB::query($querStr);
            break;
        case 2:
            $codelikeStr = $code . '%';
            $querStr = "SELECT code, name FROM classes WHERE code LIKE '$codelikeStr' ORDER BY code";
            $results = DB::query($querStr);
            break;
        default:
            $codelikeStr = '%00';
            $querStr = "SELECT code, name FROM classes WHERE code LIKE '$codelikeStr' ORDER BY code";
            $results = DB::query($querStr);
            break;
    }
//    var_dump($results);
    // <option value="{{ c.code }}">{{ c.name }}</option>
    $isFirstOption = true;
    foreach ($results as $row) {
        if ($isFirstOption) {
            $isFirstOption = false;
            echo "<option value='000' selected>Choose...</option>";
            echo "<option value='" . $row['code'] . "'>";
            echo $row['name'] . "</option>\n";
        } else {
            echo "<option value='" . $row['code'] . "'>";
            echo $row['name'] . "</option>\n";
        }
    }
});

// <editor-fold defaultstate="collapsed" desc="user-description">
// </editor-fold>
// <editor-fold defaultstate="collapsed" desc="user-description">
// </editor-fold>
// <editor-fold defaultstate="collapsed" desc="MeekroDB Actions">

/*
  //INSERT new item
  DB::insert('items', array(
  'ISBN' => $ISBN,
  //Other stuff
  'imagePath' => $image['name']
  ));

  //
  $log->debug("Adding with new Id = " . DB::insertId());

  $itemId = DB::insertId();
  $app->render('item_add_success.html.twig', array('productId' => $productId));






  //INSERT new User
  DB::insert('items', array(
  'email' => $email,
  //Other Stuff
  'password' => $password
  ));

  //$log->debug("Adding with new Id = " . DB::insertId());

  $UserId = DB::insertId();
  $_SESSION['userId'] = $userID;
  $app->render('index.html.twig', $_SESSION['userId']);





  //Query for Registered User
  //
  //  NOT WORKING YET
  //
  $userEmail = $app->request()->post('userEmail');


  $userProfile = DB::queryFirstRow("SELECT * FROM users WHERE id=%i", $userId);

  if (!$userProfile)
  {
  $app->notFound();
  return;
  }
  $app->render('product_view.html.twig', array('p' => $product));
  });








  //Query All


  //Index's Sidebar is comprised of text links
  //<a href = index/~~~~/~~~~>~~~~~~~</a>



  //case('all') //TAKEN FROM URL TOKEN
  $items = DB::query("SELECT * FROM items");
  $app->render('index.html.twig', array('items' => $items));



  //Query Type1, type2, type3

  //case('type1')     //TAKEN FROM URL TOKEN
  //$type1            //TAKEN FROM URL TOKEN

  $items = DB::query("SELECT * FROM items WHERE type1=%s", $type1);
  $app->render('index.html.twig', array('items' => $items));




  //Query By Price

  //case('highprice')     //TAKEN FROM URL TOKEN
  //$price                //TAKEN FROM URL TOKEN

  $items = DB::query("SELECT * FROM items WHERE price>%d", $price);
  $app->render('index.html.twig', array('items' => $items));



  //Order by Author


  //Add the same functionality as the sidebar
  //links to the author line of the main



  //case('author')     //TAKEN FROM URL TOKEN
  //$author            //TAKEN FROM URL TOKEN

  $items = DB::query("SELECT * FROM items WHERE author=%s", $author);
  $app->render('index.html.twig', array('items' => $items));




  //Query Disctinct ISBN's

  //case('ISDN')     //TAKEN FROM URL TOKEN

  $items = DB::query("SELECT DISTINCT ISBN FROM items");
  $app->render('index.html.twig', array('items' => $items));





  //Query Users transaction history

  $userID = $_SESSION['userId'];

  $items = DB::query(""
  . "SELECT * "
  . "FROM orderitems "
  . "INNER JOIN orders "
  . "ON orderitems.orderId=orders.id "
  . "WHERE orders.userId=%s", $userID
  );


  //If we add a timestamp to the orders we can return the
  //transacrion history in chroniclogical order with

  //"ORDER BY orders.timestamp ASC"



  $app->render('transactionhistory.html.twig', array('items' => $items));



  //Query Users Sale History

  $userID = $_SESSION['userId'];

  $items = DB::query("SELECT * FROM items WHERE sellerId=%s", $userID);
  $app->render('sellhistory.html.twig', array('items' => $items));




  //Query Users Cart (SessionId)


  $sessionId = session_id();

  $items = DB::query(""
  . "SELECT * "
  . "FROM cartitems "
  . "INNER JOIN items "
  . "ON cartitems.itemId=items.id "
  . "WHERE cartitems.sessionId=%s "
  . "ORDER BY cartitems.createdTS ASC", $userID
  );

  $app->render('cart.html.twig', array('items' => $items));





  //Query Users Cart (UserId)

  $userID = $_SESSION['userId'];

  $items = DB::query(""
  . "SELECT * "
  . "FROM cartitems "
  . "INNER JOIN items "
  . "ON cartitems.itemId=items.id "
  . "WHERE cartitems.userId=%s "
  . "ORDER BY cartitems.createdTS ASC", $userID
  );

  $app->render('cart.html.twig', array('items' => $items));







  //Add Item to Cart






  //Remove Item from Cart








  //Remove item







  //Transaction (INSERT items in Cart to History, Remove items from Items, Delete items in Cart)





 */













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

