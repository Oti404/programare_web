<?php
session_start();

if (extension_loaded('gd')) {
    // --- Varianta cu GD (imagine) ---
    $chars = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
    $captcha_string = '';
    for ($i = 0; $i < 5; $i++) {
        $captcha_string .= $chars[rand(0, strlen($chars) - 1)];
    }
    $_SESSION['captcha_code'] = $captcha_string;
    $_SESSION['captcha_type'] = 'image';

    $width = 150;
    $height = 45;
    $image = imagecreatetruecolor($width, $height);

    $bg_color   = imagecolorallocate($image, 255, 255, 255);
    $text_color = imagecolorallocate($image, 44, 62, 80);
    $line_color = imagecolorallocate($image, 189, 195, 199);

    imagefilledrectangle($image, 0, 0, $width, $height, $bg_color);
    for ($i = 0; $i < 6; $i++) {
        imageline($image, 0, rand() % $height, $width, rand() % $height, $line_color);
    }
    for ($i = 0; $i < 60; $i++) {
        imagesetpixel($image, rand() % $width, rand() % $height, $line_color);
    }
    imagestring($image, 5, 40, 14, $captcha_string, $text_color);

    header('Content-Type: image/png');
    imagepng($image);
    imagedestroy($image);
} else {
    // --- Fallback: captcha matematic (fara GD) ---
    $a = rand(1, 9);
    $b = rand(1, 9);
    $_SESSION['captcha_code'] = (string)($a + $b);
    $_SESSION['captcha_type'] = 'math';

    // Returnam un JSON pe care login.php il va citi
    header('Content-Type: application/json');
    echo json_encode(['question' => "$a + $b = ?"]);
}
?>
