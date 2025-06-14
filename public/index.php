<?php
require_once __DIR__ . '/../core/Router.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/UserController.php';
require_once __DIR__ . '/../controllers/ProductController.php';
require_once __DIR__ . '/../controllers/TransactionController.php';
$auth = new AuthController();
$router = new Router();

$router->get('/login', [$auth, 'showLoginForm']);
$router->post('/login', ['AuthController', 'handleLogin']);
$router->get('/logout', ['AuthController', 'logout']);

$router->get('/users', ['UserController', 'index']);
$router->get('/users/create', ['UserController', 'create']); // optional
$router->post('/users/store', ['UserController', 'store']);
$router->get('/users/edit', ['UserController', 'edit']); // ?id=xx
$router->post('/users/update', ['UserController', 'update']);
$router->get('/users/delete', ['UserController', 'delete']);

$router->get('/products', ['ProductController', 'index']);
$router->get('/products/create', ['ProductController', 'create']);
$router->post('/products/store', ['ProductController', 'store']);
$router->get('/products/edit', ['ProductController', 'edit']); // ?id=5
$router->post('/products/update', ['ProductController', 'update']);
$router->get('/products/delete', ['ProductController', 'delete']); // ?id=5

$router->get('/transactions', ['TransactionController', 'index']);
$router->get('/transactions/create', ['TransactionController', 'create']);
$router->post('/transactions/store', ['TransactionController', 'store']);
$router->get('/transactions/delete', ['TransactionController', 'delete']);

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
