<?
//调用这个类前,请先设定这些外部变量
//$cfg_dbhost="";
//$cfg_dbname="";
//$cfg_dbuser="";
//$cfg_dbpwd="";
//前缀名称
//$cfg_dbprefix="";
$cfg_db_language = "gbk";
class DedeSql
{
	var $linkID;
	var $dbHost;
	var $dbUser;
	var $dbPwd;
	var $dbName;
	var $dbPrefix;
	var $result;
	var $queryString;
	var $parameters;
	//
	//用外部定义的变量初始类，并连接数据库
	//
	function __construct($pconnect=false)
 	{
 		if($this->linkID==0) $this->Init($pconnect);
  }
	
	function DedeSql($pconnect=false)
	{
		if($this->linkID==0) $this->Init($pconnect);
	}
	
	function Init($pconnect=false)
	{
		$this->linkID = 0;
		$this->queryString = "";
		$this->parameters = Array();
		$this->dbHost = $GLOBALS["cfg_dbhost"];
		$this->dbUser = $GLOBALS["cfg_dbuser"];
		$this->dbPwd = $GLOBALS["cfg_dbpwd"];
		$this->dbName = $GLOBALS["cfg_dbname"];
		$this->dbPrefix = $GLOBALS["cfg_dbprefix"];
		$this->result["me"] = 0;
		$this->Open($pconnect);
	}
	//
	//用指定参数初始数据库信息
	//
	function SetSource($host,$username,$pwd,$dbname,$dbprefix="dede_")
	{
		$this->dbHost = $host;
		$this->dbUser = $username;
		$this->dbPwd = $pwd;
		$this->dbName = $dbname;
		$this->dbPrefix = $dbprefix;
		$this->result["me"] = 0;
	}
	function SelectDB($dbname)
	{
		mysql_select_db($dbname);
	}
	//
	//设置SQL里的参数
	//
	function SetParameter($key,$value)
	{
		$this->parameters[$key]=$value;
	}
	//
	//连接数据库
	//
	function Open($pconnect=true)
	{
		//连接数据库
		if($pconnect){ $this->linkID = @mysql_pconnect($this->dbHost,$this->dbUser,$this->dbPwd); }
		else{ $this->linkID = @mysql_connect($this->dbHost,$this->dbUser,$this->dbPwd); }
		//处理错误，成功连接则选择数据库
		if(!$this->linkID){
			$this->DisplayError("Connect Database Server False!");
			return false;
		}
		else{ @mysql_select_db($this->dbName); }
		mysql_query("SET NAMES '".$GLOBALS['cfg_db_language']."';",$this->linkID);
		return true;
	}
	//
	//获得错误描述
	//
	function GetError()
	{
		$str = ereg_replace("'|\"","`",mysql_error());
		return $str;
	}
	//
	//关闭数据库
	//
	function Close()
	{
		@mysql_close($this->linkID);
		$this->FreeResultAll();
	}
	//
	//关闭指定的数据库连接
	//
	function CloseLink($dblink)
	{
		@mysql_close($dblink);
	}
	//
	//执行一个不返回结果的SQL语句，如update,delete,insert等
	//
	function ExecuteNoneQuery()
	{
		if(is_array($this->parameters)){
			foreach($this->parameters as $key=>$value){
				$this->queryString = str_replace("@".$key,"'$value'",$this->queryString);
			}
		}
		return mysql_query($this->queryString,$this->linkID);
	}
	function ExecNoneQuery()
	{
		return $this->ExecuteNoneQuery();
	}
	//
	//执行一个带返回结果的SQL语句，如SELECT，SHOW等
	//
	function Execute($id="me")
	{
		$this->result[$id] = @mysql_query($this->queryString,$this->linkID);
		
		if(!$this->result[$id]){
			$this->DisplayError(mysql_error()." - Execute Query False! <font color='red'>".$this->queryString."</font>");
		}
	}
	function Query($id="me")
	{
		$this->Execute($id);
	}
	//
	//执行一个SQL语句,返回前一条记录或仅返回一条记录
	//
	function GetOne($sql="")
	{
		if($sql!=""){ 
		  if(!eregi("limit",$sql)) $this->SetQuery(eregi_replace("[,;]$","",trim($sql))." limit 0,1;");
		  else $this->SetQuery($sql);
		}
		$this->Execute("one");
		$arr = $this->GetArray("one");
		if(!is_array($arr)) return("");
		else { @mysql_free_result($this->result["one"]); return($arr);}
		
	}
	//
	//执行一个不与任何表名有关的SQL语句,Create等
	//
	function ExecuteSafeQuery($sql,$id="me")
	{
		$this->result[$id] = @mysql_query($sql,$this->linkID);
	}
	//
	//返回当前的一条记录并把游标移向下一记录
	//
	function GetArray($id="me")
	{
		if($this->result[$id]==0) return false;
		else return mysql_fetch_array($this->result[$id]);
	}
	function GetObject($id="me")
	{
		if($this->result[$id]==0) return false;
		else return mysql_fetch_object($this->result[$id]);
	}
	//
	//检测是否存在某数据表
	//
	function IsTable($tbname)
	{
		$this->result = mysql_list_tables($this->dbName,$this->linkID);
		while ($row = mysql_fetch_array($this->result)){
			if($row[0]==$tbname)
			{
				mysql_freeresult($this->result);
				return true;
			}
		}
		mysql_freeresult($this->result);
		return false;
	}
	//
	//获得MySql的版本号
	//
	function GetVersion()
	{
		$rs = mysql_query("SELECT VERSION();",$conn);
    $row = mysql_fetch_array($rs);
    $mysql_version = $row[0];
    mysql_free_result($rs);
    return $mysql_version;
	}
	//
	//获取特定表的信息
	//
	function GetTableFields($tbname,$id="me")
	{
		$this->result[$id] = mysql_list_fields($this->dbName,$tbname,$this->linkID);
	}
	//
	//获取字段详细信息
	//
	function GetFieldObject($id="me")
	{
		return mysql_fetch_field($this->result[$id]);
	}
	//
	//获得查询的总记录数
	//
	function GetTotalRow($id="me")
	{
		if($this->result[$id]==0) return -1;
		else return mysql_num_rows($this->result[$id]);
	}
	//
	//获取上一步INSERT操作产生的ID 
	//
	function GetLastID()
	{
		//如果 AUTO_INCREMENT 的列的类型是 BIGINT，则 mysql_insert_id() 返回的值将不正确。
		//可以在 SQL 查询中用 MySQL 内部的 SQL 函数 LAST_INSERT_ID() 来替代。 
		//$rs = mysql_query("Select LAST_INSERT_ID() as lid",$this->linkID);
		//$row = mysql_fetch_array($rs);
		//return $row["lid"];
		return mysql_insert_id($this->linkID);
	}
	//
	//释放记录集占用的资源
	//
	function FreeResult($id="me")
	{
		@mysql_free_result($this->result[$id]);
	}
	function FreeResultAll()
	{
		if(!is_array($this->result)) return "";
		foreach($this->result as $kk => $vv){
			if($vv) @mysql_free_result($vv);
		}
	}
	//
	//设置SQL语句，会自动把SQL语句里的#@__替换为$this->dbPrefix(在配置文件中为$cfg_dbprefix)
	//
	function SetQuery($sql)
	{
		$prefix="#@__";
		$sql = trim($sql);
		$inQuote = false;
		$escaped = false;
		$quoteChar = '';
		$n = strlen($sql);
		$np = strlen($prefix);
		$restr = '';
		for($j=0; $j < $n; $j++)
		{
			$c = $sql{$j};
			$test = substr($sql, $j, $np);
			if(!$inQuote)
			{
				if ($c == '"' || $c == "'") {
					$inQuote = true;
					$escaped = false;
					$quoteChar = $c;
				}
			}
			else
			{
				if ($c == $quoteChar && !$escaped) {
					$inQuote = false;
				} else if ($c == "\\" && !$escaped) {
					$escaped = true;
				} else {
					$escaped = false;
				}
			}
			if ($test == $prefix && !$inQuote)
			{
			    $restr .= $this->dbPrefix;
			    $j += $np-1;
			}
			else
			{
				$restr .= $c;
			}
		}
		$this->queryString = $restr;
	}
	function SetSql($sql)
	{
		$this->SetQuery($sql);
	}
	//
	//显示数据链接错误信息
	//
	function DisplayError($msg)
	{
		echo "<html>\r\n";
		echo "<head>\r\n";
		echo "<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>\r\n";
		echo "<title>DedeCms Error Track</title>\r\n";
		echo "</head>\r\n";
		echo "<body>\r\n<p style='line-helght:150%;font-size:10pt'>\r\n";
		echo $msg;
		echo "<br/><br/>";
		echo "</p>\r\n</body>\r\n";
		echo "</html>";
		//exit();
	}
}
?>