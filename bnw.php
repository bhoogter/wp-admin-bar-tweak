<?php
$s = $_REQUEST['i'];
$t = substr($s, strlen($s)-3);

$ch = curl_init();
curl_setopt ($ch, CURLOPT_URL, $s);
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 0);
$fileContents = curl_exec($ch);
curl_close($ch);

$newImg = imagecreatefromstring($fileContents);
if (!imagefilter($newImg, IMG_FILTER_GRAYSCALE)) die("Couldn't convert");

switch($t)
	{
	case "jpg":	header('Content-Type: image/jpeg');imagejpeg($newImg);break;
	case "png":	header('Content-Type: image/png');imagesavealpha($newImg, true);imagepng($newImg);break;
	case "gif":	header('Content-Type: image/gif');imagegif($newImg);break;
	case "bmp":	header('Content-Type: image/bmp');imagewbmp($newImg);break;
	}
