<?
require_once("config_base.php");
require_once("inc_dedetag.php");
/*
这个类用于解析和创建文章列表
*/
class MakeListCode
{
	var $con;
	var $baseDir;
	var $artDir;
	var $webName;
	var $typeID;
	var $imgDdir;
	var $typeDir;
	var $listStep=0;
	var $titleInfos;
	var $valuePosition="";
	var $valueTitle="";
	var $movePage=1;
	var $pageUrl;
	var $totalPage=0;
	var $totalResult;
	var $pageSize=20;
	var $sunID="";
	var $shortName=".htm";
	var $modDir="";
	var $typeName="";
	var $nowPage=1;
	var $totalRecord=0;
	function MakeListCode()
	{
        $this->SetGlobal();
    }
	function SetGlobal()
	{
		global $base_dir;
		global $art_dir;
		global $art_shortname;
		global $mod_dir;
		$this->con = connectMySql();
		$this->baseDir = $base_dir;
		$this->artDir = $art_dir;
		$this->shortName = $art_shortname;
		$this->modDir = $mod_dir;
	}
    // 设置要解析的类目ID,并重置相关成员
    function SetType($typeid,$npage=1,$nrecord=0)
    {
        $this->typeID=$typeid;
		$this->listStep=0;
		$this->titleInfos="";
		$this->valuePosition="";
		$this->valueTitle="";
		$this->movePage=1;
		$this->pageUrl="list.php?id=$typeid";
		$this->totalPage="";
		$this->totalResult="";
		$this->pageSize=20;
		$this->sunID="";
        $rs = mysql_query("select * from dede_arttype where ID=$typeid",$this->con);
        $row = mysql_fetch_object($rs);
        $this->modPage = $this->baseDir.$this->modDir."/".$row->modname."/".$row->channeltype."/列表.htm";
        $this->typeDir = $row->typedir;
        $this->typeName = $row->typename;
        $this->nowPage = $npage;
        $this->totalRecord=$nrecord;
     }
    //
    //---获得文章网址----------
    //
	function GetFileName($ID,$typedir,$stime,$rank=0)
	{
		global $art_nametag;
		global $art_shortname;
		global $art_php_dir;
		if($rank>0) return $art_php_dir."/viewart.php?ID=$ID";
		if($art_nametag=="maketime")
		{
			$ds = split("-",$stime);
			return $this->artDir."/".$ds[0]."/".$ds[1].$ds[2]."/".$ID.$art_shortname;
		}
		else
			return $this->artDir."/".$typedir."/".$ID.$art_shortname;
	}
    //
	//创建列表
    //
	function Display()
	{
		global $tag_start_char;
		global $tag_end_char;
		$mod = "";
		$modpage = $this->modPage;
		//初始化pageUrl和totalResult
		$orwhere = $this->GetSunID($this->typeID);
		if($this->totalRecord==""||$this->totalRecord==0)
		{
			$query = "Select dede_art.ID From dede_art left join dede_membertype on dede_art.rank=dede_membertype.rank where dede_art.rank>=0 And $orwhere";
			$rs = mysql_query($query,$this->con);
			$this->totalResult = mysql_num_rows($rs);
			$this->totalRecord = $this->totalResult;
		}
		else
			$this->totalResult=$this->totalRecord;
		//读取模板---------------------------------
		$CDTagParse = new DedeTagParse();
		$CDTagParse->TagStartWord = $tag_start_char;
		$CDTagParse->TagEndWord = $tag_end_char;
		$CDTagParse->LoadTemplate($modpage);
		$tagCount = $CDTagParse->Count;
		//获得用户自定义的布面大小
		$pageTag = $CDTagParse->GetTag("page");
		$userPageSize = 0;
		if($pageTag!="") $userPageSize=trim($pageTag->GetAtt("pagesize"));
		if($userPageSize!=""&&$userPageSize!=0) $this->pageSize=$userPageSize;
		//计算总页数
		$this->totalPage = ceil($this->totalResult/$this->pageSize);
		if($this->totalPage==0) $this->totalPage=1;
		//创建列表-----------------------------------------
		$this->movePage = $this->nowPage;
		//--创建部分--------------
		for($tagID=0;$tagID<=$tagCount;$tagID++)
		{
			$this->TagToValue($CDTagParse,$CDTagParse->CTags[$tagID],$tagID);
		}
		echo $CDTagParse->GetResult();
		unset($CDTagParse);
	    //------------------------------------
    }
	//整合指定的标签与对应的代码
	function TagToValue(&$TagPar,&$mtag,$tagID)
	{
		switch($mtag->TagName){
		case "page":
			$TagPar->ReplaceTag($tagID,"");
			break;
		case "field":
			if($mtag->GetAtt("name")=="title")
				$TagPar->ReplaceTag($tagID,$this->GetTitle());
			if($mtag->GetAtt("name")=="position")
				$TagPar->ReplaceTag($tagID,$this->GetPosition());
			if($mtag->GetAtt("name")=="typename")
				$TagPar->ReplaceTag($tagID,$this->typeName);
			break;
		case "hotart":
			$TagPar->ReplaceTag(
				$tagID,
				$this->GetHot(
					$mtag->GetAtt("titleLength"),
			  		$mtag->GetAtt("line"),
			  		$mtag->InnerText)
			);
			break;
		case "coolart":
			$TagPar->ReplaceTag(
				$tagID,
				$this->GetCommend(
					$mtag->GetAtt("titleLength"),
			  		$mtag->GetAtt("line"),
			  		$mtag->InnerText)
			  	);
			break;
		case "channel":
			$TagPar->ReplaceTag(
				$tagID,
				$this->GetChannel($mtag->GetAtt("type"),$mtag->InnerText)
			);
			break;
		case "list":
			if($mtag->GetAtt("type")=="full")
				$TagPar->ReplaceTag
				(
					$tagID,
					$this->GetListText($mtag->GetAtt("titleLength"),
					$mtag->GetAtt("infoLength"),
					$mtag->InnerText)
				);
			else if($mtag->GetAtt("type")=="small")
				$TagPar->ReplaceTag
				(
					$tagID,$this->GetList($mtag->GetAtt("titleLength"),
					$mtag->InnerText)
				);
			else if($mtag->GetAtt("type")=="pagelist")
			{
				$TagPar->ReplaceTag(
					$tagID,
					$this->GetPageList($mtag->GetAtt("size"))
				);
			}
			else if($mtag->GetAtt("type")=="imglist")
			{
				$TagPar->ReplaceTag
				(
					$tagID,
					$this->GetImgList(
						$mtag->GetAtt("titleLength"),
						$mtag->GetAtt("infoLength"),
						$mtag->InnerText)
				);
			}
			else if($mtag->GetAtt("type")=="multiimglist")
			{
				$TagPar->ReplaceTag
				(
					$tagID,
					$this->GetMultiImgList(
						$mtag->GetAtt("titleLength"),
						$mtag->GetAtt("imgwidth"),
						$mtag->GetAtt("imgheight"),
						$mtag->GetAtt("row"),
						$mtag->GetAtt("col"),
						$mtag->GetAtt("hastitle"),
						$mtag->InnerText)
				);
			}
			else if($mtag->GetAtt("type")=="soft")
			{
				$TagPar->ReplaceTag
				(
					$tagID,
					$this->GetSoftList(
						$mtag->GetAtt("titleLength"),
						$mtag->GetAtt("infoLength"),
						$mtag->InnerText)
				);
			}
			break;
			//list样式
		case "rss":
			$TagPar->ReplaceTag($tagID,$this->GetRssLink());
			break;
		}//End Switch
	}
	//返回标题
	function GetTitle()
	{
        if($this->valueTitle=="")
        	$this->ParPosition($this->typeID);
		return $this->valueTitle;
	}
	//返回当前位置
	function GetPosition()
	{
        if($this->valuePosition=="")
        	$this->ParPosition($this->typeID);
		return $this->valuePosition;
	}
	//--获得推荐和专题文章列表-----------
	function GetCommend($w=24,$h=10,$innertext="")
	{
		//设置默认参数
		if($w=="") $w=24;
		if($h=="") $h=10;
		/////////////////////
        $commendlist = $this->ParShortList("commend",$w,$h,$innertext);
		return $commendlist;
	}
    //--获得热门文章列表-----------
	function GetHot($w=24,$h=10,$innertext="")
	{
		//设置默认参数
		if($w=="") $w=24;
		if($h=="") $h=10;
		/////////////////////
        $hlist = $this->ParShortList("hot",$w,$h,$innertext);
		return $hlist;
	}
    //
    //--获得热门或推荐文章列表,逻辑部分-----------
    //
	function ParShortList($sorttype="hot",$w=24,$h=10,$innertext="")
	{
		//设置默认参数
        if($sorttype=="hot") $sorttype="hot";
        if($w=="") $w=24;
		if($h=="") $h=10;
        if($innertext=="")
        	$innertext="·<a href='~filename~'>~title~</a><br>\r\n";
        $slist = "";
		/////////////////////
        $textLinkSql="Select dede_art.ID,dede_art.title,dede_art.stime,dede_art.ismake,dede_art.click,dede_art.rank,dede_arttype.typedir From dede_art left join dede_arttype on dede_art.typeid=dede_arttype.ID ";
		$orwhere = $this->GetSunID($this->typeID);
		$artlist = "";
        if($sorttype=="hot")
        	$wheresql = " where dede_art.rank>=0 And $orwhere order by dede_art.click desc";
        else
            $wheresql = " where dede_art.rank>=0 And $orwhere And dede_art.redtitle>0 order by dede_art.ID desc";
        $rs = mysql_query($textLinkSql.$wheresql." limit 0,$h",$this->con);
		if(mysql_num_rows($rs)==0) $slist ="无内容！";
		while($row = mysql_fetch_object($rs))
		{
            $filename = $this->GetFileName($row->ID,$row->typedir,$row->stime,$row->rank);
            $ID = $row->ID;
            $stime = $row->stime;
            $title = cn_substr($row->title,$w);
            $click = $row->click;
            $bodys = split("~",$innertext);
			$bn = count($bodys);
            for($i=0;$i<$bn;$i++)
			{
				if($i%2==1)
				{
            	  if(isset(${$bodys[$i]})) $slist.=${$bodys[$i]};
            	}
				else $slist.=$bodys[$i];
			}
		}
		return $slist;
	}
	//
	//--获得与该类相关的类目------
	//$typetype 的值为： sun 下级分类 self 同级分类 top 顶级分类
	//
	function GetChannel($typetype="sun",$innertext="")
	{
		if($innertext=="") $innertext="·<a href='~typelink~'>~typename~</a><br>\r\n";
		if($typetype=="") $typetype="sun";
		$likeType = "";
		$bodys = split("~",$innertext);
		$bn = count($bodys);
		if($typetype=="self")
		{
			$rs = mysql_query("Select reID From dede_arttype where ID=".$this->typeID,$this->con);
			$row = mysql_fetch_object($rs);
			$reID = $row->reID;
			if($reID==0) return "";
			$rs = mysql_query("Select * From dede_arttype where reID=$reID And ID<>".$this->typeID,$this->con);
			while($row=mysql_fetch_object($rs))
			{
				$typelink = "list.php?id=".$row->ID;
				$typename = $row->typename;
				for($i=0;$i<$bn;$i++)
				{
					if($i%2==1)
					{
                    	if(isset(${$bodys[$i]})) $likeType.=${$bodys[$i]};
                	}
					else
                   	$likeType.=$bodys[$i];
				}
			}
		}
		else
		{
			if($typetype=="top") $reID=0;
			else $reID=$this->typeID;
			$rs = mysql_query("Select * From dede_arttype where reID=$reID",$this->con);
			while($row=mysql_fetch_object($rs))
			{
				$typelink = "list.php?id=".$row->ID;
				$typename = $row->typename;
				for($i=0;$i<$bn;$i++)
				{
					if($i%2==1)
					{
                    	if(isset(${$bodys[$i]})) $likeType.=${$bodys[$i]};
                	}
					else
                   		$likeType.=$bodys[$i];
				}
			}
		}
		return $likeType;
	}
	//--获得每页列表内容--------------
	function GetListText($titlelen=50,$infolen=300,$innertext="")
	{
       //member,title,filename,fulltitle
       //ID,time,click,shortinfo,picname
       $dededfimg = $this->modDir."/defdd.gif";
       if($infolen=="") $infolen=300;
       if($titlelen=="") $titlelen=50;
       if($innertext=="") $innertext=$this->GetLowMod("list_fulllist.htm");
        ///////////////////////////
        $page = $this->movePage;
		$pageSize = $this->pageSize;
		$startpos = ($page-1)*$pageSize;
		$orwhere = $this->GetSunID($this->typeID);
		$nexttype = "";
		$list = "	<table width='98%' border='0' cellspacing='0' cellpadding='0'>
		<tr height='2'><td></td></tr>
		";
		$query = "Select dede_art.ID,dede_art.title,dede_art.stime,dede_art.msg,dede_art.picname,dede_art.isdd,dede_art.typeid,dede_art.redtitle,dede_art.rank,dede_art.ismake,dede_art.click,dede_membertype.membername From dede_art left join dede_membertype on dede_art.rank=dede_membertype.rank where dede_art.rank>=0 And $orwhere order by dede_art.ID desc  limit $startpos,$pageSize";
		$rs = mysql_query($query,$this->con);
		if(mysql_num_rows($rs)==0) $list.="<tr><td colspan='4'>该分类暂时没有任何文章!</td></tr>";
		else
		{
			while($row=mysql_fetch_object($rs))
			{
				$spec = "";
				$tstyle = "";
				$pic = "";
				$typedir = $this->GetTypeDir($row->typeid);
				if($row->typeid!=$this->typeID) $nexttype=$this->GetArtTypeLink($row->typeid);
				if($row->isdd>0&&$row->redtitle!=2) $pic="(图文)";
				if($row->redtitle>=1) $tstyle=" style='color:red'";
				if($row->redtitle==2) $spec=" [专题]";
                //以下变量提供给二重模板使用
                $member = "";
                if($row->rank>0) $member=$row->membername;
                $title = cn_substr($row->title,$titlelen);
                $filename = $this->GetFileName($row->ID,$typedir,$row->stime,$row->rank);
                $fulltitle = "$nexttype<a href='$filename'$tstyle>$title$pic$spec</a>";
                $ID = $row->ID;
                $stime = $row->stime;
                $click = $row->click;
                if($infolen>0)
                	$shortinfo = cn_substr($row->msg,$infolen);
                else
                	$shortinfo="";
                $picname = $row->picname;
                if($picname=="") $picname = $dededfimg;
                ////////////////////////////////
                $bodys = split("~",$innertext);
				$bn = count($bodys);
                $list.="<tr><td>\r\n";
                for($i=0;$i<$bn;$i++)
				{
					if($i%2==1)
					{
                    	if(isset(${$bodys[$i]})) $list.=${$bodys[$i]};
                    }
					else
                    	$list.=$bodys[$i];
				}
				$list.="</td></tr>\r\n";
			}

		}
		$list.="</table>\r\n";
		return $list;
	}
	//--获得每页列表内容，仅显示列表--------------
	function GetList($titlelen=50,$innertext="")
	{
       //member,title,filename,fulltitle
       //ID,time,click,picname
       if($titlelen==""||$titlelen=="0") $titlelen=50;
       if($innertext=="") $innertext=$this->GetLowMod("list_smalllist.htm");
	   return $this->GetListText($titlelen,0,$innertext);
	}
	//--获得每页列表内容，样式三，图片类--------------
	function GetImgList($titlelen=50,$infolen=300,$innertext="")
	{
		//设置空参数
		if($titlelen=="") $titlelen=50;
		if($infolen=="") $infolen=300;
		if($innertext=="") $innertext=$this->GetLowMod("list_imglist.htm");
        ///////////////////////////////////////
		return $this->GetListText($titlelen,$infolen,$innertext);
	}
	//
	//--获得软件列表
	//
	function GetSoftList($titlelen=50,$infolen=300,$innertext="")
	{
		//设置空参数
		if($titlelen=="") $titlelen=50;
		if($infolen=="") $infolen=300;
		if($innertext=="") $innertext=$this->GetLowMod("list_softlist.htm");
        ///////////////////////////////////////
		return $this->GetListText($titlelen,$infolen,$innertext);
	}
	//
	//--获得每页列表内容，样式三，多列图片展示形式--------------
	//innertext 可定制的字段
	//filename--链接的绝对网址，ID--图片的文章ID，img--缩略图
	//stime--发布日期，click--文章点击数
	//
	function GetMultiImgList($titlelen=24,$imgw=180,$imgh=180,$line=4,$vline=3,$istitle="yes",$innertext="")
	{
		if($titlelen=="") $titlelen=24;
		if($imgw=="") $imgw=180;
		if($imgh=="") $imgh=180;
		if($line==""||$line==0) $line=4;
		if($vline==""||$vline==0) $vline=3;
		if($istitle=="") $istitle="yes";
		if($innertext=="") $innertext=$this->GetLowMod("list_multiimglist.htm");
		//////////////////////////////////////////////
		$this->pageSize = $line*$vline;
		$pageSize = $this->pageSize;
		$this->totalPage = ceil($this->totalResult/$this->pageSize);
		$tdwidth = ceil(100/$vline)."%";
		if($this->totalPage==0) $this->totalPage=1;
		$page = $this->movePage;
		$startpos = ($page-1)*$pageSize;
		$orwhere = $this->GetSunID($this->typeID);
		$relist = "";
		$pictable = "<table width=$imgw height=$imgh border=0 cellpadding=0 cellspacing=1 bgcolor=#6F7269><tr><td bgcolor=#FFFFFF align=center>没缩略图</td></tr></table>\r\n";
		$query = "Select dede_art.ID,dede_art.title,dede_art.stime,dede_art.isdd,dede_art.typeid,dede_art.redtitle,dede_art.rank,dede_art.ismake,dede_art.click,dede_art.picname From dede_art left join dede_membertype on dede_art.rank=dede_membertype.rank where dede_art.rank>=0 And $orwhere order by dede_art.ID desc  limit $startpos,$pageSize";
		$rs = mysql_query($query,$this->con);
		if(mysql_num_rows($rs)==0) $relist.="该分类暂时没有任何文章!";
		else
		{
			$relist.="<table width='100%' border='0' cellpadding='0' cellspacing='2'>";
			for($i=1;$i<=$line;$i++)
			{
                $relist .= "<tr>";
				for($j=1;$j<=$vline;$j++)
				{
					if($row = @mysql_fetch_object($rs))
					{
						$typedir = $this->GetTypeDir($row->typeid);
						///以下是可以在二重模板中使用的变量
						$title = cn_substr($row->title,$titlelen);
						$ID = $row->ID;
						$filename = $this->GetFileName($ID,$typedir,$row->stime,$row->rank);
						$stime = $row->stime;
						$click = $row->click;
				        //////////////////////////////
						if($istitle=="yes")
						{
							$titleline = "
						<tr align='center'>
							<td>
							<a href='$filename'><u>$title</u></a>
							</td>
						</tr>";
						}
						else
						{
							$titleline="";
						}
						$picurl = $row->picname;
						$picfile = $this->baseDir.$picurl;
						//img在二重模板中使用
						if($picurl==""||!file_exists($picfile)) $picurl=$this->modDir."/defdd.gif";
						$img = "<img src='$picurl' border='0' width='$imgw' height='$imgh'>";
						$pictable = "";
						$bodys = split("~",$innertext);
						$bn = count($bodys);
						for($k=0;$k<$bn;$k++)
						{
							if($k%2==1)
							{
								if(isset(${$bodys[$k]})) $pictable.=${$bodys[$k]};
							}
							else $pictable.=$bodys[$k];
						}
					$relist.="<td bgcolor='#FFFFFF' width='$tdwidth'>
                    <table width='90%' border='0' cellpadding='0' cellspacing='0'>
                      <tr align='center'>
                       <td>
                       $pictable
                       </td>
                     </tr>
                     $titleline
                   	</table>
                  	</td>\r\n";
			     	}
			     	else
			     	{
			     		$relist.="<td bgcolor='#FFFFFF' width='$tdwidth'>
                    	<table width='90%' border='0' cellpadding='0' cellspacing='0'>
                      	<tr align='center'><td>&nbsp;</td></tr><tr><td></td></tr>
                   		</table>
                  		</td>\r\n";
			     	}
			     	//-----如果已经结束记录---------------
		     	 }
		     //----结束一行的列循环----------------------
		     $relist.="</tr>";
		   }
		   //--结束行循环--------------------
		   $relist.="</table>\r\n";
		}
		//--End Else----------------------
		return $relist;
	}
    //
	//--获得分页列表--------------
    //
	function GetPageList($listLen)
	{
		if($listLen=="") $listLen=3;
		$pageurl = $this->pageUrl;
		$total_page = $this->totalPage;
		$page = $this->movePage;
		$pageList="共".$page."/".$total_page."页 ";
		$prepage = $page-1;
		$nextpage = $page+1;
		if($total_page!=0&&$page!=1) $pageList.="<a href='".$pageurl."&page=1&totalrecord=".$this->totalRecord."'>首页</a> ";
		if($prepage!=0) $pageList.="<a href='".$pageurl."&page=".$prepage."&totalrecord=".$this->totalRecord."'>上页</a> ";
		if(($page-$listLen)>0)
		{
        	if($total_page>=($page+$listLen))
        	{$i=$page-$listLen;$endpos=$page+$listLen+1;}
        	else
        	{$i=$total_page-($listLen*2);$endpos=$total_page;}
        }
        else
        {$i=1;$endpos=$listLen*2;}
        if($i<=0) $i=1;
        for(;$i<$endpos;$i++)
        {
             if($i>$total_page) break;
	     	 if($i!=$page) $pageList.="<a href='".$pageurl."&page=".$i."&totalrecord=".$this->totalRecord."'>[".$i."]</a> ";
	     	 else $pageList.=$i." ";
		}
		if($nextpage<=$total_page) $pageList.="<a href='".$pageurl."&page=".$nextpage."&totalrecord=".$this->totalRecord."'>下页</a> ";
		if($page!=$total_page&&$total_page!=0) $pageList.="<a href='".$pageurl."&page=".$total_page."&totalrecord=".$this->totalRecord."'>未页</a> ";
		return $pageList;
	}
    //
	//文章的所属频道
    //
	function GetArtTypeLink($typeid)
	{
		$rs = mysql_query("Select typename,typedir,isdefault from dede_arttype where ID=$typeid",$this->con);
		$row = mysql_fetch_object($rs);
		$link = "<a href='list.php?id=$typeid'><u>[".$row->typename."]</u></a> ";
		return $link;
	}
	//获得某ID的下级ID(包括本身)的SQL语句“(dede_art.typeid=id1 or dede_art.typeid=id2...)”
	function GetSunID($ID)
	{
		$this->sunID = "";
		$this->ParSunID($ID);
		return "(dede_art.typeid=$ID".$this->sunID.")";
	}
	function ParSunID($ID)
	{
		$rs = mysql_query("Select ID From dede_arttype where reID=$ID",$this->con);
		if(mysql_num_rows($rs)>0)
		{
			while($row=mysql_fetch_object($rs))
			{
				$NID = $row->ID;
				$this->sunID.=" or dede_art.typeid=$NID";
				$this->ParSunID($NID);
			}
		}
	}
	//获取某类目的下级类目列表,以 ` 分开的字符串形式返回
	function GetSunIDS($ID)
	{
		$this->sunID = "";
		$this->ParSunID2($ID);
		return $this->sunID;
	}
	function ParSunID2($ID)
	{
		$rs = mysql_query("Select ID From dede_arttype where reID=$ID",$this->con);
		if(mysql_num_rows($rs)>0)
		{
			while($row=mysql_fetch_object($rs))
			{
				$NID = $row->ID;
				$this->sunID.="$NID`";
				$this->ParSunID2($NID);
			}
		}
	}
    //
    //  GetPosition 的逻辑部分
    //
    function ParPosition($ID)
	{
		$rs = mysql_query("Select * from dede_arttype where ID=".$ID,$this->con);
		$row = mysql_fetch_object($rs);
		if($row->reID!=0)
		{
			$this->titleInfos[$this->listStep]=$row->ID."`".$row->typename;
			$this->listStep++;
			$this->ParPosition($row->reID);
		}
		else
		{
			$fpath = $this->artDir;
			$this->titleInfos[$this->listStep]=$row->ID."`".$row->typename;
			$position = "<a href='/'>首页</a>&gt;&gt;";
			$title = $this->webName."-";
			for($this->listStep;$this->listStep>=0;$this->listStep--)
			{
			    list($tid,$tname) = split("`",$this->titleInfos[$this->listStep]);
				$position.="<a href='list.php?id=$tid'><b>$tname</b></a>&gt;&gt;";
				$title.= $tname."/";
			}
			//$position.="所有文章";
			//$title.="所有文章";
			$this->listStep = 0;
			$this->titleInfos = "";
			$this->valuePosition=$position;
			$this->valueTitle=$title;
		}
	}
	//-----获得指定的type 的Dir--------
	function GetTypeDir($tid)
	{
		$rs = mysql_query("Select typedir from dede_arttype where ID=$tid",$this->con);
		$row = mysql_fetch_array($rs);
		return $row[0];
	}
	//
	//获得低层模板
	//
	function GetLowMod($filename)
	{
		$restr = "";
		$filename=$this->baseDir.$this->modDir."/低层模板/".$filename;
		if(file_exists($filename))
		{
			$fp = fopen($filename,"r");
			$restr = fread($fp,filesize($filename));
			fclose($fp);
		}
		return $restr;
	}
	//
	//检测目录是否存在，如果不存在则创建
	//
	function CheckTypeDir($tdir)
	{
		global $dir_purview;
		$dirs = split("/",$tdir);
		$ds = count($dirs);
		$ndir = "";
		for($i=0;$i<$ds;$i++)
		{
			$ndir .= "/".$dirs[$i];
			if(!is_dir($this->baseDir.$ndir))
				@mkdir($this->baseDir.$ndir,$dir_purview)||die("无法创建文件夹： ".$this->baseDir.$ndir);
		}
	}
	//
	//获得一个rss链接
	//
	function GetRssLink()
	{
		global $art_php_dir;
		return $art_php_dir."/rss.php?typeid=".$this->typeID;
	}
}
?>