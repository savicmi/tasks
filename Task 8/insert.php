<?php

function insert_query(Db_Mysql $db) {

    $fullname = $db->escape('Miloš Savić');
    $email = $db->escape('savicmi@gmail.com');
    $country = $db->escape('Serbia');

    $stmt = "INSERT INTO user (fullname, email, country)
             VALUES ('{$fullname}', '{$email}', '{$country}')";

    // query result
    $result = $db->query($stmt);

    if (!$result) {
        echo $db->errno() . ' ' . $db->error();
    }
    else {
        // gets the number of affected rows
        $num_rows = $db->affectedRows();
        // gets the auto generated id used in the last query
        $insert_id = $db->insertId();

        echo 'Number of affected rows: ' . $num_rows .'<br>';
        echo 'Auto generated id used in the last query: ' . $insert_id;
    }
}
