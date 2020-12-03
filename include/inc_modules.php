<?php
/*-------------------------
Dede 模块管理
--------------------------*/
require_once(dirname(__FILE__)."/pub_dedeattribute.php");

class DedeModule
{
	var $modulesPath;
	var $modules;
	var $fileListNames;
	function __construct($modulespath='')
 	{
 		$this->fileListNames = array();
 		$this->modulesPath = $modulespath;
 	}
	function DedeModule($modulespath='')
 	{
 		$this->__construct($modulespath);
 	}

	//
	//枚举系统里已经存在的模块
	//
	function GetModuleList()
	{
		if(is_array($this->modules)) return $this->modules;
		$dh = dir($this->modulesPath) or die("没找到模块目录：({$this->modulesPath})！");
		$fp = @fopen($this->modulesPath.'/modulescache.php','w');
		@fwrite($fp,"<"."?php\r\n") or die('读取文件权限出错,目录文件'.$this->modulesPath.'/modulescache.php不可写!');
		fwrite($fp,"global \$allmodules;\r\n");
		while($filename = $dh->read())
		{
			if(eregi("\.dev$",$filename))
			{
				$minfos = $this->GetModuleInfo(str_replace('.dev','',$filename));
				if($minfos['hash']!=''){
					$this->modules[$minfos['hash']] = $minfos;
					fwrite($fp,'$'."GLOBALS['allmodules']['{$minfos['hash']}']='{$filename}';\r\n");
				}
			}
		}
		fwrite($fp,'?'.'>');
		fclose($fp);
		$dh->Close();
		return $this->modules;
	}
	//
	//获得指定hash的模块文件
	//
	function GetHashFile($hash)
	{
		require_once($this->modulesPath.'/modulescache.php');
		if(isset($allmodules[$hash])) return $allmodules[$hash];
		else return $hash.'.dev';
	}

	//
	//获得某模块的基本信息
	//
	function GetModuleInfo($hash,$ftype='hash')
	{
		if($ftype=='file') $filename = $hash;
		else $filename = $this->modulesPath.'/'.$this->GetHashFile($hash);
		$start = 0;
		$minfos = array();
		$minfos['name']='';
    $minfos['team']='';
    $minfos['time']='';
    $minfos['email']='';
    $minfos['url']='';
    $minfos['hash']='';
    $minfos['filename'] = $filename;
    $minfos['filesize'] = filesize($filename)/1024;
    $minfos['filesize'] = number_format($minfos['filesize'],2,'.','').' Kb';
		$fp = fopen($filename,'r') or die("文件 {$filename} 不存在或不可读!");
		$n = 0;
		while(!feof($fp))
		{
		  $n++;
		  if($n>15) break;
		  $line = fgets($fp,256);
		  if($start==0)
		  {  if(preg_match("/<baseinfo/is",$line)) $start = 1; }
		  else
		  {
		  	if(preg_match("/<\/baseinfo/is",$line)) break;
		  	$line = trim($line);
		  	list($skey,$svalue) = explode('=',$line);
		  	$skey = trim($skey);
		  	$minfos[$skey] = $svalue;
		  }
		}
		fclose($fp);
		return $minfos;
	}

	//
	//获得系统文件的内容
	//指安装、删除、协议文件
	//
	function GetSystemFile($hashcode,$ntype,$enCode=true)
	{
		$start = false;
		$filename = $this->modulesPath.'/'.$this->GetHashFile($hashcode);
		$fp = fopen($filename,'r') or die("文件 {$filename} 不存在或不可读!");
		$okdata = '';
		while(!feof($fp))
		{
		  $line = fgets($fp,1024);
		  if(!$start)
		  {
		    if(eregi("<{$ntype}",$line)) $start = true;
		  }
		  else
		  {
		  	if(eregi("</{$ntype}",$line)) break;
		  	$okdata .= $line;
		  	unset($line);
		  }
		}
		fclose($fp);
		$okdata = trim($okdata);
		if(!empty($okdata) && $enCode) $okdata = base64_decode($okdata);
		return $okdata;
	}

