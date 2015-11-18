<?php

require_once 'db/mysql.php';
require_once 'select.php';
require_once 'insert.php';
require_once 'update.php';
require_once 'delete.php';

$db = new Db_Mysql(array(
    'dbname' => 'shift_planning_test',
    'username' => 'root',
    'password' => 'root',
    'host' => 'localhost'
));
?>

<!DOCTYPE html>
<html>
<head>
    <title>Task Eight</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script type="text/javascript" src="js/jquery.js"></script>
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>

    <section>
        <div class="container">
            <div class="row">
                <div class="col-md-10 col-md-offset-1">

                    <div class="panel panel-default my-panel">
                        <!-- SELECT panel -->
                        <div class="panel-heading">
                            SELECT query
                        </div>
                        <div class="panel-body">

                            <?php
                                $db->connect();
                                select_query($db);
                            ?>

                        </div>
                    </div>

                    <div class="panel panel-default my-panel">
                        <!-- INSERT panel -->
                        <div class="panel-heading">
                            INSERT query
                        </div>
                        <div class="panel-body">

                            <?php
                                $db->ping();
                                insert_query($db);
                            ?>

                        </div>
                    </div>

                    <div class="panel panel-default my-panel">
                        <!-- UPDATE panel -->
                        <div class="panel-heading">
                            UPDATE query
                        </div>
                        <div class="panel-body">

                            <?php
                            $db->ping();
                            update_query($db);
                            ?>

                        </div>
                    </div>

                    <div class="panel panel-default my-panel">
                        <!-- DELETE panel -->
                        <div class="panel-heading">
                            DELETE query
                        </div>
                        <div class="panel-body">

                            <?php
                            $db->ping();
                            delete_query($db);
                            ?>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <?php
        // closes a previously opened database connection
        $db->close();
    ?>
    <!-- Bootstrap framework -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
</body>
</html>


