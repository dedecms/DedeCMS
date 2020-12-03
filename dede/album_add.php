<?php
require_once(dirname(__FILE__)."/config.php");
CheckPurview('a_New,a_AccNew');
require_once(DEDEINC."/customfields.func.php");
require_once(DEDEADMIN."/inc/inc_archives_functions.php");

if(empty($dopost))
{
	$dopost = '';
}
if($dopost!='save')
{
	require_once(DEDEINC."/dedetag.class.php");
	require_once(DEDEADMIN."/inc/inc_catalog_options.php");
	$channelid = empty($channelid) ? 0 : intval($channelid);
	$cid = empty($cid) ? 0 : intval($cid);

	//获得频道模型ID
	if($cid>0 && $channelid==0)
	{
		$row = $dsql->GetOne("Select channeltype From `#@__arctype` where id='$cid'; ");
		$channelid = $row['channeltype'];
	}
	else
	{
		if($channelid==0)
		{
			$channelid = 2;
		}
	}

	//获得频道模型信息
	$cInfos = $dsql->GetOne(" Select * From  `#@__channeltype` where id='$channelid' ");
	$channelid = $cInfos['id'];
	include DedeInclude("templets/album_add.htm");
	exit();
}

/*--------------------------------
function __save(){  }
-------------------------------*/
else if($dopost=='save')
{
	require_once(DEDEINC.'/image.func.php');
	require_once(DEDEINC.'/oxwindow.class.php');
	
	$flag = isset($flags) ? join(',',$flags) : '';
	if(!isset($typeid2)) $typeid2 = 0;
	if(!isset($autokey)) $autokey = 0;
	if(!isset($remote)) $remote = 0;
	if(!isset($dellink)) $dellink = 0;
	if(!isset($autolitpic)) $autolitpic = 0;
	if(!isset($formhtml)) $formhtml = 0;
	if(!isset($formzip)) $formzip = 0;
	if(!isset($ddisfirst)) $ddisfirst = 0;
	if(!isset($delzip)) $delzip = 0;

	if($typeid==0)
	{
		ShowMsg("请指定文档的栏目！","-1");
		exit();
	}
	if(empty($channelid))
	{
		ShowMsg("文档为非指定的类型，请检查你发布内容的表单是否合法！","-1");
		exit();
	}
	if(!CheckChannel($typeid,$channelid) )
	{
		ShowMsg("你所选择的栏目与当前模型不相符，请选择白色的选项！","-1");
		exit();
	}
	if(!TestPurview('a_New'))
	{
		CheckCatalog($typeid,"对不起，你没有操作栏目 {$typeid} 的权限！");
	}

	//对保存的内容进行处理
	if(empty($writer))$writer=$cuserLogin->getUserName();
	if(empty($source))$source='未知';
	$pubdate = GetMkTime($pubdate);
	$senddate = time();
	$sortrank = AddDay($pubdate,$sortup);
	$ismake = $ishtml==0 ? -1 : 0;
	$title = cn_substrR($title,$cfg_title_maxlen);
	$shorttitle = cn_substrR($shorttitle,36);
	$color =  cn_substrR($color,7);
	$writer =  cn_substrR($writer,20);
	$source = cn_substrR($source,30);
	$description = cn_substrR($description,250);
	$keywords = cn_substrR($keywords,30);
	$filename = trim(cn_substrR($filename,40));
	$userip = GetIP();
	if(!TestPurview('a_Check,a_AccCheck,a_MyCheck'))
	{
		$arcrank = -1;
	}
	$adminid = $cuserLogin->getUserID();

	//处理上传的缩略图
	if(empty($ddisremote))
	{
		$ddisremote = 0;
	}
	$litpic = GetDDImage('litpic',$picname,$ddisremote);

	//使用第一张图作为缩略图
	if($ddisfirst==1 && $litpic=='')
	{
		if(isset($imgurl1))
		{
			$litpic = GetDDImage('ddfirst',$imgurl1,$isrm);
		}
	}

	//生成文档ID
	$arcID = GetIndexKey($arcrank,$typeid,$sortrank,$channelid,$senddate,$adminid);
	if(empty($arcID))
	{
		ShowMsg("无法获得主键，因此无法进行后续操作！","-1");
		exit();
	}

	$imgurls = "{dede:pagestyle maxwidth='$maxwidth' pagepicnum='$pagepicnum' ddmaxwidth='$ddmaxwidth' row='$row' col='$col' value='$pagestyle'/}\r\n";
	$hasone = false;

	//处理并保存所指定的图片从
	//网上复制
	/*---------------------
	function _getformhtml()
	------------------*/
	if($formhtml==1)
	{
		$imagebody = stripslashes($imagebody);
		$imgurls .= GetCurContentAlbum($imagebody,$copysource,$litpicname);
		if($ddisfirst==1 && $litpic=='' && !empty($litpicname))
		{
			$litpic = $litpicname;
			$hasone = true;
		}
	}

	/*---------------------
	function _getformzip()
	ZIP中解压
	---------------------*/
	if($formzip==1)
	{
		include_once(DEDEINC."/zip.class.php");
		include_once(DEDEADMIN."/file_class.php");
		$zipfile = $cfg_basedir.str_replace($cfg_mainsite,'',$zipfile);
		$tmpzipdir = DEDEDATA.'/ziptmp/'.cn_substr(md5(ExecTime()),16);
		$ntime = time();
		if(file_exists($zipfile))
		{
			@mkdir($tmpzipdir,$GLOBALS['cfg_dir_purview']);
			@chmod($tmpzipdir,$GLOBALS['cfg_dir_purview']);
			$z = new zip();
			$z->ExtractAll($zipfile,$tmpzipdir);
			$fm = new FileManagement();
			$imgs = array();
			$fm->GetMatchFiles($tmpzipdir,"jpg|png|gif",$imgs);
			$i = 0;
			foreach($imgs as $imgold)
			{
				$i++;
				$savepath = $cfg_image_dir."/".MyDate("Y-m",$ntime);
				CreateDir($savepath);
				$iurl = $savepath."/".MyDate("d",$ntime).dd2char(MyDate("His",$ntime).'-'.$adminid."-{$i}".mt_rand(1000,9999));
				$iurl = $iurl.substr($imgold,-4,4);
				$imgfile = $cfg_basedir.$iurl;
				copy($imgold,$imgfile);
				unlink($imgold);

				if(is_file($imgfile))
				{
					$litpicname = $pagestyle > 2 ? GetImageMapDD($iurl,$ddmaxwidth) : '';
					//指定了提取第一张为缩略图的情况强制使用第一张缩略图
					if($i=='1')
					{
						if(!$hasone && $ddisfirst==1 && $litpic=='' && empty($litpicname))
						{
							$litpicname = GetImageMapDD($iurl,$ddmaxwidth);
						}
					}
					$info = '';
					$imginfos = GetImageSize($imgfile,$info);
					$imgurls .= "{dede:img ddimg='$litpicname' text='' width='".$imginfos[0]."' height='".$imginfos[1]."'} $iurl {/dede:img}\r\n";

					//把图片信息保存到媒体文档管理档案中
					$inquery = "
                   INSERT INTO #@__uploads(title,url,mediatype,width,height,playtime,filesize,uptime,mid)
                    VALUES ('{$title}','{$iurl}','1','".$imginfos[0]."','".$imginfos[1]."','0','".filesize($imgfile)."','".$ntime."','$adminid');
                 ";
					$dsql->ExecuteNoneQuery($inquery);
					WaterImg($imgfile,'up');

					if(!$hasone && $ddisfirst==1 && $litpic=='')
					{
						if(empty($litpicname))
						{
							$litpicname = $iurl;
							$litpicname = GetImageMapDD($iurl, $ddmaxwidth);
						}
						$litpic = $litpicname;
						$hasone = true;
					}
				}
			}
			if($delzip==1)
			{
				unlink($zipfile);
			}
			$fm->RmDirFiles($tmpzipdir);
		}
	}

	/*---------------------
	function _getformupload()
	---------------------*/
	//正常上传或指定网址
	for($i=1;$i<=120;$i++)
	{
		if(isset(${'imgurl'.$i})||(isset($_FILES['imgfile'.$i]['tmp_name']) && is_uploaded_file($_FILES['imgfile'.$i]['tmp_name'])))
		{
			$iinfo = str_replace("'","`",stripslashes(${'imgmsg'.$i}));

			//非上传图片
			if(!is_uploaded_file($_FILES['imgfile'.$i]['tmp_name']))
			{
				$iurl = stripslashes(${'imgurl'.$i});
				if(trim($iurl)=="")
				{
					continue;
				}
				$iurl = trim(str_replace($cfg_basehost,"",$iurl));
				if((eregi("^http://",$iurl) && !eregi($cfg_basehost,$iurl)) && $cfg_isUrlOpen)
				{
					//远程图片
					$reimgs = "";
					if($cfg_isUrlOpen && $isrm==1)
					{
						$reimgs = GetRemoteImage($iurl,$adminid);
						if(is_array($reimgs))
						{
							$litpicname = $pagestyle > 2 ? GetImageMapDD($reimgs[0],$ddmaxwidth) : '';
							$imgurls .= "{dede:img ddimg='$litpicname' text='$iinfo' width='".$reimgs[1]."' height='".$reimgs[2]."'} ".$reimgs[0]." {/dede:img}\r\n";
						}
						else
						{
							echo "下载：".$iurl." 失败，可能图片有反采集功能或http头不正确！<br />\r\n";
						}
					}
					else
					{
						$imgurls .= "{dede:img text='$iinfo' width='' height=''} ".$iurl." {/dede:img}\r\n";
					}
					//站内图片
				}
				else if($iurl!="")
				{
					$imgfile = $cfg_basedir.$iurl;
					if(is_file($imgfile))
					{
						$litpicname = $pagestyle > 2 ? GetImageMapDD($iurl,$ddmaxwidth) : '';
						$info = "";
						$imginfos = GetImageSize($imgfile,$info);
						$imgurls .= "{dede:img ddimg='$litpicname' text='$iinfo' width='".$imginfos[0]."' height='".$imginfos[1]."'} $iurl {/dede:img}\r\n";
					}
				}
				//直接上传的图片
			}
			else
			{
				$sparr = Array("image/pjpeg","image/jpeg","image/gif","image/png","image/xpng","image/wbmp");
				if(!in_array($_FILES['imgfile'.$i]['type'],$sparr))
				{
					continue;
				}
				$uptime = time();
				$imgPath = $cfg_image_dir."/".MyDate("ymd",$uptime);
				MkdirAll($cfg_basedir.$imgPath,$GLOBALS['cfg_dir_purview']);
				CloseFtp();
				$picfilename = $imgPath."/".dd2char($cuserLogin->getUserID().MyDate("His",$uptime).mt_rand(1000,9999))."-{$i}";
				$fs = explode(".",$_FILES['imgfile'.$i]['name']);
				$picfilename = $picfilename.".".$fs[count($fs)-1];
				move_uploaded_file($_FILES['imgfile'.$i]['tmp_name'],$cfg_basedir.$picfilename);

				//缩图
				$litpicname = $pagestyle > 2 ? GetImageMapDD($picfilename,$ddmaxwidth) : '';
				//指定了提取第一张为缩略图的情况强制使用第一张缩略图
				if($i=='1')
				{
					if(!$hasone && $ddisfirst==1 && $litpic=='' && empty($litpicname))
					{
						$litpicname = GetImageMapDD($picfilename,$ddmaxwidth);
					}
				}

				//水印
				$imgfile = $cfg_basedir.$picfilename;
				WaterImg($imgfile,'up');
				if(is_file($imgfile))
				{
					$iurl = $picfilename;
					$info = "";
					$imginfos = GetImageSize($imgfile,$info);
					$imgurls .= "{dede:img ddimg='$litpicname' text='$iinfo' width='".$imginfos[0]."' height='".$imginfos[1]."'} $iurl {/dede:img}\r\n";
					//把新上传的图片信息保存到媒体文档管理档案中
					$inquery = "
                   INSERT INTO #@__uploads(title,url,mediatype,width,height,playtime,filesize,uptime,mid)
                    VALUES ('$title".$i."','$picfilename','1','".$imginfos[0]."','".$imginfos[1]."','0','".filesize($imgfile)."','".time()."','$adminid');
                 ";
					$dsql->ExecuteNoneQuery($inquery);
				}
			}
			if(!$hasone && $ddisfirst==1 && $litpic=="")
			{
				if(empty($litpicname)) {
					$litpicname = $iurl;
					if(!ereg('^http:', $iurl)) {
						$litpicname = GetImageMapDD($iurl, $ddmaxwidth);
					}
				}
				$litpic = $litpicname;
				$hasone = true;
			}
		}//含有图片的条件

	}//循环结束

	$imgurls = addslashes($imgurls);

	//分析处理附加表数据
	$inadd_f = '';
	$inadd_v = '';
	if(!empty($dede_addonfields))
	{
		$addonfields = explode(';',$dede_addonfields);
		$inadd_f = '';
		$inadd_v = '';
		if(is_array($addonfields))
		{
			foreach($addonfields as $v)
			{
				if($v=='')
				{
					continue;
				}
				$vs = explode(',',$v);
				if(!isset(${$vs[0]}))
				{
					${$vs[0]} = '';
				}
				else if($vs[1]=='htmltext'||$vs[1]=='textdata') //HTML文本特殊处理
				{
					${$vs[0]} = AnalyseHtmlBody(${$vs[0]},$description,$litpic,$keywords,$vs[1]);
				}
				else
				{
					if(!isset(${$vs[0]}))
					{
						${$vs[0]} = '';
					}
					${$vs[0]} = GetFieldValueA(${$vs[0]},$vs[1],$arcID);
				}
				$inadd_f .= ','.$vs[0];
				$inadd_v .= " ,'".${$vs[0]}."' ";
			}
		}
	}

	//处理图片文档的自定义属性
	if($litpic!='' && !ereg('p',$flag))
	{
		$flag = ($flag=='' ? 'p' : $flag.',p');
	}
	if($redirecturl!='' && !ereg('j',$flag))
	{
		$flag = ($flag=='' ? 'j' : $flag.',j');
	}

	//加入主档案表
	$query = "INSERT INTO `#@__archives`(id,typeid,typeid2,sortrank,flag,ismake,channel,arcrank,click,money,title,shorttitle,
     color,writer,source,litpic,pubdate,senddate,mid,description,keywords,filename)
    VALUES ('$arcID','$typeid','$typeid2','$sortrank','$flag','$ismake','$channelid','$arcrank','0','$money','$title','$shorttitle',
    '$color','$writer','$source','$litpic','$pubdate','$senddate','$adminid','$description','$keywords','$filename'); ";
	if(!$dsql->ExecuteNoneQuery($query))
	{
		$gerr = $dsql->GetError();
		$dsql->ExecuteNoneQuery(" Delete From `#@__arctiny` where id='$arcID' ");
		ShowMsg("把数据保存到数据库主表 `#@__archives` 时出错，请把相关信息提交给DedeCms官方。".str_replace('"','',$gerr),"javascript:;");
		exit();
	}

	//加入附加表
	$cts = $dsql->GetOne("Select addtable From `#@__channeltype` where id='$channelid' ");
	$addtable = trim($cts['addtable']);
	if(empty($addtable))
	{
		$dsql->ExecuteNoneQuery("Delete From `#@__archives` where id='$arcID'");
		$dsql->ExecuteNoneQuery("Delete From `#@__arctiny` where id='$arcID'");
		ShowMsg("没找到当前模型[{$channelid}]的主表信息，无法完成操作！。","javascript:;");
		exit();
	}
	$useip = GetIP();
	$query = "INSERT INTO `$addtable`(aid,typeid,redirecturl,userip,pagestyle,maxwidth,imgurls,row,col,isrm,ddmaxwidth,pagepicnum{$inadd_f})
         Values('$arcID','$typeid','$redirecturl','$useip','$pagestyle','$maxwidth','$imgurls','$row','$col','$isrm','$ddmaxwidth','$pagepicnum'{$inadd_v}); ";
	if(!$dsql->ExecuteNoneQuery($query))
	{
		$gerr = $dsql->GetError();
		$dsql->ExecuteNoneQuery("Delete From `#@__archives` where id='$arcID'");
		$dsql->ExecuteNoneQuery("Delete From `#@__arctiny` where id='$arcID'");
		ShowMsg("把数据保存到数据库附加表 `{$addtable}` 时出错，请把相关信息提交给DedeCms官方。".str_replace('"','',$gerr),"javascript:;");
		exit();
	}

	//生成HTML
	InsertTags($tags,$arcID);
	$artUrl = MakeArt($arcID,true,true);
	if($artUrl=='')
	{
		$artUrl = $cfg_phpurl."/view.php?aid=$arcID";
	}

	//返回成功信息
	$msg = "
    　　请选择你的后续操作：
    <a href='album_add.php?cid=$typeid'><u>继续发布图片</u></a>
    &nbsp;&nbsp;
    <a href='archives_do.php?aid=".$arcID."&dopost=editArchives'><u>更改图集</u></a>
    &nbsp;&nbsp;
    <a href='$artUrl' target='_blank'><u>预览文档</u></a>
    &nbsp;&nbsp;
    <a href='catalog_do.php?cid=$typeid&dopost=listArchives'><u>已发布图片管理</u></a>
    &nbsp;&nbsp;
    <a href='catalog_main.php'><u>网站栏目管理</u></a>
    ";
	$wintitle = "成功发布一个图集！";
	$wecome_info = "文章管理::发布图集";
	$win = new OxWindow();
	$win->AddTitle("成功发布一个图集：");
	$win->AddMsgItem($msg);
	$winform = $win->GetWindow("hand","&nbsp;",false);
	$win->Display();
}
?>