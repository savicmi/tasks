<?php
require_once 'includes/functions.php';

$newsletter = NewsLetter::getInstance();
$rows = $newsletter->save();

// if the number of affected rows is greater than 0, insert query is successful
$success = $rows > 0 ? 1 : 0;

$json = array('success'=>$success);
echo json_encode($json);