<?php

function update_query(Db_Mysql $db) {

    $country = $db->escape('Serbia');

    $stmt = "UPDATE user
             SET country = '{$country}'
             WHERE country = 'Burundi'";

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