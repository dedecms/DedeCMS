<?php
require_once(dirname(__FILE__)."/include/config_base.php");
require_once(dirname(__FILE__)."/include/inc_taglist_view.php");
$PageNo = 1;
if(isset($_SERVER["QUERY_STRING"])){
	$tag = trim($_SERVER["QUERY_STRING"]);
	$tags = explode('/',$tag);
	$tag = $tags[1];
	if(count($tags)>3) $PageNo = intval($tags[2]);
}else{ 
	$tag = "";
}
$tag = urldecode($tag);
//如果没有Tag或Tag不合法，显示所有Tag
if($tag=="" || $tag!=addslashes($tag) ){
	$dlist = new TagList($tag,'tag.htm');
}
//如果有Tag，显示文档列表
else{
	$dlist = new TagList($tag,'taglist.htm');
}

$dlist->Display();

exit();
?>
