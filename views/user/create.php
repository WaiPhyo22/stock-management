<?php ob_start(); ?>
    <h2>Create New User</h2>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <form action="/users/store" method="post">
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" id="name" required class="form-control" />
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" required class="form-control" />
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" id="password" required class="form-control" />
        </div>
        <div class="mb-3">
            <label for="role" class="form-label">Role</label>
            <select name="role" id="role" required class="form-select">
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Create User</button>
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
        
            if (password.length < 6) {
                alert('Password must be at least 6 characters.');
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
$title = 'User Create';
include __DIR__ . '/../layout/app.php'; // Or use @extends('layout') in Blade
?>
