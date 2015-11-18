<?php
/**
 * UserServiceProvider class
 * User: Milos Savic
 */

class UserServiceProvider implements Provider {

    public function register(Container $container) {

        $db = $container->get("db");

        $container->set("UserService", function() use($db) {
            return new UserService($db);
        });

        $container->set("UserApplicationService", function() use($db) {
            return new UserApplicationService($db);
        });
    }
}