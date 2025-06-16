<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../controllers/AuthController.php';

class UserController
{
    private $userModel;
    private $auth;

    public function __construct($userModel = null, $auth = null)
    {
        $this->userModel = $userModel ?? new User();
        $this->auth = $auth ?? new AuthController();
        $this->auth->checkAuth();
        $this->auth->isAdmin();
    }

    public function index()
    {
        $perPage = 10;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $perPage;

        $users = $this->userModel->getPaginated($perPage, $offset);
        $totalUsers = $this->userModel->countAll();
        $totalPages = ceil($totalUsers / $perPage);

        require __DIR__ . '/../views/user/index.php';
    }

    public function create()
    {
        require __DIR__ . '/../views/user/create.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = [];

            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? '';

            if ($name === '') {
                $errors[] = 'Name is required.';
            }

            if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'A valid email is required.';
            }

            if (strlen($password) < 6) {
                $errors[] = 'Password must be at least 6 characters.';
            }

            if (!in_array($role, ['user', 'admin'])) {
                $errors[] = 'Invalid role selected.';
            }

            if ($this->userModel->existsByEmail($email)) {
                $errors[] = 'Email already exists.';
            }

            if (!empty($errors)) {
                require __DIR__ . '/../views/user/create.php';
                return;
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $created = $this->userModel->create($name, $email, $hashedPassword, $role);
            if ($created) {
                $_SESSION['success'] = "User created successfully.";
                header('Location: /users');
                exit;
            } else {
                $error = "Failed to create user.";
                require __DIR__ . '/../views/user/create.php';
            }
        }
    }

    public function edit()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: /users');
            exit;
        }
        $user = $this->userModel->findById($id);
        if (!$user) {
            header('Location: /users');
            exit;
        }
        require __DIR__ . '/../views/user/edit.php';
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = [];

            $id = $_POST['id'] ?? null;
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? '';

            if (!$id || !is_numeric($id)) {
                $errors[] = 'Invalid user ID.';
            }

            if ($name === '') {
                $errors[] = 'Name is required.';
            }

            if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'A valid email is required.';
            } elseif ($this->userModel->existsByEmail($email, $id)) {
                $errors[] = 'Email already exists.';
            }

            if ($password !== '' && strlen($password) < 6) {
                $errors[] = 'If provided, password must be at least 6 characters.';
            }

            if (!in_array($role, ['user', 'admin'])) {
                $errors[] = 'Invalid role selected.';
            }

            if (!empty($errors)) {
                $user = $this->userModel->findById($id);
                require __DIR__ . '/../views/user/edit.php';
                return;
            }

            if (!empty($password)) {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            } else {
                $oldUser = $this->userModel->findById($id);
                $hashedPassword = $oldUser['password'];
            }

            $updated = $this->userModel->update($id, $name, $email, $hashedPassword, $role);
            if ($updated) {
                $_SESSION['success'] = "User updated successfully.";
                header('Location: /users');
                exit;
            } else {
                $error = "Failed to update user.";
                $user = ['id' => $id, 'name' => $name, 'email' => $email, 'role' => $role];
                require __DIR__ . '/../views/user/edit.php';
            }
        }
    }

    public function delete()
    {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->userModel->delete($id);
            $_SESSION['success'] = "User deleted successfully.";
        }
        header('Location: /users');
        exit;
    }
}
