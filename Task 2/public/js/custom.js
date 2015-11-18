$(document).ready(function() {

    // action on click on the previous and next buttons
    $(document).on('click', '.calendar_header a', (function (event) {
        event.preventDefault();

        var params = getUrlVars($(this).attr('href'));

        $.ajax({
                url: 'includes/functions.php',
                type: 'POST',
                data: { action: 'get_month', mesec: params.mesec, godina: params.godina},
                dataType: 'json',
                success: function (data) {
                    $("div").html(data.calendar);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $("div").html('Error: ' + textStatus + '<br>' + errorThrown);
                }
        })

    }))

    // gets a month and year from the url
    function getUrlVars(url) {

        var vars = {};
        var params = url.split("?")[1];
        var param = params.split('&');

        for (var i = 0; i < param.length; i++) {
            params = param[i].split("=");
            vars[params[0]] = params[1];
        }
        return vars;
    }

});
