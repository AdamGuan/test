/**
* 模块定义
*/
(function(jModulePage){
	
	/**
	* 键盘动作函数
	*/
	jModulePage.keywordSubmit = function(evt){
		evt = window.event || evt;
		if(evt.keyCode ==13 ){//如果取到的键值是回车
			var href = $("#category").attr("href");
			if(href != undefined)
			{
				window.location.href=href;
			}
		}
		if(evt.keyCode ==39 ){//如果取到的键值是right
			var href = $("#next_article").attr("href");
			if(href != undefined)
			{
				window.location.href=href;
			}			
		}
		if(evt.keyCode ==37 ){//如果取到的键值是left
			var href = $("#pre_article").attr("href");
			if(href != undefined)
			{
				window.location.href=href;
			}	
		}
	}

})(window.jModulePage = window.jModulePage || {});


/**
* 自执行
*/
(function(){

	//当有键按下时执行函数
	window.document.onkeydown=window.jModulePage.keywordSubmit;

})();
