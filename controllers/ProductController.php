<?php
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../controllers/AuthController.php';

class ProductController {
    private $productModel;
    private $auth;

    public function __construct($productModel = null, $auth = null) {
        $this->productModel = $productModel ?: new Product();
        $this->auth = $auth ?: new AuthController();
        $this->auth->checkAuth();
    }

    public function index() {
        $limit = 10;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;

        // Sorting parameters
        $sort = $_GET['sort'] ?? 'id';
        $order = $_GET['order'] ?? 'asc';

        // Validate sort and order
        $validSorts = ['id', 'name', 'price', 'quantity_available'];
        $validOrders = ['asc', 'desc'];

        if (!in_array($sort, $validSorts)) {
            $sort = 'id';
        }
        if (!in_array(strtolower($order), $validOrders)) {
            $order = 'asc';
        }

        $products = $this->productModel->getAll($limit, $offset, $sort, $order);
        $totalProducts = $this->productModel->countAll();
        $totalPages = ceil($totalProducts / $limit);

        require __DIR__ . '/../views/products/index.php';
    }

    public function create() {
        $this->auth->isAdmin();
        require __DIR__ . '/../views/products/create.php';
    }

    public function store() {
        $this->auth->isAdmin();
        $errors = [];

        $name = trim($_POST['name'] ?? '');
        $price = $_POST['price'] ?? null;
        $quantity = $_POST['quantity'] ?? null;

        if ($name === '') {
            $errors[] = 'Name is required.';
        }

        if (!is_numeric($price) || $price < 1) {
            $errors[] = 'Price must be a positive number at least 1.';
        }

        if (!is_numeric($quantity) || (int)$quantity < 0 || floor($quantity) != $quantity) {
            $errors[] = 'Stock must be an integer 0 or greater.';
        }

        if (!empty($errors)) {
            require __DIR__ . '/../views/products/create.php';
            return;
        }

        $this->productModel->create($name, $price, $quantity);
        header('Location: /products');
        exit;
    }

    public function edit() {
        $this->auth->isAdmin();
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: /products');
            exit;
        }
        $product = $this->productModel->findById($id);
        require __DIR__ . '/../views/products/edit.php';
    }

    public function update() {
        $this->auth->isAdmin();
        $id = $_POST['id'] ?? null;
        $errors = [];

        $name = trim($_POST['name'] ?? '');
        $price = $_POST['price'] ?? null;
        $quantity = $_POST['quantity'] ?? null;

        if (!$id) {
            $errors[] = 'Product id is not found.';
        }

        if ($name === '') {
            $errors[] = 'Name is required.';
        }

        if (!is_numeric($price) || $price < 1) {
            $errors[] = 'Price must be a positive number at least 1.';
        }

        if (!is_numeric($quantity) || (int)$quantity < 0 || floor($quantity) != $quantity) {
            $errors[] = 'Stock must be an integer 0 or greater.';
        }

        if (!empty($errors)) { 
            $product = $this->productModel->findById($id);
            require __DIR__ . '/../views/products/edit.php';
            return;
        }

        $this->productModel->update($id, $name, $price, $quantity);
        header('Location: /products');
        exit;
    }

    public function delete() {
        $this->auth->isAdmin();
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->productModel->delete($id);
        }
        header('Location: /products');
        exit;
    }
}
