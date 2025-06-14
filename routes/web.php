<?php
$uri = $_GET['uri'] ?? '/';

switch ($uri) {
    case '/':
        require_once 'controllers/ProductController.php';
        $controller = new ProductController();
        $controller->index();
        break;
    case '/products/create':
        require_once 'controllers/ProductController.php';
        $controller = new ProductController();
        $controller->create();
        break;
    case '/products/store':
        require_once 'controllers/ProductController.php';
        $controller = new ProductController();
        $controller->store();
        break;
    // Add edit, update, delete, show etc.
    default:
        echo "404 - Page Not Found";
        break;
}
