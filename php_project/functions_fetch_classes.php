<?php

header('Content-Type: application/json');

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
                $aResult['result'] = substr($_POST['arguments'][0], 1);
//                   $aResult['result'] = $_POST['arguments'][0]), floatval($_POST['arguments'][1]));
                if (substr($_POST['arguments'][0], 1) == '00') {
                    $a = substr($_POST['arguments'][0], 0, 1);
                    $codeLike = $a . '%%';
                    $aResult['result'] = $codeLike;
                }
            }
            break;

        default:
            $aResult['error'] = 'Not found function ' . $_POST['functionname'] . '!';
            break;
    }
}

echo json_encode($aResult);
?>