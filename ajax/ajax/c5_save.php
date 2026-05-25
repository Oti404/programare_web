<?php
require_once '../config.php';
requireLogin();

header('Content-Type: application/json');

$data      = json_decode(file_get_contents('php://input'), true);
$id        = (int)($data['id']        ?? 0);
$full_name = trim($data['full_name']  ?? '');
$bio       = trim($data['bio']        ?? '');

if (!$id || $full_name === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Date invalide']);
    exit;
}

$pdo  = getPDOConnection();
$stmt = $pdo->prepare("UPDATE users SET full_name = ?, bio = ? WHERE id = ? AND role_id = (SELECT id FROM roles WHERE name = 'student')");
$stmt->execute([$full_name, $bio, $id]);

if ($stmt->rowCount() === 0) {
    http_response_code(404);
    echo json_encode(['error' => 'Student negasit sau nicio modificare']);
    exit;
}

echo json_encode(['success' => true]);
