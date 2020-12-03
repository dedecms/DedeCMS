<?
require_once(dirname(__FILE__)."/config_base.php");
require_once(dirname(__FILE__)."/pub_dedetag.php");
require_once(dirname(__FILE__)."/inc_channel_unit_functions.php");
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
	function MakeField($fname,$fvalue,$addvalue="")
	{
		if($fvalue==""){ $fvalue = $this->ChannelFields[$fname]["default"]; }
		if($this->ChannelFields[$fname]["function"]!=""){
			$fvalue = $this->EvalFunc($fvalue,$this->ChannelFields[$fname]["function"]);
		}
		//处理各种数据类型
		$ftype = $this->ChannelFields[$fname]["type"];
		if($ftype=="text"){
			$fvalue = ClearHtml($fvalue);
		}
		else if($ftype=="img"){
			$fvalue = $this->GetImgLinks($fvalue);
		}
		else if($ftype=="textdata"){
			if(!is_file($GLOBALS['cfg_basedir'].$fvalue)) return "";
			$fp = fopen($GLOBALS['cfg_basedir'].$fvalue,'r');
			$fvalue = "";
			while(!feof($fp)){ $fvalue .= fgets($fp,1024); }
			fclose($fp);
		}
		else if($ftype=="addon"){
			$foldvalue = $fvalue;
			$fvalue  = "<table width='300'><tr><td height='30' width='20'>";
			$fvalue .= "<a href='$foldvalue' target='_blank'><img src='".$GLOBALS['cfg_plus_dir']."/img/addon.gif' border='0' align='center'></a>";
			$fvalue .= "</td><td><a href='$foldvalue' target='_blank'><u>$foldvalue</u></a>";
			$fvalue .= "</td></tr></table>\r\n";
		}
		else if($ftype=="softlinks"){
			$fvalue = $this->GetAddLinkPage($fvalue);
		}
		else if($ftype=="specialtopic"){
			$fvalue = $this->GetSpecList($fname,$fvalue);
		}
		return $fvalue;
	}
	//获得专题文章的列表
	//--------------------------------
	function GetSpecList($fname,$noteinfo,$noteid="")
	{
		if(!isset($GLOBALS['__SpGetArcList'])) require_once(dirname(__FILE__)."/inc/inc_fun_SpGetArcList.php");
		if($noteinfo=="") return "";
		$rvalue = "";
		$tempStr = GetSysTemplets("channel/channel_spec_note.htm");
		$dtp = new DedeTagParse();
		$dtp->LoadSource($noteinfo);
		if(is_array($dtp->CTags))
		{
			foreach($dtp->CTags as $k=>$ctag){
				$notename = $ctag->GetAtt("name");
				if($noteid!="" && $ctag->GetAtt("noteid")!=$noteid){ continue; } //指定名称的专题节点
				$isauto = $ctag->GetAtt("isauto");
				$idlist = trim($ctag->GetAtt("idlist"));
				$rownum = trim($ctag->GetAtt("rownum"));
				if(empty($rownum)) $rownum = 40;
				$keywords = "";
				$stypeid = 0;
				
				if($isauto==1){
				  $idlist = "";
				  $keywords = trim($ctag->GetAtt("keywords"));
				  $stypeid = $ctag->GetAtt("typeid");
			  }
				
				if(trim($ctag->GetInnerText())!="") $listTemplet = $ctag->GetInnerText();
				else $listTemplet = GetSysTemplets("spec_arclist.htm");
				$idvalue = SpGetArcList($this->dsql,
				$stypeid,$rownum,$ctag->GetAtt("col"),
				$ctag->GetAtt("titlelen"),$ctag->GetAtt("infolen"),
				$ctag->GetAtt("imgwidth"),$ctag->GetAtt("imgheight"),
				"all","default",$keywords,$listTemplet,100,0,$idlist);
				
				$notestr = str_replace("~notename~",$notename,$tempStr);
				$notestr = str_replace("~spec_arclist~",$idvalue,$notestr);
				$rvalue .= $notestr;
				if($noteid!="" && $ctag->GetAtt("noteid")==$noteid){ break; }
			}
		}
		$dtp->Clear();
		return $rvalue;
	}
	
	//获得进入附件下载页面的链接
	//---------------------------------
	function GetAddLinkPage($fvalue)
	{
		$row = $this->dsql->GetOne("Select downtype From #@__softconfig");
		$phppath = $GLOBALS["cfg_plus_dir"];
		$downlinkpage = "";
		if($row['downtype']=='0'){
		   return $this->GetAddLinks($fvalue);
	  }else{
	  	 $tempStr = GetSysTemplets("channel/channel_downlinkpage.htm");
		   $links = $phppath."/download.php?open=0&aid=".$this->ArcID."&cid=".$this->ChannelID;
		   $downlinkpage = str_replace("~link~",$links,$tempStr);
		   return $downlinkpage;
	  }
	}
	
	//获得附件的下载所有链接地址
	//-----------------------------------
	function GetAddLinks($fvalue)
	{
		$row = $this->dsql->GetOne("Select ismoresite,sites,gotojump From #@__softconfig");
		$phppath = $GLOBALS['cfg_phpurl'];
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
    	  $serverName = trim($ctag->GetAtt("text"));
    	  if(!isset($firstLink)){ $firstLink = $links; }
    	  if($row['gotojump']==1) $links = $phppath."/download.php?open=1&link=".urlencode(base64_encode($links));
    	  $temp = str_replace("~link~",$links,$tempStr);
    	  $temp = str_replace("~server~",$serverName,$temp);
    	  $downlinks .= $temp;
      }
    }
    $dtp->Clear();
    //启用镜像功能的情况
    if($row['ismoresite']==1 && !empty($row['sites']) && isset($firstLink)){
    	$firstLink = eregi_replace($GLOBALS['cfg_basehost'],"",$firstLink);
    	$row['sites'] = ereg_replace("\n{1,}","\n",str_replace("\r","\n",$row['sites']));
    	$sites = explode("\n",trim($row['sites']));
    	foreach($sites as $site){
    		if(trim($site)=='') continue;
    		list($link,$serverName) = explode('|',$site);
    		$link = trim($link).$firstLink;
    		if($row['gotojump']==1) $link = $phppath."/download.php?open=1&link=".urlencode(base64_encode($link));
    	  $temp = str_replace("~link~",$link,$tempStr);
    	  $temp = str_replace("~server~",$serverName,$temp);
    	  $downlinks .= $temp;
    	}
    }
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
    if(is_object($ptag)){
    	$pagestyle = $ptag->GetAtt('value');
    	$maxwidth = $ptag->GetAtt('maxwidth');
    	$ddmaxwidth = $ptag->GetAtt('ddmaxwidth');
    	$irow = $ptag->GetAtt('row');
    	$icol = $ptag->GetAtt('col');
    	if(empty($maxwidth)) $maxwidth = $GLOBALS['cfg_album_width'];
    }else{
    	$pagestyle = 2;
    	$maxwidth = $GLOBALS['cfg_album_width'];
    	$ddmaxwidth = 200;
    }
    if($pagestyle == 3){
      if(empty($irow)) $irow = 4;
      if(empty($icol)) $icol = 4;
    }
    //遍历图片信息
    $mrow = 0;
    $mcol = 0;
    $photoid = 0;
    $images = array();
    foreach($dtp->CTags as $ctag){
    	if($ctag->GetName()=="img"){
    		$iw = $ctag->GetAtt('width');
    		$ih = $ctag->GetAtt('heigth');
    		$alt = str_replace("'","",$ctag->GetAtt('text'));
    		$src = trim($ctag->GetInnerText());
    		if($iw > $maxwidth) $iw = $maxwidth;
    		$iw = (empty($iw) ? "" : "width='$iw'");
    		//全部列出式或分页式图集
    		if($pagestyle<3){
    		   if($revalue==""){
    			   $revalue = "<center><a href='$src' target='_blank'><img src='$src' alt='$alt' $iw border='0'/></a><br/>$alt<br/></center>\r\n";
    		   }else{
    			   if($pagestyle==2) $revalue .= "#p#分页标题#e#<center><a href='$src' target='_blank'><img src='$src' alt='$alt' $iw border='0'/></a><br/>$alt<br/></center>\r\n";
    			   else $revalue .= "<center><a href='$src' target='_blank'><img src='$src' alt='$alt' $iw border='0'/></a><br/>$alt<br/></center>\r\n";
    		   }
    		//多列式图集
    		}else if($pagestyle==3){
    			$images[$photoid][0] = $src;
    			$images[$photoid][1] = $alt;
    			$photoid++;
    		}
      }
    }
    //重新运算多列式图集
    if($pagestyle==3){
    	if(empty($ddmaxwidth)) $ddmaxwidth = 200;
    	$picnum = count($images);
    	$sPos = 0;
    	if($icol==0) $icol = 1;
    	$tdwidth = ceil(100 / $icol);
    	while($sPos < $picnum){
    		$revalue .= "<table width='90%' border='0' cellpadding='5' cellspacing='1'>\r\n";
    		$revalue .= "<tr height='0'>\r\n";
    		for($j=0;$j < $icol;$j++){ $revalue .= "<td width='{$tdwidth}%'></td>\r\n"; }
    		$revalue .= "</tr>";
    		for($i=0;$i < $irow;$i++){
    			$revalue .= "<tr align='center'>\r\n";
    			for($j=0;$j < $icol;$j++){
    				if(!isset($images[$sPos])){ $revalue .= "<td></td>\r\n"; }
    				else{
    					$src = $images[$sPos][0];
    					$alt = $images[$sPos][1];
    					$revalue .= "<td valign='top'><a href='{$GLOBALS['cfg_phpurl']}/showphoto.php?aid={$this->ArcID}&src=".urlencode($src)."&npos=$sPos' target='_blank'><img src='$src' alt='$alt' width='$ddmaxwidth' border='0'/></a><br/>$alt\r\n</td>\r\n";
    					$sPos++;
    				}
    			}
    			$revalue .= "</tr>\r\n";
    			if(!isset($images[$sPos])) break;
    		}
    		if(!isset($images[$sPos])){
    		  $revalue .= "</table>";
    			break;
    		}else{
    			$revalue .= "</table>#p#分页标题#e#";
    		}
    	}
    }
    unset($dtp);
    unset($images);
    return $revalue;
	}
	
	//处理引用的函数的字段
	//-----------------------------
	function EvalFunc($fvalue,$functionname)
	{
		$DedeMeValue = $fvalue;
		$phpcode = preg_replace("/'@me'|\"@me\"|@me/isU",'$DedeMeValue',$functionname);
		eval($phpcode.";");
		return $DedeMeValue;
	}
	
	//关闭所占用的资源
 	//------------------
 	function Close(){
 		$this->dsql->Close();
 	}
	
}//End  class ChannelUnit
?>