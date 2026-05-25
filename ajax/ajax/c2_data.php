<?php
require_once '../config.php';
requireLogin();

header('Content-Type: application/xml');

$offset = max(0, (int)($_GET['offset'] ?? 0));
$limit  = 5;

$pdo  = getPDOConnection();
$stmt = $pdo->prepare("SELECT username, full_name, bio FROM users u JOIN roles r ON u.role_id = r.id WHERE r.name = 'student' ORDER BY username LIMIT ? OFFSET ?");
$stmt->execute([$limit, $offset]);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = (int)$pdo->query("SELECT COUNT(*) FROM users u JOIN roles r ON u.role_id = r.id WHERE r.name = 'student'")->fetchColumn();

$xml = new SimpleXMLElement('<response/>');
$xml->addAttribute('total', $total);
$xml->addAttribute('limit', $limit);
$xml->addAttribute('offset', $offset);

foreach ($students as $s) {
    $node = $xml->addChild('student');
    $node->addChild('username',  htmlspecialchars($s['username']));
    $node->addChild('full_name', htmlspecialchars($s['full_name']));
    $node->addChild('bio',       htmlspecialchars($s['bio']));
}

echo $xml->asXML();
