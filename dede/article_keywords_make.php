<?php
@ob_start();
@set_time_limit(3600);
require_once(dirname(__FILE__).'/config.php');
CheckPurview('sys_Keyword');
if(empty($dopost))
{
	$dopost = '';
}

//分析已存在的关键字（适用于默认的文章模型）
if($dopost=='analyse')
{
	echo "正在读取关键字数据库...<br/>\r\n";
	flush();
	$ws = $wserr = $wsnew = "";
	$dsql->SetQuery("Select * from `#@__keywords`");
	$dsql->Execute();
	while($row = $dsql->GetObject())
	{
		if($row->sta==1)
		{
			$ws[$row->keyword] = 1;
		}
		else
		{
			$wserr[$row->keyword] = 1;
		}
	}
	echo "完成关键字数据库的载入！<br/>\r\n";
	flush();
	echo "读取档案数据库，并对禁用的关键字和生字进行处理...<br/>\r\n";
	flush();
	$dsql->SetQuery("Select id,keywords from `#@__archives`");
	$dsql->Execute();
	while($row = $dsql->GetObject())
	{
		$keywords = explode(',',trim($row->keywords));
		$nerr = false;
		$mykey = '';
		if(is_array($keywords))
		{
			foreach($keywords as $v)
			{
				$v = trim($v);
				if($v=='')
				{
					continue;
				}
				if(isset($ws[$v]))
				{
					$mykey .= $v." ";
				}
				else if(isset($wsnew[$v]))
				{
					$mykey .= $v.' ';
					$wsnew[$v]++;
				}
				else if(isset($wserr[$v]))
				{
					$nerr = true;
				}
				else
				{
					$mykey .= $v." ";
					$wsnew[$v] = 1;
				}
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
			if(strlen($k)>20)
			{
				continue;
			}
			$dsql->SetQuery("Insert Into `#@__keywords`(keyword,rank,sta,rpurl) Values('".addslashes($k)."','$v','1','')");
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
	ShowMsg('完成所有操作，现在转到关键字列表页！','article_keywords_main.php');
	exit();
}
//自动获取关键字（适用于默认的文章模型）
else if($dopost=='fetch')
{
	require_once(DEDEINC."/splitword.class.php");
	if(empty($startdd))
	{
		$startdd = 0;
	}
	if(empty($pagesize))
	{
		$pagesize = 20;
	}
	if(empty($totalnum))
	{
		$totalnum = 0;
	}

	//统计记录总数
	if($totalnum==0)
	{
		$row = $dsql->GetOne("Select count(*) as dd From `#@__archives` where channel='1' ");
		$totalnum = $row['dd'];
	}

	//获取记录，并分析关键字
	if($totalnum > $startdd+$pagesize)
	{
		$limitSql = " limit $startdd,$pagesize";
	}
	else if(($totalnum-$startdd)>0)
	{
		$limitSql = " limit $startdd,".($totalnum - $startdd);
	}
	else
	{
		$limitSql = '';
	}
	$tjnum = $startdd;
	if($limitSql!='')
	{
		$fquery = "Select arc.id,arc.title,arc.keywords,addon.body From `#@__archives` arc
	          left join `#@__addonarticle` addon on addon.aid=arc.id where arc.channel='1' $limitSql ";
		$dsql->SetQuery($fquery);
		$dsql->Execute();
		$sp = new SplitWord($cfg_soft_lang, $cfg_soft_lang);
		while($row=$dsql->GetObject())
		{
			if($row->keywords!='')
			{
				continue;
			}
			$tjnum++;
			$id = $row->id;
			$keywords = "";
			$sp->SetSource($row->title, $cfg_soft_lang, $cfg_soft_lang);
			$titleindexs = $sp->GetFinallyIndex();
			$sp->SetSource(Html2Text($row->body), $cfg_soft_lang, $cfg_soft_lang);
			$allindexs = $sp->GetFinallyIndex();
			if(is_array($allindexs) && is_array($titleindexs))
			{
				foreach($titleindexs as $k)
				{
					if(strlen($keywords)>=30)
					{
						break;
					}
					else
					{
						$keywords .= $k.",";
					}
				}
				foreach($allindexs as $k)
				{
					if(strlen($keywords)>=30)
					{
						break;
					}
					else if(!in_array($k,$titleindexs))
					{
						$keywords .= $k.",";
					}
				}
			}
			$keywords = addslashes($keywords);
			if($keywords=='')
			{
				$keywords = ',';
			}
			$dsql->ExecuteNoneQuery("update `#@__archives` set keywords='$keywords' where id='$id'");
		}
		unset($sp);
	}//end if limit

	//返回提示信息
	if($totalnum>0)
	{
		$tjlen = ceil( ($tjnum/$totalnum) * 100 );
	}
	else
	{
		$tjlen=100;
	}

	$dvlen = $tjlen * 2;
	$tjsta = "<div style='width:200;height:15;border:1px solid #898989;text-align:left'><div style='width:$dvlen;height:15;background-color:#829D83'></div></div>";
	$tjsta .= "<br/>完成处理文档总数的：$tjlen %，位置：{$startdd}，继续执行任务...";

	if($tjnum < $totalnum)
	{
		$nurl = "article_keywords_make.php?dopost=fetch&totalnum=$totalnum&startdd=".($startdd+$pagesize)."&pagesize=$pagesize";
		ShowMsg($tjsta,$nurl,0,500);
	}
	else
	{
		ShowMsg("完成所有任务！","javascript:;");
	}
	exit();
}

include DedeInclude('templets/article_keywords_make.htm');

?>