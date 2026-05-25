<?php
require_once '../config.php';
requireLogin();

header('Content-Type: application/json');

$offset = max(0, (int)($_GET['offset'] ?? 0));
$limit  = 5;

$pdo  = getPDOConnection();
$stmt = $pdo->prepare("SELECT username, full_name, bio FROM users u JOIN roles r ON u.role_id = r.id WHERE r.name = 'student' ORDER BY username LIMIT ? OFFSET ?");
$stmt->execute([$limit, $offset]);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = (int)$pdo->query("SELECT COUNT(*) FROM users u JOIN roles r ON u.role_id = r.id WHERE r.name = 'student'")->fetchColumn();

echo json_encode(['students' => $students, 'total' => $total, 'limit' => $limit, 'offset' => $offset]);
