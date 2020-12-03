<?
require_once("config_base.php");
//class TypeLink
//获得文章的位置和文章的类目位置等
////////////////////////////////////////////////
class TypeLink
{
	var $listStep = 0;
	var $titleInfos = "";
	var $valuePosition = "";
	var $typeDir = "";
	var $sunID="";
	var $artDir;
	var $con;
	var $typeID;
	var $artNameTag;
	var	$artShortName;
	var $baseDir;
	var $modDir="";
	var $indexUrl="";
	var $indexName="";
	//构造函数///////
	//对于使用默认构造函数的情况
	//GetTypeLink()将不可用
	function TypeLink($typeid=0)
	{
		global $base_dir;
		global $art_dir;
		global $art_nametag;
		global $art_shortname;
		global $mod_dir;
		global $index_url;
		global $index_name;
		$this->indexUrl = $index_url;
		$this->indexName = $index_name;
		$this->typeID=$typeid;
		$this->baseDir = $base_dir;
		$this->artDir = $art_dir;
		$this->artNameTag = $art_nametag;
		$this->artShortName = $art_shortname;
		$this->modDir = $mod_dir;
		$this->con = connectMySql();
	}
	//
	//重设类目ID
	//
	function SetTypeID($typeid)
	{
		$this->typeID=$typeid;
		$this->valuePosition = "";
	    $this->typeDir = "";
	}
	//
	//获得这个类目的路径
	//
	function GetTypePath($typeid=-1)
	{
		if($typeid!=-1) $this->SetTypeID($typeid);
		if($this->typeDir!="") return $this->typeDir;
		else if($this->typeID==0) return "";
		else
		{
			$rs = mysql_query("Select typedir from dede_arttype where ID=".$this->typeID,$this->con);
			$row = mysql_fetch_object($rs);
			$this->typeDir = $row->typedir;
			return $this->typeDir;
		}
	}
	//
	//---获得文章网址----------
	//
	function GetFileName($ID,$typedir,$stime,$rank=0)
	{
		global $art_php_dir;
		if($rank>0||$rank==-1) return $art_php_dir."/viewart.php?ID=$ID";
		if($this->artNameTag=="maketime")
		{
			$ds = split("-",$stime);
			return $this->artDir."/".$ds[0]."/".$ds[1].$ds[2]."/".$ID.$this->artShortName;			
		}
		else
			return $this->artDir."/".$typedir."/".$ID.$this->artShortName;
	}
	//---获得新文件网址----------
	//本函数会自动创建目录，并且返回的文件名是不带扩展名的
	function GetFileNewName($ID,$typedir,$stime,$rank=0)
	{
		global $dir_purview;
		global $art_php_dir;
		$ndir = "";
		if($rank>0||$rank==-1) return $art_php_dir."/viewart.php?ID=$ID";
		if($this->artNameTag=="maketime")
		{
			$ds = split("-",$stime);
			$redir = $this->artDir."/".$ds[0]."/".$ds[1].$ds[2]."/".$ID;			
		}
		else
			$redir = $this->artDir."/".$typedir."/".$ID;
		$dirs = split("/",$redir);
		$ds = count($dirs);
		for($i=0;$i<$ds-1;$i++)
		{
			$ndir .= "/".$dirs[$i];
			if(!is_dir($this->baseDir.$ndir) && !is_dir($this->baseDir.$ndir."/"))
			{
				@mkdir($this->baseDir.$ndir,$dir_purview);
			}					
		}
		return $redir;
	}
	//获得某类目的链接列表 如：类目一>>类目二>> 这样的形式
	function GetTypeLink($typeid=-1)
	{
		if($typeid!=-1) $this->SetTypeID($typeid);
		if($this->valuePosition!="")
			return $this->valuePosition;
		else if($this->typeID==0)
			return "<a href='".$this->indexUrl."'>".$this->indexName."</a>&gt;&gt;";
		else
		{
			$this->getPosition($this->typeID);
			return $this->valuePosition;
		}
	}
	//获得某类目的链接列表，递归逻辑部分
	function getPosition($ID)
	{
		$rs = mysql_query("Select * from dede_arttype where ID=".$ID,$this->con);
		$row = mysql_fetch_object($rs);
		if($row->reID!=0)
		{
			$this->titleInfos[$this->listStep]=$row->ID."`".$row->typename."`".$row->typedir."`".$row->isdefault."`".$row->defaultname;
			$this->listStep++;
			$this->getPosition($row->reID);
		}
		else
		{
			$this->titleInfos[$this->listStep]=$row->ID."`".$row->typename."`".$row->typedir."`".$row->isdefault."`".$row->defaultname;
			$position = "<a href='".$this->indexUrl."'>".$this->indexName."</a>&gt;&gt;";
			for($this->listStep;$this->listStep>=0;$this->listStep--)
			{
			    list($tid,$tname,$typedir,$isdefault,$defaultname) = split("`",$this->titleInfos[$this->listStep]);
				$fpath = $this->artDir."/".$typedir."/";
				if($isdefault=="1")
					$position.="<a href='$fpath$defaultname'>$tname</a>&gt;&gt;";
				else if($isdefault=="0")
					$position.="<a href='$fpath"."list_$tid"."_1".$this->artShortName."'>$tname</a>&gt;&gt;";
				else
					$position.="<a href='".$GLOBALS["art_php_dir"]."/list.php?id=$tid'>$tname</a>&gt;&gt;";
					
			}
			$this->listStep = 0;
			$this->titleInfos = "";
			$this->valuePosition=$position;
		}
	}
	//
	//获得某ID的下级ID(包括本身)的SQL语句“($tb.typeid=id1 or $tb.typeid=id2...)”
	//
	function GetSunID($typeid=-1,$tb="dede_art",$channel=0)
	{
		if($typeid>0) $this->SetTypeID($typeid);
		$this->sunID = "";
		$this->parSunID($this->typeID,$tb,$channel);
		if($this->typeID!=0)
			return "(".$tb.".typeid=".$this->typeID.$this->sunID.")";
		else
			return "(".ereg_replace("^ or","",$this->sunID).")";
	}
	//GetSunID的递归逻辑部分
	function parSunID($ID,$tb="dede_art",$channel=0)
	{
		if($ID<=0) $ID=0;
		if($channel!=0) $channelsql = " And channeltype=$channel";
		else $channelsql="";
		$rs = mysql_query("Select ID From dede_arttype where reID=$ID $channelsql",$this->con);
		if(mysql_num_rows($rs)>0)
		{
			while($row=mysql_fetch_object($rs))
			{
				$NID = $row->ID;
				$this->sunID.=" or ".$tb.".typeid=$NID";
				$this->parSunID($NID,$tb,$channel);
			}
		}
	}
	//
	//--获得类别列表---------------
	//
	function GetOptionArray($hid=0,$oper=0,$channeltype=0)
	{
    	if($hid!=0)
    	{
    		$rs = @mysql_query("Select * From dede_arttype where ID=$hid",$this->con);
    		$row=mysql_fetch_object($rs);
    		echo "<option value='".$row->ID."'>".$row->typename."</option>\r\n";
    	}
    	if($channeltype==0) $ctsql="";
    	else $ctsql=" And channeltype=$channeltype";
    	if($oper!=0)
    		$rs = @mysql_query("Select * From dede_arttype where ID=$oper $ctsql",$this->con);
    	else
    		$rs = @mysql_query("Select * From dede_arttype where reID=0 $ctsql",$this->con);
        while($row=mysql_fetch_object($rs))
    	{
          	echo "<option value='".$row->ID."'>".$row->typename."</option>\r\n";
          	$this->GetSunOptionArray($row->ID,"─");
        }     
	}
	function GetSunOptionArray($ID,$step)
	{
		$rs = mysql_query("Select * From dede_arttype where reID=".$ID,$this->con);
		while($row=mysql_fetch_object($rs))
        {
             echo "<option value='".$row->ID."'>$step".$row->typename."</option>\r\n";
             $this->GetSunOptionArray($row->ID,$step."─");
        }
	}
	//
	//---频道的链接 ------
    //
	function GetArtTypeLink($typeid=-1)
	{
		if($typeid!=-1) $this->SetTypeID($typeid);
		$rs = mysql_query("Select typename,typedir,isdefault from dede_arttype where ID=".$this->typeID,$this->con);
		$row = mysql_fetch_object($rs);
		if($row->isdefault=="1")
			$link = "<a href='".$this->artDir."/".$row->typedir."'><u>".$row->typename."</u></a> ";
		else if($row->isdefault=="0")
			$link = "<a href='".$this->artDir."/".$row->typedir."/list_".$typeid."_1'".$this->artShortName."><u>".$row->typename."</u></a> ";
		else
			$link = "<a href='".$GLOBALS["art_php_dir"]."/list.php?id=$typeid'><u>".$row->typename."</u></a> ";
		return $link;
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
				if($row->isdefault=="1")
					$typelink = $this->artDir."/".$row->typedir;
				else if($row->isdefault=="-1")
					$typelink = $GLOBALS["art_php_dir"]."/list.php?id=".$row->ID;
				else
					$typelink = $this->artDir."/".$row->typedir."/list_".$row->ID."_1".$this->artShortName;
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
				if($row->isdefault=="1")
					$typelink = $this->artDir."/".$row->typedir;
				else if($row->isdefault=="-1")
					$typelink = $GLOBALS["art_php_dir"]."/list.php?id=".$row->ID;
				else
					$typelink = $this->artDir."/".$row->typedir."/list_".$row->ID."_1".$this->artShortName;
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
}
?>