	//
	//把某系统文件转换为文件
	//
	function WriteSystemFile($hashcode,$ntype)
	{
		$filename = $hashcode."-{$ntype}.php";
		$fname = $this->modulesPath.'/'.$filename;
		$filect = $this->GetSystemFile($hashcode,$ntype);
		$fp = fopen($fname,'w') or die('生成 {$ntype} 文件失败！');
		fwrite($fp,$filect);
		fclose($fp);
		return $filename;
	}

	//
	//删除系统文件
	//
	function DelSystemFile($hashcode,$ntype)
	{
		$filename = $this->modulesPath.'/'.$hashcode."-{$ntype}.php";
		unlink($filename);
	}

	//
	//检查是否已经存在指定的模块
	//
	function HasModule($hashcode)
	{
		$modulefile = $this->modulesPath.'/'.$this->GetHashFile($hashcode);
		if(file_exists($modulefile) && !is_dir($modulefile)) return true;
		else  return false;
	}

	//
	//读取文件，返回编码后的文件内容
	//
	function GetEncodeFile($filename,$isremove=false)
	{
		$fp = fopen($filename,'r') or die("文件 {$filename} 不存在或不可读!");
		$str = @fread($fp,filesize($filename));
		fclose($fp);
		if($isremove) @unlink($filename);
		if(!empty($str)) return base64_encode($str);
		else return '';
	}

	//
	//获取模块包里的文件名列表
	//
	function GetFileLists($hashcode)
	{
		$dap = new DedeAttParse();
		$filelists = array();
		$modulefile = $this->modulesPath.'/'.$this->GetHashFile($hashcode);
		$fp = fopen($modulefile,'r') or die("文件 {$modulefile} 不存在或不可读!");
		$i = 0;
		while(!feof($fp))
	  {
	  	$line = fgets($fp,1024);
	  	if(preg_match("/^[\s]{0,}<file/i",$line))
	  	{
	  		$i++;
	  		$line = trim(preg_replace("/[><]/","",$line));
	  		$dap->SetSource($line);
	  		$filelists[$i]['type'] = $dap->CAtt->GetAtt('type');
	  		$filelists[$i]['name'] = $dap->CAtt->GetAtt('name');
	  	}
	  }
		fclose($fp);
		return $filelists;
	}

	//
	//删除已安装模块附带的文件
	//
	function DeleteFiles($hashcode,$isreplace=0)
	{
		if($isreplace==0) return true;
		else
		{
			$dap = new DedeAttParse();
		  $modulefile = $this->modulesPath.'/'.$this->GetHashFile($hashcode);
		  $fp = fopen($modulefile,'r') or die("文件 {$modulefile} 不存在或不可读!");
		  $i = 0;
		  $dirs = '';
		  while(!feof($fp))
	    {
	  	   $line = fgets($fp,1024);
	  	   if(preg_match("/^[\s]{0,}<file/i",$line))
	  	   {
	  		    $i++;
	  		    $line = trim(preg_replace("/[><]/","",$line));
	  		    $dap->SetSource($line);
	  		    $filetype = $dap->CAtt->GetAtt('type');
	  		    $filename = $dap->CAtt->GetAtt('name');
	  		    $filename = str_replace("\\","/",$filename);
	  		    if($filetype=='dir'){ $dirs[] = $filename; }
	  		    else{ @unlink($filename); }
	  		 }
	  	}
	  	$okdirs = array();
	  	if(is_array($dirs)){
	  		$st = count($dirs) -1;
	  		for($i=$st;$i>=0;$i--){  @rmdir($dirs[$i]); }
	  	}
	  	fclose($fp);
	  }
		return true;
  }

