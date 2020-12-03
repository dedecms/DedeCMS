<?

//本验证采用加密的cookie验证
//新文件的验证数据为 GetCookie("dd_ckstr")

require_once(dirname(__FILE__)."/config_hand.php");

//Session保存路径
$sessSavePath = dirname(__FILE__)."/sessions/";
if(is_writeable($sessSavePath) && is_readable($sessSavePath)){ session_save_path($sessSavePath); }

//按默认参数设置一个Cookie
function PutCookie($key,$value,$kptime,$pa="/"){
	 global $cfg_cookie_encode;
	 setcookie($key,$value,time()+$kptime,$pa);
	 setcookie($key.'__ckMd5',substr(md5($cfg_cookie_encode.$value),0,16),time()+$kptime,$pa);
}

//获取随机字符
$rndstring = "";
for($i=0;$i<4;$i++){
	$rndstring .= chr(mt_rand(65,90));
}

//如果支持GD，则绘图
if(function_exists("imagecreate"))
{
	//PutCookie("dd_ckstr",strtolower($rndstring),1800,"/");
	session_register('dd_ckstr');
	$_SESSION['dd_ckstr'] = strtolower($rndstring);
	$rndcodelen = strlen($rndstring);
  $im = imagecreate(50,20);
  $bgcolor = ImageColorAllocate($im, 250,255,180);
  $black = ImageColorAllocate($im, 0,0,0);
  $fontColor = ImageColorAllocate($im, 50,110,0); 
  $lineColor1 = ImageColorAllocate($im,190,220,170);
  $lineColor2 = ImageColorAllocate($im,250,250,170);
  
  //背景线
  for($j=3;$j<=16;$j=$j+3) imageline($im,2,$j,48,$j,$lineColor1);
  for($j=2;$j<52;$j=$j+(mt_rand(3,6))) imageline($im,$j,2,$j-6,18,$lineColor2);
  
  //边框
  imagerectangle($im, 0, 0, 49, 19, $black);
  
  //文字
  for($i=0;$i<$rndcodelen;$i++){
	  imagestring($im, 5, $i*10+6, mt_rand(2,4), $rndstring[$i], $fontColor);
  }
  
  header("Pragma:no-cache\r\n");
  header("Cache-Control:no-cache\r\n");
  header("Expires:0\r\n");
  //输出特定类型的图片格式，优先级为 gif -> jpg ->png
  if(function_exists("imagejpeg")){
  	header("content-type:image/jpeg\r\n");
  	imagejpeg($im);
  }else{
    header("content-type:image/png\r\n");
  	imagepng($im);
  }
  ImageDestroy($im);

}else{ //不支持GD，只输出字母 ABCD	
	//PutCookie("dd_ckstr","abcd",1800,"/");
	session_register('dd_ckstr');
	$_SESSION['dd_ckstr'] = "abcd";
	header("content-type:image/jpeg\r\n");
	header("Pragma:no-cache\r\n");
  header("Cache-Control:no-cache\r\n");
  header("Expires:0\r\n");
	$fp = fopen("./vdcode.jpg","r");
	echo fread($fp,filesize("./vdcode.jpg"));
	fclose($fp);
}

?>