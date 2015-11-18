$(document).ready(function(){
	$.getJSON('public/js/data.json', function(json){

        // if there is no data in the json.data array, messages will not be displayed
        if (json.data.length == 0) {
            $(".message-panel").remove();
        }
        // generate message panels
        else {
            var i = 0;
            var $msg_panel = $(".message-panel").last().parent().clone().html();
            $.each(json.data, function (i, field) {
                if (i > 0) {
                    $(".message-panel").last().after($msg_panel);
                }

                // get last message panel and insert data
                var $msg = $(".message-panel").last();
                $msg.attr('id', field.id);
                $msg.find('.from').attr('id', field.from.id);
                $msg.find('.from').text(field.from.name);

                // sets the date and time based on an Unix timestamp
                var date_sent = new Date(parseInt(field.date_sent) * 1000),
                    dd = ('0' + date_sent.getDate()).slice(-2),// Days are zero based, so add leading 0
                    mm = ('0' + (date_sent.getMonth() + 1)).slice(-2), // Add leading 0
                    yyyy = date_sent.getFullYear(),
                    h = ('0' + date_sent.getHours()).slice(-2),
                    min = ('0' + date_sent.getMinutes()).slice(-2),
                    sec = ('0' + date_sent.getSeconds()).slice(-2);
                date_sent = dd + '.' + mm + '.' + yyyy + ', ' + h + ':' + min + ':' + sec;
                $msg.find('.date span').text(date_sent);

                $msg.find('.subject').text(field.subject);
                $msg.find('.subject').after(field.message);

                i++;
            });
        }
	});

    // Get the message id and call delete_message function
    $(document).on('click', '.delete', function (event) {
        event.preventDefault();
        var $message = $(this).closest('.message-panel');
        var msg_id = $message.attr('id'); // message id

        delete_message(msg_id); // call delete_message
		$(this).blur();
    });

	// Get the message id and the sender id and call reply_message function
    $(document).on('click', '.reply', function (event) {
		event.preventDefault();
		var $message = $(this).closest('.message-panel');
        var msg_id = $message.attr('id'); // message id
        var sender_id = $message.find('.from').attr('id'); // sender id

        reply_message(msg_id, sender_id); // call reply_message
		$(this).blur();
	});

});

function delete_message(id){
	console.log('Delete message with id: '+id);
}

function reply_message(id, sender){
	console.log('Message id: '+id);
	console.log('Reply to: '+sender);
}