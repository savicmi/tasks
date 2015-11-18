<!DOCTYPE html>
<html>
    <head>
        <title>Task Five</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <script type="text/javascript" src="js/jquery.js"></script>
        <script type="text/javascript" src="js/custom.js"></script>
        <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" href="css/style.css">
    </head>
    <body>

        <header>
            <div class="container">
                <div class="row">
                    <div class="col-md-6  col-md-offset-3">

                        <div class="page-header">
                            <h1>Simple web parser</h1>
                        </div>

                    </div>
                </div>
            </div>
        </header>

        <!-- Form section -->
        <section>
            <div class="container">
                <div class="row">
                    <div class="col-md-6  col-md-offset-3">

                        <div class="panel panel-default form-panel">
                            <div class="panel-body">

                             <form class="form" id="parser">
                                <div class="form-group col-md-12">
                                    <label for="url">
                                        Address
                                    </label>
                                    <input type="text" class="form-control" id="url" required="required" placeholder="Enter address..." />
                                    <div class="alert alert-danger" role="alert">
                                        <span class="sr-only">Error:</span>
                                        Error
                                    </div>
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="item">
                                        Item
                                    </label>
                                    <input type="text" class="form-control" id="item" required="required" placeholder="Enter item..." />
                                    <div class="alert alert-danger" role="alert">
                                        <span class="sr-only">Error:</span>
                                        Error
                                    </div>
                                </div>
                                <div class="form-group col-md-12">
                                    <button type="submit" class="btn btn-custom btn-lg btn-block" id="submit">Submit</button>
                                </div>
                            </form>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>

        <!-- Results section -->
        <section>
            <div class="container">
                <div class="row">
                    <div class="col-md-8  col-md-offset-2">

                        <div class="panel panel-default results-panel">
                            <div class="panel-heading">
                                Results
                            </div>

                            <div class="panel-body" id="results">

                            </div>
                        </div>

                        <div class="panel panel-default links-panel">
                            <div class="panel-heading">
                                Links
                            </div>

                            <div class="panel-body" id="links">

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>

        <div id="ajax-loader"><span>Please wait</span></div>

		<!-- Bootstrap framework -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    </body>
</html>
