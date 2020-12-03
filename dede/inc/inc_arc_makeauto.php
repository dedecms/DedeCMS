<?php
$autolitpic = (empty($autolitpic) ? '' : $autolitpic);
 ${$vs[0]} = stripslashes(${$vs[0]});
//获得文章body里的外部资源
if($cfg_isUrlOpen && $remote==1){ ${$vs[0]} = GetCurContent(${$vs[0]}); }
//去除内容中的站外链接
if($dellink==1)
{
	${$vs[0]} = str_replace($cfg_basehost,'#basehost#',${$vs[0]});
	${$vs[0]} = preg_replace("/(<a[ \t\r\n]{1,}href=[\"']{0,}http:\/\/[^\/]([^>]*)>)|(<\/a>)/isU","",${$vs[0]});
  ${$vs[0]} = str_replace('#basehost#',$cfg_basehost,${$vs[0]});
}
//自动摘要
if($description=="" && $cfg_auot_description>0)
{
    $description = cn_substr(html2text(${$vs[0]}),$cfg_auot_description);
	  $description = trim(preg_replace("/#p#|#e#/","",$description));
	  $description = addslashes($description);
	  $autodes = true;
}
//自动获取缩略图
if($autolitpic==1 && $litpic==''){
   $litpic = GetDDImgFromBody(${$vs[0]});
   $autopic = true;
}
${$vs[0]} = addslashes(${$vs[0]});
${$vs[0]} = GetFieldValueA(${$vs[0]},$vs[1],$arcID);  
?>       