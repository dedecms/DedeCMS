<?
require_once("config_base.php");
//////////////////////////////////
//这个类用于管理投票
///////////////////////////////////
class DedeVote
{
	var $voteName="";
	var $baseDir="";
	var $phpDir="";
	var $voteFileName="";
	var $Items="";
	var $Count=-1;
	function SetVote($votename="")
	{
		global $base_dir;
		global $art_php_dir;
		$this->voteName=$votename;
		$this->baseDir=$base_dir;
		$this->phpDir=$art_php_dir;
		$this->voteFileName = $this->baseDir.$this->phpDir."/vote/".$this->voteName.".dat";
		$this->Items="";
		$this->Count=-1;
		$this->ParVoteNote();
	}
	//
	//分析投票内容的项目
	//
	function ParVoteNote()
	{
		$fp = fopen($this->voteFileName,"r") or die("读取投票数据失败！");
		flock($fp,2);
		$i = 0;
		while($line=fgets($fp,1024))
		{
			$line = trim($line);
			if($line!="")
			{
				$lines = split(">",trim($line));
				$lnum = count($lines);
				if($lnum==3)
				{
					$this->Items[$lines[0]]["count"]=$lines[1];
					$this->Items[$lines[0]]["name"]=$lines[2];
					$i++;
				}
			}
		}
		fclose($fp);
		$this->Count = $i;
	}
	//
	//获得投票项目总投票次数
	//
	function GetTotalCount()
	{
		if(!empty($this->Items[0]["count"])) return $this->Items[0]["count"];
		else return 0;
	}
	//
	//获得投票项目创建时间
	//
	function GetMakeTime()
	{
		if(!empty($this->Items[0]["name"])) return $this->Items[0]["name"];
		else return 0;
	}
	//
	//删除当前投票内容
	//
	function DelVote()
	{
		@unlink($this->voteFileName);
	}
	//
	//增加指定ID的票数
	//
	function AddVoteCount($ID)
	{
		if(isset($this->Items[$ID]["count"]))
		{
			$this->Items[$ID]["count"]=$this->Items[$ID]["count"]+1;
			if(isset($this->Items[0]["count"])) $this->Items[0]["count"]=$this->Items[0]["count"]+1;
		}
	}
	//
	//获得项目的投票表单
	//
	function GetVoteForm($lineheight=24,$tablewidth="100%",$titlebgcolor="#EDEDE2",$titlebackgroup="",$tablebg="#FFFFFF")
	{
		//省略参数
		if($lineheight=="") $lineheight=24;
		if($tablewidth=="") $tablewidth="100%";
		if($titlebgcolor=="") $titlebgcolor="#EDEDE2";
		if($titlebackgroup!="") $titlebackgroup="background='$titlebackgroup'";
		if($tablebg=="") $tablebg="#FFFFFF";
		
		$items = "<table width='$tablewidth' border='0' cellspacing='1' cellpadding='1' bgcolor='$tablebg'>\r\n";
		$items .= "<form name='voteform' method='get' action='".$this->phpDir."/vote.php' target='_blank'>\r\n";
		$items .= "<input type='hidden' name='id' value='$this->voteName'>\r\n";
		
		if($this->Count > 0)
		{
			foreach($this->Items as $key=>$value)
			{
				if($key==0)
					$items.="<tr><td height='$lineheight' bgcolor='$titlebgcolor' $titlebackgroup>".$this->voteName."</td></tr>\r\n";
				else
					$items.="<tr><td height=$lineheight><input type=radio name=voteitem value=".$key.">".$this->Items[$key]["name"]."</td></tr>\r\n";
			}
			$items.="<tr><td height='$lineheight' bgcolor='#FFFFFF'><input type='submit' style='width:40;background-color:$titlebgcolor;border:1px soild #818279' name='aaa1' value='投票'> <input type='button' style='width:80;background-color:$titlebgcolor;border:1px soild #818279' name='aaa2' value='查看结果' onClick=\"javascript:window.open('".$this->phpDir."/vote.php?job=view&id=".urlencode($this->voteName)."');\"></td></tr>\r\n";
		}
		
		$items.="</form>\r\n</table>\r\n";
		return $items;
	}
	//
	//重新写入数据文件
	//请不要在输出任何内容之前使用SaveVote()方法!
	//
	function SaveVote()
	{
		$items="";
		//检查用户是否已投过票，cookie大约保存约十天
		if(isset($_COOKIE["DEDE_VOTENAME"]))
		{
			if($_COOKIE["DEDE_VOTENAME"]==$this->voteName) return false;
			else
				setcookie("DEDE_VOTENAME",$this->voteName,time()+360000,"/");
		}
		else
		{
			setcookie("DEDE_VOTENAME",$this->voteName,time()+360000,"/");
		}
		if($this->Count > 0)
		{
			foreach($this->Items as $key=>$value)
			{
				$items .= $key.">".$this->Items[$key]["count"].">".$this->Items[$key]["name"]."\r\n";
			}
			$items = trim($items);
			$fp = fopen($this->voteFileName,"w") or die("写入投票数据时失败！");
			flock($fp,2);
			fwrite($fp,$items);
			fclose($fp);
		}
	}
	//
	//获得项目的投票结果
	//
	function GetVoteResult($tablewidth="600",$lineheight="24",$tablesplit="40%")
	{
		$res = "<table width='$tablewidth' border='0' cellspacing='1' cellpadding='1'>\r\n";
		$res .= "<tr height='8'><td width='$tablesplit'></td><td></td></tr>\r\n";
		if($this->Count>0)
		{
			foreach($this->Items as $key=>$value)
			{
				if($key!=0)
				{
					$res.="<tr height='$lineheight'><td style='border-bottom:1px solid'>".$key."、".$this->Items[$key]["name"]."</td>";
					$res.="<td style='border-bottom:1px solid'>投票数：".$this->Items[$key]["count"]."</td></tr>\r\n";
				}
			}
		}
		$res .= "<tr><td></td><td></td></tr>\r\n";
		$res .= "</table>\r\n";
		return $res;
	}
}
?>