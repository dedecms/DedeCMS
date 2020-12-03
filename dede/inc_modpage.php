<?
require_once("config_base.php");
//这个文件主要要用于获取模板的操作
class modPage
{
	var $BaseDir="";
	var $ModDir="";
	var $ArtDir="";
	function modPage()
	{
		global $base_dir;
		global $mod_dir;
		global $art_dir;
		$this->BaseDir = $base_dir;
		$this->ModDir = $mod_dir;
		$this->ArtDir = $art_dir;
	}
	//
	//获得指定模板的绝对路径
	//
	function GetFullName($hname="默认模板",$typename="文章",$channeltype="1")
	{
		return $this->BaseDir.$this->ModDir."/$hname/".$channeltype."/".$typename.".htm";
	}
	//
	//获取模板文件夹里的模板列表
	//
	function GetModArray($hname="")
	{
		$i=0;
		if($hname!="")
		{
			$ds[0] = $hname;
			if($hname!="默认模板")
			{
				$i++;
				$ds[$i] = "默认模板";
			}
		}
		else
			$ds[0] = "默认模板";
		$i++;
		$mpath = $this->BaseDir.$this->ModDir;
        $dh = dir($mpath);
        while($filename=$dh->read())
        {
            if(!ereg("^\.|低层模板|默认模板|images|img|dedeimg|主页向导",$filename)&&$filename!=$hname&&is_dir($mpath."/".$filename))
            {
            	$ds[$i] = $filename;
            	$i++;
            }
        }
        return $ds;
	}
	//
	//获取模板文件夹里的模板列表
	//
	function GetHomePageArrays($hname="")
	{
		$i=0;
		if($hname!="")
		{
			$ds[0] = $hname;
			$i++;
		}
		$mpath = $this->BaseDir.$this->ModDir."/主页向导";
        $dh = dir($mpath);
        while($filename=$dh->read())
        {
            if(ereg("\.htm$|\.html$",$filename)&&$filename!=$hname)
            {
            	$ds[$i] = $filename;
            	$i++;
            }
        }
        return $ds;
	}
}
?>