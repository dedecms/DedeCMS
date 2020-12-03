<?php
$cfg_needFilter = true;
require_once(dirname(__FILE__)."/../include/inc_memberlogin.php");
//获得当前脚本名称，如果你的系统被禁用了$_SERVER变量，请自行更改这个选项
$dedeNowurl = "";
$s_scriptName="";
$dedeNowurl = GetCurUrl();
$dedeNowurls = explode("?",$dedeNowurl);
$s_scriptName = $dedeNowurls[0];
$cfg_ml = new MemberLogin();
$Honor = $cfg_ml->M_Honor;
if(empty($Honor)) $Honor = "未授衔";
//$cfg_ml->PutLoginInfo($cfg_ml->M_ID);
class PubmemberContent
{
	var $db;
	var $CacheArray;
	var $rs;
	//-------------------------------
	//php5构造函数
	//-------------------------------
	function __construct()
	{
		$this->db = new DedeSql(false);
		$this->CacheArray = Array();
		$this->rs = Array();
	}
	
	function PubmemberContent()
	{
		$this->__construct();
	}
	
	function SetQuery($sql){
		$this->db->SetQuery($sql);
		$this->db->Execute();
		while($this->rs = $this->db->GetArray()) array_push ($this->CacheArray,$this->rs);
		$temp = $this->CacheArray;
		$this->CacheArray = Array();
		$this->rs = Array();
		return $temp;
	}
	function Close(){
		$this->db->Close();
	}
}
function DedeID2Dir2($aid)
{
	$n = ceil($aid / 1000);
	return $n;
}
function GetBookUrl2($bid,$title,$gdir=0)
{
	global $cfg_cmspath;
	if($gdir==1) $bookurl = "{$cfg_cmspath}/book/".DedeID2Dir2($bid);
	else $bookurl = "{$cfg_cmspath}/book/".DedeID2Dir2($bid).'/'.GetPinyin($title).'-'.$bid.'.html';
	return $bookurl;
}

?>