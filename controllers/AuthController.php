<?php
require_once __DIR__ . '/../models/User.php';
session_start();

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function showLoginForm($error = '') {
        include __DIR__ . '/../views/auth/login.php';  // login form view
    }

    public function handleLogin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $user = $this->userModel->findByEmail($email);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'role' => $user['role']
                ];
                if ($user['role'] === 'admin') {
                    header("Location: /users");
                } else {
                    header("Location: /products");

                }
                exit;
            } else {
                $this->showLoginForm("Invalid email or password.");
            }
        }
    }

    public function logout() {
        session_destroy();
        header("Location: /login");
    }

    public function checkAuth() {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
    }

    public function isAdmin() {
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
            include __DIR__ . '/../views/error/403.php';
            exit;
        }
    }
}
?>
