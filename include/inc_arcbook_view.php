<?php
require_once(dirname(__FILE__)."/config_base.php");
require_once(DEDEINC."/inc_channel_unit_functions.php");
require_once(DEDEINC."/pub_dedetag.php");
require_once(DEDEINC."/inc_bookfunctions.php");
/******************************************************
//Copyright 2004-2006 by DedeCms.com itprato
//本类的用途是用于连载频道的内容浏览
******************************************************/
@set_time_limit(0);
class BookView
{
	var $dsql;
	var $dtp;
	var $InitType;
	var $CatalogID;
	var $BookID;
	var $ContentID;
	var $ChapterID;
	var $BookType;
	var $PageNo;
	var $TotalPage;
	var $TotalResult;
	var $PageSize;
	var $CacheArray;
	var $PreNext;
	var $Keys;
	//-------------------------------
	//php5构造函数
	//-------------------------------
	function __construct($rsid=0,$inittype='catalog',$kws='')
 	{
 		global $cfg_basedir,$PubFields;
 		$this->CacheArray = Array();
 		$this->PreNext = Array();
 		$this->InitType = $inittype;
	  $this->CatalogID = 0;;
	  $this->BookID = 0;
	  $this->ContentID = 0;
	  $this->ChapterID = 0;
	  $this->TotalResult = -1;
	  $this->BookType = 'story';
	  if(is_array($kws)) $this->Keys = $kws;
	  else $this->Keys = Array();

 		$this->dsql = new DedeSql(false);
 		$this->dtp = new DedeTagParse();
 		$this->dtp->SetNameSpace("dede","{","}");
 		$this->Fields['id'] = $rsid;
 		$this->Fields['position'] = '';
 		$this->Fields['title'] = '';
 		//设置一些全局参数的值
 		foreach($PubFields as $k=>$v) $this->Fields[$k] = $v;

 		$this->MakeFirst($inittype,$rsid);

 		//如果分类有上级栏目，获取其信息
 		$this->Fields['pclassname'] = '';
 		if($inittype!='index' && $inittype!='search')
 		{
 		  if($this->Fields['pid']>0){
 			  $row = $this->dsql->GetOne("Select classname From #@__story_catalog where id='{$this->Fields['pid']}' ");
 			  $this->Fields['pclassname'] = $row['classname'];
 		  }
 		  $this->Fields['fulltitle'] = $this->Fields['title'];
 		  if($this->Fields['pclassname']!=''){
 			  $this->Fields['fulltitle'] = $this->Fields['fulltitle']." - ".$this->Fields['pclassname'];
 		  }
    }
  }
  //php4构造函数
 	//---------------------------
 	function BookView($rsid=0,$inittype='catalog',$kws='')
 	{
 		$this->__construct($rsid,$inittype);
 	}

