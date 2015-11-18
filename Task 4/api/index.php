<?php
/**
 * User: Milos Savic
 * Please read "readme" file for instructions on how to use API
 */

// this abstract class will act as a wrapper for all of the custom endpoints that our API will be using
abstract class API
{
    /**
     * Property: method
     * The HTTP method this request was made in (GET, POST, PUT or DELETE)
     */
    protected $method = '';

    /**
     * Property: endpoint
     * The Model requested in the URI (eg: /messages)
     */
    protected $endpoint = '';

    /**
     * Property: args
     * Any additional URI components after the endpoint have been removed,
     * in our case, an integer ID for the resource (eg: /messages/<arg0>/<arg1>
     * or /messages/<arg0>)
     */
    protected $args = array();

    /**
     * Property: file
     * Stores the input of the PUT request
     */
    protected $file = null;

    /**
     * Constructor: __construct
     * @param string $request
     * @throws Exception
     * Allow for CORS, assemble and pre-process the data
     */
    public function __construct($request) {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: *");
        header("Content-Type: application/json");

        // $request variable from the .htaccess file has been exploded around the slash
        $this->args = explode('/', rtrim($request, '/'));

        // the first element we set to the $endpoint and any remaining items are used as $args
        $this->endpoint = array_shift($this->args);

        // get $method
        // DELETE and PUT requests are hidden inside a POST request through the use of the HTTP_X_HTTP_METHOD header
        $this->method = $_SERVER['REQUEST_METHOD'];
        if ($this->method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
            if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
                $this->method = 'DELETE';
            } else if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
                $this->method = 'PUT';
            } else {
                throw new Exception("Unexpected header");
            }
        }

        switch($this->method) {
            case 'GET':
                $this->request = $this->_cleanInputs($_GET);
                break;
            case 'DELETE':
                $this->request = $this->_cleanInputs($_POST);
                break;
            case 'PUT':
                $this->request = $this->_cleanInputs($_GET);
                $this->file = file_get_contents("php://input");
                break;
            case 'POST':
                $this->request = $this->_cleanInputs($_POST);
                $this->file = file_get_contents("php://input");
                break;
            default:
                $this->_response('Invalid method', 405);
                break;
        }
    }

    // checks if the concrete class implements a method for the endpoint that the client requested
    public function processAPI() {
        if (method_exists($this, $this->endpoint)) {
            return $this->_response($this->{$this->endpoint}($this->args));
        }
        return $this->_response("No endpoint: $this->endpoint", 404);
    }

    // returns the response
    private function _response($data, $status = 200) {
        header("HTTP/1.1 " . $status . " " . $this->_requestStatus($status));
        return json_encode($data, JSON_PRETTY_PRINT);
    }

    // input sanitizer
    private function _cleanInputs($data) {
        $clean_input = array();
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $clean_input[$k] = $this->_cleanInputs($v);
            }
        } else {
            $clean_input = trim(strip_tags($data));
        }
        return $clean_input;
    }

    private function _requestStatus($code) {
        $status = array(
            200 => 'OK',
            404 => 'Not found',
            405 => 'Method not allowed',
            500 => 'Internal server error',
        );
        return ($status[$code])?$status[$code]:$status[500];
    }
}

class MyAPI extends API
{
    // invoke a parent constructor from API class
    public function __construct($request) {
        parent::__construct($request);
    }

    protected function messages() {
        if ($this->method == 'GET' && !empty($this->args)) {

            $response = self::get_messages($this->args);

            if (is_null($response))
                $response = [];

            $json = array('data'=>$response);
            return $json;

        } elseif ($this->method == 'GET') {

            $json_data = file_get_contents('../public/js/data.json');
            $json = json_decode($json_data, true);

            return $json;

        } elseif ($this->method == 'DELETE' && !empty($this->args)) {

            $response = self::delete_messages($this->args);

            if (is_null($response))
                $response = [];

            $json = array('data'=>$response);
            return $json;

        } elseif ($this->method == 'DELETE') {
            return "DELETE request requires an argument (message id)";
        } elseif ($this->method == 'PUT' && !empty($this->args)) {

            $file = $this->file;
            $file = json_decode($file, true);

            if (!$file)
                return 'Argument is not a valid JSON';

            $response = self::put_message($this->args, $file);

            if ($response == false)
                return 'Message with ID '.$this->args[0].' does not exist. If you want to add a new message with that ID, use POST request!';

            $json = array('data'=>$response);
            return $json;

        } elseif ($this->method == 'PUT') {
            return "PUT request requires an argument (message id)";
        } elseif ($this->method == 'POST') {

            $file = $this->file;
            $file = json_decode($file, true);

            if (!$file)
                return 'Argument is not a valid JSON';

            $response = self::post_message($file);

            $json = array('data'=>$response);
            return $json;

        } else {
            return "Only accepts GET, POST, PUT and DELETE requests";
        }
    }

