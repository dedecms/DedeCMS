<?
function pic_resize($srcFile,$toFile,$toW,$toH) 
 {
    $data = GetImageSize($srcFile,&$info);
    switch ($data[2]) 
    {
	            case 1:
		     $im = @ImageCreateFromGIF($srcFile);
		     break;
	             case 2:
		     $im = @ImageCreateFromJpeg($srcFile);    
		     break;
	             case 3:
		     $im = @ImageCreateFromPNG($srcFile);    
		     break;
   }
  $srcW=ImageSX($im);
  $srcH=ImageSY($im);
  $toWH=$toW/$toH;
  $srcWH=$srcW/$srcH;
  if($toWH<=$srcWH)
  {
           $ftoW=$toW;
           $ftoH=$ftoW*($srcH/$srcW);
   }
   else
   {
           $ftoH=$toH;
           $ftoW=$ftoH*($srcW/$srcH);
  }    
  if($srcW>$toW||$srcH>$toH)
  {
       if(function_exists("imagecopyresampled"))
       {
             $ni = imagecreatetruecolor($ftoW,$ftoH);
             ImageCopyResampled($ni,$im,0,0,0,0,$ftoW,$ftoH,$srcW,$srcH);
        }
        else
        {
           $ni=imagecreatetruecolor($ftoW,$ftoH);
       	   ImageCopyResized($ni,$im,0,0,0,0,$ftoW,$ftoH,$srcW,$srcH);
        }
       ImageJPEG($ni,$toFile);
       Imagedestroy($ni);
  }
  else
  {
  	copy($srcFile,$toFile);
  }	
  Imagedestroy($im);
}
?>