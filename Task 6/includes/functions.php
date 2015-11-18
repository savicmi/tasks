<?php
include_once 'database.php';

class NewsLetter {

    private static $_instance = null;

    protected $email;
    protected $content;
    protected $conn;

    private $servername = "localhost"; // Host name
    private $username = "root"; // MySQL user name
    private $password = "root"; // MySQL password
    private $dbname = "newsletter"; // Database name

    private function __construct() {
        // connect to MySQL database
        $conn = new database($this->servername, $this->username, $this->password, $this->dbname);
        $this->conn = $conn->conn;
    }

    /**
     * send email messages
     * @return array $return_data
     */
    function send()
    {
        $stmt = "SELECT id, email, content
                 FROM newsletters
                 WHERE sent_status = 0";

        $result = $this->conn->query($stmt);
        $results = $result->fetch_all(MYSQLI_ASSOC);

        // the number of email messages to send
        $num_email = $result->num_rows;
        // the number of successfully sent email messages
        $sent = 0;

        if ($num_email > 0) {
            $sent_emails = [];

            foreach ($results as $row) {

                // Send mail form
                $to = $row['email'];
                $subject = "Newsletter";
                $message = '<html>
                                <head>
                                    <title>Newsletter</title>
                                </head>
                                <body>
                                <table style="width: 100%;">
                                  <tr>
                                    <td>
                                        <!--[if mso]>
                                        <table style="width: 600px;"><tr><td>
                                        <![endif]-->
                                        <div style="max-width: 700px; margin: 0 auto;">
                                          <table style="text-align: left;">
                                            <tr>
                                              <td>
                                                <table style="background:#f2f8ea; border-collapse:collapse; border-spacing:0; border:1px solid #426514; padding:0; margin:0 auto; text-align:left; vertical-align:top">
	                                                <tbody>
		                                                <tr style="padding:0; text-align:left; vertical-align:top" align="left">
                                                            <td style="border-collapse:collapse!important; color:#333333; font-family:Helvetica,Arial,sans-serif; font-size:14px; line-height:20px; margin:0; padding:0; text-align:left; vertical-align:top;" align="left" valign="top">
				                                                <div style="font-size:14px; font-weight:normal; line-height:20px; margin:20px">
					                                                <p style="color:#333333; margin:0 0 10px; padding:0;">
						                                                '.$row['content'].'
					                                                </p>
                                                                </div>
                                                            </td>
                                                        </tr>
	                                                </tbody>
                                                </table>
                                              </td>
                                            </tr>
                                          </table>
                                        </div>
                                      <!--[if mso]>
                                      </td></tr></table>
                                      <![endif]-->
                                    </td>
                                  </tr>
                                </table>
                                </body>
                            </html>';

                // To send HTML mail, the Content-type header must be set
                $headers  = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
                // Additional headers
                $headers .= 'From: Milos <milos@localhost.com>' . "\r\n";

                // returns true if the mail was successfully accepted for delivery
                if (mail($to, $subject, $message, $headers))
                    array_push($sent_emails, $row['id']);
            }

            $sent = count($sent_emails);

            // update sent status in the database
            $inclause = implode(',', array_fill(0,$sent,'?'));
            $update_stmt = "UPDATE newsletters
                            SET sent_status = 1
                            WHERE id IN (%s)";

            $prepared_sql = sprintf($update_stmt, $inclause);
            $upd_stmt = $this->conn->prepare($prepared_sql);

            // bind data
            $sentemails = array();
            $param_type = '';
            for($i = 0; $i < $sent; $i++)
                $param_type .= 'i';
            // with call_user_func_array, array params must be passed by reference
            $sentemails[] = & $param_type;
            for($i = 0; $i < $sent; $i++)
                $sentemails[] = & $sent_emails[$i];
            call_user_func_array(array($upd_stmt, 'bind_param'), $sentemails);

            $upd_stmt->execute();
            $upd_stmt->close();
        }

        $this->conn->close();

        $return_data = array('sent'=>$sent, 'num_email'=>$num_email);
        return $return_data;
    }

    /**
     * save email and content data to the database
     * @return int $num_rows
     */
    function save()
    {
        $stmt = "INSERT INTO newsletters (email, content)
                 VALUES (?, ?)";

        $stmt = $this->conn->prepare($stmt);

        // set parameters
        $this->email = $_POST['email'];
        $this->content = nl2br($_POST['content']);

        // bind data
        $stmt->bind_param("ss", $this->email, $this->content);
        $stmt->execute();

        // gets the number of affected rows in a previous MySQL operation
        $num_rows = $stmt->affected_rows;

        $stmt->close();
        $this->conn->close();
        return $num_rows;
    }

    public static function getInstance()
    {

        if (self::$_instance == null)
        {
            self::$_instance = new NewsLetter();
        }

        return self::$_instance;
    }

}