    protected function get_messages($args)
    {
        $json_data = file_get_contents('../public/js/data.json');
        $json = json_decode($json_data, true);

        // iterates over each value in the array passing them to the callback function
        $messages = array_filter($json['data'], function($data) use ($args) {;
            return in_array($data['id'], $args);
        });

        if ($messages) {
            // returns all the values from the array and indexes the array numerically from 0
            $messages = array_values($messages);

            // sort the response array according to the message id
            // using a user defined function
            usort($messages, function($a, $b) {
                return $a['id'] > $b['id'] ? -1 : 1;
            });

            return $messages;
        }
        else
            return null;
    }

    protected function delete_messages($args)
    {
        $json_data = file_get_contents('../public/js/data.json');
        $json = json_decode($json_data, true);

        // iterates over each value in the array passing them to the callback function
        // gets messages where id is not equal to arguments
        $messages = array_filter($json['data'], function($data) use ($args) {;
            return !in_array($data['id'], $args);
        });

        if ($messages) {
            // returns all the values from the array and indexes the array numerically from 0
            $messages = array_values($messages);

            // sort the response array according to the message id
            // using a user defined function
            usort($messages, function($a, $b) {
                return $a['id'] > $b['id'] ? -1 : 1;
            });

            return $messages;
        }
        else
            return null;
    }

