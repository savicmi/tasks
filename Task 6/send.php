<?php
require_once 'includes/functions.php';

$newsletter = NewsLetter::getInstance();
$rows = $newsletter->send();

// if the number of affected rows is greater than 0, insert query is successful
if ($rows['num_email'] == 0)
    echo 'There is no email messages to send.';
else
    echo 'It was sent successfully ' .$rows['sent']. ' of ' .$rows['num_email']. ' email messages.';
