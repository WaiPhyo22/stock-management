<?php ob_start(); ?>
    <h2>Transactions list</h2>
    <a href="/transactions/create" class="btn btn-primary mb-3">New Transaction</a>
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php 
                echo $_SESSION['success']; 
                unset($_SESSION['success']);
            ?>
        </div>
    <?php endif; ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Total Price</th>
                <th>Date</th>
                <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                <th>Action</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transactions as $txn): ?>
                <tr>
                    <td><?= $txn['id'] ?></td>
                    <td><?= $txn['user_name'] ?></td>
                    <td><?= $txn['product_name'] ?></td>
                    <td><?= $txn['quantity'] ?></td>
                    <td>$<?= $txn['total_price'] ?></td>
                    <td><?= $txn['created_at'] ?></td>
                    <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                    <td>
                        <a href="/transactions/delete?id=<?= $txn['id'] ?>" class="btn btn-danger btn-sm"
                           onclick="return confirm('Are you sure to delete?')">Delete</a>
                    </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <nav>
            <ul class="pagination justify-content-center">
                <?php
                $baseQuery = http_build_query(array_diff_key($_GET, ['page' => '']));
                for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                        <a class="page-link" href="/transactions?page=<?= $i ?>&<?= $baseQuery ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>

<?php
$content = ob_get_clean();
$title = 'Product List';
include __DIR__ . '/../layout/app.php'; // Or use @extends('layout') in Blade
?>