 	/*---------------------------
 	//根据初始化的类型获取filed值，并载入模板
 	--------------------------*/
 	function MakeFirst($inittype,$rsid)
 	{
 		global $cfg_basedir,$PubFields;
 		//列表
 		if($inittype=='catalog')
 		{
 			$this->CatalogID = $rsid;
 			$row = $this->dsql->GetOne("Select * From #@__story_catalog where id='$rsid' ");
 			foreach($row as $k=>$v) if(ereg('[^0-9]',$k)) $this->Fields[$k] = $v;
 			unset($row);
 			$this->dtp->LoadTemplet($cfg_basedir.$PubFields['templetdef'].'/books_list.htm');
 			$this->Fields['title'] = $this->Fields['classname'];
 			$this->Fields['catid'] = $this->Fields['id'];
 			$this->Fields['bcatid'] = $this->Fields['pid'];
 			$this->Fields['position'] = $this->GetPosition($this->Fields['catid'],$this->Fields['bcatid']);
 		}
 		//图书
 		else if($inittype=='book')
 		{
 			$this->BookID = $rsid;
 			$row = $this->dsql->GetOne("Select c.classname,c.pid,bk.* From #@__story_books bk left join #@__story_catalog c on c.id=bk.catid where bk.id='$rsid' ");
 		  foreach($row as $k=>$v) if(ereg('[^0-9]',$k)) $this->Fields[$k] = $v;
 		  unset($row);
 		  $this->dtp->LoadTemplet($cfg_basedir.$PubFields['templetdef'].'/books_book.htm');
 		  $this->Fields['title'] = $this->Fields['bookname'];
 		  $this->CatalogID = $this->Fields['catid'];
 		  if($this->Fields['status']==1) $this->Fields['status']='已完成连载';
 			else $this->Fields['status']='连载中...';
 			$this->Fields['position'] = $this->GetPosition($this->Fields['catid'],$this->Fields['bcatid']);
 		}
 		//内容
 		else if($inittype=='content')
 		{
 			$this->ContentID = $rsid;
 			$nquery = "Select c.classname,c.pid,ct.*,cp.chaptername,cp.chapnum
 			From #@__story_content ct
 			left join #@__story_catalog c on c.id=ct.catid
 			left join #@__story_chapter cp on cp.id=ct.chapterid
 			where ct.id='$rsid' ";
 			$row = $this->dsql->GetOne($nquery,MYSQL_ASSOC);
 			if(!is_array($row)) {showmsg('内容不存在','javascript:;');exit();}
 		  $this->ChapterID = $row['chapterid'];
 		  $this->BookID = $row['bookid'];
 		  $this->Fields['bookurl'] = GetBookUrl($row['bookid'],$row['bookname']);
 		  foreach($row as $k=>$v) if(ereg('[^0-9]',$k)) $this->Fields[$k] = $v;
 		  unset($row);
 		  if($this->Fields['booktype']==1) $this->dtp->LoadTemplet($cfg_basedir.$PubFields['templetdef'].'/books_photo.htm');
 		  else $this->dtp->LoadTemplet($cfg_basedir.$PubFields['templetdef'].'/books_story.htm');
 		  $this->Fields['position'] = $this->GetPosition($this->Fields['catid'],$this->Fields['bcatid']);
 		  $row = $this->dsql->GetOne("Select freenum From #@__story_books where id='{$this->Fields['bookid']}' ");
 		  $this->Fields['freenum'] = $row['freenum'];
 		  $this->Fields['body'] = GetBookText($this->Fields['id']);
 		  $this->Fields['bookid'] = $this->BookID;
 		}
 		//封面
 		else if($inittype=='index')
 		{
 			$this->dtp->LoadTemplet($cfg_basedir.$PubFields['templetdef'].'/books_index.htm');
 		}
 		//搜索界面
 		else if($inittype=='search')
 		{
 			if($this->Keys['id']>0) $this->CatalogID = $this->Keys['id'];
 			$this->dtp->LoadTemplet($cfg_basedir.$PubFields['templetdef'].'/books_search.htm');
 		}
 	}

