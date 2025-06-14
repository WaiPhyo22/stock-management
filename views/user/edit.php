<?php ob_start(); ?>
    <h2>Edit User</h2>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <form action="/users/update" method="post">
        <input type="hidden" name="id" value="<?= htmlspecialchars($user['id']) ?>" />
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" id="name" required class="form-control" value="<?= htmlspecialchars($user['name']) ?>" />
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" required class="form-control" value="<?= htmlspecialchars($user['email']) ?>" />
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password (leave blank if no change)</label>
            <input type="password" name="password" id="password" class="form-control" />
        </div>
        <div class="mb-3">
            <label for="role" class="form-label">Role</label>
            <select name="role" id="role" required class="form-select">
                <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update User</button>
        <a href="/users" class="btn btn-secondary">Back to List</a>
    </form>
    <script>
        document.querySelector('form').addEventListener('submit', function(e) {
            const email = this.email.value.trim();
            const password = this.password.value;
            const role = this.role.value;
        
            if (!/^\S+@\S+\.\S+$/.test(email)) {
                alert('Please enter a valid email address.');
                e.preventDefault();
                return;
            }
        
            if (!['user', 'admin'].includes(role)) {
                alert('Invalid role selected.');
                e.preventDefault();
            }
        });
    </script>
<?php
$content = ob_get_clean();
$title = 'User Edit';
include __DIR__ . '/../layout/app.php'; // Or use @extends('layout') in Blade
?>
