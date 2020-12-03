<?
@set_time_limit(0);
/*=======================================
//织梦Http下载类V1.0版
=======================================*/
class DedeHttpDown
{
	var $m_url = "";
	var $m_urlpath = "";
	var $m_scheme = "http";
	var $m_host = "";
	var $m_port = "80";
	var $m_user = "";
	var $m_pass = "";
	var $m_path = "/";
	var $m_query = "";
	var $m_fp = "";
	var $m_error = "";
	var $m_httphead = "" ;
	var $m_html = "";
	var $m_puthead = "";
	var $BaseUrlPath = "";
	var $HomeUrl = "";
	var $JumpCount = 0;//防止多重重定向陷入死循环
	//
	//初始化系统
	//
	function PrivateInit($url)
	{
			if($url=="") return ;
			$urls = "";
			$urls = @parse_url($url);
			$this->m_url = $url;
	    if(is_array($urls))
	    {
		  	$this->m_host = $urls["host"];
		  	if(!empty($urls["scheme"])) $this->m_scheme = $urls["scheme"];
		  	
		  	if(!empty($urls["user"])){
					$this->m_user = $urls["user"];
		  	}
		  	
		  	if(!empty($urls["pass"])){
					$this->m_pass = $urls["pass"];
		  	}

		  	if(!empty($urls["port"])){
					$this->m_port = $urls["port"];
		  	}
		  	
		  	if(!empty($urls["path"])) $this->m_path = $urls["path"];
		  	$this->m_urlpath = $this->m_path;
		  	
		  	if(!empty($urls["query"])){
					$this->m_query = $urls["query"];
					$this->m_urlpath .= "?".$this->m_query;
		  	}
		  	$this->HomeUrl = $urls["host"];
		  	$this->BaseUrlPath = $this->HomeUrl.$urls["path"];
		  	$this->BaseUrlPath = preg_replace("/\/([^\/]*)\.(.*)$/","/",$this->BaseUrlPath);
		  	$this->BaseUrlPath = preg_replace("/\/$/","",$this->BaseUrlPath);
		 }
	}
	//
	//打开指定网址
	//
	function OpenUrl($url)
	{
		//重设各参数
		$this->m_url = "";
		$this->m_urlpath = "";
		$this->m_scheme = "http";
		$this->m_host = "";
		$this->m_port = "80";
		$this->m_user = "";
		$this->m_pass = "";
		$this->m_path = "/";
		$this->m_query = "";
		$this->m_error = "";
		$this->JumpCount = 0;
		$this->m_httphead = Array() ;
		//$this->m_puthead = "";
		$this->m_html = "";
		$this->Close();
		//初始化系统
		$this->PrivateInit($url);
		$this->PrivateStartSession();
	}
	//
	//打开303重定向网址
	//
	function JumpOpenUrl($url)
	{
		//重设各参数
		$this->m_url = "";
		$this->m_urlpath = "";
		$this->m_scheme = "http";
		$this->m_host = "";
		$this->m_port = "80";
		$this->m_user = "";
		$this->m_pass = "";
		$this->m_path = "/";
		$this->m_query = "";
		$this->m_error = "";
		$this->JumpCount++;
		$this->m_httphead = Array() ;
		$this->m_html = "";
		$this->Close();
		//初始化系统
		$this->PrivateInit($url);
		$this->PrivateStartSession();
	}
	//
	//获得某操作错误的原因
	//
	function printError()
	{
		echo "错误信息：".$this->m_error;
		echo "具体返回头：<br/>";
		foreach($this->m_httphead as $k=>$v)
		{ echo "$k => $v <br/>\r\n"; }
	}
	//
	//判别用Get方法发送的头的应答结果是否正确
	//
	function IsGetOK()
	{
		if( ereg("^2",$this->GetHead("http-state")) )
		{	return true; }
		else
		{
			$this->m_error .= $this->GetHead("http-state")." - ".$this->GetHead("http-describe")."<br/>";
			return false;
		}
	}
	//
	//看看返回的网页是否是text类型
	//
	function IsText()
	{
		if(ereg("^2",$this->GetHead("http-state"))
			&& eregi("^text",$this->GetHead("content-type")))
		{	return true; }
		else
		{
			$this->m_error .= "内容为非文本类型或网址重定向<br/>";
			return false;
		}
	}
	//
	//判断返回的网页是否是特定的类型
	//
	function IsContentType($ctype)
	{
		if(ereg("^2",$this->GetHead("http-state"))
			&& $this->GetHead("content-type")==strtolower($ctype))
		{	return true; }
		else
		{
			$this->m_error .= "类型不对 ".$this->GetHead("content-type")."<br/>";
			return false;
		}
	}
	//
	//用Http协议下载文件
	//
	function SaveToBin($savefilename)
	{
		if(!$this->IsGetOK()) return false;
		if(@feof($this->m_fp)) { $this->m_error = "连接已经关闭！"; return false; }
		
		$fp = fopen($savefilename,"w");
		while(!feof($this->m_fp)){
			fwrite($fp,fread($this->m_fp,1024));
		}
		fclose($this->m_fp);
		
		fclose($fp);
		return true;
	}
	//
	//保存网页内容为Text文件
	//
	function SaveToText($savefilename)
	{
		if($this->IsText()) $this->SaveBinFile($savefilename);
		else return "";
	}
	//
	//用Http协议获得一个网页的内容
	//
	function GetHtml()
	{
		if(!$this->IsText()) return "";
		if($this->m_html!="") return $this->m_html;
		if(!$this->m_fp||@feof($this->m_fp)) return "";
		while(!feof($this->m_fp)){
			$this->m_html .= fgets($this->m_fp,256);
		}
		@fclose($this->m_fp);
		return $this->m_html;
	}
	//
	//开始HTTP会话
	//
	function PrivateStartSession()
	{
		
		if(!$this->PrivateOpenHost()){
			$this->m_error .= "打开远程主机出错!";
			return false;
		}
		
		if($this->GetHead("http-edition")=="HTTP/1.1") $httpv = "HTTP/1.1";
		else $httpv = "HTTP/1.0";
		
		//发送固定的起始请求头GET、Host信息
		fputs($this->m_fp,"GET ".$this->m_urlpath." $httpv\r\n");
		$this->m_puthead["Host"] = $this->m_host;
		
		//发送用户自定义的请求头
		if(!isset($this->m_puthead["Accept"])) { $this->m_puthead["Accept"] = "*/*"; }
		if(!isset($this->m_puthead["User-Agent"])) { $this->m_puthead["User-Agent"] = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.2)"; }
		if(!isset($this->m_puthead["Refer"])) { $this->m_puthead["Refer"] = "http://".$this->m_puthead["Host"]; }
		foreach($this->m_puthead as $k=>$v){
			$k = trim($k);
			$v = trim($v);
			if($k!=""&&$v!=""){
				fputs($this->m_fp,"$k: $v\r\n");
			}
		}
		
		//发送固定的结束请求头
		//HTTP1.1协议必须指定文档结束后关闭链接,否则读取文档时无法使用feof判断结束
		if($httpv=="HTTP/1.1") fputs($this->m_fp,"Connection: Close\r\n\r\n");
		else fputs($this->m_fp,"\r\n");
		
		//获取应答头状态信息
		$httpstas = explode(" ",fgets($this->m_fp,256));
		$this->m_httphead["http-edition"] = trim($httpstas[0]);
		$this->m_httphead["http-state"] = trim($httpstas[1]);
		$this->m_httphead["http-describe"] = "";
		for($i=2;$i<count($httpstas);$i++){
			$this->m_httphead["http-describe"] .= " ".trim($httpstas[$i]);
		}
		//获取详细应答头
		while(!feof($this->m_fp)){
			$line = trim(fgets($this->m_fp,256));
			if($line == "") break;
			$hkey = "";
			$hvalue = "";
			$v = 0;
			for($i=0;$i<strlen($line);$i++){
				if($v==1) $hvalue .= $line[$i];
				if($line[$i]==":") $v = 1;
				if($v==0) $hkey .= $line[$i];
			}
			$hkey = trim($hkey);
			if($hkey!="") $this->m_httphead[strtolower($hkey)] = trim($hvalue);
		}
		//判断是否是3xx开头的应答
		if(ereg("^3",$this->m_httphead["http-state"]))
		{
			if($this->JumpCount > 3) return;
			if(isset($this->m_httphead["location"])){
				$newurl = $this->m_httphead["location"];
				if(eregi("^http",$newurl)){
					$this->JumpOpenUrl($newurl);
				}
				else{
					$newurl = $this->FillUrl($newurl);
					$this->JumpOpenUrl($newurl);
				}
			}
			else
			{	$this->m_error = "无法识别的转移应答！"; }
		}//
	}
	//
	//获得一个Http头的值
	//
	function GetHead($headname)
	{
		$headname = strtolower($headname);
		if(isset($this->m_httphead[$headname]))
			return $this->m_httphead[$headname];
		else
			return "";
	}
	//
	//设置Http头的值
	//
	function SetHead($skey,$svalue)
	{
		$this->m_puthead[$skey] = $svalue;
	}
	//
	//打开连接
	//
	function PrivateOpenHost()
	{
		if($this->m_host=="") return false;
		$errno = "";
		$errstr = "";
		$this->m_fp = @fsockopen($this->m_host, $this->m_port, $errno, $errstr,10);
		if(!$this->m_fp){
			$this->m_error = $errstr;
			return false;
		}
		else{
			return true;
		}
	}
	//
	//关闭连接
	//
	function Close(){
		@fclose($this->m_fp);
	}
	//
	//补全相对网址
	//
	function FillUrl($surl)
  {
    $i = 0;
    $dstr = "";
    $pstr = "";
    $okurl = "";
    $pathStep = 0;
    $surl = trim($surl);
    if($surl=="") return "";
    $pos = strpos($surl,"#");
    if($pos>0) $surl = substr($surl,0,$pos);
    if($surl[0]=="/"){
    	$okurl = "http://".$this->HomeUrl.$surl;
    }
    else if($surl[0]==".")
    {
      if(strlen($surl)<=1) return "";
      else if($surl[1]=="/")
      {
      	$okurl = "http://".$this->BaseUrlPath."/".substr($surl,2,strlen($surl)-2);
    	}
      else{
        $urls = explode("/",$surl);
        foreach($urls as $u){
          if($u=="..") $pathStep++;
          else if($i<count($urls)-1) $dstr .= $urls[$i]."/";
          else $dstr .= $urls[$i];
          $i++;
        }
        $urls = explode("/",$this->BaseUrlPath);
        if(count($urls) <= $pathStep)
        	return "";
        else{
          $pstr = "http://";
          for($i=0;$i<count($urls)-$pathStep;$i++)
          { $pstr .= $urls[$i]."/"; }
          $okurl = $pstr.$dstr;
        }
      }
    }
    else
    {
      if(strlen($surl)<7)
        $okurl = "http://".$this->BaseUrlPath."/".$surl;
      else if(strtolower(substr($surl,0,7))=="http://")
        $okurl = $surl;
      else
        $okurl = "http://".$this->BaseUrlPath."/".$surl;
    }
    $okurl = eregi_replace("^(http://)","",$okurl);
    $okurl = eregi_replace("/{1,}","/",$okurl);
    return "http://".$okurl;
  }
}
?>