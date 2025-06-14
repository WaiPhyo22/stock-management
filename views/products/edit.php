<?php ob_start(); ?>
    <h2>Edit Product</h2>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <form method="POST" action="/products/update">
        <input type="hidden" name="id" value="<?= $product['id'] ?>" />
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required />
        </div>
        <div class="mb-3">
            <label>Price</label>
            <input type="number" name="price" class="form-control" value="<?= $product['price'] ?>" required />
        </div>
        <div class="mb-3">
            <label>Quantity</label>
            <input type="number" name="quantity" class="form-control" value="<?= $product['quantity_available'] ?>" required />
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="/products" class="btn btn-secondary">Back</a>
    </form>
    <script>
        document.querySelector('form').addEventListener('submit', function(e) {
            const price = parseFloat(this.price.value);
            const quantity = parseInt(this.quantity.value);
        
            if (isNaN(price) || price < 1) {
                alert('Price must be at least 1');
                e.preventDefault();
                return;
            }
        
            if (isNaN(quantity) || quantity < 0 || !Number.isInteger(quantity)) {
                alert('Stock must be an integer 0 or greater');
                e.preventDefault();
                return;
            }
        });
    </script>
<?php
$content = ob_get_clean();
$title = 'Product Edit';
include __DIR__ . '/../layout/app.php'; // Or use @extends('layout') in Blade
?>
