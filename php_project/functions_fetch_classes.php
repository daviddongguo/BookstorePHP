<?php

header('Content-Type: application/json');

require_once 'vendor/autoload.php';

DB::debugMode();

if (true) {
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

$aResult = array();

if (!isset($_POST['functionname'])) {
    $aResult['error'] = 'No function name!';
}

if (!isset($_POST['arguments'])) {
    $aResult['error'] = 'No function arguments!';
}

if (!isset($aResult['error'])) {

    switch ($_POST['functionname']) {
        case 'fetchclasses':
            if (!is_array($_POST['arguments']) || (count($_POST['arguments']) < 2)) {
                $aResult['error'] = 'Error in arguments!';
            } else {
//                $aResult['result'] = substr($_POST['arguments'][0], 0, 1);

                $codelikeStr = substr($_POST['arguments'][0], 0, 1) . '%0';
                $querStr = "SELECT code, name FROM classes WHERE code LIKE '$codelikeStr' ORDER BY code";
                $results = DB::query($querStr);
                
//                $aResult['result'] = $querStr;
                $aResult = array(
                    'result' => $results
                );
                        
            }
            break;

        default:
            $aResult['error'] = 'Not found function ' . $_POST['functionname'] . '!';
            break;
    }
}

echo json_encode($aResult);
?>