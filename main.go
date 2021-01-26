package main

import (
	"bufio"
	"os"
	"regexp"
	"strings"

	"github.com/mycalf/util"
)

func main() {
	l, _ := util.OS("./dede").Ls("*.htm", "R")

	for _, v := range l {
		formatFile(v)
		fs, _ := util.OS(v).Read()

		fs = deleteExtraSpace(fs)

		str := util.Text("<!--").Enter().
			Add("- @founder   IT柏拉图, https: //weibo.com/itprato").Enter().
			Add("- @author    DedeCMS团队").Enter().
			Add("- @copyright Copyright (c) 2007 - 2021, 上海卓卓网络科技有限公司 (DesDev, Inc.)").Enter().
			Add("--->").Enter().Enter().
			Add(fs).Get()
		util.OS(v).Write([]byte(str), false)

	}
}

func deleteExtraSpace(s string) string {
	//删除字符串中的多余空格，有多个空格时，仅保留一个空格
	s1 := strings.Replace(s, "	", " ", -1)      //替换tab为空格
	regstr := "\\s{2,}"                         //两个及两个以上空格的正则表达式
	reg, _ := regexp.Compile(regstr)            //编译正则表达式
	s2 := make([]byte, len(s1))                 //定义字符数组切片
	copy(s2, s1)                                //将字符串复制到切片
	spcIndex := reg.FindStringIndex(string(s2)) //在字符串中搜索
	for len(spcIndex) > 0 {                     //找到适配项
		s2 = append(s2[:spcIndex[0]+1], s2[spcIndex[1]:]...) //删除多余空格
		spcIndex = reg.FindStringIndex(string(s2))           //继续在字符串中搜索
	}
	return string(s2)
}

func formatFile(filename string) error {
	f, err := os.OpenFile(filename, os.O_RDWR, 0666)
	if nil != err {
		return err
	}
	defer f.Close()
	var content string
	fc := bufio.NewScanner(f)
	for fc.Scan() {
		temp := strings.Trim(fc.Text(), " ") + "\r\n"
		content += strings.Trim(temp, "	")
	}

	err = f.Truncate(0)
	if nil != err {
		return err
	}

	_, err = f.Seek(0, 0)

	if nil != err {
		return err
	}

	_, err = f.WriteString(content)
	if nil != err {
		return err
	}

	return nil
}
