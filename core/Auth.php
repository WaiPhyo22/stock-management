<?php
// core/Auth.php

class Auth
{
    public static function start() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function loginCheck() {
        self::start();
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
    }

    public static function isAdmin() {
        self::start();
        if (!($_SESSION['user']['role'] ?? '') === 'admin') {
            http_response_code(403);
            echo "<h1>403 Forbidden</h1>";
            echo "<p>Admin access only.</p>";
            exit;
        }
    }
}
