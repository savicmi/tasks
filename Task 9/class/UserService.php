<?php
/**
 * UserService class
 * User: Milos Savic
 */

class UserServiceBase {

    public $db;

    public function __construct(\PDO $pdo) {

        try
        {
            $this->db = $pdo;
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        }
        catch(PDOException $e)
        {
            echo 'Connection failed: ' . $e->getMessage();
        }

    }
}

class UserService extends UserServiceBase {

    public function getUser($userId) {
        // this should return data about user from some database
        // for instance, I've used newsletter database from task 5

        $stmt = "SELECT id, email, content
                 FROM newsletters
                 WHERE id = :id";

        $stmt = $this->db->prepare($stmt);
        $stmt->bindParam(":id", $userId, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $user_info = [];
        foreach ($result as $row) {
            if (!array_key_exists($row['id'], $user_info)) {
                $user_info[$row['id']] = [
                    'email' => $row['email'],
                    'content' => $row['content'],
                ];
            }
        }

        foreach ($user_info as $iter) {
            echo '<strong>User email:</strong> ' . $iter['email'] .'<br><strong>User message:</strong> '. $iter['content'] .'<br>';
        }

    }
}