 	//-------------------------------
 	//解析固定模板标记
 	//-----------------------------
 	function ParseTempletsFirst()
 	{
 		if( !is_array($this->dtp->CTags) ) return 0;
 	  foreach($this->dtp->CTags as $tagid=>$ctag)
 		{
 			$tagname = $ctag->GetTagName();
 		  //字段
 		  if($tagname=='field')
 		  {
 		  	if(isset($this->Fields[$ctag->GetAtt('name')])) $this->dtp->Assign($tagid,$this->Fields[$ctag->GetAtt('name')]);
 				else $this->dtp->Assign($tagid,"");
 		  }
 		  //章节信息
 		  else if($tagname=='chapter')
 		  {
 		  	$this->dtp->Assign($tagid, $this->GetChapterList($this->BookID, $ctag->GetInnerText()) );
 		  }
 		  //栏目信息
 		  else if($tagname=='catalog')
 		  {
 		  	$pid = 0;
 		  	if($ctag->GetAtt('type')=='son') $pid = $this->Fields['catid'];
 		  	$this->dtp->Assign($tagid, $this->GetCatalogList($pid,$ctag->GetInnerText()));
 		  }
 		  //当前图书的最新章节
 		  else if($tagname=='newcontent')
 		  {
 		  	if($ctag->GetAtt('bookid')=='') $bid = $this->BookID;
 		  	if(empty($bid)) $this->dtp->Assign($tagid,'');
 		  	else $this->dtp->Assign($tagid, $this->GetNewContentLink($bid,$ctag->GetInnerText()));
 		  }
 		  //指定记录的图书
 		  else if($tagname=='booklist')
 		  {
 		  	if($ctag->GetAtt('catid')!='') $catid = $ctag->GetAtt('catid');
 		  	else $catid = $this->CatalogID;
 		  	$this->dtp->Assign($tagid,
 		  	    $this->GetBookList(
 		  	       $ctag->GetAtt('row'),
 		  	       $ctag->GetAtt('booktype'),
 		  	       $ctag->GetAtt('titlelen'),
 		  	       $ctag->GetAtt('orderby'),
 		  	       $catid,$this->BookID,
 		  	       $ctag->GetAtt('author'),0,
 		  	       $ctag->GetInnerText(),0,
 		  	       $ctag->GetAtt('imgwidth'),
 		  	       $ctag->GetAtt('imgheight')
 		  	    )
 		  	);
 		  }
 		  //指定记录的章节
 		  else if($tagname=='contentlist')
 		  {
 		  	$this->dtp->Assign($tagid,
 		  	    $this->GetBookList(
 		  	       $ctag->GetAtt('row'),
 		  	       $ctag->GetAtt('booktype'),
 		  	       $ctag->GetAtt('titlelen'),
 		  	       'lastpost',$ctag->GetAtt('catid'),0,
 		  	       '',1,$ctag->GetInnerText()
 		  	    )
 		  	);
 		 }
 		 else if($tagname=='prenext'){
 		  	$this->dtp->Assign($tagid, $this->GetPreNext($ctag->GetAtt('get')));
 		 }

 		 }//End Foreach
 	}


 	//---------------------
 	//解析动态标签
 	//即是指和页码相关的标记
 	//---------------------
 	function ParseDmFields($mtype,$pageno)
 	{
 		if(empty($pageno)) $this->PageNo = 1;
 	  else $this->PageNo=$pageno;
 	  $ctag = $this->dtp->GetTag('list');
 		//先处理 list 标记，因为这个标记包含统计信息
 		$this->dtp->AssignName('list',
 		  	  $this->GetBookList(
 		  	      0,
 		  	      $ctag->GetAtt('booktype'),
 		  	      $ctag->GetAtt('titlelen'),
 		  	      $ctag->GetAtt('orderby'),
 		  	      $this->CatalogID,0,'',-1,
 		  	      $ctag->GetInnerText(),
 		  	      $ctag->GetAtt('pagesize'),
 		  	      $ctag->GetAtt('imgwidth'),
 		  	      $ctag->GetAtt('imgheight')
 		  	  )
 		);

 		//其它动态标记
 	  foreach($this->dtp->CTags as $tagid=>$ctag)
 		{
 			 $tagname = $ctag->GetTagName();
 		   if($tagname=='pagelist'){
 		   	  if($mtype=='dm') $this->dtp->Assign($tagid,$this->GetPageListDM($ctag->GetAtt('listsize'),$ctag->GetAtt('listitem')) );
 		   }
 		}

 	}

