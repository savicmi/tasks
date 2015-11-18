<?php
    require_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Task Two</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
        <link href="public/css/style.css" rel="stylesheet" type="text/css" media="screen" />
        <script src="public/js/jquery.js" type="text/javascript"></script>
        <script src="public/js/custom.js" type="text/javascript"></script>
    </head>
    <body>
        <div>
            <?php
                $kalendar = Kalendar::getInstance();
                echo $kalendar->prikazKalendara();
            ?>

        </div>
    </body>
</html>
