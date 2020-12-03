/*评分效果*/
$(function(){
	var settings = {
		after_click:  $("ul.rating li a").click(function() {$(this).enabled= false;})
	}; 
	
	//通过修改样式来显示不同的星级
    $("ul.rating li a").click(function(){
	     var title = $(this).attr("title");
	     //alert("您给此书的评分是："+title);
		 var cl = $(this).parent().attr("class");
		 $(this).parent().parent().removeClass().addClass("rating "+cl+"star");
		 $(this).blur();//去掉超链接的虚线框
		 if (!settings.enabled) {	// 若是不能更改，则隐藏
			$(".rating > li a").hide();
		}
		 return false;
	})
	
	// 处理回调事件
	if (typeof settings.after_click == 'function') {
	 settings.after_click(data);
	}
})