<?php
/**
 * CAPTCHA matematic - nu necesita extensii GD sau PEAR
 * Genereaza o intrebare matematica simpla si salveaza raspunsul in sesiune
 */
session_start();

$a = rand(1, 15);
$b = rand(1, 15);
$_SESSION['captcha_code'] = (string)($a + $b);
$_SESSION['captcha_type'] = 'math';

// Stocat in sesiune; login.php citeste valorile de acolo
// Acest fisier poate fi apelat direct pentru a regenera intrebarea
header('Content-Type: application/json');
echo json_encode(['a' => $a, 'b' => $b]);
