/**
 * 
 * @version        $Id: diy.js 1 22:28 2010年7月20日 $
 * @package        DedeCMS.Administrator
 * @founder        IT柏拉图, https://weibo.com/itprato
 * @author         DedeCMS团队
 * @copyright      Copyright (c) 2007 - 2021, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
 
 
function showHide2(objname){
    var obj = $Obj(objname);
    if(obj.style.display != 'block'){ obj.style.display = 'block' }
    else{  obj.style.display = 'none'; }
}