 	//------------------
 	//生成HTML（仅图书，内容页因为考虑到收费图书问题，不生成HTML）
 	//$isclose 是是否关闭数据库，
 	//由于mysql每PHP进程只需关闭一次mysql连接，因此在批量更新HTML的时候此选项应该用 false
 	//------------------
 	function MakeHtml($isclose=true)
 	{
 	  global $cfg_basedir;
 	  $bookdir = GetBookUrl($this->Fields['id'],$this->Fields['bookname'],1);
 	  $bookurl = GetBookUrl($this->Fields['id'],$this->Fields['bookname']);
 	  if(!is_dir($cfg_basedir.$bookdir)) CreateDir($bookdir);
 	  $this->ParseTempletsFirst();
 	  $fp = fopen($cfg_basedir.$bookurl,'w');
 		fwrite($fp,$this->dtp->GetResult());
 		fclose($fp);
 		if($isclose) $this->Close();
 		//if($displaysucc) echo "<a href='{$bookurl}' target='_blank'>创建或更新'{$this->Fields['bookname']}'的HTML成功！</a><br />\r\n";
 		return $bookurl;
 	}

 	//------------------
 	//显示内容
 	//------------------
 	function Display()
 	{
 	  global $PageNo;
 	  $this->ParseTempletsFirst();
 	  if($this->InitType=='catalog' || $this->InitType=='search'){
 	     $this->ParseDmFields('dm',$PageNo);
 	  }
 		$this->dtp->Display();
 		$this->Close();
 	}