	//
	//把模块包里的文件写入服务器
	//
	function WriteFiles($hashcode,$isreplace=3)
	{
		global $AdminBaseDir;
		$dap = new DedeAttParse();
		$modulefile = $this->modulesPath.'/'.$this->GetHashFile($hashcode);
		$fp = fopen($modulefile,'r') or die("文件 {$modulefile} 不存在或不可读!");
		$i = 0;
		while(!feof($fp))
	  {
	  	$line = fgets($fp,1024);
	  	if(preg_match("/^[\s]{0,}<file/i",$line))
	  	{
	  		$i++;
	  		$line = trim(preg_replace("/[><]/","",$line));
	  		$dap->SetSource($line);
	  		$filetype = $dap->CAtt->GetAtt('type');
	  		$filename = $dap->CAtt->GetAtt('name');
	  		$filename = str_replace("\\","/",$filename);
	  		if(!empty($AdminBaseDir)) $filename = $AdminBaseDir.$filename;
	  		if($filetype=='dir')
	  		{
	  			if(!is_dir($filename))
	  			{
	  				@mkdir($filename,$GLOBALS['cfg_dir_purview']);
	  				@chmod($filename,$GLOBALS['cfg_dir_purview']);
	  			}
	  		}else{
	  			$this->TestDir($filename);
	  			if($isreplace==0) continue;
	  			if($isreplace==3){
	  				if(is_file($filename)){
	  					$copyname = @preg_replace("/([^\/]{1,}$)/","bak-$1",$filename);
	  					@copy($filename,$copyname);
	  				}
	  			}
	  			if(!empty($filename)){
	  			  $fw = fopen($filename,'w') or die("写入文件 {$filename} 失败，请检查相关目录的权限！");
	  			  $ct = "";
	  			  while(!feof($fp)){
	  			    $l = fgets($fp,1024);
	  			    if(preg_match("/^[\s]{0,}<\/file/i",trim($l))){ break; }
	  			    $ct .= $l;
	  			  }
	  			  $ct = base64_decode($ct);
	  			  fwrite($fw,$ct);
	  			  fclose($fw);
	  		  }
	  		}
	  	}
	  }
		fclose($fp);
		return true;
	}

	//
	//测试某文件的文件夹是否创建
	//
	function TestDir($filename)
	{
		$fs = explode('/',$filename);
		$fn = count($fs) - 1 ;
		$ndir = '';
		for($i=0;$i < $fn;$i++)
		{
			if($ndir!='') $ndir = $ndir."/".$fs[$i];
			else $ndir = $fs[$i];
			if( !is_dir($ndir) ){ @mkdir($ndir,$GLOBALS['cfg_dir_purview']); @chmod($ndir,$GLOBALS['cfg_dir_purview']); }
		}
		return true;
	}

	//
	//获取某个目录或文件的打包数据
	//
	function MakeEncodeFile($basedir,$f,$fp)
	{
		$this->fileListNames = array();
		$this->MakeEncodeFileRun($basedir,$f,$fp);
		return true;
	}

	//
	//获取个目录或文件的打包数据，递归
	//
  function MakeEncodeFileRun($basedir,$f,$fp)
	{
		$filename = $basedir.'/'.$f;
		if(isset($this->fileListNames[$f])) return;
		else if(eregi("Thumbs\.db",$f)) return;
		else $this->fileListNames[$f] = 1;
		$fileList = '';
		if(is_dir($filename))
		{
			$fileList .= "<file type='dir' name='$f'>\r\n";
			$fileList .= "</file>\r\n";
			fwrite($fp,$fileList);
			$dh = dir($filename);
			while($filename = $dh->read())
			{
				if($filename[0]=='.') continue;
				$nfilename = $f.'/'.$filename;
				$this->MakeEncodeFileRun($basedir,$nfilename,$fp);
			}
		}
		else
		{
			$fileList .= "<file type='file' name='$f'>\r\n";
			$fileList .= $this->GetEncodeFile($filename);
			$fileList .= "\r\n</file>\r\n";
			fwrite($fp,$fileList);
		}
	}

	//
	//清理
	//
	function Clear()
	{
		unset($this->modules);
		unset($this->fileList);
		unset($this->fileListNames);
	}

}//End Class


?>