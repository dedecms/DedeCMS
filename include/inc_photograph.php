<?
//--------------------------------
//缩图片自动生成函数
//--------------------------------
function ImageResize($srcFile,$toW,$toH) 
{
   $toFile = $srcFile;
   $info = "";
   $data = GetImageSize($srcFile,$info);
   switch ($data[2]) 
   {
	   case 1:
		    if(!function_exists("imagecreatefromgif")){
		    	echo "你的GD库不能使用GIF格式的图片，请使用Jpeg或PNG格式！<a href='javascript:go(-1);'>返回</a>";
		    	exit();
		    }
		    $im = ImageCreateFromGIF($srcFile);
		    break;
	   case 2:
		    if(!function_exists("imagecreatefromjpeg")){
		    	echo "你的GD库不能使用jpeg格式的图片，请使用其它格式的图片！<a href='javascript:go(-1);'>返回</a>";
		    	exit();
		    }
		    $im = ImageCreateFromJpeg($srcFile);    
		    break;
	   case 3:
		    $im = ImageCreateFromPNG($srcFile);    
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
     if(function_exists("imagecopyresampled")){
        $ni = imagecreatetruecolor($ftoW,$ftoH);
        ImageCopyResampled($ni,$im,0,0,0,0,$ftoW,$ftoH,$srcW,$srcH);
     }
     else{
        $ni=imagecreatetruecolor($ftoW,$ftoH);
       	ImageCopyResized($ni,$im,0,0,0,0,$ftoW,$ftoH,$srcW,$srcH);
     }
     if(function_exists('imagejpeg')) imagejpeg($ni,$toFile);
     else imagepng($ni,$toFile);
     Imagedestroy($ni);
  }
  Imagedestroy($im);
}
//-------------------------------------
//图片自动加水印函数
//-------------------------------------
/*
function ImageWatermark($groundImage,$waterPos=0,$waterImage="",$waterText="",$textFont=5,$textColor="#FF0000") 
{ 
    $isWaterImage = FALSE; 
    $formatMsg = "图片格式错误，只能处理PNG、GIF、Jpeg格式的图片！"; 
    if(!empty($waterImage) && file_exists($waterImage)) 
    { 
        $isWaterImage = TRUE; 
        $water_info = etImageSize($waterImage); 
        $water_w    = $water_info[0];
        $water_h    = $water_info[1];
        switch($water_info[2])
        { 
            case 1: $water_im = ImageCreateFromGIF($waterImage); break; 
            case 2: $water_im = ImageCreateFromJpeg($waterImage); break; 
            case 3: $water_im = ImageCreateFromPNG($waterImage); break; 
            default:die($formatMsg); 
        } 
    } 
    //用来组合的图片
    if(!empty($groundImage) && file_exists($groundImage)) 
    { 
        $ground_info = getimagesize($groundImage); 
        $ground_w    = $ground_info[0];
        $ground_h    = $ground_info[1];
        switch($ground_info[2])
        { 
            case 1:$ground_im = ImageCreateFromGIF($groundImage);break; 
            case 2:$ground_im = ImageCreateFromJpeg($groundImage);break; 
            case 3:$ground_im = ImageCreateFromPNG($groundImage);break; 
            default:die($formatMsg); 
        } 
    }else { 
        die(""); 
    } 
 
    if($isWaterImage)
    { 
        $w = $water_w; 
        $h = $water_h; 
        $label = ""; 
    } 
    else{ 
        $temp = imagettfbbox(ceil($textFont*2.5),0,"./cour.ttf",$waterText);
        $w = $temp[2] - $temp[6]; 
        $h = $temp[3] - $temp[7]; 
        unset($temp); 
        $label = ""; 
    } 
    if( ($ground_w<$w) || ($ground_h<$h) ) 
    { 
        echo "".$label.""; 
        return; 
    } 
    switch($waterPos) 
    { 
        case 0:
            $posX = rand(0,($ground_w - $w)); 
            $posY = rand(0,($ground_h - $h)); 
            break; 
        case 1:
            $posX = 0; 
            $posY = 0; 
            break; 
        case 2:
            $posX = ($ground_w - $w) / 2; 
            $posY = 0; 
            break; 
        case 3:
            $posX = $ground_w - $w; 
            $posY = 0; 
            break; 
        case 4:
            $posX = 0; 
            $posY = ($ground_h - $h) / 2; 
            break; 
        case 5:
            $posX = ($ground_w - $w) / 2; 
            $posY = ($ground_h - $h) / 2; 
            break; 
        case 6:
            $posX = $ground_w - $w; 
            $posY = ($ground_h - $h) / 2; 
            break; 
        case 7:
            $posX = 0; 
            $posY = $ground_h - $h; 
            break; 
        case 8:
            $posX = ($ground_w - $w) / 2; 
            $posY = $ground_h - $h; 
            break; 
        case 9:
            $posX = $ground_w - $w; 
            $posY = $ground_h - $h; 
            break; 
        default:
            $posX = rand(0,($ground_w - $w)); 
            $posY = rand(0,($ground_h - $h)); 
            break;     
    } 
 
    imagealphablending($ground_im, true); 
 
    if($isWaterImage)
    { 
        imagecopy($ground_im, $water_im, $posX, $posY, 0, 0, $water_w,$water_h);         
    } 
    else
    { 
        if( !empty($textColor) && (strlen($textColor)==7) ) 
        { 
            $R = hexdec(substr($textColor,1,2)); 
            $G = hexdec(substr($textColor,3,2)); 
            $B = hexdec(substr($textColor,5)); 
        } 
        else 
        { 
            die(""); 
        } 
        imagestring ( $ground_im, $textFont, $posX, $posY, $waterText, imagecolorallocate($ground_im, $R, $G, $B));         
    } 
 
    @unlink($groundImage); 
    switch($ground_info[2])
    { 
        case 1:imagegif($ground_im,$groundImage);break; 
        case 2:imagejpeg($ground_im,$groundImage);break; 
        case 3:imagepng($ground_im,$groundImage);break; 
        default:die($errorMsg); 
    } 
    if(isset($water_info)) unset($water_info); 
    if(isset($water_im)) imagedestroy($water_im); 
    unset($ground_info); 
    imagedestroy($ground_im); 
} 
*/
?>