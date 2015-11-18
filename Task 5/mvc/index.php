<?php
/**
 * User: Milos Savic
 * This file will delegate all the requests to the controller
 */

include_once("controllers/parser_controller.php");

if (isset($_POST['address']) && isset($_POST['item'])) {
    $controller = new Controller($_POST['address'], $_POST['item']);
    $results = $controller->invoke();

    $json = array('success'=>1, 'results'=>$results['nodes'], 'links'=>$results['links']);
    echo json_encode($json);
}
else {
    $json = array('success'=>0, 'message'=>'Missing input data.');
    echo json_encode($json);
}