<?php

declare(strict_types=1);

require __DIR__ . '/db.php';

$errors = [];
$success = '';
$editingUser = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $name = trim((string) ($_POST['name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));

        if ($name === '' || $email === '') {
            $errors[] = 'Name and email are required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email format is invalid.';
        } else {
            $stmt = $pdo->prepare('INSERT INTO users (name, email) VALUES (:name, :email)');
            $stmt->execute(['name' => $name, 'email' => $email]);
            $success = 'User created.';
        }
    }

    if ($action === 'update') {
        $id = (int) ($_POST['id'] ?? 0);
        $name = trim((string) ($_POST['name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));

        if ($id <= 0 || $name === '' || $email === '') {
            $errors[] = 'Valid id, name and email are required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email format is invalid.';
        } else {
            $stmt = $pdo->prepare('UPDATE users SET name = :name, email = :email WHERE id = :id');
            $stmt->execute(['id' => $id, 'name' => $name, 'email' => $email]);
            $success = 'User updated.';
        }
    }

    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            $errors[] = 'Valid id is required to delete.';
        } else {
            $stmt = $pdo->prepare('DELETE FROM users WHERE id = :id');
            $stmt->execute(['id' => $id]);
            $success = 'User deleted.';
        }
    }
}

if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    if ($id > 0) {
        $stmt = $pdo->prepare('SELECT id, name, email FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $editingUser = $stmt->fetch() ?: null;
    }
}

$users = $pdo->query('SELECT id, name, email, created_at FROM users ORDER BY id DESC')->fetchAll();

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple PHP CRUD</title>
    <style>
        body { font-family: sans-serif; margin: 2rem; max-width: 840px; }
        form { margin-bottom: 1.5rem; border: 1px solid #ddd; padding: 1rem; }
        input { margin: 0.25rem 0; padding: 0.5rem; width: 100%; box-sizing: border-box; }
        button { margin-top: 0.5rem; padding: 0.5rem 0.75rem; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 0.5rem; text-align: left; }
        .row-actions { display: flex; gap: 0.5rem; }
        .message { padding: 0.5rem; margin-bottom: 1rem; }
        .error { background: #ffe2e2; border: 1px solid #ffb3b3; }
        .success { background: #e8ffea; border: 1px solid #b8efbe; }
        .inline-form { margin: 0; padding: 0; border: 0; }
    </style>
</head>
<body>
    <h1>Users CRUD</h1>

    <?php foreach ($errors as $error): ?>
        <div class="message error"><?= e($error) ?></div>
    <?php endforeach; ?>

    <?php if ($success !== ''): ?>
        <div class="message success"><?= e($success) ?></div>
    <?php endif; ?>

    <form method="post" action="">
        <h2><?= $editingUser ? 'Edit user' : 'Create user' ?></h2>
        <input type="hidden" name="action" value="<?= $editingUser ? 'update' : 'create' ?>">
        <?php if ($editingUser): ?>
            <input type="hidden" name="id" value="<?= (int) $editingUser['id'] ?>">
        <?php endif; ?>

        <label for="name">Name</label>
        <input id="name" name="name" required value="<?= e((string) ($editingUser['name'] ?? '')) ?>">

        <label for="email">Email</label>
        <input id="email" name="email" type="email" required value="<?= e((string) ($editingUser['email'] ?? '')) ?>">

        <button type="submit"><?= $editingUser ? 'Update user' : 'Add user' ?></button>
        <?php if ($editingUser): ?>
            <a href="index.php">Cancel edit</a>
        <?php endif; ?>
    </form>

    <h2>Saved users</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!$users): ?>
                <tr>
                    <td colspan="5">No users yet.</td>
                </tr>
            <?php endif; ?>

            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= (int) $user['id'] ?></td>
                    <td><?= e((string) $user['name']) ?></td>
                    <td><?= e((string) $user['email']) ?></td>
                    <td><?= e((string) $user['created_at']) ?></td>
                    <td>
                        <div class="row-actions">
                            <a href="?edit=<?= (int) $user['id'] ?>">Edit</a>
                            <form class="inline-form" method="post" action="" onsubmit="return confirm('Delete this user?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= (int) $user['id'] ?>">
                                <button type="submit">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
