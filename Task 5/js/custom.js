$(document).ready(function() {

    $("input[required='required']").closest(".form-group").children("label").append('<span class="req">*</span>');

    // Log in
    $('#submit').click(function (event) {
        event.preventDefault();
        var $formObject = $(this).closest('form');
        var valid = validate($formObject.attr('id'));

        if (valid == 0) {
            var fields = {
                address: $.trim($formObject.find('#url').val()),
                item: $.trim($formObject.find('#item').val())
            };

            $.ajax({
                url: 'mvc/index.php',
                type: 'POST',
                data: fields,
                dataType: 'json',
                beforeSend: function(){
                    $("#ajax-loader").fadeIn();
                },
                success: function (data) {
                    $("#ajax-loader").fadeOut();

                    $(".results-panel").slideDown('slow');
                    if (!data.links)
                        $(".links-panel").slideUp('slow');
                    else
                        $(".links-panel").slideDown('slow');

                    if (data.success == 0)
                        $("#results").html(data.message);
                    else {
                        if (!data.results)
                            $("#results").html('That item doesn\'t exist or there is no visual content.');
                        else {
                            $("#results").html(data.results);
                            $("#links").html(data.links);
                        }
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $(".results-panel").slideDown('slow');
                    $("#results").html('Error: ' + textStatus + '<br>' + errorThrown);
                },
                statusCode: {
                    404: function() {
                        $(".results-panel").slideDown('slow');
                        $("#results").html('Error: Page not found.');
                    }
                }
            })
                .always(function(html) {
                    $("#ajax-loader").fadeOut();
                });
        }
		$(this).blur();

    });

    // Input data validation
    function validate(form_id) {
        var errors = 0;
        $("form#" + form_id + " :input").each(function () {
            var input = $(this);

            // Condition for web address
            if (input.attr('id') == 'url') {
                if (/^https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&;//=]*)$/i.test($.trim(input.val()))) {
                    input.next().slideUp().text('');
                    input.parent().removeClass('has-error');
                }
                else {
                    input.parent().addClass('has-error');
                    errors++;
                    if (input.next().is(":hidden")) {
                        input.next().slideDown("slow").text('Invalid URL format.');
                    }
                }
            }

            // Condition for item, allow between 2 and 100 characters including spaces
            if (input.attr('id') == 'item') {
                if (/^.{2,100}$/i.test($.trim(input.val()))) {
                    input.next().slideUp().text('');
                    input.parent().removeClass('has-error');
                }
                else {
                    input.parent().addClass('has-error');
                    errors++;
                    if (input.next().is(":hidden")) {
                        input.next().slideDown("slow").text('Invalid input for an item. It must contains between 2 and 100 characters.');
                    }
                }
            }
        });

        return errors;
    }

});