<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . "/config/db.php"; // Adjust path if needed

if (!isset($pdo)) {
    die("❌ Database connection not established. Check config/db.php.");
}

try {
    $users = [
        [
            'full_name'   => 'Admin User',
            'username'    => 'admin',
            'password'    => 'admin123',
            'role'        => 'admin',
            'branch_id'   => 1,
            'status'      => 'active'
        ],
        [
            'full_name'   => 'Cashier User',
            'username'    => 'cashier',
            'password'    => 'cashier123',
            'role'        => 'cashier',
            'branch_id'   => 1,
            'status'      => 'active'
        ],
        [
            'full_name'   => 'Manager User',
            'username'    => 'manager',
            'password'    => 'manager123',
            'role'        => 'manager',
            'branch_id'   => 1,
            'status'      => 'active'
        ]
    ];

    $stmt = $pdo->prepare("
        INSERT INTO users (full_name, username, password_hash, role, branch_id, status)
        VALUES (:full_name, :username, :password_hash, :role, :branch_id, :status)
    ");

    foreach ($users as $u) {
        $password_hash = password_hash($u['password'], PASSWORD_DEFAULT);
        $stmt->execute([
            ':full_name'    => $u['full_name'],
            ':username'     => $u['username'],
            ':password_hash'=> $password_hash,
            ':role'         => $u['role'],
            ':branch_id'    => $u['branch_id'],
            ':status'       => $u['status']
        ]);
        echo "✅ Created user: {$u['username']} with password: {$u['password']}<br>";
    }

    echo "<br>🎯 All users inserted successfully.";
} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage();
}