    protected function put_message($args, $file)
    {
        $json_data = file_get_contents('../public/js/data.json');
        $json = json_decode($json_data, true);

        // check if message ID ($args[0]) exists and gets index
        $id_exists = false;
        foreach ($json['data'] as $key=>$message)
            if (isset($message['id']) && ($message['id'] == $args[0])) {
                $id_exists = true;
                $index = $key;
                break;
            }
        if ($id_exists == false)
            return false;

        // checks if a message already has the same id and if so, the message will keep its old id
        if (isset($file['id']) && ctype_digit($file['id']))
            $msg_id = abs($file['id']);
        else
            $msg_id = $args[0];
        if ($file['id'] != $args[0])
            foreach ($json['data'] as $key=>$message)
                if (isset($message['id']) && isset($file['id']) && ($message['id'] == $file['id'])) {
                    $msg_id = $args[0];
                    break;
                }

        // $current is a message with
        $current = $json['data'][$index];

        // this is the updated message
        $postData = array (
            'id' => "$msg_id",
            'from' =>
                array (
                    'id' => (array_key_exists('id',$file['from']) ? $file['from']['id'] : $current['from']['id']),
                    'name' => (array_key_exists('name',$file['from']) ? $file['from']['name'] : $current['from']['name']),
                ),
            'to' =>
                array (
                    'id' => (array_key_exists('id',$file['to']) ? $file['to']['id'] : $current['to']['id']),
                    'name' => (array_key_exists('name',$file['to']) ? $file['to']['name'] : $current['to']['name']),
                ),
            'type' => (array_key_exists('type',$file) ? $file['type'] : $current['type']),
            'replyto' => (array_key_exists('replyto',$file) ? $file['replyto'] : $current['replyto']),
            'date_sent' => (array_key_exists('date_sent',$file) ? $file['date_sent'] : $current['date_sent']),
            'date_read' => (array_key_exists('date_read',$file) ? $file['date_read'] : $current['date_read']),
            'subject' => (array_key_exists('subject',$file) ? $file['subject'] : $current['subject']),
            'message' => (array_key_exists('message',$file) ? $file['message'] : $current['message']),
            'message_formatted' => (array_key_exists('message_formatted',$file) ? $file['message_formatted'] : $current['message_formatted']),
            'date_sent_formatted' =>
                array (
                    'id' => (array_key_exists('id',$file['date_sent_formatted']) ? $file['date_sent_formatted']['id'] : $current['date_sent_formatted']['id']),
                    'timestamp' => (array_key_exists('timestamp',$file['date_sent_formatted']) ? $file['date_sent_formatted']['timestamp'] : $current['date_sent_formatted']['timestamp']),
                    'month' => (array_key_exists('month',$file['date_sent_formatted']) ? $file['date_sent_formatted']['month'] : $current['date_sent_formatted']['month']),
                    'day' => (array_key_exists('day',$file['date_sent_formatted']) ? $file['date_sent_formatted']['day'] : $current['date_sent_formatted']['day']),
                    'year' => (array_key_exists('year',$file['date_sent_formatted']) ? $file['date_sent_formatted']['year'] : $current['date_sent_formatted']['year']),
                    'week' => (array_key_exists('week',$file['date_sent_formatted']) ? $file['date_sent_formatted']['week'] : $current['date_sent_formatted']['week']),
                    'dayid' => (array_key_exists('dayid',$file['date_sent_formatted']) ? $file['date_sent_formatted']['dayid'] : $current['date_sent_formatted']['dayid']),
                    'weekday' => (array_key_exists('weekday',$file['date_sent_formatted']) ? $file['date_sent_formatted']['weekday'] : $current['date_sent_formatted']['weekday']),
                    'mname' => (array_key_exists('mname',$file['date_sent_formatted']) ? $file['date_sent_formatted']['mname'] : $current['date_sent_formatted']['mname']),
                    'formatted' => (array_key_exists('formatted',$file['date_sent_formatted']) ? $file['date_sent_formatted']['formatted'] : $current['date_sent_formatted']['formatted']),
                ),
            'date_read_formatted' =>
                array (
                    'id' => (array_key_exists('id',$file['date_read_formatted']) ? $file['date_read_formatted']['id'] : (array_key_exists('id',$current['date_read_formatted']) ? $current['date_read_formatted']['id'] : '')),
                    'timestamp' => (array_key_exists('timestamp',$file['date_read_formatted']) ? $file['date_read_formatted']['timestamp'] : (array_key_exists('timestamp',$current['date_read_formatted']) ? $current['date_read_formatted']['timestamp'] : '')),
                    'month' => (array_key_exists('month',$file['date_read_formatted']) ? $file['date_read_formatted']['month'] : (array_key_exists('month',$current['date_read_formatted']) ? $current['date_read_formatted']['month'] : '')),
                    'day' => (array_key_exists('day',$file['date_read_formatted']) ? $file['date_read_formatted']['day'] : (array_key_exists('day',$current['date_read_formatted']) ? $current['date_read_formatted']['day'] : '')),
                    'year' => (array_key_exists('year',$file['date_read_formatted']) ? $file['date_read_formatted']['year'] : (array_key_exists('year',$current['date_read_formatted']) ? $current['date_read_formatted']['year'] : '')),
                    'week' => (array_key_exists('week',$file['date_read_formatted']) ? $file['date_read_formatted']['week'] : (array_key_exists('week',$current['date_read_formatted']) ? $current['date_read_formatted']['week'] : '')),
                    'dayid' => (array_key_exists('dayid',$file['date_read_formatted']) ? $file['date_read_formatted']['dayid'] : (array_key_exists('dayid',$current['date_read_formatted']) ? $current['date_read_formatted']['dayid'] : '')),
                    'weekday' => (array_key_exists('weekday',$file['date_read_formatted']) ? $file['date_read_formatted']['weekday'] : (array_key_exists('weekday',$current['date_read_formatted']) ? $current['date_read_formatted']['weekday'] : '')),
                    'mname' => (array_key_exists('mname',$file['date_read_formatted']) ? $file['date_read_formatted']['mname'] : (array_key_exists('mname',$current['date_read_formatted']) ? $current['date_read_formatted']['mname'] : '')),
                    'formatted' => (array_key_exists('formatted',$file['date_read_formatted']) ? $file['date_read_formatted']['formatted'] : (array_key_exists('formatted',$current['date_read_formatted']) ? $current['date_read_formatted']['formatted'] : '')),
                )
        );

        $json['data'][$index] = $postData;

        $messages = $json['data'];

        // sort the response array according to the message id
        // using a user defined function
        usort($messages, function($a, $b) {
            return $a['id'] > $b['id'] ? -1 : 1;
        });

        return $messages;
    }

