$(document).ready(function() {

    if ($('.delivery_method').length > 0) {

        var addrange_link = {}; // store "Add ranges" links for every delivery method
        var amount_field = {}; // store value input fields for every delivery method
        var show_ranges = '<p class="form-control-static"><a href="#" class="show_ranges" data-toggle="collapse">Show Ranges</a></p>';

        // store number of ranges rows for every delivery method
        var number_of_ranges = {};

        start_settings();

        // on hover a delivery row, "Add ranges" link and "Show Options" button are displayed
        $(document).on('mouseenter', '.panel-heading', function () {
            $(this).find('.add_ranges, button.options').show();
        });

        $(document).on('mouseleave', '.panel-heading', function () {
            $(this).find('.add_ranges, button.options').hide();
        });

        // on hover a delivery method, left red border is displayed to improve the view
        $(document).on('mouseenter', '.delivery_method', function () {
            $(this).css('border-left', '2px solid #6d0303');
        });
        $(document).on('mouseleave', '.delivery_method', function () {
            $(this).css('border-left', '2px solid transparent');
        });

        // on click "Add ranges" link, a new ranges row is added and opened
        $(document).on('click', '.add_ranges', function () {
            event.preventDefault();
            var method_id =  $(this).closest('.delivery_method').attr('id');
            var method_id_number = method_id.split('_').pop();

            var $ranges_row = $(".panelranges").html();
            $(this).closest('.panel-heading').next().append($ranges_row);

            var $new_row = $(this).closest('.delivery_method').find('.ranges').last();
            number_of_ranges[method_id]++;
            $new_row
                .find('[for="range_from_"],[for="range_to_"],[for="range_amount_"]').attr('for', function( i, val ) {
                        return val + method_id_number + '_' + number_of_ranges[method_id];
                    }).end()
                .find('[id="range_from_"],[id="range_to_"],[id="range_amount_"]').attr('id', function( i, val ) {
                        return val + method_id_number + '_' + number_of_ranges[method_id];
                    }).end();

            // removes "Add ranges" link and value input field and adds "Show Ranges" link
            $(this).closest('.panel-heading').parent().find('.panel_ranges').hide().slideDown("slow");
            amount_field[method_id] = $(this).closest('.panel-heading').find('.amount_field_show_ranges').children().detach();
            $(this).closest('.panel-heading').find('.amount_field_show_ranges').append(show_ranges);

            $(this).closest('.delivery_method').find('.panel_ranges').attr('aria-expanded', 'true');
            addrange_link[method_id] = $(this).parent().detach();
        });

        // on click "Show Ranges" link, the ranges rows are opened
        $(document).on('click', '.show_ranges', function () {
            var $ranges = $(this).closest('.delivery_method').find('.panel_ranges');

            if (($ranges).is( ":hidden" ))
                $ranges.attr('aria-expanded', 'true');
            else
                $ranges.attr('aria-expanded', 'false');

            $ranges.slideToggle("slow");
            $(this).blur();
        });

        // on click "Show Options" button, options panel for delivery method is opened
        $(document).on('click', 'button.options', function () {
            var $options = $(this).closest('.delivery_method').find('.panel-body.options');

            if (($options).is( ":hidden" ))
                $options.attr('aria-expanded', 'true');
            else
                $options.attr('aria-expanded', 'false');

            $options.slideToggle("slow");
        });

        // on click "Add New" link, a new range row will be added under that
        $(document).on('click', '.add_new_range', function (event) {
            event.preventDefault();
            var method_id =  $(this).closest('.delivery_method').attr('id');
            var $range_row = $(this).closest('.panel-body.ranges');
            var $clone = $range_row.clone();
            $clone.find(':input').val('');
            $clone.find('.has-error').removeClass('has-error');

            number_of_ranges[method_id]++;
            $clone
                .find('[for^="range_from_"],[for^="range_to_"],[for^="range_amount_"]').attr('for', function( i, val ) {
                        var i = val.lastIndexOf('_');
                        if (i != -1) val = val.substr(0, i+1) + number_of_ranges[method_id];
                        return val;
                    }).end()
                .find('[id^="range_from_"],[id^="range_to_"],[id^="range_amount_"]').attr('id', function( i, val ) {
                        var i = val.lastIndexOf('_');
                        if (i != -1) val = val.substr(0, i+1) + number_of_ranges[method_id];
                        return val;
                    }).end();

            $range_row.after($clone);

            $(this).blur();
        });

        // on click "Delete" link, that range row will be deleted
        $(document).on('click', '.delete_range', function (event) {
            event.preventDefault();
            var method_id =  $(this).closest('.delivery_method').attr('id');

            // if there are no more ranges rows, change the delivery method row to the appropriate state
            if ($(this).closest('.delivery_method').find('.panel-body.ranges').length == 1) {
                $(this).closest('.delivery_method').find('.panel-heading .amount_field_show_ranges').empty().append(amount_field[method_id]);
                $(this).closest('.delivery_method').find('.panel-heading .addrange_link').append(addrange_link[method_id]).find('.add_ranges').hide();
            }
            $(this).closest('.panel-body.ranges').remove();
        });

    }

    // Log in
    $('#submit').click(function (event) {
        event.preventDefault();
        $(this).blur();

        var $formObject = $('form');
        var valid = validate($formObject.attr('id'));

        if (valid == 0) {
            $('.alert').slideUp().text('');

            // create JSON
            var form_data = [];
            var i = 0;

            $('.delivery_method').each(function () {

                form_data.push({
                    id : parseInt($.trim($(this).attr('id')).split('_').pop()),
                    name : $.trim($(this).find('.name').text()),
                    value : parseFloat($.trim($(this).find('[id^="amount"]').val())),
                    url : $.trim($(this).find('[id^="url"]').val()),
                    weight_from: parseFloat($.trim($(this).find('[id^="weight_from"]').val())),
                    weight_to: parseFloat($.trim($(this).find('[id^="weight_to"]').val())),
                    notes: $.trim($(this).find('[id^="notes"]').val()),
                    ranges: []
                });

                $(this).find('.ranges').each(function () {
                    form_data[i]['ranges'].push({
                        "range_from": parseFloat($.trim($(this).find('[id^="range_from"]').val())),
                        "range_to": parseFloat($.trim($(this).find('[id^="range_to"]').val())),
                        "price": parseFloat($.trim($(this).find('[id^="range_amount"]').val()))
                    });
                });
                i++;
            });

            // send form data to a PHP function
            var json_data = JSON.stringify(form_data);

            $.ajax({
                url: 'class/deliveryMethods.php',
                type: 'POST',
                data: {action: 'save_form', formdata: json_data},
                dataType: 'json',
                beforeSend: function(){
                    $("#ajax-loader").fadeIn();
                },
                success: function (data) {
                    $("#ajax-loader").fadeOut();

                    if (data.success == 1) {
                        $('#messageModal').on('show.bs.modal', function () {
                            var modal = $(this);
                            modal.find('.modal-title').text('Delivery methods are saved!');
                            modal.find('.modal-body p').text('All form data have been successfully added to the database.');
                            modal.find('.modal-content').removeClass('modal-fail').addClass('modal-success');
                        })
                    }
                    else if (data.success == 0) {
                        $('#messageModal').on('show.bs.modal', function () {
                            var modal = $(this);
                            modal.find('.modal-title').text('Back-end form validation');
                            modal.find('.modal-body p').text('There are some invalid input fields.');
                            modal.find('.modal-content').removeClass('modal-success').addClass('modal-fail');
                        })
                    }
                    else {
                        $('#messageModal').on('show.bs.modal', function () {
                            var modal = $(this);
                            modal.find('.modal-title').text('Delivery methods are not saved!');
                            modal.find('.modal-body p').text('There was an unpredicted error.');
                            modal.find('.modal-content').removeClass('modal-success').addClass('modal-fail');
                        })
                    }
                    $("#messageModal").modal({ backdrop: "static" });
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $('#messageModal').on('show.bs.modal', function () {
                        var modal = $(this);
                        modal.find('.modal-title').text('Error');
                        modal.find('.modal-body p').html(textStatus + '<br>' + errorThrown);
                        modal.find('.modal-content').removeClass('modal-success').addClass('modal-fail');
                    })
					$("#messageModal").modal({ backdrop: "static" });
                }
                })
                .always(function(html) {
                    $("#ajax-loader").fadeOut();
                });
        }
        else {
            var $ranges = $('.delivery_method').find('.panel_ranges');
            $ranges.attr('aria-expanded', 'true');
            $ranges.slideDown("slow");

            var $options = $('.delivery_method').find('.panel-body.options');
            $options.attr('aria-expanded', 'true');
            $options.slideDown("slow");

            if ($('.alert').is(":hidden")) {
                $('.alert-danger').slideDown("slow").text('Invalid input fields are highlighted in red. Make sure you enter numeric values in the appropriate fields, and whether the values in the "from" fields are less than or equal to the values in the "to" fields. Also, the values for ranges must be filled.');
            }
        }

    });

    // updates the "id" and label "for" attributes and sets the layout of delivery method rows
    function start_settings() {

        // for every delivery method
        $('.delivery_method').each(function () {
            // gets method id
            var method_id =  $(this).attr('id');
            // gets only order number from method id
            var method_id_number = method_id.split('_').pop();
            number_of_ranges[method_id] = 0;

            $(this)
                .find('[for="amount_"],[for="url_"],[for="weight_from_"],[for="weight_to_"],[for="notes_"]').attr('for', function( i, val ) {
                return val + method_id_number;
            }).end()
                .find('[id="amount_"],[id="url_"],[id="weight_from_"],[id="weight_to_"],[id="notes_"]').attr('id', function( i, val ) {
                return val + method_id_number;
            }).end();

            $(this).find('.ranges').each(function () {
                number_of_ranges[method_id]++;
                $(this)
                    .find('[for="range_from_"],[for="range_to_"],[for="range_amount_"]').attr('for', function( i, val ) {
                    return val + method_id_number + '_' + number_of_ranges[method_id];
                }).end()
                    .find('[id="range_from_"],[id="range_to_"],[id="range_amount_"]').attr('id', function( i, val ) {
                    return val + method_id_number + '_' + number_of_ranges[method_id];
                }).end();
            });

            // if ranges are set, changes layout of delivery method row
            if ($(this).find('.ranges').length > 0) {
                amount_field[method_id] = $(this).find('.amount_field_show_ranges').children().detach();
                $(this).find('.amount_field_show_ranges').append(show_ranges);
                $(this).find('.panel_ranges').attr('aria-expanded', 'false');
                addrange_link[method_id] = $(this).find('.addrange_link').children().detach();
            }

        });
    }

    // Input data validation
    function validate(form_id) {
        var errors = 0;
        $("form#" + form_id + " :input").each(function () {
            var input = $(this);

            // if ranges are set, "from" field is mandatory
            if(input.is('[id^="range_from"]')){
                if ($.isNumeric($.trim(input.val())) && parseFloat($.trim(input.val())) >= 0) {
                    input.parent().removeClass('has-error');
                }
                else {
                    input.parent().addClass('has-error');
                    errors++;
                }
            }

            // "to" field for ranges is not mandatory, because shop might not want to set an upper limit
            if(input.is('[id^="range_to"]')){
                if (!$.trim(input.val()) || ($.isNumeric($.trim(input.val())) && parseFloat($.trim(input.val())) > 0 &&
                    parseFloat($.trim(input.val())) >= parseFloat($.trim(input.closest('.form-inline').find('input[id^="range_from"]').val()))) ) {
                    input.parent().removeClass('has-error');
                }
                else {
                    input.parent().addClass('has-error');
                    errors++;
                }
            }

            // condition for URL
            if(input.is('[id^="url"]')){
                if (!$.trim(input.val()) ||  /^https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&;/=]*)$/i.test($.trim(input.val()))) {
                    input.parent().removeClass('has-error');
                }
                else {
                    input.parent().addClass('has-error');
                    errors++;
                }
            }

            // every delivery method can have no value or value as price (including zero)
            if(input.is('[id^="amount"]') || input.is('[id^="weight_from"]')){
                if (!$.trim(input.val()) || ($.isNumeric($.trim(input.val())) && parseFloat($.trim(input.val())) >= 0)) {
                    input.parent().removeClass('has-error');
                }
                else {
                    input.parent().addClass('has-error');
                    errors++;
                }
            }

            // "to" field for weight is not mandatory, but if it's set, check if it's a nonnegative number and compare to "from" field
            if(input.is('[id^="weight_to"]')){
                if (!$.trim(input.val()) || (!$.isNumeric(parseFloat($.trim(input.closest('.form-inline').find('input[id^="weight_from"]').val()))) && $.isNumeric($.trim(input.val())) && parseFloat($.trim(input.val())) > 0) ||
                    ($.isNumeric(parseFloat($.trim(input.closest('.form-inline').find('input[id^="weight_from"]').val()))) &&
                    parseFloat($.trim(input.val())) >= parseFloat($.trim(input.closest('.form-inline').find('input[id^="weight_from"]').val()))) ) {
                    input.parent().removeClass('has-error');
                }
                else {
                    input.parent().addClass('has-error');
                    errors++;
                }
            }

            // for every range there is a specific price (zero or more)
            if(input.is('[id^="range_amount"]')){
                if ($.isNumeric($.trim(input.val())) && parseFloat($.trim(input.val())) >= 0) {
                    input.parent().removeClass('has-error');
                }
                else {
                    input.parent().addClass('has-error');
                    errors++;
                }
            }
        });

        return errors;
    }

});