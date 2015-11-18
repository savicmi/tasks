<!DOCTYPE html>
<html>
    <head>
	<title>Task Four</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<script type="text/javascript" src="public/js/jquery.js"></script>
	<script type="text/javascript" src="public/js/custom.js"></script>
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" />
	<link rel="stylesheet" type="text/css" href="public/css/style.css">
    </head>
    <body>

		<?php
            // I've generated messages using jQuery, because it was expected to be done in custom.js,
            // otherwise, it can be performed using php...

            // $json_data = file_get_contents('public/js/data.json');
            // convert json object to a php associative array
            // $data = json_decode($json_data, true);
            // array of all messages
            // $messages = $data['data']; etc

            echo '
            <section>
                <div class="container">
                    <div class="row">
                        <div class="col-md-8 col-md-offset-2">

                            <div class="panel panel-default message-panel">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-xs-7 col-md-8 from"></div>
                                        <div class="col-xs-5 col-md-4 date"><span class="pull-right"></span></div>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <div class="subject"></div>
                                </div>
                                <div class="panel-footer">
                                    <button type="button" class="btn btn-default reply">Reply</button>
                                    <button type="button" class="btn btn-default delete">Delete</button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </section>';

		?>

		<!-- Bootstrap framework -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    </body>
</html>
