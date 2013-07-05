//判断是否为顶级窗口，如果是则跳转到首页
/*
(function($){
	if (window == top)
	{
		location.href='admin'; 
	}
})(jQuery);
*/

/**
* 模块： 按钮
*/
(function(j_module_page){

		/**
		* 设置行内的按钮样式
		*/
		j_module_page.action_button = function(domClass){
			$('.'+domClass).linkbutton({  
			});
		};

		/**
		* 设置datetime
		*/
		j_module_page.set_datetime = function(domid,value){
			$('#'+domid).datetimebox({  
				value: value,
				showSeconds: true  
			}); 
		};
		
		/**
		* 设置linkbutton
		*/
		j_module_page.set_linkbutton = function(domid,icon_cls){
			var $this = $("#"+domid);
			$this.linkbutton({  
				iconCls: icon_cls
			});				
		};

		/**
		* 设置下拉样式
		*/
		j_module_page.set_select = function(domid,datas,multiple){
			var $this = $("#"+domid);
			$this.combobox({  
				data:datas,  
				valueField:'id',  
				textField:'text',
				multiple:multiple,
			}); 				
		};
		
		/**
		* 获取iframe页面内容高度
		*/
		j_module_page.get_content_height = function(){
//			return parseInt($(window).height()) - 27;
			return parseInt(document.body.clientHeight) - 27;
		};
		
		/**
		* 获取iframe页面内容宽带
		*/
		j_module_page.get_content_width = function(){
			return parseInt($(window).width()) -20;
		};
		
		/**
		* 设置成功时的颜色
		*/
		j_module_page.set_color_success = function(text){
			return '<span class="green">'+text+'</span>';
		};
		
		/**
		* 设置失败时的颜色
		*/
		j_module_page.set_color_failed = function(text){
			return '<span class="red">'+text+'</span>';
		};
		
})(window.j_module_page = window.j_module_page || {});