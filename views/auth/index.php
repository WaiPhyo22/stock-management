<!DOCTYPE html>
<html>
<head>
    <title>User List</title>
    <style>
        table { border-collapse: collapse; width: 70%; margin: 20px auto; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <h2 style="text-align:center;">User List</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th><th>Name</th><th>Email</th><th>Role</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['id']) ?></td>
                    <td><?= htmlspecialchars($user['name']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['role']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
