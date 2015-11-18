<?php
/**
 * User: Milos Savic
 */
include_once 'database.php';

class DeliveryMethods
{
    public $conn;

    public function __construct() {

        $servername = "localhost"; // Host name
        $username = "root"; // MySQL user name
        $password = "root"; // MySQL password
        $dbname = "deliveries"; // Database name

        $conn = new database($servername, $username, $password, $dbname);

        $this->conn = $conn->conn;
    }

    /**
     * gets delivery methods from database
     * @return array $deliveryMethods
     */
    public function get_methods()
    {
        $stmt = "SELECT dm.*, r.range_from, r.range_to, r.price
                 FROM delivery_method as dm LEFT JOIN delivery_ranges as r ON dm.id = r.delivery_method_id
                 ORDER BY dm.id";

        $result = $this->conn->query($stmt);
        $results = $result->fetch_all(MYSQLI_ASSOC);

        $deliveryMethods = [];

        foreach ($results as $row) {
            // checks if the given key (method's id) exists in the array and puts data into it
            if (!array_key_exists($row['id'], $deliveryMethods)) {
                $deliveryMethods[$row['id']] = [
                    'id' => (int)$row['id'],
                    'name' => $row['name'],
                    'value' => ($row['value'] != NULL) ? (float)$row['value'] : NULL,
                    'url' => $row['url'],
                    'weight_from' => ($row['weight_from'] != NULL) ? (float)$row['weight_from'] : NULL,
                    'weight_to' => ($row['weight_to'] != NULL) ? (float)$row['weight_to'] : NULL,
                    'notes' => $row['notes'],
                    'status' => $row['status'],
                    'ranges' => []
                ];
            }

            // if all three values for ranges are null, then that method have no ranges
            if ($row['range_from'] != NULL || $row['range_to'] != NULL || $row['price'] != NULL)
                $deliveryMethods[$row['id']]['ranges'][] =
                    [   'range_from' => ($row['range_from'] != NULL) ? (float)$row['range_from'] : NULL,
                        'range_to' => ($row['range_to'] != NULL) ? (float)$row['range_to'] : NULL,
                        'price' => ($row['price'] != NULL) ? (float)$row['price'] : NULL
                    ];

        }
        // close database connection
        $this->conn->close();

        return $deliveryMethods;
        //print("<pre>" . json_encode(array_values($deliveryMethods), JSON_PRETTY_PRINT). "</pre>");
    }

    /**
     * save delivery methods
     * @param array $data
     */
    public function save_data($data) {
        $update_dm_stmt = "UPDATE delivery_method
                           SET value = ?, url = ?, weight_from = ?, weight_to = ?, notes = ?, status = ?
                           WHERE id = ?";

        $delete_range_stmt = "DELETE FROM delivery_ranges
                              WHERE delivery_method_id = ?";

        $insert_range_stmt = "INSERT INTO delivery_ranges (delivery_method_id, range_from, range_to, price)
                              VALUES (?, ?, ?, ?) ";

        // first we update methods in the delivery_method table
        $stmt = $this->conn->prepare($update_dm_stmt);
        foreach ($data as $dm) {

            // set parameters
            $value = $dm['value'];
            $url = $dm['url'];
            $weight_from = $dm['weight_from'];
            $weight_to = $dm['weight_to'];
            $notes = $dm['notes'];
            $id = $dm['id'];

            if (!empty($dm['ranges']))
                $status = 'ranges';
            else if (is_null($value))
                $status = 'unavailable';
            else if ($value == 0)
                $status = 'free';
            else
                $status = 'has_price';

            // bind data
            $stmt->bind_param("dsddssi", $value, $url, $weight_from, $weight_to, $notes, $status, $id);

            // execute
            $stmt->execute();

            // as the second step, we delete all existing ranges of our methods
            $delete_stmt = $this->conn->prepare($delete_range_stmt);
            $delete_stmt->bind_param("i", $dm['id']);
            $delete_stmt->execute();

            // finally, in the third step, we insert new ranges for delivery methods
            foreach ($dm['ranges'] as $range) {
                $range_stmt = $this->conn->prepare($insert_range_stmt);

                // set parameters
                $delivery_method_id = $dm['id'];
                $range_from = $range['range_from'];
                $range_to = $range['range_to'];
                $price = $range['price'];

                // bind data
                $range_stmt->bind_param("iddd", $delivery_method_id, $range_from, $range_to, $price);
                $range_stmt->execute();

                $range_stmt->close();
            }
            $delete_stmt->close();
        }

        $stmt->close();
        $this->conn->close();
    }

    /**
     * back-end form validation
     * @param array $data
     * @return boolean $validate
     */
    public function validate($data) {

        $validate = true;
        foreach ($data as $dm) {

            //  if a variable is set and is not NULL
            if (isset($dm['value'])) {
                if (!(is_numeric($dm['value']) && $dm['value'] >= 0)) {
                    $validate = false;
                    break;
                }
            }

            if (isset($dm['url']) && $dm['url'] != '') {
                if (!preg_match('/^https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&;\/=]*)$/i', $dm['url'])) {
                    $validate = false;
                    break;
                }
            }

            if (isset($dm['weight_from'])) {
                if (!(is_numeric($dm['weight_from']) && $dm['weight_from'] >= 0)) {
                    $validate = false;
                    break;
                }
                if (isset($dm['weight_to']) && $dm['weight_to'] < $dm['weight_from']) {
                    $validate = false;
                    break;
                }
            }
            if (isset($dm['weight_to'])) {
                if (!(is_numeric($dm['weight_to']) && $dm['weight_to'] > 0)) {
                    $validate = false;
                    break;
                }
            }

            // check ranges
            if (isset($dm['ranges']) && !empty($dm['ranges'])) {
                foreach ($dm['ranges'] as $range) {
                    if (!array_key_exists('range_from', $range) || !(is_numeric($range['range_from']) && $range['range_from'] >= 0)) {
                        $validate = false;
                        break;
                    }
                    if (!array_key_exists('range_to', $range) || !((is_numeric($range['range_to']) && $range['range_to'] > 0) || is_null($range['range_to']))) {
                        $validate = false;
                        break;
                    }
                    if (is_numeric($range['range_from']) && is_numeric($range['range_to']) && $range['range_to'] < $range['range_from']) {
                        $validate = false;
                        break;
                    }
                    if (!array_key_exists('price', $range) || !(is_numeric($range['price']) && $range['price'] >= 0)) {
                        $validate = false;
                        break;
                    }
                }
            }
        }

        return $validate;
    }

}

// communication with jQuery Ajax methods
if(isset($_POST['action']) && !empty($_POST['action'])) {
    $action = $_POST['action'];

    switch($action) {
        case 'save_form' : save_form(); break;
    }
}

function save_form() {
    error_reporting(E_ERROR);
    if (!ini_get('display_errors'))
        ini_set('display_errors', 1);

    if(isset($_POST['formdata']) && !empty($_POST['formdata']))
        $data = json_decode($_POST['formdata'], true);

    $dm = new DeliveryMethods();

    if($dm->validate($data)){
        $dm->save_data($data);
        $json = array('success' => 1);
        echo json_encode($json);
    }
    else {
        $json = array('success' => 0);
        echo json_encode($json);
    }

}

