<?php
session_start();

//generate id
$id = session_id();
$max_index = strlen($id) - 1;
$num_digits = 6;
$new_id = "";

for($i = 0; $i < $num_digits; $i++){
    $new_id .= substr($id, mt_rand(0, $max_index), 1);
}

$_SESSION["captcha_id"] = $new_id;

//create image
$width = 250;
$img = imagecreate($width, 40);

$black = imagecolorallocate($img, 0, 0, 0);
$silver = imagecolorallocate($img, 100, 100, 100);
$white = imagecolorallocate($img, 255, 255, 255);

imagefill($img, 0, 0, $silver);

$num_lineas = 100;
for ($i = 0; $i < $num_lineas; $i++) {
    $x_inicio = mt_rand(0, $width);
    $x_fin = mt_rand(0, $width);

    imageline($img, $x_inicio, 0, $x_fin, $width, $black);
}

imagettftext($img, 30, 3, 60, 34, $white, '../fonts/segoeuisl.ttf', $new_id);

header("Content-type:image/jpg");
imagejpeg($img);
imagedestroy($img);