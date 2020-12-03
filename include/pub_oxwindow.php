<?php 
require_once(dirname(__FILE__)."/pub_dedetag.php");
class OxWindow
{
	var $myWin = '';
	var $myWinItem = '';
	var $checkCode = '';
	var $formName = '';
	var $tmpCode = '//checkcode';
	var $hasStart = false;
	var $mainTitle = '';
	//---------------------------------
	//初始化为含表单的页面
	//---------------------------------
	function Init($formaction="",$checkScript="js/blank.js",$formmethod="POST",$formname="myform")
	{
		$this->myWin .= "<script language='javascript'>\r\n";
		if($checkScript!="" && file_exists($checkScript)){
			$fp = fopen($checkScript,"r");
			$this->myWin .= fread($fp,filesize($checkScript));
			fclose($fp);
		}
		else{
			$this->myWin .= "<!-- function CheckSubmit()\r\n{ return true; } -->";
		}
		$this->myWin .= "</script>\r\n";
		$this->formName = $formname;
		$isupload = '';
		if($formmethod=='data')
		{
			$formmethod = 'post';
			$isupload = " enctype='multipart/form-data' ";
		}
		$this->myWin .= "<form name='$formname' method='$formmethod'{$isupload} onSubmit='return CheckSubmit();' action='$formaction'>\r\n";
	}
	//-------------------------------
	//增加隐藏域
	//-------------------------------
	function AddHidden($iname,$ivalue){
		$this->myWin .= "<input type='hidden' name='$iname' value='$ivalue'>\r\n";
	}
	function StartWin()
	{
		$this->myWin .= "<table width='100%' border='0' cellpadding='1' cellspacing='1' align='center' class='tbtitle' style='background:#E2F5BC;'>\r\n";
	}
	//-----------------------------
	//增加一个两列的行
	//-----------------------------
	function AddItem($iname,$ivalue)
	{
		$this->myWinItem .= "<tr>\r\n";
    $this->myWinItem .= "<td width='25%'>$iname</td>\r\n";
    $this->myWinItem .= "<td width='75%'>$ivalue</td>\r\n";
    $this->myWinItem .= "</tr>\r\n";
	}
	//---------------------------
	//增加一个单列的消息行
	//---------------------------
	function AddMsgItem($ivalue,$height="100",$col="2")
	{
		if($height!=""&&$height!="0") $height = " height='$height'";
		else $height="";
		if($col!=""&&$col!=0) $colspan="colspan='$col'";
		else $colspan="";
		$this->myWinItem .= "<tr>\r\n";
    $this->myWinItem .= "<td $colspan $height> $ivalue </td>\r\n";
    $this->myWinItem .= "</tr>\r\n";
	}
	//-------------------------------
	//增加单列的标题行
	//-------------------------------
	function AddTitle($title,$col="2")
	{
		if($col!=""&&$col!="0") $colspan="colspan='$col'";
		else $colspan="";
		$this->myWinItem .= "<tr>\r\n";
    $this->myWinItem .= "<td $colspan ><font color='#666600'><b>$title</b></font></td>\r\n";
    $this->myWinItem .= "</tr>\r\n";
	}
	//----------------------
	//结束Window
	//-----------------------
	function CloseWin($isform=true)
	{
		if(!$isform) $this->myWin .= "</table>\r\n";
		else $this->myWin .= "</table></form>\r\n";
	}
	
	//-------------------------
	//增加自定义JS脚本
	//-------------------------
	function SetCheckScript($scripts)
	{
		$pos = strpos($this->myWin,$this->tmpCode);
		if($pos>0) $this->myWin = substr_replace($this->myWin,$scripts,$pos,strlen($this->tmpCode));
	}
	
	//----------------------
	//获取窗口
	//-----------------------
	function GetWindow($wintype="save",$msg="",$isform=true)
	{
		$this->StartWin();
		$this->myWin .= $this->myWinItem;
		if($wintype!="")
		{
			if($wintype=="okonly")
			{
			$this->myWin .= "
<tr>
<td colspan='2' >
<table width='270' border='0' cellpadding='0' cellspacing='0'>
<tr align='center'>
<td width='90'><input name='imageField1' type='image' class='np' src='img/button_ok.gif' width='60' height='22' border='0' style='border:0px'></td>
<td><a href='#'><img src='img/button_back.gif' width='60' height='22' border='0' onClick='history.go(-1);' style='border:0px'></a></td>
</tr>
</table>
</td>
</tr>";
			}
			else if($wintype!="hand")
			{
			$this->myWin .= "
<tr>
<td colspan='2' >
<table width='270' border='0' cellpadding='0' cellspacing='0'>
<tr align='center'>
<td width='90'><input name='imageField1' type='image' class='np' src='img/button_".$wintype.".gif' width='60' height='22' border='0' style='border:0px'></td>
<td width='90'><a href='#'><img class='np' src='img/button_reset.gif' width='60' height='22' border='0' onClick='this.form.reset();return false;' style='border:0px'></a></td>
<td><a href='#'><img src='img/button_back.gif' width='60' height='22' border='0' onClick='history.go(-1);' style='border:0px'></a></td>
</tr>
</table>
</td>
</tr>";
			}
			else
			{
			$this->myWin .= "
<tr>
<td>
$msg
</td>
</tr>";
			}
		}
		$this->CloseWin($isform);
		return $this->myWin;
	}
	//----------------------
	//显示页面
	//----------------------
	function Display($modfile="")
	{
		global $cfg_templets_dir,$cfg_basedir,$maintitle,$winform;
		if(empty($this->mainTitle)) $maintitle = '通用对话框';
		else $maintitle = $this->mainTitle;
		if(empty($winform)) $winform = $this->myWin;
		if(empty($cfg_templets_dir)) $cfg_templets_dir = dirname(__FILE__)."/../templets";
		else $cfg_templets_dir = $cfg_basedir.$cfg_templets_dir;
		$ctp = new DedeTagParse();
		if($modfile=="") $ctp->LoadTemplate($cfg_templets_dir."/win_templet.htm");
		else $ctp->LoadTemplate($modfile);
		$emnum = $ctp->Count;
		for($i=0;$i<=$emnum;$i++)
		{
			if(isset($GLOBALS[$ctp->CTags[$i]->GetTagName()]))
			{
				$ctp->Assign($i,$GLOBALS[$ctp->CTags[$i]->GetTagName()]);
			}
		}
		$ctp->Display();
		$ctp->Clear();
	}
}

/*------
显示一个不带表单的普通提示
-------*/
function ShowMsgWin($msg,$title)
{
  $win = new OxWindow();
  $win->Init();
  $win->mainTitle = "DeDeCms系统提示：";
	$win->AddTitle($title);
	$win->AddMsgItem("<div style='padding-left:20px;line-height:150%'>$msg</div>");
	$winform = $win->GetWindow("hand");
	$win->Display();
}
?>