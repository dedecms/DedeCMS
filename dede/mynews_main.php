<?php
require_once(dirname(__FILE__)."/config.php");
require_once(DEDEINC."/typelink.class.php");
require_once(DEDEINC."/datalistcp.class.php");
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");
$sql = "Select
 #@__mynews.aid,#@__mynews.title,#@__mynews.writer,
 #@__mynews.senddate,#@__mynews.typeid,
 #@__arctype.typename
 From #@__mynews
 left join #@__arctype on #@__arctype.id=#@__mynews.typeid
 order by aid desc";
$dlist = new DataListCP();
$dlist->SetTemplet(DEDEADMIN."/templets/mynews_main.htm");
$dlist->SetSource($sql);
$dlist->display();

?>