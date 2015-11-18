$(document).ready(function() {

    $("input[required='required'], textarea").closest(".form-group").children("label").append('<span class="req">*</span>');

    // send form data to the database
    $('#save').click(function (event) {
        event.preventDefault();
        var $formObject = $(this).closest('form');
        var valid = validate($formObject.attr('id'));

        if (valid == 0) {
            var fields = {
                email: $.trim($formObject.find('#email').val()),
                content: $.trim($formObject.find('#content').val())
            };

            $.ajax({
                url: 'add.php',
                type: 'POST',
                data: fields,
                dataType: 'json',
                beforeSend: function(){
                    $("#ajax-loader").fadeIn();
                },
                success: function (data) {
                    $("#ajax-loader").fadeOut();

                    if (data.success == 1) {
                        $('#messageModal').on('show.bs.modal', function () {
                            var modal = $(this);
                            modal.find('.modal-title').text('Data for a newsletter are saved!');
                            modal.find('.modal-body p').text('All form data have been successfully added to the database.');
                            modal.find('.modal-content').removeClass('modal-fail').addClass('modal-success');
                        })
                    }
                    else if (data.success == 0) {
                        $('#messageModal').on('show.bs.modal', function () {
                            var modal = $(this);
                            modal.find('.modal-title').text('Data for a newsletter are not saved!');
                            modal.find('.modal-body p').text('Data entry to the database is failed.');
                            modal.find('.modal-content').removeClass('modal-success').addClass('modal-fail');
                        })
                    }
                    $("#messageModal").modal({ backdrop: "static" });
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $('#messageModal').on('show.bs.modal', function () {
                        var modal = $(this);
                        modal.find('.modal-title').text('Error '+jqXHR.status);
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

        $(this).blur();

    });

    // Input data validation
    function validate(form_id) {
        var errors = 0;
        $("form#" + form_id + " :input").each(function () {
            var input = $(this);

            // Condition for email
            if (input.attr('id') == 'email') {
                if (/^([\w'-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i.test($.trim(input.val()))) {
                    input.next().slideUp().text('');
                    input.parent().removeClass('has-error');
                }
                else {
                    input.parent().addClass('has-error');
                    errors++;
                    if (input.next().is(":hidden")) {
                        input.next().slideDown("slow").text('Invalid Email address.');
                    }
                }
            }

            // Condition for email content, allow between 5 and 500 characters including spaces
            if (input.attr('id') == 'content') {
                if (/^(.|[ \r\n]){5,500}$/im.test($.trim(input.val()))) {
                    input.next().slideUp().text('');
                    input.parent().removeClass('has-error');
                }
                else {
                    input.parent().addClass('has-error');
                    errors++;
                    if (input.next().is(":hidden")) {
                        input.next().slideDown("slow").text('Invalid email content. It must contains between 5 and 500 characters.');
                    }
                }
            }
        });

        return errors;
    }

});