 	//------------------
 	//获得指定条件的图书列表
 	//-------------------
 	function GetBookList($num=12,$booktype='-1',$titlelen=24,$orderby='lastpost',$catid=0,$bookid=0,$author='',$getcontent=0,$innertext='',$pagesize=0,$imgwidth=90,$imgheight=110)
 	{
 		global $cfg_cmspath;
 		if(empty($num)) $num = 12;
 		if($booktype=='') $booktype = -1;
 		if(empty($titlelen)) $titlelen = 24;
 		if(empty($orderby)) $orderby = 'lastpost';
 		if(empty($bookid)) $bookid = 0;
 		$addquery = '';
 		if(empty($innertext)){
 			  //普通图书列表
 			  if($getcontent==0) $innertext = GetSysTemplets('book_booklist.htm');
 			  //分页图书列表
 			  else if($getcontent==-1) $innertext = GetSysTemplets('book_booklist_m.htm');
 			  //最新章节列表
 			  else $innertext = GetSysTemplets('book_contentlist.htm');
 		}
 		if($booktype!=-1) $addquery .= " And b.booktype='{$booktype}' ";
 		if($bookid>0) $addquery .= " And b.id<>'{$bookid}' ";
 		if($orderby=='commend'){
 			$addquery .= " And b.iscommend=1 ";
 			$orderby = 'lastpost';
 		}
 		if($catid>0){
 			$addquery .= " And (b.catid='$catid' Or b.bcatid='$catid') ";
 		}

 		//分页、搜索列表选项
 		if($getcontent==-1)
 		{
 		  if(empty($pagesize)) $this->PageSize = 20;
 		  else $this->PageSize = $pagesize;
 			$limitnum = ($this->PageSize * ($this->PageNo-1)).','.$this->PageSize;
 		}
 		else{
 			$limitnum = $num;
 		}

 		if(!empty($this->Keys['author'])) $author = $this->Keys['author'];

 		if(!empty($author)){
 			$addquery .= " And b.author like '$author' ";
 		}

 		//关键字条件
 		if(!empty($this->Keys['keyword'])){
 			$keywords = explode(' ',$this->Keys['keyword']);
 			$keywords = array_unique($keywords);
 			if(count($keywords)>0) $addquery .= " And (";
 			foreach($keywords as $v) $addquery .= " CONCAT(b.author,b.bookname,b.keywords) like '%$v%' OR";
 			$addquery = ereg_replace(" OR$","",$addquery);
 			$addquery .= ")";
 		}

 		$clist = '';
 		$query = "Select SQL_CALC_FOUND_ROWS b.id,b.catid,b.ischeck,b.booktype,b.iscommend,b.click,b.bookname,b.lastpost,
 		b.author,b.memberid,b.litpic,b.pubdate,b.weekcc,b.monthcc,b.description,c.classname,c.classname as typename,c.booktype as catalogtype
 		From #@__story_books b left join #@__story_catalog c on c.id=b.catid
 		where b.postnum>0 And b.ischeck>0 $addquery order by b.{$orderby} desc limit $limitnum";
 		$this->dsql->SetQuery($query);
 		$this->dsql->Execute();

 		//统计记录数
 		if($this->TotalResult==-1 && $getcontent==-1)
 		{
 		  $row = $this->dsql->GetOne("SELECT FOUND_ROWS() as dd ");
		  $this->TotalResult = $row['dd'];
		  $this->TotalPage = ceil($this->TotalResult/$this->PageSize);
		}

 		$ndtp = new DedeTagParse();
 		$ndtp->SetNameSpace("field","[","]");
 		$GLOBALS['autoindex'] = 0;
 		while($row = $this->dsql->GetArray())
 		{
 			$GLOBALS['autoindex']++;
 			$row['title'] = $row['bookname'];
 			$ndtp->LoadString($innertext);

 			//获得图书最新的一个更新章节
 			$row['contenttitle'] = '';
 			$row['contentid'] = '';
 			if($getcontent==1){
 				$nrow = $this->GetNewContent($row['id']);
 				$row['contenttitle'] = $nrow['title'];
 			  $row['contentid'] = $nrow['id'];
 			  //echo "{$row['contenttitle']} 0 {$row['contentid']}";
 			}

 			if($row['booktype']==1) $row['contenturl'] = $cfg_cmspath.'/book/story.php?id='.$row['id'];
 			else $row['contenturl'] = $cfg_cmspath.'/book/show-photo.php?id='.$row['id'];

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
 	//------------------
 	//获得指定条件的内容
 	//-------------------
 	function GetNewContent($bid)
  {
 		$row = $this->dsql->GetOne("Select id,title,chapterid From #@__story_content where bookid='$bid' order by id desc ");
 		return $row;
 	}
 	//------------------
 	//获得指定条件的内容
 	//-------------------
 	function GetNewContentLink($bid,$innertext='')
  {
 		global $cfg_cmspath;
 		$rstr = '';
 		$row = $this->GetNewContent($bid);
 		if(!is_array($row)) return '';
 		if(empty($innertext)) $innertext = "<a href='[field:url/]'>[field:title/]</a>";
 		if($this->Fields['booktype']==1){
 			$burl = $cfg_cmspath.'/book/show-photo.php?id='.$row['id'];
 		}else{
 				$burl = $cfg_cmspath.'/book/story.php?id='.$rowch['id'];
 		}
 		$rstr = preg_replace("/\[field:url([\s]{0,})\/\]/isU",$burl,$innertext);
 		//$rstr = preg_replace("/\[field:ch([\s]{0,})\/\]/isU",$row['chapterid'],$rstr);
 		$rstr = preg_replace("/\[field:title([\s]{0,})\/\]/isU",$row['title'],$rstr);
 		return $rstr;
 	}
 	//------------------
 	//获得章节列表
 	//-------------------
 	function GetChapterList($bookid,$innertext)
 	{
 		global $cfg_cmspath;
 		$clist = '';
 		$this->dsql->SetQuery("Select id,chaptername,chapnum From #@__story_chapter where bookid='{$bookid}' order by chapnum asc ");
 		$this->dsql->Execute();
 		$ndtp = new DedeTagParse();
 		$ndtp->SetNameSpace("in","{","}");
 		$ch = 0;
 		while($row = $this->dsql->GetArray())
 		{
 			$ch++;
 			$ndtp->LoadString($innertext);
 		if(is_array($ndtp->CTags))
 		{
 			foreach($ndtp->CTags as $tagid=>$ctag)
 			{
 				$tagname = $ctag->GetTagName();
 				//field类型
 				if($tagname=='field')
 				{
 					if(isset($row[$ctag->GetAtt('name')])) $ndtp->Assign($tagid,$row[$ctag->GetAtt('name')]);
 					else $ndtp->Assign($tagid,'');
 				}
 				//内容列表
 				else if($tagname=='content')
 				{
 					$this->dsql->SetQuery("Select id,title,sortid From #@__story_content where chapterid='{$row['id']}' order by sortid asc");
 					$this->dsql->Execute('ch');
 					$ct = 0;
 					$nlist = '';
 					while($rowch = $this->dsql->GetArray('ch'))
 					{
 						$ct++;
 						if($this->Fields['booktype']==1){
 							$rowch['url'] = $cfg_cmspath.'/book/show-photo.php?id='.$rowch['id'];
 							//$rowch['title'] = "";
 						}else{
 							$rowch['url'] = $cfg_cmspath.'/book/story.php?id='.$rowch['id'];
 						}
 						$rbtext = preg_replace("/\[field:url([\s]{0,})\/\]/isU",$rowch['url'],$ctag->GetInnerText());
 						$rbtext = preg_replace("/\[field:ch([\s]{0,})\/\]/isU",$rowch['sortid'],$rbtext);
 						$rbtext = preg_replace("/\[field:title([\s]{0,})\/\]/isU",$rowch['title'],$rbtext);
 						$nlist .= $rbtext;
 					}
 					$ndtp->Assign($tagid,$nlist);
 				}
 			}//End foreach
 			 $clist .= $ndtp->GetResult();
 			}
 		}
 		return $clist;
 	}

 	//------------------
 	//获得栏目列表
 	//-------------------
 	function GetCatalogList($pid,$innertext)
 	{
 		global $cfg_cmspath;
 		$clist = '';
 		$this->dsql->SetQuery("Select id,pid,classname From #@__story_catalog where pid='{$pid}' order by rank asc ");
 		$this->dsql->Execute();
 		$ndtp = new DedeTagParse();
 		$ndtp->SetNameSpace("in","{","}");
 		$ch = 0;
 		if(trim($innertext)==""){
 			if($pid==0) $innertext = GetSysTemplets('book_catalog.htm');
 			else $innertext = GetSysTemplets('book_catalog_son.htm');
 		}
 		while($row = $this->dsql->GetArray())
 		{
 			$ch++;
 			$ndtp->LoadString($innertext);
 			$row['url'] = $cfg_cmspath.'/book/list.php?id='.$row['id'];
 		if(is_array($ndtp->CTags))
 		{
 			foreach($ndtp->CTags as $tagid=>$ctag)
 			{
 				$tagname = $ctag->GetTagName();
 				//field类型
 				if($tagname=='field')
 				{
 					if(isset($row[$ctag->GetAtt('name')])) $ndtp->Assign($tagid,$row[$ctag->GetAtt('name')]);
 					else $ndtp->Assign($tagid,'');
 				}
 				//内容列表
 				else if($tagname=='sonlist')
 				{
 					$this->dsql->SetQuery("Select id,pid,classname From #@__story_catalog where pid='{$row['id']}' order by rank asc");
 					$this->dsql->Execute('ch');
 					$ct = 0;
 					$nlist = '';
 					while($rowch = $this->dsql->GetArray('ch'))
 					{
 						$ct++;
 						$rowch['url'] = $cfg_cmspath.'/book/list.php?id='.$rowch['id'];
 						$rbtext = preg_replace("/\[field:url([\s]{0,})\/\]/isU",$rowch['url'],$ctag->GetInnerText());
 						$rbtext = preg_replace("/\[field:id([\s]{0,})\/\]/isU",$rowch['id'],$rbtext);
 						$rbtext = preg_replace("/\[field:classname([\s]{0,})\/\]/isU",$rowch['classname'],$rbtext);
 						$nlist .= $rbtext;
 					}
 					$ndtp->Assign($tagid,$nlist);
 				}
 			}//End foreach
 			 $clist .= $ndtp->GetResult();
 			}
 		}
 		return $clist;
 	}

 	//-----------------
 	//获取栏目导航
 	//--------------
 	function GetPosition($catid,$bcatid)
 	{
 		global $cfg_cmspath,$cfg_list_symbol;
 		$oklink = '';
 		$this->dsql->SetQuery("Select id,classname From #@__story_catalog where id='$catid' Or id='$bcatid' order by pid asc ");
 		$this->dsql->Execute();
 		$row = $this->dsql->GetArray();
 		if(is_array($row)) $oklink  = "<a href='{$cfg_cmspath}/book/list.php?id={$row['id']}'>{$row['classname']}</a>";
 		$row = $this->dsql->GetArray();
 		if(is_array($row)) $oklink  .= " ".trim($cfg_list_symbol)." {$row['classname']}";
 		return $oklink;
 	}

 	//---------------------------
 	//关闭相关资源
 	//---------------------------
 	function Close()
 	{
 		@$this->dsql->Close();
 		unset($this->dtp);
 	}


 	//-------------------
 	//获取上一页连接
 	//-------------------
 	function GetPreNext($gtype)
 	{
 		if(count($this->PreNext)==0)
 		{
 			$chapnum = $this->Fields['chapnum'];
 			//获得上一条记录
 			$row = $this->dsql->GetOne("Select id,title,sortid From #@__story_content where bookid={$this->Fields['bookid']} And chapterid={$this->Fields['chapterid']} And sortid<{$this->Fields['sortid']} order by sortid desc ");
 			if(!is_array($row)){
 				$row = $this->dsql->GetOne("Select id From #@__story_chapter where bookid={$this->Fields['bookid']} And chapnum<$chapnum order by chapnum desc ");
 				if(is_array($row)){
 				  $row = $this->dsql->GetOne("Select id,title,sortid From #@__story_content where bookid={$this->Fields['bookid']} And chapterid='{$row['id']}' order by sortid desc ");
 				}
 			}
 			if(!is_array($row)){
 				$this->PreNext['pre']['id']=0;
 				$this->PreNext['pre']['link']="javascript:alert('刚开始哦');";
 				$this->PreNext['pre']['title']='这是第一页';
 			}else{
 				$this->PreNext['pre']['id']=$row['id'];
 				$this->PreNext['pre']['title']=$row['title'];
 				if($this->Fields['booktype']==1){
 					$this->PreNext['pre']['link']="show-photo.php?id=".$row['id'];
 					$this->PreNext['pre']['title'] = "上一页";
 				}
 				else  $this->PreNext['pre']['link']="story.php?id=".$row['id'];
 			}
 			//获得下一条记录
 			$row = $this->dsql->GetOne("Select id,title,sortid From #@__story_content where bookid={$this->Fields['bookid']} And chapterid={$this->Fields['chapterid']} And sortid>{$this->Fields['sortid']} order by sortid asc ");
 			if(!is_array($row)){
 				$row = $this->dsql->GetOne("Select id From #@__story_chapter where bookid={$this->Fields['bookid']} And chapnum>$chapnum order by chapnum asc ");
 				if(is_array($row)){
 				  $row = $this->dsql->GetOne("Select id,title,sortid From #@__story_content where bookid={$this->Fields['bookid']} And chapterid={$row['id']} order by sortid  asc ");
 			  }
 			}
 			if(!is_array($row)){
 				$this->PreNext['next']['id']=0;
 				$this->PreNext['next']['link']="javascript:alert('没有了哦');";
 				$this->PreNext['next']['title']='这是最后一页';
 			}else{
 				$this->PreNext['next']['id']=$row['id'];
 				$this->PreNext['next']['title']=$row['title'];
 				if($this->Fields['booktype']==1){
 					$this->PreNext['next']['link']="show-photo.php?id=".$row['id'];
 					$this->PreNext['next']['title'] = "下一页";
 				}
 				else  $this->PreNext['next']['link']="story.php?id=".$row['id'];
 			}
 		}

 		if($gtype=='prelink') return "<a href='{$this->PreNext['pre']['link']}'>$this->PreNext['pre']['title']</a>";
 		else if($gtype=='nextlink') return "<a href='{$this->PreNext['next']['link']}'>$this->PreNext['next']['title']</a>";
 		else if($gtype=='preurl') return $this->PreNext['pre']['link'];
 		else if($gtype=='nexturl') return $this->PreNext['next']['link'];
 		else return "";
 	}

 	//---------------------------------
  //获取动态的分页列表
  //---------------------------------
	function GetPageListDM($list_len,$listitem="index,end,pre,next,pageno")
	{
		$prepage="";
		$nextpage="";
		$prepagenum = $this->PageNo-1;
		$nextpagenum = $this->PageNo+1;
		if($list_len==""||ereg("[^0-9]",$list_len)) $list_len=3;
		$totalpage = ceil($this->TotalResult/$this->PageSize);

		//页面小于或等于一
		if($totalpage<=1 && $this->TotalResult>0) return "共1页/".$this->TotalResult."条记录";
		if($this->TotalResult == 0) return "共0页/".$this->TotalResult."条记录";

		$maininfo = "<dd><span>共{$totalpage}页/".$this->TotalResult."条记录</span></dd>";

		$purl = $this->GetCurUrl();
		$geturl = "id=".$this->CatalogID."&keyword=".$this->Keys['keyword']."&author=".$this->Keys['author']."&";
		$hidenform = "<input type='hidden' name='id' value='".$this->CatalogID."'>\r\n";
		$hidenform .= "<input type='hidden' name='keyword' value='".$this->Keys['keyword']."'>\r\n";
		$hidenform .= "<input type='hidden' name='author' value='".$this->Keys['author']."'>\r\n";

		$purl .= "?".$geturl;

		//获得上一页和下一页的链接
		if($this->PageNo != 1){
			$prepage.="<dd><a href='".$purl."PageNo=$prepagenum'>上一页</a></dd>\r\n";
			$indexpage="<dd><a href='".$purl."PageNo=1'>首页</a></dd>\r\n";
		}
		else{
			$indexpage="";
		}

		if($this->PageNo!=$totalpage && $totalpage>1){
			$nextpage.="<dd><a href='".$purl."PageNo=$nextpagenum'>下一页</a></dd>\r\n";
			$endpage="<dd><a href='".$purl."PageNo=$totalpage'>末页</a></dd>\r\n";
		}
		else{
			$endpage="";
		}
		//获得数字链接
		$listdd="";
		$total_list = $list_len * 2 + 1;
		if($this->PageNo >= $total_list) {
    		$j = $this->PageNo-$list_len;
    		$total_list = $this->PageNo+$list_len;
    		if($total_list>$totalpage) $total_list=$totalpage;
		}else{
   			$j=1;
   			if($total_list>$totalpage) $total_list=$totalpage;
		}
		for($j;$j<=$total_list;$j++){
   		if($j==$this->PageNo) $listdd.= "<dd><span>$j</span></dd>\r\n";
   		else $listdd.="<dd><a href='".$purl."PageNo=$j'>[".$j."]</a></dd>\r\n";
		}
		$plist = "<form name='pagelist' action='".$this->GetCurUrl()."'>$hidenform";
		$plist .=  "<dl id='dedePageList'>\r\n";
		$plist .= $maininfo.$indexpage.$prepage.$listdd.$nextpage.$endpage;
		if($totalpage>$total_list){
			$plist.="<dd><input type='text' name='PageNo' style='width:30px;height:18px' value='".$this->PageNo."'></dd>\r\n";
			$plist.="<dd><input type='submit' name='plistgo' value='GO' style='width:24px;height:18px;font-size:9pt'></dd>\r\n";
		}
		$plist .= "</dl>\r\n</form>\r\n";
		return $plist;
	}

 	//---------------
  //获得当前的页面文件的url
  //----------------
  function GetCurUrl()
	{
		if(!empty($_SERVER["REQUEST_URI"])){
			$nowurl = $_SERVER["REQUEST_URI"];
			$nowurls = explode("?",$nowurl);
			$nowurl = $nowurls[0];
		}else{ $nowurl = $_SERVER["PHP_SELF"]; }
		return $nowurl;
	}


}//End Class
?>