<body>
	<div id="tabs" class="easyui-tabs" style="margin:0px;" fit="true" border="false">  
		<div title="小说采集配置" style="padding:10px;">
			<input id="plan_id" name="plan_id">
			<a id="confirm_link" href="javascript:void(0);window.j_module_page.plan_confirm('plan_id','list');">确认</a>
			<div id="list"></div>
		</div>
	</div>
	<div id="modify_win"></div>

	<script type="text/javascript">
		/**
		* 模块：这个页面的js函数
		*/
		(function(j_module_page){
			/**
			* 展示book list
			*/
			j_module_page.show_book_list = function(book_list_domid,list,fields){
				var $book_list = $("#"+book_list_domid);
				$book_list.datagrid({
					data:list,
					rownumbers:true,
					fitColumns:true,
					singleSelect:false,
					width:window.j_module_page.get_content_width(),
					height:(window.j_module_page.get_content_height()-31-22),
					columns:fields,
					onLoadSuccess:function(){
						window.j_module_page.action_button('actions');
					},
				});  
			};

			/**
			* 打开修改窗口 
			*/
			j_module_page.open_modify_win = function(book_id,book_name){
				$.messager.progress(); 
				
				var plan_id = $("#plan_id").combobox('getValue');

				$.ajax({
					type: "POST",
					url: "<?php echo base_url('background/c_crawler/book_crawler_config');?>",
					data: "sign=get_an_book_info&book_name="+book_name+"&book_id="+book_id+"&plan_id="+plan_id,
						success: function(msg){
							var msg = eval("("+msg+")");
							var table = msg.table;
							var content = table+'<div><a  id="modify_save" href="javascript:void(0);window.j_module_page.update(\''+book_id+'\');">保存</a><div>';

							var $win = $("#modify_win");
							$win.html(content);

							window.j_module_page.set_linkbutton('modify_save');

							$win.window({  
								width:600,
								modal:true,
								closed:false,
								region:'center'
							});
							$.messager.progress('close'); 
						}
				});

			};
			
			/**
			* 设置linkbutton
			*/
			j_module_page.set_linkbutton = function(domid){
				var $this = $("#"+domid);
				$this.linkbutton({  
					iconCls: 'icon-save'
				});				
			};

			/**
			* 设置ok linkbutton
			*/
			j_module_page.set_ok_linkbutton = function(domid){
				var $this = $("#"+domid);
				$this.linkbutton({  
					iconCls: 'icon-ok'
				});				
			};

			/**
			* 更新一本书的信息
			*/
			j_module_page.update = function(book_id){
				//获取值
				var datas = '1';
				var $table_inputs = $("#tab_data input");
				for(var i=0;i<$table_inputs.length;++i)
				{
					datas += '&'+$($table_inputs[i]).attr("name")+'='+$($table_inputs[i]).val();
				}
				var plan_id = $("#plan_id").combobox('getValue');

				//ajax 修改
				$.messager.progress(); 
				$.ajax({
					type: "POST",
					url: "<?php echo base_url('background/c_crawler/book_crawler_config');?>",
					data: 'sign=doconfig&crawler_plan_id='+plan_id+'&'+datas+"&book_id="+book_id,
					success: function(msg){
						$.messager.progress('close');
						if(msg > 0)
						{
							//关闭修改窗口
							$("#modify_win").window('close');
							//刷新小说列表
							window.j_module_page.set_book_list('plan_id','list');
						}
						else
						{
							$.messager.alert('错误','修改失败!','error');
						}
					}
				}); 
			};
			
			/**
			* 设置方案选择的下拉
			*/
			j_module_page.set_plan_select = function(plan_domid){

				$('#'+plan_domid).combobox({  
					data:<?php echo $crawler_plan_json;?>,  
					valueField:'id',  
					textField:'text'  
				});
			};

			/**
			* 设置book list
			*/
			j_module_page.set_book_list = function(plan_domid,list_domid){

				var plan_id = $('#'+plan_domid).combobox('getValue');
				$.ajax({
					type: "POST",
					url: "<?php echo base_url('background/c_crawler/book_crawler_config');?>",
					data: "sign=get_book_config_list&plan_id="+plan_id,
					success: function(msg){
						var msg = eval('('+msg+')');
						var list = msg.book_list;
						var fields = msg.fields;
						window.j_module_page.show_book_list(list_domid,list,fields);
					}
				}); 
			};
			
			/**
			* 方案确认的动作
			*/
			j_module_page.plan_confirm = function(plan_domid,list_domid){

				window.j_module_page.set_book_list(plan_domid,list_domid);
			};

		})(window.j_module_page = window.j_module_page || {});

		/**
		* 自执行
		*/
		(function(){
			
			//设置方案选择的下拉
			window.j_module_page.set_plan_select('plan_id');

			//设置方案确认的按钮样式
			window.j_module_page.set_ok_linkbutton('confirm_link');
			
			//展示book list
			window.j_module_page.set_book_list('plan_id','list');

		})();
		

	</script>