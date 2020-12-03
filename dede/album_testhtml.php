<?php 
$__ONLYDB = true;
$__ONLYCONFIG = true;
require_once(dirname(__FILE__)."/../include/config_base.php");
require_once(dirname(__FILE__)."/../include/pub_charset.php");
AjaxHead(); 

$myhtml = UnicodeUrl2Gbk(stripslashes($myhtml));

echo "<div class='coolbg4' style='width:380px'>[<a href='#' onclick='javascript:HideObj(\"_myhtml\")'>关闭</a>]</div>\r\n";

preg_match_all("/(src|SRC)=[\"|'| ]{0,}(http:\/\/(.*)\.(gif|jpg|jpeg|png))/isU",$body,$img_array);
$img_array = array_unique($img_array[2]);

echo "捕获的图片：";
print_r($img_array);

echo "<span class='coolbg5'>&nbsp;</span>\r\n";
?>