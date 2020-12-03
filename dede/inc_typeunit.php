<?
//class TypeUnit
//这个类主要是封装频道管理时的一些复杂操作 
//
require_once("config_base.php");
class TypeUnit
{
	var $con;
	var $artDir;
	var $baseDir;
	var $idCounter=0;
	var $idArrary;
	var $sunID;
	var $shortName=".htm";
	function TypeUnit()
	{
		global $art_shortname;
		global $art_dir;
		global $base_dir;
		global $art_shortname;
		$this->con = connectMySql();
		$this->artDir = $art_dir;
		$this->baseDir = $base_dir;
		$this->shortName = $art_shortname;
	}
	//
	//----读出所有分类,在类目管理页(list_type)中使用----------
	//
	function ListAllType($chennel=0,$nowdir=0)
	{
		if($chennel>0)
			$rs = mysql_query("Select * From dede_arttype where ID=$chennel",$this->con);
		else
			$rs = mysql_query("Select * From dede_arttype where reID=0",$this->con);
		while($row=mysql_fetch_object($rs))
		{	 
			$typeDir = $row->typedir;
			$typeName = $row->typename;
			$ID = $row->ID;
			echo "<table width='100%' border='0' cellspacing='0' cellpadding='2'>\r\n";
			echo "<tr bgcolor='#F5F5F5'>\r\n";
			echo "<td width='1%' style='cursor:hand' onClick='showHide(this);'>+</td>\r\n";
			echo "<td>";
			echo "<input type='checkbox' name='typeinfo' value='$ID`$typeName' class='np'>";
			echo "<a href='#' onClick='showHide(this);'><u>$typeName</u></a>"."[ID:".$ID."]";
			echo "</td></tr>\r\n";
			echo "<tr style='display: none'><td colspan='2'>\r\n";
			echo "<table width='100%' border='0' cellspacing='0' cellpadding='0'>\r\n";
			if($nowdir == -1) $this->ListAllSunType($ID,"　");
			else if($nowdir == $ID) $this->ListAllSunType($ID,"　");
			echo "</table>\r\n</td></tr>\r\n";
			echo "</table>";
		}
	}
	//获得子类目的递归调用
	function ListAllSunType($ID,$step)
	{
		$rs = mysql_query("Select * From dede_arttype where reID=".$ID,$this->con);
		if(mysql_num_rows($rs)>0)
		{
		while($row=mysql_fetch_object($rs))
		{
			$typeDir = $row->typedir;
			$typeName = $row->typename;
			$reID = $row->reID;
			$ID = $row->ID;
			if($step=="　") $stepdd = 2;
			else $stepdd = 3;
			echo "<tr><td>$step<input type='checkbox' name='typeinfo' value='$ID`$typeName' class='np'>\r\n";
			echo "<u>$typeName</u>"."[ID:".$ID."]";
			echo "</td></tr>\r\n";
			$this->ListAllSunType($ID,$step."　");
		}
		}
	}
	//
	//------删除一个类目-----------------------
	//
	function DelType($ID,$isDelFile)
	{
		$this->GetSunTypes($ID);
		if($isDelFile=="yes")
		{
			$rs = mysql_query("select typedir from dede_arttype where ID=$ID",$this->con);
			$row=mysql_fetch_object($rs);
			@$this->DelFiles($ID);
			$this->DelDir($this->baseDir.$this->artDir."/".$row->typedir);
		}
		foreach($this->idArray as $id)
		{
			mysql_query("Delete From dede_arttype where ID=$id",$this->con);
		}
		@reset($this->idArray);
	}
	//
	//--删除这个类目的静态文章------------------
	//
	function DelFiles($ID)
	{
		foreach($this->idArray as $id)
		{
			mysql_query("Delete From dede_art where typeid=$id",$this->con);
			mysql_query("Delete From dede_spec where typeid=$id",$this->con);
		}
		@reset($this->idArray);
	}
	//
	//-----返回与某个目相关的下级目录的类目ID列表(删除类目或文章时调用)
	//
	function GetSunTypes($ID)
	{
		$this->idArray[$this->idCounter]=$ID;
		$this->idCounter++;
		$rs = mysql_query("Select ID From dede_arttype where reID=$ID",$this->con);
		if(mysql_num_rows($rs)!=0)
		{
			while($row=mysql_fetch_object($rs))
			{
				$nid = $row->ID;
				$this->GetSunTypes($nid);
			}
		}
	}
	//获得某ID的下级ID(包括本身)的SQL语句“($tb.typeid=id1 or $tb.typeid=id2...)”
	function GetSunID($ID,$tb="dede_art")
	{
		$this->sunID = "";
		$this->ParSunID($ID,$tb);
		return "(".$tb.".typeid=$ID".$this->sunID.")";
	}
	function ParSunID($ID,$tb="art")
	{
		$rs = mysql_query("Select ID From dede_arttype where reID=$ID",$this->con);
		if(mysql_num_rows($rs)>0)
		{
			while($row=mysql_fetch_object($rs))
			{
				$NID = $row->ID;
				$this->sunID.=" or ".$tb.".typeid=$NID";
				$this->ParSunID($NID,$tb);
			}
		}
	}
	//
	//---- 删除指定目录的所有文件------------------
	//
	function DelDir($indir)
	{
		$this->RmDirFile($indir);
		@rmdir($indir);
	}
	function RmDirFile($indir)
	{
   		$dh = dir($indir);
   		while($file = $dh->read()) {
      		if($file == "." || $file == "..") continue;
      		else if(is_file("$indir/$file")) unlink("$indir/$file");
     	 	else
      		{
         		RmDirFile("$indir/$file");
      		}
      		if(is_dir("$indir/$file")){
         		@rmdir("$indir/$file");
      		}
   		}
   		$dh->close();
   		return(1);
	}
}
?>