<?php
require_once(dirname(__FILE__)."/config.php");
AjaxHead();
$myhtml = UnicodeUrl2Gbk(stripslashes($myhtml));
echo "<div class='coolbg61'>[<a href='#' onclick='javascript:HideObj(\"_myhtml\")'>¹Ø±Õ</a>]</div>\r\n";
preg_match_all("/(src|SRC)=[\"|'| ]{0,}(http:\/\/(.*)\.(gif|jpg|jpeg|png))/isU",$myhtml,$img_array);
$img_array = array_unique($img_array[2]);
echo "<div class='coolbg62'><xmp>";
echo "²¶»ñµÄÍ¼Æ¬£º\r\n";
print_r($img_array);
echo "</xmp></div>\r\n";
?>