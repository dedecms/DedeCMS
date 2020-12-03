<?
require_once(dirname(__FILE__)."/config_start.php");
session_start();
session_register("s_validate");
$rndstring = "";
for($i=0;$i<4;$i++)
{
	$rndstring .= chr(mt_rand(65,90));
}
if(function_exists("imagecreate"))
{
	$_SESSION["s_validate"]=strtolower($rndstring);
	$rndcodelen = strlen($rndstring);
  $im = imagecreate(50,20);
  $bgcolor = ImageColorAllocate($im, 248,212,20);
  $black = ImageColorAllocate($im, 0,0,0);
  imagerectangle($im, 0, 0, 49, 19, $black);
  for($i=0;$i<$rndcodelen;$i++)
  {
	  imagestring($im, mt_rand(2,5), $i*10+6, mt_rand(2,5), $rndstring[$i], $black);
  }
  if(function_exists("imagejpeg"))
  { header("content-type:image/jpeg\r\n"); ImageJpeg($im); }
  else
  { header("content-type:image/png\r\n"); ImagePng($im); }
  ImageDestroy($im);
}
else
{
	$_SESSION["s_validate"]="abcd";
	header("content-type:image/jpeg\r\n");
	$fp = fopen("./vdcode.jpg","r");
	echo fread($fp,filesize("./vdcode.jpg"));
	fclose($fp);
}
?>