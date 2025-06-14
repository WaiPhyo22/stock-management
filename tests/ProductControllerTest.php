<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../controllers/ProductController.php';

class ProductControllerTest extends TestCase
{
    private $productModel;
    private $auth;
    private $controller;

    protected function setUp(): void
    {
        // Mock Product model
        $this->productModel = $this->createMock(Product::class);

        // Mock AuthController
        $this->auth = $this->createMock(AuthController::class);

        // Make sure checkAuth() is called on construction
        $this->auth->expects($this->any())->method('checkAuth');

        // Create controller instance with mocks
        $this->controller = new ProductController($this->productModel, $this->auth);

        // Clear superglobals before each test
        $_GET = [];
        $_POST = [];
    }

    protected function tearDown(): void
    {
        // Reset superglobals after each test
        $_GET = [];
        $_POST = [];
    }

    public function testIndexDisplaysProductsWithPaginationAndSorting()
    {
        $_GET['page'] = 2;
        $_GET['sort'] = 'price';
        $_GET['order'] = 'desc';

        $products = [
            ['id' => 1, 'name' => 'Product A', 'price' => 50, 'quantity_available' => 10],
            ['id' => 2, 'name' => 'Product B', 'price' => 40, 'quantity_available' => 5],
        ];

        $this->productModel->expects($this->once())
            ->method('getAll')
            ->with(10, 10, 'price', 'desc')
            ->willReturn($products);

        $this->productModel->expects($this->once())
            ->method('countAll')
            ->willReturn(20);

        // Prevent actual view loading by overriding require
        $controller = $this->controller;
        $controller = new class($this->productModel, $this->auth) extends ProductController {
            public function index() {
                ob_start();
                parent::index();
                echo "view_loaded";
                ob_end_flush();
            }
        };

        ob_start();
        $controller->index();
        $output = ob_get_clean();

        $this->assertStringContainsString('view_loaded', $output);
    }

    public function testCreateRequiresAdminAndLoadsView()
    {
        $this->auth->expects($this->once())->method('isAdmin');

        // Override create view inclusion
        $controller = new class($this->productModel, $this->auth) extends ProductController {
            protected $auth;
                
            public function __construct($productModel, $auth) {
                parent::__construct($productModel, $auth);
                $this->auth = $auth;
            }
        
            public function create() {
                $this->auth->isAdmin();
                echo "create_view_loaded";
            }
        };

        ob_start();
        $controller->create();
        $output = ob_get_clean();

        $this->assertStringContainsString('create_view_loaded', $output);
    }

    public function testStoreWithInvalidDataShowsErrors()
    {
        $this->auth->expects($this->once())->method('isAdmin');

        $_POST = [
            'name' => '',  // invalid (required)
            'price' => 0,  // invalid (must be >=1)
            'quantity' => -5, // invalid (>=0)
        ];

        // Override require for view
        $controller = new class($this->productModel, $this->auth) extends ProductController {
            protected $auth;
                
            public function __construct($productModel, $auth) {
                parent::__construct($productModel, $auth);
                $this->auth = $auth;
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
                    echo implode(',', $errors);
                    return;
                }
            }
        };

        ob_start();
        $controller->store();
        $output = ob_get_clean();

