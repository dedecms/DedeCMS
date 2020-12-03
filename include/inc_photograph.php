<?php 
//检测用户系统支持的图片格式
$cfg_photo_type['gif'] = false;
$cfg_photo_type['jpeg'] = false;
$cfg_photo_type['png'] = false;
$cfg_photo_type['wbmp'] = false;
$cfg_photo_typenames = Array();
$cfg_photo_support = "";
if(function_exists("imagecreatefromgif") && function_exists("imagegif")){
	$cfg_photo_type["gif"] = true;
	$cfg_photo_typenames[] = "image/gif";
	$cfg_photo_support .= "GIF ";
}
if(function_exists("imagecreatefromjpeg") && function_exists("imagejpeg")){
	$cfg_photo_type["jpeg"] = true;
	$cfg_photo_typenames[] = "image/pjpeg";
	$cfg_photo_typenames[] = "image/jpeg";
	$cfg_photo_support .= "JPEG ";
}
if(function_exists("imagecreatefrompng") && function_exists("imagepng")){
	$cfg_photo_type["png"] = true;
	$cfg_photo_typenames[] = "image/png";
	$cfg_photo_typenames[] = "image/x-png";
	$cfg_photo_support .= "PNG ";
}
if(function_exists("imagecreatefromwbmp") && function_exists("imagewbmp")){
	$cfg_photo_type["wbmp"] = true;
	$cfg_photo_typenames[] = "image/wbmp";
	$cfg_photo_support .= "WBMP ";
}
//--------------------------------
//缩图片自动生成函数，来源支持bmp、gif、jpg、png
//但生成的小图只用jpg或png格式
//--------------------------------
function ImageResize($srcFile,$toW,$toH,$toFile="") 
{

   global $cfg_photo_type,$cfg_jpeg_query;
   if(empty($cfg_jpeg_query)) $cfg_jpeg_query = 85;
   if($toFile==""){ $toFile = $srcFile; }
   $info = "";
   $srcInfo = GetImageSize($srcFile,$info);

   switch ($srcInfo[2])
   {
    case 1:
      if(!$cfg_photo_type['gif']) return false;
      $im = imagecreatefromgif($srcFile);
      break;
    case 2:	

      if(!$cfg_photo_type['jpeg']) return false;
      $im = imagecreatefromjpeg($srcFile);    
      break;
    case 3:
      if(!$cfg_photo_type['png']) return false;
      $im = imagecreatefrompng($srcFile);    
      break;
   case 6:
      if(!$cfg_photo_type['bmp']) return false;
      $im = imagecreatefromwbmp($srcFile);    
    break;
  }
  $srcW=ImageSX($im);
  $srcH=ImageSY($im);
  $toWH=$toW/$toH;
  $srcWH=$srcW/$srcH;
  if($toWH<=$srcWH){
    $ftoW=$toW;
    $ftoH=$ftoW*($srcH/$srcW);
  }
  else{
    $ftoH=$toH;
    $ftoW=$ftoH*($srcW/$srcH);
  }    
  if($srcW>$toW||$srcH>$toH)
  {
     if(function_exists("imagecreatetruecolor")){
        $ni = imagecreatetruecolor($ftoW,$ftoH);
        if($ni) imagecopyresampled($ni,$im,0,0,0,0,$ftoW,$ftoH,$srcW,$srcH);
        else{
         $ni=imagecreate($ftoW,$ftoH);
         imagecopyresized($ni,$im,0,0,0,0,$ftoW,$ftoH,$srcW,$srcH);
        }
     }else{
        $ni=imagecreate($ftoW,$ftoH);
        imagecopyresized($ni,$im,0,0,0,0,$ftoW,$ftoH,$srcW,$srcH);
     }

     switch ($srcInfo[2])
     {
     
       case 1:
         imagegif($ni,$toFile);
         break;
       case 2:
         imagejpeg($ni,$toFile,$cfg_jpeg_query);   
         break;
      case 3:
         imagepng($ni,$toFile);   
         break;
      case 6:
         imagebmp($ni,$toFile);  
         break;
       default:
         return false;
     }
     imagedestroy($ni);
  }else{
  	copy($srcFile,$toFile);
  }
  imagedestroy($im);
  return true;
}
//--------------------------------
//获得GD的版本
//--------------------------------
function gdversion()
{ 
  static $gd_version_number = null; 
  if ($gd_version_number === null)
  { 
    ob_start(); 
    phpinfo(8); 
    $module_info = ob_get_contents(); 
    ob_end_clean(); 
    if(preg_match("/\bgd\s+version\b[^\d\n\r]+?([\d\.]+)/i", $module_info,$matches))
    {   $gdversion_h = $matches[1];  }
    else
    {  $gdversion_h = 0; }
  } 
  return $gdversion_h; 
}
//-------------------------------------
//图片自动加水印函数
//-------------------------------------
function WaterImg($srcFile,$fromGo='up')
{
   include(dirname(__FILE__)."/inc_photowatermark_config.php");
   if($photo_markup!='1') return;
   $info = "";
   $srcInfo = GetImageSize($srcFile,$info);
   $srcFile_w    = $srcInfo[0];
   $srcFile_h    = $srcInfo[1];
   if($srcFile_w < $photo_wwidth || $srcFile_h < $photo_wheight) return;
   if($fromGo=='up' && $photo_markup=='0') return;
   if($fromGo=='down' && $photo_markdown=='0') return;   
   $trueMarkimg = dirname(__FILE__).'/data/'.$photo_markimg;
   if(!file_exists($trueMarkimg) || empty($photo_markimg)) $trueMarkimg = "";
   ImgWaterMark($srcFile,$photo_waterpos,$trueMarkimg,$photo_watertext,$photo_fontsize,$photo_fontcolor,$photo_diaphaneity); 
}
//图片自动加水印函数原始函数
//------------------------------------------------
function ImgWaterMark($srcFile,$w_pos=0,$w_img="",$w_text="",$w_font=5,$w_color="#FF0000",$w_pct)
{
    $font_type = dirname(__FILE__).'/data/ant1.ttf';
    if(empty($srcFile) || !file_exists($srcFile)) return ;
    $info = '';
    $srcInfo = getimagesize($srcFile,$info);
    $srcFile_w    = $srcInfo[0];
    $srcFile_h    = $srcInfo[1];
    switch($srcInfo[2]){
      case 1 :
         if(!function_exists("imagecreatefromgif")) return;
         $srcFile_img = imagecreatefromgif($srcFile);
         break;
      case 2 :
         if(!function_exists("imagecreatefromjpeg")) return;
         $srcFile_img = imagecreatefromjpeg($srcFile);
         break;
      case 3 :
          if(!function_exists("imagecreatefrompng")) return;
         $srcFile_img = imagecreatefrompng($srcFile);
         break;
      case 6:
          if(!function_exists("imagewbmp")) return;
          $srcFile_img = imagecreatefromwbmp($srcFile);    
          break;
      default :
          return;
    }
    //读取水印图片
    if(!empty($w_img) && file_exists($w_img)){
     $ifWaterImage = 1;
     $info = '';
     $water_info = getimagesize($w_img,$info);
     $width      = $water_info[0];
     $height     = $water_info[1];
     switch($water_info[2]){
           case 1 :
       if(!function_exists("imagecreatefromgif")) return;
       $water_img = imagecreatefromgif($w_img);
       break;
           case 2 :
       if(!function_exists("imagecreatefromjpeg")) return;
       $water_img = imagecreatefromjpeg($w_img);
       break;
           case 3 :
       if(!function_exists("imagecreatefrompng")) return;
       $water_img = imagecreatefrompng($w_img);
       break;
           case 6 :
       if(!function_exists("imagecreatefromwbmp")) return;
       $srcFile_img = imagecreatefromwbmp($w_img);    
       break;
           default :
       return;
     }
    }else{
      $ifWaterImage = 0;
      $ifttf = 1;
      @$temp = imagettfbbox($w_font,0,$font_type,$w_text);
      $width = $temp[2] - $temp[6];
      $height = $temp[3] - $temp[7];
      unset($temp);
      if(empty($width)&&empty($height)){
        $width = strlen($w_text) * 10;
        $height = 20;
        $ifttf = 0;
      }
    }
    //水印位置
    if($w_pos==0){ //随机位置
      $wX = rand(0,($srcFile_w - $width));
      $wY = rand(0,($srcFile_h - $height));
    }else if($w_pos==1){ //左上角
      $wX = 5;
      if($ifttf==1) $wY = $height + 5;
      else $wY = 5;
    }else if($w_pos==2){ //左中
      $wX = 5;
      $wY = ($srcFile_h - $height) / 2;
    }else if($w_pos==3){ //左下
      $wX = 5;
      $wY = $srcFile_h - $height - 5;
    }else if($w_pos==4){ //上中
      $wX = ($srcFile_w - $width) / 2;
      if($ifttf==1) $wY = $height + 5;
      else $wY = 5;
    }else if($w_pos==5){ //正中
      $wX = ($srcFile_w - $width) / 2;
      $wY = ($srcFile_h - $height) / 2;
    }else if($w_pos==6){ //下中
      $wX = ($srcFile_w - $width) / 2;
      $wY = $srcFile_h - $height - 5;
    }else if($w_pos==7){ //右上
      $wX = $srcFile_w - $width - 5;
      if($ifttf==1) $wY = $height + 5;
      else $wY = 5;
    }else if($w_pos==8){ //右中
      $wX = $srcFile_w - $width - 5;
      $wY = ($srcFile_h - $height) / 2;
    }else if($w_pos==9){ //右下
      $wX = $srcFile_w - $width - 5;
      $wY = $srcFile_h - $height - 5;
    }else{ //中
      $wX = ($srcFile_w - $width) / 2;
      $wY = ($srcFile_h - $height) / 2;
    }
    //写入水印
    imagealphablending($srcFile_img, true);
    if($ifWaterImage){
      imagecopymerge($srcFile_img, $water_img, $wX, $wY, 0, 0, $width,$height,$w_pct);
    }else{
      if(!empty($w_color) && (strlen($w_color)==7)){
         $R = hexdec(substr($w_color,1,2));
         $G = hexdec(substr($w_color,3,2));
         $B = hexdec(substr($w_color,5));
      }else{
         return;
      }
      if($ifttf==1) imagettftext($srcFile_img, $w_font, 0, $wX, $wY, imagecolorallocate($srcFile_img,$R,$G,$B), $font_type, $w_text); 
      else imagestring($srcFile_img,$w_font,$wX,$wY,$w_text,imagecolorallocate($srcFile_img,$R,$G,$B));
    }
    //保存结果
    switch($srcInfo[2]){
       case 1 :
    if(function_exists("imagegif")) imagegif($srcFile_img,$srcFile);
    break;
       case 2 :
    if(function_exists("imagejpeg")) imagejpeg($srcFile_img,$srcFile);
    break;
       case 3 :
    if(function_exists("imagepng")) imagepng($srcFile_img,$srcFile);
    break;
       case 6 :
    if(function_exists("imagewbmp")) imagewbmp($srcFile_img,$srcFile);
    break;
       default :
    return;
    }
    if(isset($water_info)) unset($water_info);
    if(isset($water_img)) imagedestroy($water_img);
    unset($srcInfo);
    imagedestroy($srcFile_img);
}
?>