    protected function post_message($file)
    {
        $json_data = file_get_contents('../public/js/data.json');
        $json = json_decode($json_data, true);

        // checks if a message already has the same id and if so, a new message will change
        // to the first higher number after the biggest in the array
        if (isset($file['id']) && ctype_digit($file['id']))
            $msg_id = abs($file['id']);

        $max_id = -1;
        $id_exists = false;
        foreach ($json['data'] as $key=>$message) {
            if (isset($message['id']) && $message['id'] > $max_id)
                $max_id = $message['id'];
            if (isset($message['id']) && isset($file['id']) && ($message['id'] == $file['id']))
                $id_exists = true;
        }
        if ($id_exists == true)
            $msg_id = $max_id + 1;

        // this is the updated message
        $postData = array (
            'id' => "$msg_id",
            'from' =>
                array (
                    'id' => (array_key_exists('id',$file['from']) ? $file['from']['id'] : ''),
                    'name' => (array_key_exists('name',$file['from']) ? $file['from']['name'] : ''),
                ),
            'to' =>
                array (
                    'id' => (array_key_exists('id',$file['to']) ? $file['to']['id'] : ''),
                    'name' => (array_key_exists('name',$file['to']) ? $file['to']['name'] : ''),
                ),
            'type' => (array_key_exists('type',$file) ? $file['type'] : ''),
            'replyto' => (array_key_exists('replyto',$file) ? $file['replyto'] : ''),
            'date_sent' => (array_key_exists('date_sent',$file) ? $file['date_sent'] : ''),
            'date_read' => (array_key_exists('date_read',$file) ? $file['date_read'] : ''),
            'subject' => (array_key_exists('subject',$file) ? $file['subject'] : ''),
            'message' => (array_key_exists('message',$file) ? $file['message'] : ''),
            'message_formatted' => (array_key_exists('message_formatted',$file) ? $file['message_formatted'] : ''),
            'date_sent_formatted' =>
                array (
                    'id' => (array_key_exists('id',$file['date_sent_formatted']) ? $file['date_sent_formatted']['id'] : ''),
                    'timestamp' => (array_key_exists('timestamp',$file['date_sent_formatted']) ? $file['date_sent_formatted']['timestamp'] : ''),
                    'month' => (array_key_exists('month',$file['date_sent_formatted']) ? $file['date_sent_formatted']['month'] : ''),
                    'day' => (array_key_exists('day',$file['date_sent_formatted']) ? $file['date_sent_formatted']['day'] : ''),
                    'year' => (array_key_exists('year',$file['date_sent_formatted']) ? $file['date_sent_formatted']['year'] : ''),
                    'week' => (array_key_exists('week',$file['date_sent_formatted']) ? $file['date_sent_formatted']['week'] : ''),
                    'dayid' => (array_key_exists('dayid',$file['date_sent_formatted']) ? $file['date_sent_formatted']['dayid'] : ''),
                    'weekday' => (array_key_exists('weekday',$file['date_sent_formatted']) ? $file['date_sent_formatted']['weekday'] : ''),
                    'mname' => (array_key_exists('mname',$file['date_sent_formatted']) ? $file['date_sent_formatted']['mname'] : ''),
                    'formatted' => (array_key_exists('formatted',$file['date_sent_formatted']) ? $file['date_sent_formatted']['formatted'] : ''),
                ),
            'date_read_formatted' =>
                array (
                    'id' => (array_key_exists('id',$file['date_read_formatted']) ? $file['date_read_formatted']['id'] : ''),
                    'timestamp' => (array_key_exists('timestamp',$file['date_read_formatted']) ? $file['date_read_formatted']['timestamp'] : ''),
                    'month' => (array_key_exists('month',$file['date_read_formatted']) ? $file['date_read_formatted']['month'] : ''),
                    'day' => (array_key_exists('day',$file['date_read_formatted']) ? $file['date_read_formatted']['day'] : ''),
                    'year' => (array_key_exists('year',$file['date_read_formatted']) ? $file['date_read_formatted']['year'] : ''),
                    'week' => (array_key_exists('week',$file['date_read_formatted']) ? $file['date_read_formatted']['week'] : ''),
                    'dayid' => (array_key_exists('dayid',$file['date_read_formatted']) ? $file['date_read_formatted']['dayid'] : ''),
                    'weekday' => (array_key_exists('weekday',$file['date_read_formatted']) ? $file['date_read_formatted']['weekday'] : ''),
                    'mname' => (array_key_exists('mname',$file['date_read_formatted']) ? $file['date_read_formatted']['mname'] : ''),
                    'formatted' => (array_key_exists('formatted',$file['date_read_formatted']) ? $file['date_read_formatted']['formatted'] : ''),
                )
        );

        // prepend $postData (a new message) to the beginning of the JSON array
        array_unshift($json['data'], $postData);

        $messages = $json['data'];

        // sort the response array according to the message id
        // using a user defined function
        usort($messages, function($a, $b) {
            return $a['id'] > $b['id'] ? -1 : 1;
        });

        return $messages;
    }
}

// create an instance of MyAPI class and invoke processAPI()
try {
    $api = new MyAPI($_REQUEST['request']);
    echo $api->processAPI();
} catch (Exception $e) {
    echo json_encode(array('error' => $e->getMessage()));
}