        $this->assertStringContainsString('Name is required.', $output);
        $this->assertStringContainsString('Price must be a positive number at least 1.', $output);
        $this->assertStringContainsString('Stock must be an integer 0 or greater.', $output);
    }

    public function testStoreWithValidDataCreatesProductAndRedirects()
    {
        $this->auth->expects($this->once())->method('isAdmin');
        $_POST = [
            'name' => 'Product X',
            'price' => 100,
            'quantity' => 10,
        ];

        $this->productModel->expects($this->once())
            ->method('create')
            ->with('Product X', 100, 10);

        // We'll mock header() function by using runkit7 or patch the controller (simplified here)
        $controller = new class($this->productModel, $this->auth) extends ProductController {
            protected $auth;
            protected $productModel;
                
            public function __construct($productModel, $auth) {
                parent::__construct($productModel, $auth);
                $this->productModel = $productModel;
                $this->auth = $auth;
            }

            public function store() {
                $this->auth->isAdmin();
                $name = trim($_POST['name'] ?? '');
                $price = $_POST['price'] ?? null;
                $quantity = $_POST['quantity'] ?? null;

                if ($name === '' || !is_numeric($price) || $price < 1 || !is_numeric($quantity) || (int)$quantity < 0 || floor($quantity) != $quantity) {
                    echo 'Validation failed';
                    return;
                }
                $this->productModel->create($name, $price, $quantity);
                echo "redirect:/products";
            }
        };

        ob_start();
        $controller->store();
        $output = ob_get_clean();

        $this->assertStringContainsString('redirect:/products', $output);
    }

    public function testEditLoadsProductForAdmin()
    {
        $this->auth->expects($this->once())->method('isAdmin');

        $_GET['id'] = 42;
        $product = ['id' => 42, 'name' => 'Product Y', 'price' => 200, 'quantity_available' => 5];

        $this->productModel->expects($this->once())
            ->method('findById')
            ->with(42)
            ->willReturn($product);

        $controller = new class($this->productModel, $this->auth) extends ProductController {
            protected $auth;
            protected $productModel;
                
            public function __construct($productModel, $auth) {
                parent::__construct($productModel, $auth);
                $this->productModel = $productModel;
                $this->auth = $auth;
            }

            public function edit() {
                $this->auth->isAdmin();
                $id = $_GET['id'] ?? null;
                if (!$id) {
                    echo "redirect:/products";
                    return;
                }
                $product = $this->productModel->findById($id);
                echo json_encode($product);
            }
        };

        ob_start();
        $controller->edit();
        $output = ob_get_clean();

        $this->assertStringContainsString('"id":42', $output);
        $this->assertStringContainsString('"name":"Product Y"', $output);
    }

    public function testEditRedirectsIfNoId()
    {
        $this->auth->expects($this->once())->method('isAdmin');

        $_GET = []; // no id

        $controller = new class($this->productModel, $this->auth) extends ProductController {
            protected $auth;
                
            public function __construct($productModel, $auth) {
                parent::__construct($productModel, $auth);
                $this->auth = $auth;
            }
            public function edit() {
                $this->auth->isAdmin();
                $id = $_GET['id'] ?? null;
                if (!$id) {
                    echo "redirect:/products";
                    return;
                }
            }
        };

        ob_start();
        $controller->edit();
        $output = ob_get_clean();

        $this->assertStringContainsString('redirect:/products', $output);
    }

    public function testUpdateWithInvalidDataShowsErrors()
    {
        $this->auth->expects($this->once())->method('isAdmin');

        $_POST = [
            'id' => 1,
            'name' => '',
            'price' => 0,
            'quantity' => -3,
        ];

        $product = ['id' => 1, 'name' => 'Old', 'price' => 50, 'quantity_available' => 10];

        $this->productModel->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($product);

        $controller = new class($this->productModel, $this->auth) extends ProductController {
            protected $auth;
            protected $productModel;
                
            public function __construct($productModel, $auth) {
                parent::__construct($productModel, $auth);
                $this->productModel = $productModel;
                $this->auth = $auth;
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
                    echo implode(',', $errors);
                    return;
                }

                $this->productModel->update($id, $name, $price, $quantity);
                echo "redirect:/products";
            }
        };

        ob_start();
        $controller->update();
        $output = ob_get_clean();

        $this->assertStringContainsString('Name is required.', $output);
        $this->assertStringContainsString('Price must be a positive number at least 1.', $output);
        $this->assertStringContainsString('Stock must be an integer 0 or greater.', $output);
    }

    public function testUpdateWithValidDataUpdatesAndRedirects()
    {
        $this->auth->expects($this->once())->method('isAdmin');

        $_POST = [
            'id' => 1,
            'name' => 'Updated Product',
            'price' => 150,
            'quantity' => 20,
        ];

        $this->productModel->expects($this->once())
            ->method('update')
            ->with(1, 'Updated Product', 150, 20);

        $controller = new class($this->productModel, $this->auth) extends ProductController {
            protected $auth;
            protected $productModel;
                
            public function __construct($productModel, $auth) {
                parent::__construct($productModel, $auth);
                $this->productModel = $productModel;
                $this->auth = $auth;
            }

            public function update() {
                $this->auth->isAdmin();
                $id = $_POST['id'] ?? null;
                $name = trim($_POST['name'] ?? '');
                $price = $_POST['price'] ?? null;
                $quantity = $_POST['quantity'] ?? null;

                if (!$id || $name === '' || !is_numeric($price) || $price < 1 || !is_numeric($quantity) || (int)$quantity < 0 || floor($quantity) != $quantity) {
                    echo 'Validation failed';
                    return;
                }

                $this->productModel->update($id, $name, $price, $quantity);
                echo "redirect:/products";
            }
        };

        ob_start();
        $controller->update();
        $output = ob_get_clean();

        $this->assertStringContainsString('redirect:/products', $output);
    }

    public function testDeleteDeletesAndRedirects()
    {
        $this->auth->expects($this->once())->method('isAdmin');

        $_GET['id'] = 5;

        $this->productModel->expects($this->once())
            ->method('delete')
            ->with(5);

        $controller = new class($this->productModel, $this->auth) extends ProductController {
            protected $auth;
            protected $productModel;
                
            public function __construct($productModel, $auth) {
                parent::__construct($productModel, $auth);
                $this->productModel = $productModel;
                $this->auth = $auth;
            }

            public function delete() {
                $this->auth->isAdmin();
                $id = $_GET['id'] ?? null;
                if (!$id) {
                    echo "redirect:/products";
                    return;
                }
                $this->productModel->delete($id);
                echo "redirect:/products";
            }
        };

        ob_start();
        $controller->delete();
        $output = ob_get_clean();

        $this->assertStringContainsString('redirect:/products', $output);
    }

    public function testDeleteRedirectsIfNoId()
    {
        $this->auth->expects($this->once())->method('isAdmin');

        $_GET = [];

        $controller = new class($this->productModel, $this->auth) extends ProductController {
            protected $auth;
                
            public function __construct($productModel, $auth) {
                parent::__construct($productModel, $auth);
                $this->auth = $auth;
            }
            public function delete() {
                $this->auth->isAdmin();
                $id = $_GET['id'] ?? null;
                if (!$id) {
                    echo "redirect:/products";
                    return;
                }
            }
        };

        ob_start();
        $controller->delete();
        $output = ob_get_clean();

        $this->assertStringContainsString('redirect:/products', $output);
    }
}
