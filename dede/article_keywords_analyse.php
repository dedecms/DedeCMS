<?php
@ob_start();
@set_time_limit(3600);
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_Keyword');

header("Content-Type: text/html; charset={$cfg_ver_lang}");
echo "正在读取关键字数据库...<br/>\r\n";
flush();

$ws = "";
$wserr = "";
$wsnew = "";

$dsql = new DedeSql(false);
$dsql->SetQuery("Select * from #@__keywords");
$dsql->Execute();
while($row = $dsql->GetObject())
{
	if($row->sta==1)
		$ws[$row->keyword] = 1;
	else
		$wserr[$row->keyword] = 1;
}

echo "完成关键字数据库的载入！<br/>\r\n";
flush();

echo "读取档案数据库，并对禁用的关键字和生字进行处理...<br/>\r\n";
flush();

$dsql->SetQuery("Select aid,keywords from #@__full_search");
$dsql->Execute();
while($row = $dsql->GetObject())
{
	$keywords = explode(" ",trim($row->keywords));
	$nerr = false;
	$mykey = "";
	if(is_array($keywords))
	{
		foreach($keywords as $v){
			$v = trim($v);
			if($v=="") continue;
			if(isset($ws[$v])) $mykey .= $v." ";
			else if(isset($wsnew[$v])){
				$mykey .= $v." ";
				$wsnew[$v]++;
			}
			else if(isset($wserr[$v])) $nerr = true;
			else{
				$mykey .= $v." ";
				$wsnew[$v] = 1;
			}
		}
		//如果关键字中有禁用的关键字，则更新文章的关键字
		if($nerr)
		{
			$dsql->SetQuery("update #@__full_search set keywords='".addslashes($mykey)."' where aid='".$row->aid."' ");
			$dsql->ExecuteNoneQuery();
		}
	}
}
echo "完成档案数据库的处理！<br/>\r\n";
flush();
if(is_array($wsnew))
{
  echo "对关键字进行排序...<br/>\r\n";
  flush();
  arsort($wsnew);
  echo "把关键字保存到数据库...<br/>\r\n";
  flush();
  foreach($wsnew as $k=>$v)
  {
	  if(strlen($k)>20) continue;
	  $dsql->SetQuery("Insert Into #@__keywords(keyword,rank,sta,rpurl) Values('".addslashes($k)."','$v','1','')");
	  $dsql->Execute();
  }
  echo "完成关键字的导入！<br/>\r\n";
  flush();
  sleep(1);
}
else
{
	echo "没发现任何新的关键字！<br/>\r\n";
  flush();
  sleep(1);
}
ClearAllLink();
ShowMsg("完成所有操作，现在转到关键字列表页！","article_keywords_main.php");
?>