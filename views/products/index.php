<?php ob_start(); ?>

<h2>Product List</h2>
<a href="/products/create" class="btn btn-primary mb-3">Add New Product</a>

<?php
// Sorting Helper Function
function sortLink($column, $label) {
    $currentSort = $_GET['sort'] ?? 'id';
    $currentOrder = $_GET['order'] ?? 'asc';
    $newOrder = ($currentSort === $column && $currentOrder === 'asc') ? 'desc' : 'asc';
    $icon = '';

    if ($currentSort === $column) {
        $icon = $currentOrder === 'asc' ? '↑' : '↓';
    }

    $query = http_build_query(array_merge($_GET, ['sort' => $column, 'order' => $newOrder]));
    return "<a href=\"?{$query}\">$label $icon</a>";
}
?>
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
        <th><?= sortLink('id', 'ID') ?></th>
        <th><?= sortLink('name', 'Name') ?></th>
        <th><?= sortLink('price', 'Price') ?></th>
        <th><?= sortLink('quantity_available', 'Stock') ?></th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    <?php if (!empty($products)) : ?>
        <?php foreach ($products as $product) : ?>
            <tr>
                <td><?= htmlspecialchars($product['id']) ?></td>
                <td><?= htmlspecialchars($product['name']) ?></td>
                <td><?= htmlspecialchars($product['price']) ?></td>
                <td><?= htmlspecialchars($product['quantity_available']) ?></td>
                <td>
                    <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                        <a href="/products/edit?id=<?= $product['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="/products/delete?id=<?= $product['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                    <?php endif; ?>
                    <a href="/transactions/create?product_id=<?= $product['id'] ?>" class="btn btn-sm btn-success">Buy</a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else : ?>
        <tr><td colspan="5" class="text-center">No products found.</td></tr>
    <?php endif; ?>
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
                    <a class="page-link" href="/products?page=<?= $i ?>&<?= $baseQuery ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
<?php endif; ?>

<?php
$content = ob_get_clean();
$title = 'Product List';
include __DIR__ . '/../layout/app.php';
?>