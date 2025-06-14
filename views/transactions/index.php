<?php ob_start(); ?>
    <h2>Transactions list</h2>
    <a href="/transactions/create" class="btn btn-primary mb-3">New Transaction</a>
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
<?php
$content = ob_get_clean();
$title = 'Product List';
include __DIR__ . '/../layout/app.php'; // Or use @extends('layout') in Blade
?>