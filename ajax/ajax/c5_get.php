<?php
require_once '../config.php';
requireLogin();

header('Content-Type: application/json');

$id   = (int)($_GET['id'] ?? 0);
$pdo  = getPDOConnection();
$stmt = $pdo->prepare("SELECT u.id, u.username, u.full_name, u.bio FROM users u JOIN roles r ON u.role_id = r.id WHERE r.name = 'student' AND u.id = ?");
$stmt->execute([$id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    http_response_code(404);
    echo json_encode(['error' => 'Student negasit']);
    exit;
}

echo json_encode($student);
