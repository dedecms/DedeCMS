<!--

function checkSubmitAlb()
{
   if(document.form1.title.value==""){
	   alert("档案标题不能为空！");
	   return false;
  }
  if(document.form1.likeid.value!="" && HasNotVd(document.form1.likeid.value)){
	   alert("相关文档必须填写正确的ID，如ID1,ID2...");
	   return false;
  }
  if(document.form1.typeid.value==0){
	   alert("请选择档案的主类别！");
	   return false;
  }
  document.form1.imagebody.value = $Obj('copyhtml').innerHTML;
  return true;
}

function CheckSelTable(nnum){
	var cbox = $Obj('isokcheck'+nnum);
	var seltb = $Obj('seltb'+nnum);
	if(!cbox.checked) seltb.style.display = 'none';
	else seltb.style.display = 'block';
}

var startNum = 1;
function MakeUpload(mnum)
{
   var endNum = 0;
   var upfield = document.getElementById("uploadfield");
   var pnumObj = document.getElementById("picnum");
   var fhtml = "";
   var dsel = " checked='checked' ";
   var dplay = "display:none";
 
   if(mnum==0) endNum = startNum + Number(pnumObj.value);
   else endNum = mnum;
   if(endNum>120) endNum = 120;
   
   
   
   for(startNum;startNum < endNum;startNum++)
   {
	   if(startNum==1){
	      dsel = " checked='checked' ";
		  dplay = "block";
	   }else
	   {
	      dsel = " ";
        dplay = "display:none";
	   }
	   fhtml = "";
	   fhtml += "<table width='600'><tr><td><input type='checkbox' name='isokcheck"+startNum+"' id='isokcheck"+startNum+"' value='1' class='np' "+dsel+" onClick='CheckSelTable("+startNum+")' />显示图片["+startNum+"]的选取框</td></tr></table>";
	   fhtml += "<table width=\"600\" border=\"0\" id=\"seltb"+startNum+"\" cellpadding=\"1\" cellspacing=\"1\" bgcolor=\"#E8F5D6\" style=\"margin-bottom:6px;margin-left:10px;"+dplay+"\"><tobdy>";
	   fhtml += "<tr bgcolor=\"#F4F9DD\">\r\n";
	   fhtml += "<td height=\"25\" colspan=\"2\">　<strong>图片"+startNum+"：</strong></td>";
	   fhtml += "</tr>";
	   fhtml += "<tr bgcolor=\"#FFFFFF\"> ";
	   fhtml += "<td width=\"429\" height=\"25\"> 　本地上传： ";
	   fhtml += "<input type=\"file\" name='imgfile"+startNum+"' style=\"width:330px\"  onChange=\"SeePic(document.picview"+startNum+",document.form1.imgfile"+startNum+");\"></td>";
	   fhtml += "<td width=\"164\" rowspan=\"3\" align=\"center\"><img src=\"img/pview.gif\" width=\"150\" id=\"picview"+startNum+"\" name=\"picview"+startNum+"\"></td>";
	   fhtml += "</tr>";
	   fhtml += "<tr bgcolor=\"#FFFFFF\"> ";
	   fhtml += "<td height=\"25\"> 　指定网址： ";
	   fhtml += "<input type=\"text\" name='imgurl"+startNum+"' style=\"width:260px\"> ";
	   fhtml += "<input type=\"button\" name='selpic"+startNum+"' value=\"选取\" style=\"width:65px\" class=\"inputbut\" onClick=\"SelectImageN('form1.imgurl"+startNum+"','big','picview"+startNum+"')\">";
	   fhtml += "</td></tr>";
	   fhtml += "<tr bgcolor=\"#FFFFFF\"> ";
	   fhtml += "<td height=\"56\">　图片简介： ";
	   fhtml += "<textarea name='imgmsg"+startNum+"' style=\"height:46px;width:330px\"></textarea> </td>";
	   fhtml += "</tr></tobdy></table>\r\n";
	   upfield.innerHTML += fhtml;
  }
}

function TestGet()
{
	LoadTestDiv();
}

function LoadTestDiv()
{
	var posLeft = 100; var posTop = 100;
  var newobj = $Obj('_myhtml');
  $Obj('imagebody').value = $Obj('copyhtml').innerHTML;
	if($Obj('imagebody').value=='粘贴到这里...')
	{
	  alert('你还没有粘贴任何东西都编辑框哦！');
	  return;
	}
  if(!newobj){
  	newobj = document.createElement("DIV");
    newobj.id = '_myhtml';
    newobj.style.position='absolute';
    newobj.className = "dlg2";
    newobj.style.top = posTop;
    newobj.style.left = posLeft;
    document.body.appendChild(newobj);
  }
  else{
  	newobj.style.display = "block";
  }
	var myajax = new DedeAjax(newobj,false,true,'-','-','...');
	myajax.AddKey('myhtml',$Obj('imagebody').value);
	myajax.SendPost2('album_testhtml.php');
	DedeXHTTP = null;
}

function checkMuList(psid,cmid)
{
	if($Obj(psid).checked) $Obj(cmid).style.display = 'block';
	else  $Obj(cmid).style.display = 'none';
}


-->