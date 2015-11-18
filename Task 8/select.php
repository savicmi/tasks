<?php

function select_query(Db_Mysql $db) {

    $stmt = "SELECT * FROM user";
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

        // if there are some rows
        if ($num_rows > 0) {

            $rows = array();

            // fetch a result row as an associative array
            while ($row = $db->fetch($result, MYSQLI_ASSOC)) {

                $rows[] = array(
                    'fullname' => $row['fullname'],
                    'email' => $row['email'],
                    'country' => $row['country']
                );
            }

            // prints results
            $content = '<div class="table-responsive"><table class="table table-condensed">
                            <thead>
                                <tr>
                                    <th>Full name</th>
                                    <th>Email</th>
                                    <th>Country</th>
                                </tr>
                            </thead>';

            foreach ($rows as $iter) {
                $content .= '<tbody>
                            <tr>
                                <td>' . $iter['fullname'] . '</td>
                                <td><a href="mailto:' . $iter['email'] . '">' . $iter['email'] . '</a></td>
                                <td>' . $iter['country'] . '</td>
                            </tr>';
            }

            $content .= '</tbody></table></div>';

            echo $content;
            echo 'Number of affected rows: ' . $num_rows .'<br>';
            echo 'Auto generated id used in the last query: ' . $insert_id;
        }
        else
            echo 'There are no rows in the database.';
    }
}