<?
require_once(dirname(__FILE__)."/../include/inc_arcsearch_view.php");

if(empty($typeid)) $typeid = 0;
else $typeid = ereg_replace("[^0-9]","",$typeid);

if(empty($orderby)) $orderby="";
else $orderby = eregi_replace("[^a-z]","",$orderby);

if(empty($channeltype)) $channeltype="-1";
else $channeltype = eregi_replace("[^0-9]","",$channeltype);

if(empty($searchtype)) $searchtype = "titlekeyword";
else $searchtype = eregi_replace("[^a-z]","",$searchtype);

if(empty($pagesize)) $pagesize = 20;
else $pagesize = eregi_replace("[^0-9]","",$pagesize);

if(empty($keyword) && empty($keywords))
{
	ShowMsg("关键字不能为空！","-1");
	exit();
}
else //处理关键字
{
	if(!empty($keyword)){
		if(strlen($keyword)>10)
		{
		   require_once(dirname(__FILE__)."/../include/pub_splitword_www.php");
		   $sp = new SplitWord();
	     $keywords = $sp->SplitRMM($keyword);
	     $sp->Clear();
	     $ks = explode(" ",$keywords);
	     $hk = 0;
	     foreach($ks as $k){
 			   $k = trim($k);
 			   if($k=="") continue;
 			   $hk++;
 			 }
 			 if($hk==0){ $keywords = $keyword; }
	  }
	  else{
		   $keywords = $keyword;
	  }
	}
}

if(empty($starttime)) $starttime = -1;
else //处理开始时间
{
	if($starttime>0)
	{
	  $starttime = ereg_replace("[^0-9]","",$starttime);
	  $dayst = GetMkTime("2006-1-2 0:0:0") - GetMkTime("2006-1-1 0:0:0");
	  $starttime = time() - ($starttime * $dayst);
  }
}

if(empty($kwtype)) $kwtype = 1;

$sp = new SearchView($typeid,$keywords,$orderby,$channeltype,$searchtype,$starttime,$pagesize,$kwtype);
$sp->Display();
$sp->Close();

?>