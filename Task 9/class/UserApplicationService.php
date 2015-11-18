<?php
/**
 * UserService class
 * User: Milos Savic
 */

class UserApplicationService extends UserServiceBase {

    public function getUserApplications($userId) {
        // similar to getUser, this should return data about user application from some database
        // but for instance, it prints only an id
        echo '<strong>App id:</strong> ' . $userId;
    }
}