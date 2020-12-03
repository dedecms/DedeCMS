<?php

if(!defined('DEDEINC')) exit('Request Error!');


function lib_booklist(&$ctag, &$refObj, $getcontent=0)
{
	global $dsql, $envs, $cfg_dbprefix, $cfg_cmsurl;
	
	//属性处理
	$attlist="row|12,booktype|-1,titlelen|30,orderby|lastpost,author|,keyword|";
	FillAttsDefault($ctag->CAttribute->Items,$attlist);
	extract($ctag->CAttribute->Items, EXTR_SKIP);

	if( !$dsql->IsTable("{$cfg_dbprefix}story_books") ) return '没安装连载模块';
	$addquery = '';
	
	if(empty($innertext))
	{
			if($getcontent==0) $innertext = GetSysTemplets('booklist.htm');
			else $innertext = GetSysTemplets('bookcontentlist.htm');
	}
	
	//图书类型
	if($booktype!=-1) {
			$addquery .= " And b.booktype='{$booktype}' ";
	}
	
	//推荐
	if($orderby=='commend')
	{
			$addquery .= " And b.iscommend=1 ";
			$orderby = 'lastpost';
	}

	//作者条件
	if(!empty($author))
	{
		$addquery .= " And b.author like '$author' ";
	}
	
	//关键字条件
	if(!empty($keyword))
	{
			$keywords = explode(',', $keyword);
			$keywords = array_unique($keywords);
			if(count($keywords)>0) {
				$addquery .= " And (";
			}
			foreach($keywords as $v) {
				$addquery .= " CONCAT(b.author,b.bookname,b.keywords) like '%$v%' OR";
			}
			$addquery = ereg_replace(" OR$","",$addquery);
			$addquery .= ")";
	}
	
	$clist = '';
	$query = "Select b.id,b.catid,b.ischeck,b.booktype,b.iscommend,b.click,b.bookname,b.lastpost,
 		b.author,b.mid,b.litpic,b.pubdate,b.weekcc,b.monthcc,b.description,c.classname,c.classname as typename,c.booktype as catalogtype
 		From `#@__story_books` b left join `#@__story_catalog` c on c.id=b.catid
 		where b.postnum>0 And b.ischeck>0 $addquery order by b.{$orderby} desc limit 0, $row";
	$dsql->SetQuery($query);
	$dsql->Execute();

	$ndtp = new DedeTagParse();
	$ndtp->SetNameSpace('field', '[', ']');
	$GLOBALS['autoindex'] = 0;
	while($row = $dsql->GetArray())
	{
			$GLOBALS['autoindex']++;
			$row['title'] = $row['bookname'];
			$ndtp->LoadString($innertext);

			//获得图书最新的一个更新章节
			$row['contenttitle'] = '';
			$row['contentid'] = '';
			if($getcontent==1) {
				$nrow = $dsql->GetOne("Select id,title,chapterid From `#@__story_content` where bookid='{$row['id']}' order by id desc ");
				$row['contenttitle'] = $nrow['title'];
				$row['contentid'] = $nrow['id'];
			}
			if($row['booktype']==1) {
				$row['contenturl'] = $cfg_cmspath.'/book/show-photo.php?id='.$row['contentid'];
			}
			else {
				$row['contenturl'] = $cfg_cmspath.'/book/story.php?id='.$row['contentid'];
			}

			//动态网址
			$row['dmbookurl'] = $cfg_cmspath.'/book/book.php?id='.$row['id'];

			//静态网址
			$row['bookurl'] = $row['url'] = GetBookUrl($row['id'],$row['bookname']);
			$row['catalogurl'] = $cfg_cmspath.'/book/list.php?id='.$row['catid'];
			$row['cataloglink'] = "<a href='{$row['catalogurl']}'>{$row['classname']}</a>";
			$row['booklink'] = "<a href='{$row['bookurl']}'>{$row['bookname']}</a>";
			$row['contentlink'] = "<a href='{$row['contenturl']}'>{$row['contenttitle']}</a>";
			$row['imglink'] = "<a href='{$row['bookurl']}'><img src='{$row['litpic']}' width='$imgwidth' height='$imgheight' border='0' /></a>";
			
			if($row['ischeck']==2) $row['ischeck']='已完成连载';
			else $row['ischeck']='连载中...';

			if($row['booktype']==0) $row['booktypename']='小说';
			else $row['booktypename']='漫画';

			if(is_array($ndtp->CTags))
			{
				foreach($ndtp->CTags as $tagid=>$ctag)
				{
					$tagname = $ctag->GetTagName();
					if(isset($row[$tagname])) $ndtp->Assign($tagid,$row[$tagname]);
					else $ndtp->Assign($tagid,'');
				}
			}
			$clist .= $ndtp->GetResult();
		}
		
		return $clist;
}

?>