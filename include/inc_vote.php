<?php 
require_once(dirname(__FILE__)."/config_base.php");
require_once(dirname(__FILE__)."/pub_dedetag.php");
//////////////////////////////////
//这个类用于管理投票
///////////////////////////////////
class DedeVote
{
	var $VoteInfos;
	var $VoteNotes;
	var $VoteCount;
	var $VoteID;
	var $dsql;
	//-------------------------------
	//php5构造函数
	//-------------------------------
	function __construct($aid)
 	{
		$this->dsql = new DedeSql(false);
		$this->VoteInfos = $this->dsql->GetOne("Select * From #@__vote where aid='$aid'");
		$this->VoteNotes = Array();
		$this->VoteCount = 0;
		$this->VoteID = $aid;
		if(!is_array($this->VoteInfos)) return;
		$dtp = new DedeTagParse();
		$dtp->SetNameSpace("v","<",">");
		$dtp->LoadSource($this->VoteInfos['votenote']);
		if(is_array($dtp->CTags)){
			foreach($dtp->CTags as $ctag){
				$this->VoteNotes[$ctag->GetAtt('id')]['count'] = $ctag->GetAtt('count');
				$this->VoteNotes[$ctag->GetAtt('id')]['name'] = trim($ctag->GetInnerText());
				$this->VoteCount++;
			}
		}
		$dtp->Clear();
	}
	function DedeVote($aid)
	{
		$this->__construct($aid);
	}
	function Close()
	{
		$this->dsql->Close();	
	}
	//---------------------------
	//获得投票项目总投票次数
	//---------------------------
	function GetTotalCount()
	{
		if(!empty($this->VoteInfos["totalcount"])) return $this->VoteInfos["totalcount"];
		else return 0;
	}
	//---------------------------
	//增加指定的投票节点的票数
	//---------------------------
	function AddVoteCount($aid)
	{
		if(isset($this->VoteNotes[$aid])){ $this->VoteNotes[$aid]['count']++; }
	}
	//----------------------------
	//获得项目的投票表单
	//----------------------------
	function GetVoteForm($lineheight=24,$tablewidth="100%",$titlebgcolor="#EDEDE2",$titlebackgroup="",$tablebg="#FFFFFF",$itembgcolor="#FFFFFF")
	{
		//省略参数
		if($lineheight=="") $lineheight=24;
		if($tablewidth=="") $tablewidth="100%";
		if($titlebgcolor=="") $titlebgcolor="#EDEDE2";
		if($titlebackgroup!="") $titlebackgroup="background='$titlebackgroup'";
		if($tablebg=="") $tablebg="#FFFFFF";
		if($itembgcolor=="") $itembgcolor="#FFFFFF";
		
		$items = "<table width='$tablewidth' border='0' cellspacing='1' cellpadding='1' bgcolor='$tablebg'>\r\n";
		$items .= "<form name='voteform' method='post' action='".$GLOBALS['cfg_plus_dir']."/vote.php' target='_blank'>\r\n";
		$items .= "<input type='hidden' name='dopost' value='send'>\r\n";
		$items .= "<input type='hidden' name='aid' value='".$this->VoteID."'>\r\n";
		$items .= "<input type='hidden' name='ismore' value='".$this->VoteInfos['ismore']."'>\r\n";
		$items.="<tr align='center'><td height='$lineheight' bgcolor='$titlebgcolor' $titlebackgroup>".$this->VoteInfos['votename']."</td></tr>\r\n";
		if($this->VoteCount > 0)
		{
			
			foreach($this->VoteNotes as $k=>$arr){
				 if($this->VoteInfos['ismore']==0) $items.="<tr><td height=$lineheight bgcolor=$itembgcolor><input type='radio' name='voteitem' value='$k'>".$arr['name']."</td></tr>\r\n";
				 else $items.="<tr><td height=$lineheight bgcolor=$itembgcolor><input type=checkbox name='voteitem[]' value='$k'>".$arr['name']."</td></tr>\r\n";
			}
			$items .= "<tr><td height='$lineheight' bgcolor='#FFFFFF'>\r\n";
			$items .= "<input type='submit' style='width:40;background-color:$titlebgcolor;border:1px soild #818279' name='vbt1' value='投票'>\r\n";
			$items .= "<input type='button' style='width:80;background-color:$titlebgcolor;border:1px soild #818279' name='vbt2' ";
			$items .= "value='查看结果' onClick=\"window.open('".$GLOBALS['cfg_plus_dir']."/vote.php?dopost=view&aid=".$this->VoteID."');\"></td></tr>\r\n";
		}
		
		$items.="</form>\r\n</table>\r\n";
		return $items;
	}
	//------------------------------------
	//保存投票数据
	//请不要在输出任何内容之前使用SaveVote()方法!
	//-------------------------------------
	function SaveVote($voteitem)
	{
		if(empty($voteitem)) return "你没选中任何项目！";
		$items="";
		//检查投票是否已过期
		$nowtime = time();
		if($nowtime > $this->VoteInfos['endtime']) return "投票已经过期！";
		if($nowtime < $this->VoteInfos['starttime']) return "投票还没有开始！";
		//检查用户是否已投过票，cookie大约保存约十天
		if(isset($_COOKIE["DEDE_VOTENAME_AAA"])){
			if($_COOKIE["DEDE_VOTENAME_AAA"]==$this->VoteInfos['aid']) return "你已经投过票！";
			else setcookie("DEDE_VOTENAME_AAA",$this->VoteInfos['aid'],time()+360000,"/");
		}
		else{
			setcookie("DEDE_VOTENAME_AAA",$this->VoteInfos['aid'],time()+360000,"/");
		}
		//必须存在投票项目
		if($this->VoteCount > 0)
		{
			foreach($this->VoteNotes as $k=>$v)
			{
				if($this->VoteInfos['ismore']==0){ //单选项
					if($voteitem == $k){ $this->VoteNotes[$k]['count']++; break; }
				}
				else{ //多选项
				  if(is_array($voteitem) && in_array($k,$voteitem)){ $this->VoteNotes[$k]['count']++; }
				}
			}
			foreach($this->VoteNotes as $k=>$arr){
				$items .= "<v:note id='$k' count='".$arr['count']."'>".$arr['name']."</v:note>\r\n";
			}
		}
		$this->dsql->SetQuery("Update #@__vote set totalcount='".($this->VoteInfos['totalcount']+1)."',votenote='".addslashes($items)."' where aid='".$this->VoteID."'");
		$this->dsql->ExecuteNoneQuery();
		return "投票成功！";
	}
	//
	//获得项目的投票结果
	//
	function GetVoteResult()
	{
		$totalcount = $this->VoteInfos['totalcount'];
		if($totalcount==0) $totalcount=1;
		$i=1;
		$res = "";
		foreach($this->VoteNotes as $k=>$arr){
			$c = $arr['count'];
			$vtwidth =round(($c/$totalcount)*100)."%";
			if($c/$totalcount>0.2){
				$res .="<dl><dt>".$i."、".$arr['name']."</dt><dd><span style=\"width:".$vtwidth.";\"><strong>".round(($c/$totalcount)*100)."%</strong>($c)</span></dd></dl>\n\r";
			}else{
				$res .="<dl><dt>".$i."、".$arr['name']."</dt><dd><span style=\"width:".$vtwidth.";\"></span><strong>".round(($c/$totalcount)*100)."%</strong>($c)</dd></dl>\n\r";
			}
			$i++;
		}
		return $res;
	}
}
?>