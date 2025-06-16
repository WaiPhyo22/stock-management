<?php ob_start(); ?>
    <h2>New Transaction</h2>
    <?php if (!empty($_SESSION['error'])): ?>
    <div class="alert alert-danger">
        <?php 
            echo $_SESSION['error']; 
            unset($_SESSION['error']);
        ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="/transactions/store">
        <!-- Hidden Logged-in User -->
        <input type="hidden" name="user_id" value="<?= $_SESSION['user']['id'] ?? '' ?>">

        <div class="mb-3">
        <label>Product</label>
        <select name="product_id" id="product_id" class="form-control" required onchange="updatePrice()">
            <option value="">-- Select Product --</option>
            <?php foreach ($products as $product): ?>
                <option value="<?= $product['id'] ?>" 
                        data-price="<?= $product['price'] ?>"
                        data-stock="<?= $product['quantity_available'] ?>"
                        <?= (isset($productId) && $productId == $product['id']) ? 'selected' : '' ?>>
                    <?= $product['name'] ?> (<?= $product['quantity_available'] ?> in stock)
                </option>
            <?php endforeach; ?>
        </select>
        </div>

        <div class="mb-3">
        <label>Quantity</label>
        <input type="number" name="quantity" id="quantity" min="1" class="form-control" required oninput="updateTotal()">
        </div>

        <div class="mb-3">
        <label>Total Price</label>
        <input type="text" id="total_price_display" class="form-control" disabled>
        <input type="hidden" name="total_price" id="total_price">
        </div>

        <button type="submit" class="btn btn-success">Purchase</button>
    </form>

    <script>
    function updatePrice() {
        updateTotal();
    }

    function updateTotal() {
        const productSelect = document.getElementById('product_id');
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        const price = parseFloat(selectedOption.getAttribute('data-price') || 0);
        const qty = parseInt(document.getElementById('quantity').value) || 0;
        const total = price * qty;

        document.getElementById('total_price_display').value = total.toFixed(2) + ' MMK';
        document.getElementById('total_price').value = total.toFixed(2);
    }
    </script>

<?php
$content = ob_get_clean();
$title = 'Create Transaction';
include __DIR__ . '/../layout/app.php';
?>
