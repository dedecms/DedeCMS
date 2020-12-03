<?
require_once(dirname(__FILE__)."/../include/config_base.php");
require_once(dirname(__FILE__)."/../include/inc_channel_unit_functions.php");
require_once(dirname(__FILE__)."/../include/pub_db_mysql.php");
require_once(dirname(__FILE__)."/../include/pub_dedetag.php");
require_once(dirname(__FILE__)."/../include/inc_arcpart_view.php");
/*----------------------------------
表示特定频道的附加数据结构信息
function C____ChannelUnit();
-----------------------------------*/
class ChannelUnit
{
	var $ChannelInfos;
	var $ChannelFields;
	var $AllFieldNames;
	var $ChannelID;
	var $ArcID;
	var $dsql;
	var $SplitPageField;
	var $PartView;
	//-------------
	//php5构造函数
	//-------------
	function __construct($cid,$aid=0)
 	{
 		$this->ChannelInfos = "";
 		$this->ChannelFields = "";
 		$this->AllFieldNames = "";
 		$this->SplitPageField = "";
 		$this->ChannelID = $cid;
 		$this->ArcID = $aid;
 		$this->dsql = new DedeSql(false);
 		$this->ChannelInfos = $this->dsql->GetOne("Select * from #@__channeltype where ID='$cid'");
 		if(!is_array($this->ChannelInfos)){
 			echo "读取频道信息失败，无法进行后续操作！";
 			exit();
 		}
 		$this->PartView = new PartView();
 		$dtp = new DedeTagParse();
 		$dtp->SetNameSpace("field","<",">");
    $dtp->LoadSource($this->ChannelInfos["fieldset"]);
    if(is_array($dtp->CTags))
    {
    	$tnames = Array();
    	foreach($dtp->CTags as $ctag){
    		$tname = $ctag->GetName();
    		if(isset($tnames[$tname]))
    		{ return; }
    		$tnames[$tname] = 1;
    		if($this->AllFieldNames!="") $this->AllFieldNames .= ",".$tname;
    		else $this->AllFieldNames .= $tname;
    		$this->ChannelFields[$tname]["innertext"] = $ctag->GetInnerText();
    		$this->ChannelFields[$tname]["type"] = $ctag->GetAtt("type");
    		$this->ChannelFields[$tname]["default"] = $ctag->GetAtt("default");
    		$this->ChannelFields[$tname]["rename"] = $ctag->GetAtt("rename");
    		$this->ChannelFields[$tname]["function"] = $ctag->GetAtt("function");
    		$this->ChannelFields[$tname]["value"] = "";
    		//----------------------------------------------------------------
    		$this->ChannelFields[$tname]["itemname"] = $ctag->GetAtt("itemname");
    		if($this->ChannelFields[$tname]["itemname"]=="")
    		{ $this->ChannelFields[$tname]["itemname"]=$tname; }
    		$this->ChannelFields[$tname]["isnull"] = $ctag->GetAtt("isnull");
    		$this->ChannelFields[$tname]["maxlength"] = $ctag->GetAtt("maxlength");
    		if($ctag->GetAtt("page")=="split") $this->SplitPageField = $tname;
      }
    }
    $dtp->Clear();
  }
  function ChannelUnit($cid,$aid=0)
	{
		$this->__construct($cid,$aid);
	}
	
	//设置档案ID
	//-----------------------
	function SetArcID($aid)
	{
		$this->ArcID = $aid;
	}
	
	//处理某个字段的值
	//----------------------
	function MakeField($fname,$fvalue)
	{
		if($fvalue==""){ $fvalue = $this->ChannelFields[$fname]["default"]; }
		if($this->ChannelFields[$fname]["function"]!=""){
			$fvalue = $this->EvalFunc($fvalue,$this->ChannelFields[$fname]["function"]);
		}
		//echo $this->ChannelFields[$fname]["function"]." -<br/>";
		//处理各种数据类型
		$ftype = $this->ChannelFields[$fname]["type"];
		if($ftype=="text"){
			$fvalue = ClearHtml($fvalue);
		}
		else if($ftype=="img"){
			$fvalue = $this->GetImgLinks($fvalue);
		}
		else if($ftype=="addon"){
			$foldvalue = $fvalue;
			$fvalue  = "<table width='300'><tr><td height='30' width='20'>";
			$fvalue .= "<a href='$foldvalue' target='_blank'><img src='".$GLOBALS['cfg_plus_dir']."/img/addon.gif' border='0' align='center'></a>";
			$fvalue .= "</td><td><a href='$foldvalue' target='_blank'><u>$foldvalue</u></a>";
			$fvalue .= "</td></tr></table>\r\n";
		}
		else if($ftype=="softlinks"){
			$fvalue = $this->GetAddLinkPage();
		}
		else if($ftype=="specialtopic"){
			$fvalue = $this->GetSpecList($fname,$fvalue);
		}
		return $fvalue;
	}
	//获得专题文章的列表
	//--------------------------------
	function GetSpecList($fname,$noteinfo)
	{
		if($noteinfo=="") return "";
		$rvalue = "";
		$tempStr = GetSysTemplets("channel/channel_spec_note.htm");
		$artlistTemp = GetSysTemplets("spec_arclist.htm");
		$dtp = new DedeTagParse();
		$dtp->LoadSource($noteinfo);
		if(is_array($dtp->CTags))
		{
			foreach($dtp->CTags as $k=>$ctag){
				$notename = $ctag->GetAtt("name");
				$idlist = trim($ctag->GetAtt("idlist"));
				if($idlist!=""){
					//如果想更改专题列表里的一些相关设定，可以更改订下面语句
					//参数为 GetArcList($typeid=0,$row=10,$col=1,$titlelen=30,$infolen=160,
					//$imgwidth=120,$imgheight=90,$listtype="all",$orderby="default",
					//$keyword="",$innertext="",$tablewidth="100",$arcid=0,$idlist="")
					if(trim($ctag->GetInnerText())!="") $listtmp = $ctag->GetInnerText();
					else $listtmp = $artlistTemp;
					$idvalue = $this->PartView->GetArcList(0,50,
					$ctag->GetAtt("col"),
					$ctag->GetAtt("titlelen"),
					$ctag->GetAtt("infolen"),
					$ctag->GetAtt("imgwidth"),
					$ctag->GetAtt("imgheight"),
					"all","default","",$listtmp,100,0,$idlist);
				}
				else{
					$idvalue = "";
				}
				$notestr = str_replace("~notename~",$notename,$tempStr);
				$notestr = str_replace("~spec_arclist~",$idvalue,$notestr);
				$rvalue .= $notestr;
			}
		}
		$dtp->Clear();
		return $rvalue;
	}
	
