<?
session_start();
session_register("s_validate");
$rndstring = "";
for($i=0;$i<4;$i++)
{
	$rndstring .= chr(mt_rand(65,90));
}
$_SESSION["s_validate"]=strtolower($rndstring);
$rndcodelen = strlen($rndstring);
header("content-type:image/jpeg\r\n");
$im = imagecreate(50,20);
$bgcolor = ImageColorAllocate($im, 248,212,20);
$black = ImageColorAllocate($im, 0,0,0);
imagerectangle($im, 0, 0, 49, 19, $black);
for($i=0;$i<$rndcodelen;$i++)
{
	imagestring($im, mt_rand(2,5), $i*10+6, mt_rand(2,5), $rndstring[$i], $black);
}
ImageJpeg($im);
ImageDestroy($im);
?>