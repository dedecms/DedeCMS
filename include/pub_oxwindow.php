<?php 
require_once(dirname(__FILE__)."/pub_dedetag.php");
class OxWindow
{
	var $myWin = "";
	var $myWinItem = "";
	var $checkCode = "";
	var $formName = "";
	var $tmpCode = "//checkcode";
	var $hasStart = false;
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
		$this->myWin .= "<form name='$formname' method='$formmethod' onSubmit='return CheckSubmit();' action='$formaction'>\r\n";
	}
	//-------------------------------
	//增加隐藏域
	//-------------------------------
	function AddHidden($iname,$ivalue){
		$this->myWin .= "<input type='hidden' name='$iname' value='$ivalue'>\r\n";
	}
	function StartWin()
	{
		$this->myWin .= "<table width='100%'  border='0' cellpadding='3' cellspacing='1' bgcolor='#A5D0F1'>\r\n";
	}
	//-----------------------------
	//增加一个两列的行
	//-----------------------------
	function AddItem($iname,$ivalue)
	{
		$this->myWinItem .= "<tr bgcolor='#FFFFFF'>\r\n";
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
		$this->myWinItem .= "<tr bgcolor='#FFFFFF'>\r\n";
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
		$this->myWinItem .= "<tr bgcolor='#D2EFFD'>\r\n";
    $this->myWinItem .= "<td $colspan background='img/wbg.gif'><font color='#666600'><b>$title</b></font></td>\r\n";
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
			if($wintype!="hand")
			{
			$this->myWin .= "
<tr>
<td colspan='2' bgcolor='#D2EFFD'>
<table width='270' border='0' cellpadding='0' cellspacing='0'>
<tr align='center'>
<td width='90'><input name='imageField1' type='image' class='np' src='img/button_".$wintype.".gif' width='60' height='22' border='0'></td>
<td width='90'><a href='#'><img class='np' src='img/button_reset.gif' width='60' height='22' border='0' onClick='this.form.reset();return false;'></a></td>
<td><a href='#'><img src='img/button_back.gif' width='60' height='22' border='0' onClick='history.go(-1);'></a></td>
</tr>
</table>
</td>
</tr>";
			}
			else
			{
			$this->myWin .= "
<tr>
<td bgcolor='#CBE4FE'>
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
		$ctp = new DedeTagParse();
		if($modfile=="") $ctp->LoadTemplate(dirname(__FILE__)."/win_templet.htm");
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
?>