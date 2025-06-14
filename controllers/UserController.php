<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../controllers/AuthController.php';

class UserController {
    private $userModel;
    private $auth;

    public function __construct() {
        $this->userModel = new User();
        $this->auth = new AuthController();
        $this->auth->checkAuth();
        $this->auth->isAdmin();
    }

    // List users
    public function index() {
        $perPage = 10;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $perPage;
        
        $users = $this->userModel->getPaginated($perPage, $offset);
        $totalUsers = $this->userModel->countAll();
        $totalPages = ceil($totalUsers / $perPage);
        require __DIR__ . '/../views/user/index.php';
    }

    // Show create form
    public function create() {
        require __DIR__ . '/../views/user/create.php';
    }

    // Store new user (handle POST from create form)
    public function store() {
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

            if (!empty($errors)) {
                require __DIR__ . '/../views/user/create.php';
                return;
            }

            // Hash password before storing
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $created = $this->userModel->create($name, $email, $hashedPassword, $role);
            if ($created) {
                header('Location: /users');
                exit;
            } else {
                $error = "Failed to create user.";
                require __DIR__ . '/../views/user/create.php';
            }
        }
    }

    // Show edit form
    public function edit() {
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
public function dd($data) {
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    die();
}
    // Update user (handle POST from edit form)
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = [];

            $id = $_POST['id'] ?? null;
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? '';

            // Validation
            if (!$id || !is_numeric($id)) {
                $errors[] = 'Invalid user ID.';
            }

            if ($name === '') {
                $errors[] = 'Name is required.';
            }

            if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'A valid email is required.';
            }

            if ($password !== '' && strlen($password) < 6) {
                $errors[] = 'If provided, password must be at least 6 characters.';
            }

            if (!in_array($role, ['user', 'admin'])) {
                $errors[] = 'Invalid role selected.';
            }

            if (!empty($errors)) {
                // fetch current user to show values in form again
                $user = $this->userModel->findById($id); // your function to fetch user
                require __DIR__ . '/../views/user/edit.php';
                return;
            }

            // If password is provided, hash it, else keep old password
            if (!empty($password)) {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            } else {
                // Get old password from DB to keep it
                $oldUser = $this->userModel->findById($id);
                $hashedPassword = $oldUser['password'];
            }
            $updated = $this->userModel->update($id, $name, $email, $hashedPassword, $role);
            if ($updated) {
                header('Location: /users');
                exit;
            } else {
                $error = "Failed to update user.";
                $user = ['id'=>$id, 'name'=>$name, 'email'=>$email, 'role'=>$role];
                require __DIR__ . '/../views/user/edit.php';
            }
        }
    }

    // Delete user
    public function delete() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->userModel->delete($id);
        }
        header('Location: /users');
        exit;
    }
}
