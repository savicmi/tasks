<?php
include_once 'class/deliveryMethods.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Task Seven</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <script type="text/javascript" src="js/jquery.js"></script>
        <script type="text/javascript" src="js/custom.js"></script>
        <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" href="css/style.css">
    </head>
    <body>

        <!-- Form section -->
        <section>
            <div class="container">
                <div class="row">
                    <div class="col-md-12">

                        <?php
                            // gets methods' content as an associative array
                            $dm = new DeliveryMethods();
                            $methods = $dm->get_methods();

                            // if no result from database
                            if (empty($methods)) {
                                $message = '<div class="col-md-12">
                                                <div class="alert alert-danger message" role="alert">
                                                    No methods in the database.
                                                </div>
                                            </div>';
                                echo $message;
                            }
                            else {
                                    ?>

                                    <form class="form-horizontal col-md-12" id="deliveries">

                                    <?php foreach($methods as $dm) { ?>
                                        <div class="panel panel-default delivery_method" id="del_<?php echo $dm['id']; ?>">
                                            <!-- Delivery method row -->
                                            <div class="panel-heading">

                                                <div class="form-group">
                                                    <div class="col-md-5">
                                                        <p class="form-control-static name"><?php echo $dm['name']; ?></p>
                                                    </div>
                                                    <div class="col-sm-3 col-md-2 amount_field_show_ranges">
                                                        <label class="sr-only" for="amount_">Amount (in dollars)</label>

                                                        <div class="input-group">
                                                            <input type="number" min="0" step="0.01"
                                                                   class="form-control"
                                                                   id="amount_" value="<?php
                                                                        if (!is_null($dm['value']) || !empty($dm['value']))
                                                                            echo number_format($dm['value'], 2, '.', ''); ?>"/>
                                                            <div class="input-group-addon">$</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3 col-md-3 addrange_link">
                                                        <p class="form-control-static">
                                                            <a href="#" class="add_ranges">Add ranges</a>
                                                        </p>
                                                    </div>
                                                    <div class="col-sm-3 col-md-2">
                                                        <button type="button"
                                                                class="btn btn-options btn-block pull-right options"
                                                                data-toggle="collapse">Show Options
                                                        </button>
                                                    </div>
                                                </div>

                                            </div>

                                            <!-- Ranges -->
                                            <div class="panel_ranges" aria-expanded="false">

                                                <?php foreach($dm['ranges'] as $range_row) { ?>
                                                <div class="panel-body ranges">

                                                    <div class="form-group">
                                                        <div class="form-inline col-md-5">
                                                            <label for="range_from_" class="control-label">From</label>

                                                            <div class="col-md-4 input-group">
                                                                <input type="text" class="form-control"
                                                                       id="range_from_" value="<?php
                                                                            if (!is_null($range_row['range_from']) || !empty($range_row['range_from']))
                                                                                echo number_format($range_row['range_from'], 2, '.', ''); ?>"/>

                                                                <div class="input-group-addon">$</div>
                                                            </div>
                                                            <label for="range_to_" class="control-label">to</label>

                                                            <div class="col-md-4 input-group">
                                                                <input type="text" class="form-control"
                                                                       id="range_to_" value="<?php
                                                                            if (!is_null($range_row['range_to']) || !empty($range_row['range_to']))
                                                                                echo number_format($range_row['range_to'], 2, '.', ''); ?>"/>

                                                                <div class="input-group-addon">$</div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label class="sr-only" for="range_amount_">Amount (in
                                                                dollars)</label>

                                                            <div class="input-group">
                                                                <input type="number" min="0" step="0.01"
                                                                       class="form-control" id="range_amount_" value="<?php
                                                                            if (!is_null($range_row['price']) || !empty($range_row['price']))
                                                                                echo number_format($range_row['price'], 2, '.', ''); ?>"/>

                                                                <div class="input-group-addon">$</div>
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-6 col-sm-3 col-md-2 col-md-offset-2">
                                                            <p class="form-control-static pull-right-sm"><a href="#"
                                                                                                            class="add_new_range">Add
                                                                    New</a></p>
                                                        </div>
                                                        <div class="col-xs-6 col-sm-3 col-md-1">
                                                            <p class="form-control-static pull-right-sm"><a href="#"
                                                                                                            class="delete_range">Delete</a>
                                                            </p>
                                                        </div>
                                                    </div>

                                                </div>
                                                <?php } ?>
                                            </div>

                                            <!-- Options -->
                                            <div class="panel-body options" aria-expanded="false">

                                                <div class="form-group">
                                                    <label for="url_" class="col-md-5 control-label">
                                                        Delivery URL
                                                    </label>

                                                    <div class="col-md-4">
                                                        <input type="text" class="form-control" id="url_" value="<?php echo $dm['url']; ?>"/>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="col-md-5">
                                                        <p class="form-control-static">Weight (accepted deliveries in
                                                            KG)</p>
                                                    </div>
                                                    <div class="form-inline col-md-7">
                                                        <label for="weight_from_" class="control-label">From</label>

                                                        <div class="col-md-3 input-group">
                                                            <input type="number" min="0" step="0.01"
                                                                   class="form-control"
                                                                   id="weight_from_" value="<?php
                                                                        if (!is_null($dm['weight_from']) || !empty($dm['weight_from']))
                                                                            echo $dm['weight_from']; ?>"/>

                                                            <div class="input-group-addon"></div>
                                                        </div>
                                                        <label for="weight_to_" class="control-label">To</label>

                                                        <div class="col-md-3 input-group">
                                                            <input type="number" min="0" step="0.01"
                                                                   class="form-control"
                                                                   id="weight_to_"value="<?php
                                                                        if (!is_null($dm['weight_to']) || !empty($dm['weight_to']))
                                                                            echo $dm['weight_to']; ?>"/>

                                                            <div class="input-group-addon">KG</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="notes_" class="col-md-5 control-label">
                                                        Notes
                                                    </label>

                                                    <div class="col-md-5">
                                                        <textarea class="form-control" rows="3" id="notes_"><?php echo $dm['notes']; ?></textarea>
                                                    </div>
                                                </div>

                                            </div>

                                        </div>

                                    <?php }} ?>

                                        <!-- Save Form button -->
                                        <div class="panel panel-default">
                                            <div class="panel-footer">
                                                <div class="alert alert-danger" role="alert">
                                                    <span class="sr-only">Error:</span>
                                                    Error
                                                </div>
                                                <div class="form-group">
                                                    <div class="col-md-12">
                                                        <button type="submit" class="btn btn-custom btn-lg pull-right"
                                                                id="submit">Save Form
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </form>

                    </div>
                </div>
            </div>
        </section>

        <!-- Ranges // hidden on the first load -->
        <div class="panelranges">
            <div class="panel-body ranges">

                <div class="form-group">
                    <div class="form-inline col-md-5">
                        <label for="range_from_" class="control-label">From</label>
                        <div class="col-md-4 input-group">
                            <input type="text" class="form-control" id="range_from_" />
                            <div class="input-group-addon">$</div>
                        </div>
                        <label for="range_to_" class="control-label">to</label>
                        <div class="col-md-4 input-group">
                            <input type="text" class="form-control" id="range_to_" />
                            <div class="input-group-addon">$</div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="sr-only" for="range_amount_">Amount (in dollars)</label>
                        <div class="input-group">
                            <input type="number" min="0" step="0.01" class="form-control" id="range_amount_" />
                            <div class="input-group-addon">$</div>
                        </div>
                    </div>
                    <div class="col-xs-6 col-sm-3 col-md-2 col-md-offset-2">
                        <p class="form-control-static pull-right-sm"><a href="#" class="add_new_range">Add New</a></p>
                    </div>
                    <div class="col-xs-6 col-sm-3 col-md-1">
                        <p class="form-control-static pull-right-sm"><a href="#" class="delete_range">Delete</a></p>
                    </div>
                </div>

            </div>
        </div>

        <div id="ajax-loader"><span>Please wait</span></div>

        <!-- Modal -->
        <div id="messageModal" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Modal Title</h4>
                    </div>
                    <div class="modal-body">
                        <p>Modal Message</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">OK</button>
                    </div>
                </div>

            </div>
        </div>

		<!-- Bootstrap framework -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    </body>
</html>