	//获得进入附件下载页面的链接
	//---------------------------------
	function GetAddLinkPage()
	{
		$phppath = $GLOBALS["cfg_plus_dir"];
		$downlinkpage = "";
		$tempStr = GetSysTemplets("channel/channel_downlinkpage.htm");
		$links = $phppath."/download.php?open=0&aid=".$this->ArcID."&cid=".$this->ChannelID;
		$downlinkpage = str_replace("~link~",$links,$tempStr);
		return $downlinkpage;
	}
	
	//获得附件的下载所有链接地址
	//-----------------------------------
	function GetAddLinks($fvalue)
	{
		$phppath = $GLOBALS["cfg_plus_dir"];
		$downlinks = "";
		$dtp = new DedeTagParse();
    $dtp->LoadSource($fvalue);
    if(!is_array($dtp->CTags)){
    	$dtp->Clear();
    	return "无链接信息！";
    }
    $tempStr = GetSysTemplets("channel/channel_downlinks.htm");
    foreach($dtp->CTags as $ctag){
    	if($ctag->GetName()=="link"){
    	  $links = trim($ctag->GetInnerText());
    	  //如果想保留跳转，请把此句还原
    	  //$links = $phppath."/download.php?open=1&link=".urlencode(base64_encode($links));
    	  $serverName = $ctag->GetAtt("text");
    	  $temp = str_replace("~link~",$links,$tempStr);
    	  $temp = str_replace("~server~",$serverName,$temp);
    	  $downlinks .= $temp;
      }
    }
    $dtp->Clear();
    return $downlinks;
	}
	
	//获得图片的展示页面
	//---------------------------
	function GetImgLinks($fvalue)
	{
		$revalue = "";
		$dtp = new DedeTagParse();
    $dtp->LoadSource($fvalue);
    if(!is_array($dtp->CTags)){
    	$dtp->Clear();
    	return "无图片信息！";
    }
    $ptag = $dtp->GetTag("pagestyle");
    if($ptag!=""){
    	$pagestyle = $ptag->GetAtt('value');
    	$maxwidth = $ptag->GetAtt('maxwidth');
    }
    else{
    	$pagestyle = 2;
    	$maxwidth = $GLOBALS['cfg_album_width'];
    }
    if($maxwidth=="") $maxwidth = $GLOBALS['cfg_album_width'];
    foreach($dtp->CTags as $ctag){
    	if($ctag->GetName()=="img"){
    		$iw = $ctag->GetAtt('width');
    		$ih = $ctag->GetAtt('heigth');
    		$alt = str_replace("'","",$ctag->GetAtt('text'));
    		$src = trim($ctag->GetInnerText());
    		if($iw=="") $iw = $GLOBALS['cfg_album_width'];;
    		if($iw > $maxwidth) $iw = $maxwidth;
    		if($revalue==""){
    			$revalue = "<center><a href='$src' target='_blank'><img src='$src' alt='$alt' width='$iw' border='0'/></a><br/>$alt<br/></center>\r\n";
    		}
    		else{
    			if($pagestyle==2) $revalue .= "#p#<center><a href='$src' target='_blank'><img src='$src' alt='$alt' width='$iw' border='0'/><br/>$alt<br/></center>\r\n";
    			else $revalue .= "<center><a href='$src' target='_blank'><img src='$src' alt='$alt' width='$iw' border='0'/><br/>$alt<br/></center>\r\n";
    		}
      }
    }
    return $revalue;
	}
	
	//处理引用的函数的字段
	//-----------------------------
	function EvalFunc($fvalue,$functionname)
	{
		$functionname = str_replace("{\"","[\"",$functionname);
		$functionname = str_replace("\"}","\"]",$functionname);
		$functionname = "\$fieldvalue = ".str_replace("@me",$fvalue,$functionname).";";
		eval($functionname);
		if(empty($fieldvalue)) return "";
		else return $fieldvalue;
	}
	
	//关闭所占用的资源
 	//------------------
 	function Close()
 	{
 		@$this->dsql->Close();
 	}
	
	
}//End  class ChannelUnit

?>