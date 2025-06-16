<?php
// controllers/TransactionController.php

require_once __DIR__ . '/../models/Transaction.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../controllers/AuthController.php';

class TransactionController {
    private $transactionModel;
    private $productModel;
    private $userModel;
    private $auth;

    public function __construct() {
        $this->transactionModel = new Transaction();
        $this->productModel = new Product();
        $this->userModel = new User();
        $this->auth = new AuthController();
        $this->auth->checkAuth();
    }

    public function index() {
        $perPage = 10;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $perPage;

        $transactions = $this->transactionModel->getPaginated($perPage, $offset);
        $totalUsers = $this->userModel->countAll();
        $totalPages = ceil($totalUsers / $perPage);
        require __DIR__ . '/../views/transactions/index.php';
    }

    public function create() {
        $productId = $_GET['product_id'] ?? null;
        $products = $this->productModel->getAll(100, 0);
        $users = $this->userModel->getAll();
        require __DIR__ . '/../views/transactions/create.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $product_id = $_POST['product_id'] ?? null;
            $quantity = (int) ($_POST['quantity'] ?? 0);
            // $user_id = $_SESSION['user']['id'];
            $user_id = $_POST['user_id'];
            $total_price = $_POST['total_price'];

            if (!$product_id || $quantity <= 0) {
                $_SESSION['error'] = "Invalid input.";
                header("Location: /transactions/create");
                exit;
            }

            $result = $this->transactionModel->create($product_id, $quantity, $user_id, $total_price);

            if ($result === true) {
                $_SESSION['success'] = "Transaction created successful!";
                header("Location: /transactions");
                exit;
            } else {
                $_SESSION['error'] = $result;  // Error message from create() method
                header("Location: /transactions/create");
                exit;
            }
        } else {
            // Show create purchase form
            require __DIR__ . '/../views/transactions/create.php';
        }
    }

    public function delete() {
        $this->auth->isAdmin();
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->transactionModel->delete($id);
            $_SESSION['success'] = "Transaction deleted successfully.";
        }
        header('Location: /transactions');
        exit;
    }
}