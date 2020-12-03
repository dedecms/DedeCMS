<?
require_once("config_base.php");
require_once("inc_typelink.php");
require_once("inc_dedetag.php");
class MakeSpec
{
	var $baseDir = "";
	var $modDir = "";
	var $artDir = "";
	var $con = "";
	var $mID = "";
	var $typeLink="";
	var $title="";
	var $position="";
	var $specimg="";
	var $specmsg="";
	var $typeid = 0;
	var $artID=0;
	function MakeSpec($ID)
	{
		global $base_dir;
		global $mod_dir;
		global $art_dir;
		$this->baseDir = $base_dir;
		$this->modDir = $mod_dir;
		$this->artDir = $art_dir;
		$this->mID = $ID;
		$this->con = connectMySql();
		$this->typeLink = new TypeLink();
		//获得field相关的参数
		$rs = mysql_query("Select * from dede_spec where ID=$ID",$this->con);
		$row = mysql_fetch_object($rs);
		$this->typeid = $row->typeid;
		$this->typeLink->SetTypeID($row->typeid);
	    $this->title = "专题：".$row->spectitle;
	    $this->position = $this->typeLink->GetTypeLink().$this->title;
	    $this->specimg = "
		<table width='100%' border='0' cellspacing='2' cellpadding='0'>
			<tr>
			<td align='center'><a href='".$row->imglink."'><img src='".$row->specimg."' width='120' height='100' alt='".$row->imgtitle."' border='0'></a></td>
			</tr><tr>
			<td align='center'><a href='".$row->imglink."'>".$row->imgtitle."</a></td>
		</tr></table>\r\n";
		$this->specmsg = "
		<table width='100%' border='0' cellspacing='2' cellpadding='0'>
			<tr>
			<td><b>".$row->spectitle."</b></td>
			</tr><tr>
			<td>".$row->specmsg."</td>
		</tr></table>\r\n";
	}
	//发布指定专题
	function MakeMode()
	{
		global $art_shortname;
		$rs = mysql_query("select dede_spec.*,dede_arttype.typedir from dede_spec left join dede_arttype on dede_spec.typeid=dede_arttype.ID where dede_spec.ID=".$this->mID,$this->con);
		$row = mysql_fetch_object($rs);
		if($row->AID==0)
		{
			$inQuery = "
			INSERT INTO dede_art(typeid,title,source,rank,picname,
			stime,dtime,isdd,click,msg,redtitle,ismake,body,spec,memberID)
 			VALUES ('".$row->typeid."','".$row->spectitle."','','0','".$row->specimg."',
			'".$row->stime."','".$row->dtime."','1','0','".cn_substr($row->specmsg,500)."','2','1','专题',".$this->mID.",0)";
			mysql_query($inQuery,$this->con);
			$AID = mysql_insert_id($this->con);
			if($AID>0)
				mysql_query("update dede_spec set AID=$AID where ID=".$this->mID,$this->con);
			else
			{
				echo mysql_error();
				exit();
			}
		}
		else
		{
			$AID = $row->AID;
		}
		$this->artID = $AID;
		$title = $row->spectitle;
		$ufilename = $this->typeLink->GetFileNewName($AID,$row->typedir,$row->stime,0).$art_shortname;
		$filename = $this->baseDir.$ufilename;
		$fp = fopen($filename,"w");
		fwrite($fp,$this->ParMode());
		fclose($fp);
		return "<a href='$ufilename' target='_blank'>$title</a>";
	}
	//测试指定字串的专题模板
	function ParMode()
	{
		global $tag_start_char;
		global $tag_end_char;
		$CDTagParse = new DedeTagParse();
		$CDTagParse->TagStartWord = $tag_start_char;
		$CDTagParse->TagEndWord = $tag_end_char;
		//载入模板
		$rs = mysql_query("Select * from dede_arttype where ID=".$this->typeid,$this->con);
		$row = mysql_fetch_object($rs);
		$modpage = $this->baseDir.$this->modDir."/".$row->modname."/".$row->channeltype."/专题.htm";
		$CDTagParse->LoadTemplate($modpage);
		$tagCount = $CDTagParse->Count;
		for($tagID=0;$tagID<=$tagCount;$tagID++)
		{
			$this->TagToValue($CDTagParse,$CDTagParse->CTags[$tagID],$tagID);
		}
		return $CDTagParse->GetResult();
	}
	//把标记转为实体
	function TagToValue(&$TagPar,&$mtag,$tagID)
	{
		switch($mtag->TagName){
		case "field":
			if($mtag->GetAtt("name")!="")
				if(isset($this->{$mtag->GetAtt("name")}))
					$TagPar->ReplaceTag($tagID,$this->{$mtag->GetAtt("name")});	
			break;
		case "speclist":
			$TagPar->ReplaceTag(
				$tagID,
				$this->GetSpecList(
					$this->mID,
					$mtag->GetAtt("titlelength"),
					$field="speclist",
					$mtag->InnerText)
				);
			break;
		case "speclike":
			$TagPar->ReplaceTag(
				$tagID,
				$this->GetSpecList(
					$this->mID,
					$mtag->GetAtt("titlelength"),
					$field="speclike",
					$mtag->InnerText)
				);
			break;
		case "hotart":
            $TagPar->ReplaceTag(
				$tagID,
				$this->GetArtList(
				$this->typeid,
				$mtag->GetAtt("row"),
				$mtag->GetAtt("titlelength"),
				"hot",
				"",
				$mtag->InnerText)
			);
			break;
		case "coolart":
            $TagPar->ReplaceTag(
				$tagID,
				$this->GetArtList(
				$this->typeid,
				$mtag->GetAtt("row"),
				$mtag->GetAtt("titlelength"),
				"commend",
				"",
				$mtag->InnerText)
			);
			break;
		case "channel":
			$TagPar->ReplaceTag(
				$tagID,
				$this->typeLink->GetChannel($mtag->GetAtt("type"),$mtag->InnerText)
			);
			break;
		}//End Switch
	}
	//
	//获得专题文章列表
	//其中$field 参数表示 speclist 和 speclike
	//
	function GetSpecList($ID=0,$titlelen=50,$field="speclist",$innertext="")
	{
		if($titlelen=="") $titlelen=50;
		if($innertext=="") $innertext=$this->typeLink->GetLowMod("spec_list.htm");
		$rs = mysql_query("Select specartid,speclikeid From dede_spec where ID=".$this->mID,$this->con);
		$row = mysql_fetch_object($rs);
		if($field=="speclist") $specartid=$row->specartid;
		else $specartid=$row->speclikeid;
		$speclist="";
		/////////////////////////////////////
		$ids = split(",",$specartid);
		$dd = count($ids);
		$idsql = "";
		///////////////////////////////
		$bodys = split("~",$innertext);
		$dsnum = count($bodys);
		//////////////////////////
		if($dd>0)
		{
			for($i=0;$i<$dd;$i++)
			{
				if($ids[$i]!=$this->mID)
					if($i<$dd-1) $idsql .= " dede_art.ID=".$ids[$i]." Or";
			}
		}
		$idsql = ereg_replace(" Or$","",$idsql);
		$idsql = " And ($idsql)";
		if($idsql==" And ()") $idsql="";
		$squery = "Select dede_art.ID,dede_art.typeid,dede_art.title,dede_art.click,dede_art.stime,dede_art.rank,dede_art.redtitle,dede_arttype.typedir from dede_art left join dede_arttype on dede_art.typeid=dede_arttype.ID where dede_art.rank>=0 $idsql";
		$rs = mysql_query($squery,$this->con);
		while($row = mysql_fetch_object($rs))
		{
			$filename = $this->typeLink->GetFileName($row->ID,$row->typedir,$row->stime,$row->rank);
			$title = cn_substr($row->title,$titlelen);
			$stime = $row->stime;
			$ID = $row->ID;
			$click = $row->click;
			for($i=0;$i<$dsnum;$i++)
			{
				if($i%2==1) 
				{
					if(isset(${$bodys[$i]})) $speclist.=${$bodys[$i]};
				}
				else $speclist.=$bodys[$i];
			}
		}
		return $speclist;
	}
	//
    //获得一个文章列表
    //由GetHot和GetCommend调用
    //
    function GetArtList($typeid=0,$row=10,$titlelen=30,$ordertype="hot",$keyword="",$innertext="")
    {
   		global $art_shortname;
   		if($typeid=="") $typeid=0;
		if($row=="") $row=10;
		if($titlelen=="") $titlelen=30;
		
		if($ordertype=="") $ordertype="hot";
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
        else if($ordertype=="commend") $ordersql="And dede_art.redtitle>0 order by dede_art.ID desc";
		else $ordersql="order by dede_art.ID desc";
		//类别ID
		if($typeid!=0) $orwhere .= "And ".$this->typeLink->GetSunID($typeid);
		//关键字
		if($keyword!="") $orwhere .= " And dede_art.title like '%".$keyword."%' ";
		//////////////
		$query = "Select dede_art.ID,dede_art.title,dede_art.msg,dede_art.stime,dede_art.rank,dede_art.click,dede_art.picname,dede_art.typeid,dede_arttype.typedir,dede_arttype.typename,dede_arttype.isdefault from dede_art left join dede_arttype on dede_art.typeid=dede_arttype.ID where dede_art.rank>=0 $orwhere $ordersql limit 0,$row";
		$rs = mysql_query($query,$this->con);
        $artlist = "";
        while($drow=mysql_fetch_object($rs))
        {
            $ID=$drow->ID;
            if($drow->isdefault=="1")
				$typelink = "<a href='".$this->artDir."/".$drow->typedir."'>".$drow->typename."</a>";
			else if($drow->isdefault=="0")
				$typelink = "<a href='".$this->artDir."/".$drow->typedir."/list_".$drow->typeid."_1'".$art_shortname.">".$drow->typename."</a>";
            else
            	$typelink = "<a href='".$GLOBALS["art_php_dir"]."/list.php?id=$ID'>".$drow->typename."</a>";
            $title = cn_substr($drow->title,$titlelen);
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
}
?>