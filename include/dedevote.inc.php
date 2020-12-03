<?php
if(!defined('DEDEINC'))
{
	exit("Request Error!");
}
require_once(DEDEINC."/dedetag.class.php");

class DedeVote
{
	var $VoteInfos;
	var $VoteNotes;
	var $VoteCount;
	var $VoteID;
	var $dsql;

	//php5构造函数
	function __construct($aid)
	{
		$this->dsql = $GLOBALS['dsql'];
		$this->VoteInfos = $this->dsql->GetOne("Select * From `#@__vote` where aid='$aid'");
		$this->VoteNotes = Array();
		$this->VoteCount = 0;
		$this->VoteID = $aid;
		if(!is_array($this->VoteInfos))
		{
			return;
		}
		$dtp = new DedeTagParse();
		$dtp->SetNameSpace("v","<",">");
		$dtp->LoadSource($this->VoteInfos['votenote']);
		if(is_array($dtp->CTags))
		{
			foreach($dtp->CTags as $ctag)
			{
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
	}

	//获得投票项目总投票次数
	function GetTotalCount()
	{
		if(!empty($this->VoteInfos["totalcount"]))
		{
			return $this->VoteInfos["totalcount"];
		}
		else
		{
			return 0;
		}
	}

	//增加指定的投票节点的票数
	function AddVoteCount($aid)
	{
		if(isset($this->VoteNotes[$aid]))
		{
			$this->VoteNotes[$aid]['count']++;
		}
	}

	//获得项目的投票表单
	function GetVoteForm($lineheight=24,$tablewidth="100%",$titlebgcolor="#EDEDE2",$titlebackgroup="",$tablebg="#FFFFFF",$itembgcolor="#FFFFFF")
	{
		//省略参数
		if($lineheight=="")
		{
			$lineheight=24;
		}
		if($tablewidth=="")
		{
			$tablewidth="100%";
		}
		if($titlebgcolor=="")
		{
			$titlebgcolor="#EDEDE2";
		}
		if($titlebackgroup!="")
		{
			$titlebackgroup="background='$titlebackgroup'";
		}
		if($tablebg=="")
		{
			$tablebg="#FFFFFF";
		}
		if($itembgcolor=="")
		{
			$itembgcolor="#FFFFFF";
		}
		$items = "<table width='$tablewidth' border='0' cellspacing='1' cellpadding='1' bgcolor='$tablebg'>\r\n";
		$items .= "<form name='voteform' method='post' action='".$GLOBALS['cfg_phpurl']."/vote.php' target='_blank'>\r\n";
		$items .= "<input type='hidden' name='dopost' value='send'>\r\n";
		$items .= "<input type='hidden' name='aid' value='".$this->VoteID."'>\r\n";
		$items .= "<input type='hidden' name='ismore' value='".$this->VoteInfos['ismore']."'>\r\n";
		$items.="<tr align='center'><td height='$lineheight' bgcolor='$titlebgcolor' $titlebackgroup>".$this->VoteInfos['votename']."</td></tr>\r\n";
		if($this->VoteCount > 0)
		{
			foreach($this->VoteNotes as $k=>$arr)
			{
				if($this->VoteInfos['ismore']==0)
				{
					$items.="<tr><td height=$lineheight bgcolor=$itembgcolor><input type='radio' name='voteitem' value='$k'>".$arr['name']."</td></tr>\r\n";
				}
				else
				{
					$items.="<tr><td height=$lineheight bgcolor=$itembgcolor><input type=checkbox name='voteitem[]' value='$k'>".$arr['name']."</td></tr>\r\n";
				}
			}
			$items .= "<tr><td height='$lineheight' bgcolor='#FFFFFF'>\r\n";
			$items .= "<input type='submit' style='width:40;background-color:$titlebgcolor;border:1px soild #818279' name='vbt1' value='投票'>\r\n";
			$items .= "<input type='button' style='width:80;background-color:$titlebgcolor;border:1px soild #818279' name='vbt2' ";
			$items .= "value='查看结果' onClick=\"window.open('".$GLOBALS['cfg_phpurl']."/vote.php?dopost=view&aid=".$this->VoteID."');\"></td></tr>\r\n";
		}

		$items.="</form>\r\n</table>\r\n";
		return $items;
	}

	//保存投票数据
	//请不要在输出任何内容之前使用SaveVote()方法!
	function SaveVote($voteitem)
	{
		if(empty($voteitem))
		{
			return '你没选中任何项目！';
		}
		$items = '';

		//检查投票是否已过期
		$nowtime = time();
		if($nowtime > $this->VoteInfos['endtime'])
		{
			return '投票已经过期！';
		}
		if($nowtime < $this->VoteInfos['starttime'])
		{
			return '投票还没有开始！';
		}

		//检查用户是否已投过票，cookie大约保存约十天
		if(isset($_COOKIE['DEDE_VOTENAME_AAA']))
		{
			if($_COOKIE['DEDE_VOTENAME_AAA']==$this->VoteInfos['aid'])
			{
				return '你已经投过票！';
			}
			else
			{
				setcookie('DEDE_VOTENAME_AAA',$this->VoteInfos['aid'],time()+360000,'/');
			}
		}
		else
		{
			setcookie('DEDE_VOTENAME_AAA',$this->VoteInfos['aid'],time()+360000,'/');
		}

		//必须存在投票项目
		if($this->VoteCount > 0)
		{
			foreach($this->VoteNotes as $k=>$v)
			{
				if($this->VoteInfos['ismore']==0)
				{
					//单选项
					if($voteitem == $k)
					{
						$this->VoteNotes[$k]['count']++; break;
					}
				}
				else
				{
					//多选项
					if(is_array($voteitem) && in_array($k,$voteitem))
					{
						$this->VoteNotes[$k]['count']++;
					}
				}
			}
			foreach($this->VoteNotes as $k=>$arr)
			{
				$items .= "<v:note id='$k' count='".$arr['count']."'>".$arr['name']."</v:note>\r\n";
			}
		}
		$this->dsql->ExecuteNoneQuery("Update `#@__vote` set totalcount='".($this->VoteInfos['totalcount']+1)."',votenote='".addslashes($items)."' where aid='".$this->VoteID."'");
		return "投票成功！";
	}

	//获得项目的投票结果
	function GetVoteResult($tablewidth="600",$lineheight="24",$tablesplit="40%")
	{
		$totalcount = $this->VoteInfos['totalcount'];
		if($totalcount==0)
		{
			$totalcount=1;
		}
		$res = "<table width='$tablewidth' border='0' cellspacing='1' cellpadding='1'>\r\n";
		$res .= "<tr height='8'><td width='$tablesplit'></td><td></td></tr>\r\n";
		$i=1;
		foreach($this->VoteNotes as $k=>$arr)
		{
			$res .= "<tr height='$lineheight'><td style='border-bottom:1px solid'>".$i."、".$arr['name']."</td>";
			$c = $arr['count'];
			$res .= "<td style='border-bottom:1px solid'>
			<table border='0' cellspacing='0' cellpadding='2' width='".(($c/$totalcount)*100)."%'><tr><td height='16' background='img/votebg.gif' style='border:1px solid #666666;font-size:9pt;line-height:110%'>".$arr['count']."</td></tr></table>
			</td></tr>\r\n";
		}
		$res .= "<tr><td></td><td></td></tr>\r\n";
		$res .= "</table>\r\n";
		return $res;
	}
}
?>