<?php ob_start(); ?>
    <h2>New Product</h2>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <form method="POST" action="/products/store">
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" required />
        </div>
        <div class="mb-3">
            <label>Price</label>
            <input type="number" name="price" class="form-control" required />
        </div>
        <div class="mb-3">
            <label>Stock</label>
            <input type="number" name="quantity" class="form-control" required />
        </div>
        <button type="submit" class="btn btn-success">Save</button>
        <a href="/products" class="btn btn-secondary">Cancel</a>
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
$title = 'Product Create';
include __DIR__ . '/../layout/app.php'; // Or use @extends('layout') in Blade
?>