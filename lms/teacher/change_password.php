<?php
header('Content-Type: application/json');
require_once '../db.php';
require_once '../auth.php';
require_once '../ip.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $role = $_SESSION['role'];
    $oldPassword = $_POST['old_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';

    if (empty($oldPassword) || empty($newPassword)) {
        echo json_encode(['status' => 'error', 'message' => 'Missing fields.']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE user_id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($oldPassword, $user['password_hash'])) {
        echo json_encode(['status' => 'error', 'message' => 'Old password is incorrect.']);
        exit;
    }
   
    $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
    $update = $pdo->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
    $update->execute([$newHash, $userId]);

    if($update){
        $action = "Changed Password";
        $ip_address= getUserIp();
        $logStmt = $pdo->prepare("INSERT INTO user_activities (user_id, action, role, ip_address) VALUES (?, ?, ?, ?)");
        $logStmt->execute([$userId, $action, $role, $ip_address]);
        
        echo json_encode(['status' => 'success', 'message' => 'Password changed successfully.']);

    }
    exit;
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
