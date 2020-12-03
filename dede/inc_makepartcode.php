<?
require_once("config_base.php");
require_once("inc_dedetag.php");
require_once("inc_typelink.php");
/*
这个类用于解析和创建板块模板
*/
class MakePartCode
{
	var $con;
	var $baseDir="";
	var $artDir="";
	var $sunID="";
	var $shortName=".htm";
	var $modDir="";
	var $typeLink="";
	var $typeID=0;
	function MakePartCode()
	{
		global $base_dir;
		global $art_dir;
		global $mod_dir;
		global $art_shortname;
		$this->con = connectMySql();
		$this->baseDir = $base_dir;
		$this->artDir = $art_dir;
		$this->shortName = $art_shortname;
		$this->modDir = $mod_dir;
		$this->typeLink = new TypeLink();
	}
	//
	//替换指定的标签与对应的代码
	//
	function TagToValue(&$TagPar,&$mtag,$tagID)
	{
		switch($mtag->TagName){
		case "imglist":
			$TagPar->ReplaceTag(
				$tagID,
				$this->GetImgList(
				$mtag->GetAtt("typeid"),
				$mtag->GetAtt("row"),
				$mtag->GetAtt("col"),
				$mtag->GetAtt("titlelength"),
                $mtag->GetAtt("infolength"),
				$mtag->GetAtt("imgwidth"),
				$mtag->GetAtt("imgheight"),
				$mtag->GetAtt("tablewidth"),
				$mtag->GetAtt("sort"),
				$mtag->InnerText)
			);
			break;
		case "artlist":
            $TagPar->ReplaceTag(
				$tagID,
				$this->GetArtList(
				$mtag->GetAtt("typeid"),
				$mtag->GetAtt("row"),
				$mtag->GetAtt("titlelength"),
				$mtag->GetAtt("infolength"),
				$mtag->GetAtt("sort"),
				$mtag->GetAtt("keyword"),
				$mtag->InnerText)
			);
			break;
		case "imginfolist":
            $TagPar->ReplaceTag(
				$tagID,
				$this->GetImgInfoList(
				$mtag->GetAtt("typeid"),
				$mtag->GetAtt("row"),
                $mtag->GetAtt("col"),
				$mtag->GetAtt("titlelength"),
                $mtag->GetAtt("infolength"),
				$mtag->GetAtt("imgwidth"),
				$mtag->GetAtt("imgheight"),
				$mtag->GetAtt("tablewidth"),
				$mtag->GetAtt("sort"),
				$mtag->InnerText)
			);
			break;
		case "vote":
			$TagPar->ReplaceTag(
				$tagID,
				$this->GetVote(
				$mtag->GetAtt("name"),
				$mtag->GetAtt("lineheight"),
                $mtag->GetAtt("tablewidth"),
				$mtag->GetAtt("titlebgcolor"),
                $mtag->GetAtt("titlebackground"),
                $mtag->GetAtt("tablebgcolor"))
			);
			break;
		case "link":
			$TagPar->ReplaceTag(
				$tagID,
				$this->GetFriendLink(
				$mtag->GetAtt("type"),
				$mtag->GetAtt("row"),
				$mtag->GetAtt("col"),
				$mtag->GetAtt("titlen"),
				$mtag->GetAtt("tablestyle"))
			);
			break;
		case "channel":
			$TagPar->ReplaceTag(
				$tagID,
				$this->GetChannel(
				$mtag->GetAtt("typeid"),
				$mtag->GetAtt("row"),
				$mtag->InnerText)
			);
			break;
		case "channelartlist":
            $TagPar->ReplaceTag(
				$tagID,
				$this->GetChannelArt(
				$mtag->GetAtt("typeid"),
				$mtag->GetAtt("col"),
				$mtag->GetAtt("row"),
				$mtag->GetAtt("titlelength"),
				$mtag->GetAtt("infolength"),
				$mtag->GetAtt("sort"),
				$mtag->GetAtt("keyword"),
				$mtag->GetAtt("bgcolor"),
				$mtag->GetAtt("background"),
				$mtag->GetAtt("titleheight"),
				$mtag->GetAtt("titleimg"),
				$mtag->GetAtt("tablewidth"),
				$mtag->InnerText)
			);
			break;
		case "mynews":
			$TagPar->ReplaceTag(
				$tagID,
				$this->GetMyNews($mtag->GetAtt("row"),$mtag->InnerText)
			);
			break;
		case "webinfo":
			$TagPar->ReplaceTag(
				$tagID,
				$this->GetWebInfo($mtag->GetAtt("name"))
			);
			break;
		case "field":
			$TagPar->ReplaceTag(
				$tagID,
				$this->GetTypeField($mtag->GetAtt("name"))
			);
			break;
		case "extern":
			$TagPar->ReplaceTag(
				$tagID,
				$this->GetExtern($mtag->GetAtt("name"))
			);
			break;
		case "loop":
			$TagPar->ReplaceTag(
				$tagID,
				$this->GetTable(
					$mtag->GetAtt("table"),
					$mtag->GetAtt("row"),
					$mtag->GetAtt("sort"),
					$mtag->GetAtt("if"),
					$mtag->InnerText)
			);
			break;
		}//End Switch
	}
	//
	//分析指定的模板，返回分析结果
	//
	function ParTemp($tempstring)
	{
		global $tag_start_char;
		global $tag_end_char;
		$CDTagParse = new DedeTagParse();
		$CDTagParse->TagStartWord = $tag_start_char;
		$CDTagParse->TagEndWord = $tag_end_char;
		$CDTagParse->LoadSource($tempstring);
		$tagCount = $CDTagParse->Count;
		for($tagID=0;$tagID<=$tagCount;$tagID++)
		{
			$this->tagToValue($CDTagParse,$CDTagParse->CTags[$tagID],$tagID);
		}
		return $CDTagParse->GetResult();
	}
	//
	//创建模板
	//
	function MakeMode($tempstring,$filename)
	{
		$fp = fopen($filename,"w");
		fwrite($fp,$this->ParTemp($tempstring));
		fclose($fp);
	}
	//
	//测试指定的模板，返回分析结果
	//
	function ParTempTest($tempstring)
	{
		global $tag_start_char;
		global $tag_end_char;
		$restr = "";
		$CDTagParse = new DedeTagParse();
		$CDTagParse->TagStartWord = $tag_start_char;
		$CDTagParse->TagEndWord = $tag_end_char;
		$CDTagParse->LoadSource($tempstring);
		$tagCount = $CDTagParse->Count;
		for($tagID=0;$tagID<=$tagCount;$tagID++)
		{
			//输出测试标记
			$restr .= "<xmp>测试标记为：\r\n{dede:".$CDTagParse->CTags[$tagID]->TagName." ";
			foreach($CDTagParse->CTags[$tagID]->CAttribute->Items as $key=>$value)
			{
				if($key!="tagname")
					$restr .= " $key=\"$value\"";
			}
			$restr .= "}".$CDTagParse->CTags[$tagID]->InnerText."{/dede}</xmp>结果：<hr size='1'>";
			//替换结果
			$this->tagToValue($CDTagParse,$CDTagParse->CTags[$tagID],$tagID);
		}

		$restr .= $CDTagParse->GetResult();
		return $restr;
	}
    //
    //获取一列图片
    //
	function GetImgList($typeid=0,$row=1,$col=4,$titlelen=20,$infolen=120,$imgw=100,$imgh=100,$tablewidth="100%",$ordertype="new",$innertext="")
	{
		if($typeid=="") $typeid=0;
        if($typeid==0) $typeid=$this->typeID;
		if($row=="") $row=1;
		if($col=="") $col=4;
		if($titlelen=="") $titlelen=20;
        if($infolen=="") $infolen=120;
		if($imgw=="") $imgw=100;
		if($imgh=="") $imgh=100;
		if($tablewidth=="") $tablewidth="100%";
		if($ordertype=="") $ordertype="new";
		else $ordertype=strtolower($ordertype);
		if($innertext=="") $innertext=$this->typeLink->GetLowMod("part_imglist.htm");
		///////////////////////////////////
		$bodys = split("~",$innertext);
		$dsnum = count($bodys);
		$tw = "width=".ceil(100/$col)."%'";
		$totalrecord = $row*$col;
		//按不同情况增加SQL条件
		//排序方式
		$orwhere = "";
		$ordersql = "";
		$imgline = "";
		if($ordertype=="hot") $ordersql="order by dede_art.click desc";
        else if($ordertype=="spec") $ordersql="And dede_art.redtitle=2 order by dede_art.ID desc";
        else if($ordertype=="commend") $ordersql="And dede_art.redtitle=1 order by dede_art.ID desc";
		else $ordersql="order by dede_art.ID desc";
		//类别ID
		//如果用 "," 分开,可以指定特定类目
		$reids = split(",",$typeid);
		$ridnum = count($reids);
		if($ridnum>1)
		{
			for($i=0;$i<$ridnum;$i++)
			{
				$orwhere .= "And ".$this->typeLink->GetSunID($reids[$i]);
			}
		}
		else
		{
			if($typeid!=0) $orwhere .= "And ".$this->typeLink->GetSunID($typeid);
		}
		//////////////
		$query = "Select dede_art.ID,dede_art.title,dede_art.msg,dede_art.stime,dede_art.rank,dede_art.picname,dede_arttype.typedir from dede_art left join dede_arttype on dede_art.typeid=dede_arttype.ID where dede_art.isdd=1 $orwhere And dede_art.rank>=0 $ordersql limit 0,$totalrecord";
		$rs = mysql_query($query,$this->con);
		$imgline = "<table width='$tablewidth' border='0' cellspacing='0' cellpadding='0'>\r\n";
		for($i=0;$i<$row;$i++)
		{
			$imgline.="<tr align='center'>\r\n";
			for($j=0;$j<$col;$j++)
			{
				$textlink = "";
				$imgline.="	<td $tw>\r\n";
				$img="";
				if($drow=mysql_fetch_object($rs))
				{
					$ID=$drow->ID;
					$title=cn_substr($drow->title,$titlelen);
					$filename=$this->typeLink->GetFileName($ID,$drow->typedir,$drow->stime,$drow->rank);
					$iname = $drow->picname;
					//if(ereg("\.$",$iname)) $iname=$iname."jpg";
					$img="<img src='$iname' border='0' width='$imgw' alt='$title' height='$imgh'>";
					$imglink="<a href='$filename'>$img</a>\r\n";
					$textlink="<a href='$filename'>$title</a>\r\n";
                    $info = cn_substr($drow->msg,$infolen);
				}
				else
				{
					$ID=-1;
					$title="";
					$filename="";
					$img="<img src='".$this->modDir."/defdd.gif' width='$imgw' alt='$title' height='$imgh'>";
					$imglink=$img;
					$textlink="无记录";
                    $info = "";
				}
				for($m=0;$m<$dsnum;$m++)
					if($m%2==1){if(isset(${$bodys[$m]})) $imgline.=${$bodys[$m]};}
					else $imgline.=$bodys[$m];
				$imgline.="	</td>";
			}
			$imgline.="</tr>\r\n";
		}
		$imgline.="</table>\r\n";
		return $imgline;
	}
    //
    //获得一个文章列表
    //
    function GetArtList($typeid=0,$row=10,$titlelen=30,$infolen=120,$ordertype="new",$keyword="",$innertext="")
    {
   		if($typeid=="") $typeid=0;
        if($typeid==0) $typeid=$this->typeID;
		if($row=="") $row=10;
		if($titlelen=="") $titlelen=30;
		if($infolen=="") $infolen=120;

		if($ordertype=="") $ordertype="new";
		else $ordertype=strtolower($ordertype);

		$keyword = trim($keyword);
		if($innertext=="") $innertext=$this->typeLink->GetLowMod("part_artlist.htm");
        $bodys = split("~",$innertext);
		$dsnum = count($bodys);
        ///////////////////////////////////
		//按不同情况增加SQL条件
		//排序方式
		$orwhere = "";
		$ordersql = "";
		if($ordertype=="hot") $ordersql="order by dede_art.click desc";
		else if($ordertype=="spec") $ordersql="And dede_art.redtitle=2 order by dede_art.ID desc";
        else if($ordertype=="commend") $ordersql="And dede_art.redtitle=1 order by dede_art.ID desc";
		else $ordersql="order by dede_art.ID desc";
		//类别ID
		//如果用 "," 分开,可以指定特定类目
		$reids = split(",",$typeid);
		$ridnum = count($reids);
		if($ridnum>1)
		{
			for($i=0;$i<$ridnum;$i++)
			{
				$orwhere .= "And ".$this->typeLink->GetSunID($reids[$i]);
			}
		}
		else
		{
			if($typeid!=0) $orwhere .= "And ".$this->typeLink->GetSunID($typeid);
		}
		//关键字
		if($keyword!="") $orwhere .= " And dede_art.title like '%".$keyword."%' ";
		//////////////
		$query = "Select dede_art.ID,dede_art.title,dede_art.typeid,dede_art.msg,dede_art.stime,dede_art.rank,dede_art.click,dede_art.picname,dede_arttype.typedir,dede_arttype.typename,dede_arttype.isdefault from dede_art left join dede_arttype on dede_art.typeid=dede_arttype.ID where dede_art.rank>=0 $orwhere $ordersql limit 0,$row";
		$rs = mysql_query($query,$this->con);
        $artlist = "";
        while($drow=mysql_fetch_object($rs))
        {
            $ID=$drow->ID;
            if($drow->isdefault=="1")
				$typelink = "<a href='".$this->artDir."/".$drow->typedir."'>".$drow->typename."</a>";
			else if($drow->isdefault=="0")
				$typelink = "<a href='".$this->artDir."/".$drow->typedir."/list_".$drow->typeid."_1'".$this->shortName.">".$drow->typename."</a>";
            else
            	$typelink = "<a href='".$GLOBALS["art_php_dir"]."/list.php?id=".$drow->typeid."'>".$drow->typename."</a>";
            $title = cn_substr($drow->title,$titlelen);
            $info = cn_substr($drow->msg,$infolen);
            $filename=$this->typeLink->GetFileName($ID,$drow->typedir,$drow->stime,$drow->rank);
            $textlink="<a href='$filename'>$title</a>\r\n";
            $stime = $drow->stime;
            $click = $drow->click;
            for($m=0;$m<$dsnum;$m++)
				if($m%2==1){if(isset(${$bodys[$m]})) $artlist.=${$bodys[$m]};}
				else $artlist.=$bodys[$m];
        }
        return $artlist;
    }
    //
    //获取一列图文信息列表
    //
	function GetImgInfoList($typeid=0,$row=3,$col=1,$titlelen=20,$infolen=30,$imgw=60,$imgh=60,$tablewidth="200",$ordertype="new",$innertext="")
	{
		$innertext=$this->typeLink->GetLowMod("part_imginfolist.htm");
		return $this->GetImgList($typeid,$row,$col,$titlelen,$infolen,$imgw,$imgh,$tablewidth,$ordertype,$innertext);
	}
	//
	//获取一个vote的项目
	//
	function GetVote($votename,$lineheight=24,$tablewidth="100%",$titlebgcolor="#EDEDE2",$titlebackgroup="")
	{
		include("inc_vote.php");
		$vo = new DedeVote();
		$vo->SetVote($votename);
		return $vo->GetVoteForm($lineheight,$tablewidth,$titlebgcolor,$titlebackgroup);
	}
	//
	//获取友情链接列表
	//
	function GetFriendLink($type="",$row="",$col="",$titlelen="",$tablestyle="")
	{
		if($type=="") $type="text";
		if($row=="") $row=3;
		if($col=="") $col=6;
		$tdwidth = ceil(100/$col)."%";
		if($titlelen=="") $titlelen=24;
		if($tablestyle=="") $tablestyle="width='100%' border='0' cellspacing='1' cellpadding='1'";
		$totallink = $row*$col;
		$rs = mysql_query("Select * from dede_flink order by dtime desc limit 0,$totallink",$this->con);
		$revalue = "<table $tablestyle>";
		for($i=1;$i<=$row;$i++)
		{
			$revalue.="<tr bgcolor='#FFFFFF' height='20'>\r\n";
			for($j=1;$j<=$col;$j++)
			{
				if($dbrow=mysql_fetch_object($rs))
				{
					if($type=="text")
						$link = "&nbsp;<a href='".$dbrow->url."' target='_blank'>".cn_substr($dbrow->webname,$titlelen)."</a>";
					else
						$link = "&nbsp;<a href='".$dbrow->url."' target='_blank'><img src='".$dbrow->logo."' width='88' height='31' border='0'></a>";
					$revalue.="<td width='$tdwidth'>$link</td>\r\n";
				}
				else
				{
					$revalue.="<td></td>\r\n";
				}
			}
			$revalue.="</tr>\r\n";
		}
		$revalue .= "</table>";
		return $revalue;
	}
	//
	//获得频道列表
	//
	function GetChannel($reid=0,$line=-1,$innertext="")
	{
		if($reid=="") $reid=0;
        if($reid==0) $reid=$this->typeID;
        if($reid==-1) $reid=0;
		if($innertext=="") $innertext="<a href='~typelink~'>~typename~</a> | ";
		if($line=="") $line=-1;
		$limitsql = "";
		$revalue = "";
		if($line!=-1) $limitsql = " limit 0,$line";
		$bodys = split("~",$innertext);
		$bn = count($bodys);
		$rs = mysql_query("Select * From dede_arttype where reID=$reid order by ID $limitsql",$this->con);
		while($row=mysql_fetch_object($rs))
		{
			if($row->isdefault=="1")
				$typelink = $this->artDir."/".$row->typedir;
			else if($row->isdefault=="0")
				$typelink = $this->artDir."/".$row->typedir."/list_".$row->ID."_1".$this->shortName;
			else
				$typelink = $GLOBALS["art_php_dir"]."/list.php?id=".$row->ID;
			$typename = $row->typename;
			for($i=0;$i<$bn;$i++)
			{
				if($i%2==1)
				{
                    if(isset(${$bodys[$i]})) $revalue.=${$bodys[$i]};
                }
				else
                   	$revalue.=$bodys[$i];
			}
		}
		return $revalue;
	}
	//
	//获取频道下级类目的文章列表
	//
	function GetChannelArt($reid=0,$col=1,$rowl=10,$titlelen=30,$infolen=120,$ordertype="new",$keyword="",$bgcolor="#F5F5F5",$background="",$titleheight="20",$titleimg="§",$tablewidth="100%",$innertext="")
	{
		if($reid=="") $reid=0;
        if($reid==0) $reid=$this->typeID;
		if($bgcolor=="") $bgcolor="#F5F5F5";
		if($titleheight=="") $titleheight=20;
		if($tablewidth=="") $tablewidth="100%";

		if($titleimg=="") $titleimg="§";
		else $titleimg="<img src='$titleimg'>";

		if($col==""||$col<1) $col=1;
		if($innertext=="") $innertext="·<a href='~filename~'>~title~</a>(~stime~)<br>";

		if($background!="") $bg = "background=$background";
		else $bg = "bgcolor='$bgcolor'";

		$reids = split(",",$reid);
		$ridnum = count($reids);
		$sortSql = "reID=$reid";
		if($ridnum>1)
		{
			$sortSql = "";
			for($i=0;$i<$ridnum;$i++)
			{
				$sortSql.=" ID=".$reids[$i]." Or";
			}
			$sortSql = ereg_replace(" Or$","",$sortSql);
		}

		$channellist = "<table width=100% border=0 cellpadding=0 cellspacing=0>\r\n";
		$rs = mysql_query("Select * from dede_arttype where $sortSql",$this->con);
		$totalresult = mysql_num_rows($rs);
		if($totalresult>0)
		{
			$line = ceil($totalresult/$col);
			$tdwidth = ceil(100/$col)."%";
			for($i=0;$i<$line;$i++)
			{
				$channellist.="<tr>\r\n";
				for($j=0;$j<$col;$j++)
				{
					$channellist.="<td width=$tdwidth valign=top>\r\n";
					if($row=mysql_fetch_object($rs))
					{
						if($row->isdefault=="1")
							$typelink = $this->artDir."/".$row->typedir;
						else if($row->isdefault=="0")
							$typelink = $this->artDir."/".$row->typedir."/list_".$row->ID."_1".$this->shortName;
						else
							$typelink = $GLOBALS["art_php_dir"]."/list.php?id=".$row->ID;
						$channellist.="<table width=$tablewidth border=0 cellpadding=0 cellspacing=0>";
						$channellist.="<tr><td height='$titleheight' $bg><table width=100% border=0 cellpadding=2 cellspacing=0><tr><td width=1%>$titleimg</td><td width=99%><a href='$typelink'>".$row->typename."</a></td></tr></table></td></tr>\r\n";
						$channellist.="<tr><td>".$this->GetArtList($row->ID,$rowl,$titlelen,$infolen,$ordertype,$keyword,$innertext)."</td></tr>\r\n";
						$channellist.="</table>\r\n";
					}
					else
					{
						$channellist.="<table width=$tablewidth>";
						$channellist.="<tr><td height='$titleheight' $bg></td></tr>\r\n";
						$channellist.="<tr><td></td></tr>\r\n";
						$channellist.="</table>\r\n";
					}
					$channellist.="</td>\r\n";
				}
				$channellist.="</tr>\r\n";
			}
		}
		$channellist .= "</table>\r\n";
		return $channellist;
	}
	//
	//获取站内新闻
	//
	function GetMyNews($row=3,$innertext)
	{
		global $base_dir;
		global $art_php_dir;
		if($row=="") $row=3;
		if($innertext=="")
		{
			$innertext="~title~ | ~writer~<br>~msg~(~senddate~)<hr size='1'>\r\n";
		}
		$bodys = split("~",$innertext);
		$bn = count($bodys);
		$datafile = $base_dir.$art_php_dir."/webnews/news.xml";
		if(!file_exists($datafile))
		{
			$fp = @fopen($datafile,"w") or die("无法创建文件：$datafile");
			fclose($fp);
		}
		$CDTag = new DedeTag();
		$ctp = new DedeTagParse();
		$ctp->SetNameSpace("mynews");
		$ctp->LoadTemplate($datafile);
		$mynews = "";
		$i=0;
		$j=1;
		if($ctp->Count!=-1)
		{
			for($i=0;$i<=$ctp->Count;$i++)
			{
		    	$CDTag = $ctp->CTags[$i];
		    	if($j>$row) break;
				for($k=0;$k<$bn;$k++)
				{
					if($k%2==1)
					{
						$att = trim($bodys[$k]);
						if($att!="") $att = strtolower($att);
						if($att=="msg") $mynews.=$CDTag->InnerText;
						else $mynews.=$CDTag->GetAtt($att);
					}
					else
                   		$mynews.=$bodys[$k];

				}
				$j++;
			}
		}
		return $mynews;
	}
	//
	//
	//
	function GetWebInfo($sysname)
	{
		global $webname;
		global $admin_email;
		global $base_url;
        $adminemail = $admin_email;
        $baseurl = $base_url;
        $powerby = "<a href='http://www.dedecms.com' target='_blank'>Power by 织梦内容管理系统(www.dedecms.com)</a>";
		$sysname = trim($sysname);
		if($sysname=="") return "";
		else
		{
			if(isset($$sysname)) return $$sysname;
			else return "";
		}
	}
	function GetTypeField($fname)
	{
		$fname = trim($fname);
		if($fname=="dbuserpwd") return "";
		if($this->typeID>0)
		{
			$rs = mysql_query("select * from dede_arttype where ID=".$this->typeID,$this->con);
			$row = mysql_fetch_array($rs);
			if(isset($row[$fname])) return $row[$fname];
			else
			{
				if(isset($GLOBALS[$fname])) return $GLOBALS[$fname];
				else return "";
			}
		}
		else
		{
			if(isset($GLOBALS[$fname])) return $GLOBALS[$fname];
			else return "";
		}
	}
	function GetExtern($fname)
	{
		return $this->GetTypeField($fname);
	}
	//
	//获得任意表的内容
	//
	function GetTable($tablename="",$row=6,$sort="",$ifcase="",$InnerText="")
	{
		$InnerText = trim($InnerText);
		if($tablename=="") return "";
		if($InnerText=="") return "";
		if($row=="") $row=6;
		if($sort!="") $sort = " order by $sort desc";
		if($ifcase!="") $ifcase=" where $ifcase";
		$revalue="";
		$sql = "Select * From $tablename $ifcase $sort limit 0,$row";
		$rs = @mysql_query($sql,$this->con);
		$CDTag = new DedeTag();
		$ctp = new DedeTagParse();
		$ctp->SetNameSpace("loop");
		$ctp->TagStartWord = "[";
		$ctp->TagEndWord = "]";
		$ctp->LoadSource($InnerText);
		while($row = @mysql_fetch_array($rs))
		{
		  if($ctp->Count==-1) break;
		  $ctp->ResetSource();
		  for($i=0;$i<=$ctp->Count;$i++)
		  {
		    $CDTag = $ctp->CTags[$i];
		    if($CDTag->GetTagName()=="field")
		    {
		    	$fieldname=$CDTag->GetAtt("name");
		    	if(!empty($row[$fieldname])) $fieldvalue=$row[$fieldname];
		    	if($fieldvalue!="")
		    	{
		    		$fieldvalue = $this->PutFunction($fieldvalue,$CDTag->GetAtt("function"),$CDTag->GetAtt("parameter"));
		    	}
		    }
		    $ctp->ReplaceTag($i,$fieldvalue);
		  }
		  $revalue.=$ctp->GetResult();
		}
		return $revalue;
	}
	//
	//处理field的函数
	//
	function PutFunction($fieldvalue,$functionname,$parameter)
	{
		$parameters = split(",",$parameter);
		$pnum = count($parameters);
		switch($functionname)
		{
			case "trim":
				$fieldvalue = trim($fieldvalue);
				break;
			case "replace":
				if($pnum==2)
					$fieldvalue = @str_replace($parameters[0],$parameters[1],$fieldvalue);
				break;
			case "substr":
				if($pnum==2)
				{
					if($parameters[0]=="") $parameters[0]=0;
					if($parameters[1]=="") $parameters[1]=24;
					$fieldvalue = cn_midstr($fieldvalue,$parameters[0],$parameters[1]);
				}
				break;
			case "date":
				if($pnum==1)
				{
					if($parameters[0]=="") $parameters[0]="Y-m-d";
					$fieldvalue = @date($parameters[0],$fieldvalue);
				}
				break;
		}
		return $fieldvalue;
	}
	
}
?>