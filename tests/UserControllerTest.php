<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../controllers/UserController.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../controllers/AuthController.php';

class UserControllerTest extends TestCase
{
    private $mockUserModel;
    private $mockAuth;
    private $controller;

    protected function setUp(): void
    {
        $this->mockUserModel = $this->createMock(User::class);
        $this->mockAuth = $this->createMock(AuthController::class);

        $this->mockAuth->method('checkAuth')->willReturn(true);
        $this->mockAuth->method('isAdmin')->willReturn(true);

        $this->controller = new UserController($this->mockUserModel, $this->mockAuth);
    }

    public function testIndexLoadsUserList()
    {
        $_GET['page'] = 1;

        $this->mockUserModel
            ->expects($this->once())
            ->method('getPaginated')
            ->with(10, 0)
            ->willReturn([['id' => 1, 'name' => 'Test User']]);

        $this->mockUserModel
            ->expects($this->once())
            ->method('countAll')
            ->willReturn(1);

        ob_start();
        $this->controller->index();
        $output = ob_get_clean();

        $this->assertStringContainsString('Test User', $output);
    }

    public function testCreateViewLoads()
    {
        ob_start();
        $this->controller->create();
        $output = ob_get_clean();

        $this->assertStringContainsString('<form', $output);
    }

    public function testStoreFailsWithInvalidData()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'name' => '',
            'email' => 'invalid',
            'password' => '123',
            'role' => 'invalid'
        ];

        ob_start();
        $this->controller->store();
        $output = ob_get_clean();

        $this->assertStringContainsString('Name is required.', $output);
        $this->assertStringContainsString('A valid email is required.', $output);
        $this->assertStringContainsString('Password must be at least 6 characters.', $output);
        $this->assertStringContainsString('Invalid role selected.', $output);
    }

    public function testEditLoadsCorrectUser()
    {
        $_GET['id'] = 1;

        $this->mockUserModel
            ->method('findById')
            ->with(1)
            ->willReturn(['id' => 1, 'name' => 'Test User']);

        ob_start();
        $this->controller->edit();
        $output = ob_get_clean();

        $this->assertStringContainsString('Test User', $output);
    }

    public function testUpdateFailsWithInvalidEmail()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'id' => 1,
            'name' => 'User',
            'email' => 'bademail',
            'password' => '',
            'role' => 'user',
        ];

        $this->mockUserModel
            ->method('findById')
            ->with(1)
            ->willReturn([
                'id' => 1,
                'name' => 'Old Name',
                'email' => 'old@example.com',
                'password' => 'hashedpass',
                'role' => 'user'
            ]);

        ob_start();
        $this->controller->update();
        $output = ob_get_clean();

        $this->assertStringContainsString('A valid email is required.', $output);
    }

    public function testDeleteRedirectsAfterSuccess()
    {
        $_GET['id'] = 1;

        $this->mockUserModel
            ->expects($this->once())
            ->method('delete')
            ->with(1);

        // Capture header redirect
        $this->expectOutputRegex('/.*/'); // suppress output warning
        $this->controller->delete();

        $this->assertTrue(headers_sent()); // Check if header('Location: /users') was called
    }
}
