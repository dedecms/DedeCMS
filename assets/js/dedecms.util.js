"use strict";

/**
* 从对象数组中删除键为objKey，值为objValue的对象
*
* @param  Array   objArray  对象数组
* @param  String  objKey    对象键
* @param  String  objValue  对象值
*/
function objArrayRemove(objArray, objKey, objValue) {
    return $.grep(objArray, function(n, i) {
        return n[objKey] == objValue;
    }, true);
}

/**
* 生成min到max的随机数
*
* @param  Int  min  最小值
* @param  Int  max  最大值
*/
function random(min, max) {
    return Math.floor(Math.random() * (max - min)) + min;
}
