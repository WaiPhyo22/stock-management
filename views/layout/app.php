<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title><?= $title ?? 'My App' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Stock Management</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link <?= ($_SERVER['REQUEST_URI'] == '/users') ? 'active' : '' ?>" href="/users">Users</a>
                </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link <?= ($_SERVER['REQUEST_URI'] == '/products') ? 'active' : '' ?>" href="/products">Products</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($_SERVER['REQUEST_URI'] == '/transactions') ? 'active' : '' ?>" href="/transactions">Transactions</a>
                </li>
            </ul>

            <!-- Right side of navbar -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link text-danger" href="/logout" onclick="return confirm('Are you sure you want to logout?')">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <?= $content ?? '' ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>