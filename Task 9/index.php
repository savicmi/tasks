<?php

// register classes and functions from 'class/class_name.php';
spl_autoload_register(function ($class) {
    include 'class/' . $class . '.php';
});
?>

<!DOCTYPE html>
<html>
<head>
    <title>Task Nine</title>
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
                        <div class="panel-heading">
                            OUTPUT
                        </div>
                        <div class="panel-body">

                            <?php
                                //$db->connect();
                                $container = new Container();

                                echo '<div class="subtitle">1. part</div>';

                                // sets providers
                                $container->set("book", "Lord of the flies"); // provider as a scalar
                                $container->set("number", 317); // provider as a scalar
                                $container->set("now", function() {
                                    return date("F j, Y, g:i a"); // provider as a closure
                                });
                                $container->set("hello", function($firstName, $lastName) {
                                    return "Hello {$firstName} {$lastName}"; // provider as a closure
                                });
                                $container->set("numbers", array(5,10,50,100)); // provider as an array
                                $container->set("colors", array('red'=>'#FF0000', 'green'=>'#00FF00', 'blue'=>'#0000FF')); // provider as an associative array

                                // gets providers
                                echo $container->get("book") . '<br>';
                                echo $container->get("number") . '<br>';
                                echo $container->get("now") . '<br>';
                                echo $container->get("hello", array("John", "Doe")) . '<br>';
                                echo implode(' ', $container->get("numbers")) . '<br>';
                                echo implode(' ', $container->get("colors")) . '<br>';

                                // accessing and changing providers as property
                                echo '<div class="subtitle">2. part</div>';

                                $container->book = "Lord of the flies 2";
                                echo $container->book . '<br>';
                                echo $container->now . '<br>';
                                echo implode(' ', $container->numbers) . '<br>';

                                // accessing and changing providers as array
                                echo '<div class="subtitle">3. part</div>';

                                $container["book"] = "Lord of the flies 3";
                                echo $container["book"] . '<br>';
                                echo $container["now"] . '<br>';
                                echo implode(' ', $container["numbers"]) . '<br>';

                                // accessing providers as function
                                echo '<div class="subtitle">4. part</div>';

                                echo $container->book() . '<br>';
                                echo $container->number() . '<br>';
                                echo $container->now() . '<br>';
                                echo $container->hello("John", "Doe") . '<br>';

                                // singleton access
                                echo '<div class="subtitle">5. part</div>';

                                $dsn = "mysql:host=localhost;dbname=newsletter;charset=utf8"; // Host name and database name
                                $user = "root"; // MySQL user name
                                $pass = "root"; // MySQL password

                                $container->set("db", function($dsn, $user, $pass) {
                                    return new \PDO($dsn, $user, $pass);
                                }, true);
                                $db_data = array($dsn, $user, $pass);

                                try {
                                    $db = $container->get("db", $db_data);
                                    $db2 = $container->get("db");
                                    echo 'Successfully connected to the database.' . '<br>';
                                    //echo '$db and $db2 refer to the same instance: ' . bool2str($db === $db2) . '<br>'; // this should to return true
                                    //echo spl_object_hash($db) . '<br>' . spl_object_hash($db2) .'<br>';
                                } catch (PDOException $e) {
                                    echo 'Connection failed: ' . $e->getMessage();
                                }

                                $container->set("MAX_BUFFER_SIZE", 200, true);
                                $container->set("hash", function() {
                                    return md5(gethostname() . time());
                                }, true);

                                $value = $container->MAX_BUFFER_SIZE;
                                echo $value .'<br>'; // Prints 200
                                $container->MAX_BUFFER_SIZE = 300;
                                echo $value .'<br>'; // Still prints 200

                                $value = $container->hash() .'<br>';
                                echo $value;
                                $value2 = $container->hash() .'<br>';
                                echo $value2;
                                //echo '$value and $value2 refer to the same instance: ' . bool2str($value === $value2) . '<br>';

                                // temp function converts boolean to string
                                function bool2str($bool) {
                                    if ($bool === false)
                                        return 'FALSE';
                                    else
                                        return 'TRUE';
                                }

                                // provider interface
                                echo '<div class="subtitle">6. part</div>';

                                $container->register(new UserServiceProvider());
                                $container->get("UserService")->getUser(3);
                                $container->get("UserApplicationService")->getUserApplications(317);
                                ?>

                        </div>
                    </div>


                </div>
            </div>
        </div>
    </section>

    <!-- Bootstrap framework -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
</body>
</html>


