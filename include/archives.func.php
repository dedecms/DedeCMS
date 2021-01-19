<?php if (!defined('DEDEINC')) {
    exit("DedeCMS Error: Request Error!");
}

/**
 * 文章帮助函数
 *
 * @version   $Id: archives.func.php 1 15:15 2010年7月7日 $
 * @package   DedeCMS.Libraries
 * @founder   IT柏拉图, https: //weibo.com/itprato
 * @author    DedeCMS团队
 * @copyright Copyright (c) 2007 - 2021, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license   http://help.dedecms.com/usersguide/license.html
 * @link      http://www.dedecms.com
 */

// 为了兼容旧版本文件,这里将函数直接封装到archive小助手中
// 所以这里仅做一个文件引入映射,今后的开发,如果遇到此类函数
// 在开发过程中直接使用helper('archive');即可

helper('archive');
