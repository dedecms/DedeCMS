<!--

var curtable = 'diggarea';
var lasttable = 'diggarea';

function resetTabs(t)
{
	var alltable = 'diggarea,memberarea,feedbackarea';
	var tbs  = alltable.split(',');
	for(var i=0;i<tbs.length;i++)
	{
	    if(tbs[i]==curtable) continue;
	    $DE(tbs[i]).style.display = 'none';
	    $DE(tbs[i]+'_t').className = 'bbr';
  }
}

function exchange(aid)
{
	if(curtable==aid) return ;
	lasttable = curtable;
	curtable = aid;
	$DE(aid).style.display = 'block';
	$DE(aid+'_t').className = 'bbr1';
	resetTabs(0);
}

function CheckLogin(){
  var taget_obj = document.getElementById('_userlogin');
  myajax = new DedeAjax(taget_obj,false,false,'','','');
  myajax.SendGet2("member/ajax_loginsta.php");
  DedeXHTTP = null;
}

-->
