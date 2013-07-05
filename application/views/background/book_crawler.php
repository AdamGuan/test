<body>
	<div id="tabs" class="easyui-tabs" style="margin:0px;" fit="true" border="false">  
		<div title="小说采集" style="padding:10px;">
			<div id="list"></div>
		</div>
	</div>
	<div id="win"></div>
	<div id="import_win"></div>

	<script type="text/javascript">
		/**
		* 模块：这个页面的js函数
		*/
		(function(j_module_page){
			/**
			* 展示book list
			*/
			j_module_page.show_book_list = function(book_list_domid,win_domid,import_win_domid){
				var $book_list = $("#"+book_list_domid);
				var queryParams = {sign:'get_list'};
				$book_list.datagrid({
					url:"<?php echo base_url('background/c_crawler/book_crawler');?>",
					rownumbers:true,
					queryParams:queryParams,
					fitColumns:true,
					singleSelect:false,
					width:window.j_module_page.get_content_width(),
					height:(window.j_module_page.get_content_height()-31-22),
					columns:[[  
						{field:'book_name',title:'书名',width:100},
						{field:'whole_process',title:'采集进度',width:100},
						{field:'success_total',title:'采集成功数',width:100},
						{field:'fail_total',title:'采集失败数',width:100},
						{field:'fail_process',title:'失败重新采集的进度',width:100},
						{field:'actions',title:'操作',width:200,formatter: function(value,row,index){
							return '<a class="actions" href="javascript:void(0);window.j_module_page.alert_crawler_win(\''+win_domid+'\',\''+row.book_id+'\',\''+row.book_name+'\',\''+book_list_domid+'\')">采集</a> | <a class="actions" href="javascript:void(0);window.j_module_page.alert_recrawler_win(\''+win_domid+'\',\''+row.book_id+'\',\''+row.book_name+'\',\''+book_list_domid+'\')">采集失败</a> | <a class="actions" href="javascript:void(0);window.j_module_page.alert_import_win(\''+import_win_domid+'\',\''+row.book_id+'\')">采集导入</a>';
						}},
					]],
					onLoadSuccess:function(){
						window.j_module_page.action_button('actions');
					},
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
			* 弹出采集确认窗口
			*/
			j_module_page.alert_crawler_win = function(win_domid,book_id,book_name,list_domid){
				$.messager.progress(); 

				var $win = $("#"+win_domid);
				
				var content = '<table class="tab_data">';

				content += '<tr><td>书名：</td><td>'+book_name+'</td></tr>';

				content += '<tr><td><label for="crawler_type">采集方案：</label></td><td><input id="crawler_type" name="crawler_type"></td></tr>';

				content += '</table>';

				content += '<div><a id="crawler_confirm" href="javascript:void(0);window.j_module_page.crawler_book(\''+win_domid+'\',\''+book_id+'\',\'crawler_type\',\''+list_domid+'\')">确认采集</a></div>';

				
				$win.html(content);

				//设置采集方案
				$('#crawler_type').combobox({
					valueField: 'id',  
					textField: 'text',
					data: <?php echo $paln_list;?>
				});
				//设置button
				window.j_module_page.set_ok_linkbutton('crawler_confirm');

				$win.window({  
					width:600,
					modal:true
				});
				$win.window('open');
				$win.window('center');
				$.messager.progress('close');
			};

			/**
			* 弹出重新采集失败的确认窗口
			*/
			j_module_page.alert_recrawler_win = function(win_domid,book_id,book_name,list_domid){
				$.messager.progress(); 
				var $win = $("#"+win_domid);
				
				var content = '<table class="tab_data">';

				content += '<tr><td colspan="2">'+window.j_module_page.set_color_failed('重新采集失败的')+'</td><td></td></tr>';

				content += '<tr><td>书名：</td><td>'+book_name+'</td></tr>';

				content += '<tr><td><label for="recrawler_type">采集方案：</label></td><td><input id="recrawler_type" name="recrawler_type"></td></tr>';

				content += '</table>';

				content += '<div><a id="crawler_confirm" href="javascript:void(0);window.j_module_page.recrawler_book(\''+win_domid+'\',\''+book_id+'\',\'recrawler_type\',\''+list_domid+'\')">确认采集</a></div>';

				
				$win.html(content);

				//设置采集方案
				$('#recrawler_type').combobox({
					valueField: 'id',  
					textField: 'text',
					data: <?php echo $paln_list;?>
				});
				//设置button
				window.j_module_page.set_ok_linkbutton('crawler_confirm');

				$win.window({  
					width:600,
					modal:true
				});
				$win.window('open');
				$win.window('center');
				$.messager.progress('close');
			};
			
			/**
			* 采集整本书
			*/
			j_module_page.crawler_book = function(win_domid,book_id,crawler_plan_domid,list_domid){

				var $win = $("#"+win_domid);
				var crawler_plan_id = $("#"+crawler_plan_domid).combobox('getValue');
				
//				var t = setTimeout("$('#"+list_domid+"').datagrid('reload')",5000);
				window.j_module_page.refreash_list(list_domid,5000);

				$.ajax({
				   type: "POST",
				   url: "<?php echo base_url('background/c_crawler/book_crawler');?>",
				   data: "sign=crawler_whole_book&book_id="+book_id+"&crawler_plan_id="+crawler_plan_id,
				   success: function(msg){
					   clearTimeout(window.j_module_page.settimeout_id);
					   //弹出完成提示
					   $.messager.alert('成功','采集完成');
					   //刷新
					   $("#"+list_domid).datagrid('reload',{sign:'get_list'});
				   }
				}); 
				//关闭采集窗口
				$win.window('close');
			};

			/**
			* 重新采集失败的
			*/
			j_module_page.recrawler_book = function(win_domid,book_id,crawler_plan_domid,list_domid){

				var $win = $("#"+win_domid);
				var crawler_plan_id = $("#"+crawler_plan_domid).combobox('getValue');
				
//				var t = setTimeout("$('#"+list_domid+"').datagrid('reload')",5000);
				window.j_module_page.refreash_list(list_domid,5000);

				$.ajax({
				   type: "POST",
				   url: "<?php echo base_url('background/c_crawler/book_crawler');?>",
				   data: "sign=crawler_failed&book_id="+book_id+"&crawler_plan_id="+crawler_plan_id,
				   success: function(msg){
					   clearTimeout(window.j_module_page.settimeout_id);
					   //弹出完成提示
					   $.messager.alert('成功','采集完成');
					   //刷新
					   $("#"+list_domid).datagrid('reload');
				   }
				}); 
				//关闭采集窗口
				$win.window('close');
			};
			
			/**
			* 弹出导入的窗口
			*/
			j_module_page.alert_import_win = function(import_win_domid,book_id){
				$.messager.progress(); 

				var $win = $("#"+import_win_domid);
				
				var content = '<table class="tab_data">';
				content += '<tr><td>时间：</td><td><input id="base_time" type="text" name="base_time"></input></td></tr>';
				content += '</table>';

				content += '<div><a id="import_btn" href="javascript:void(0);window.j_module_page.import_book(\'base_time\',\''+book_id+'\',\''+import_win_domid+'\')">确定导入</a></div>';

				$win.html(content);

				//设置时间input样式
				$('#base_time').datetimebox({ 
					required: true,  
					showSeconds: true  
				});  
				//设置btn
				window.j_module_page.set_ok_linkbutton('import_btn');

				$win.window({  
					width:600,
					modal:true
				});
				$win.window('open');
				$win.window('center');

				$.messager.progress('close');
			};
			
			/**
			* 导入小说到数据库动作
			*/
			j_module_page.import_book = function(base_time_domid,book_id,import_win_domid){
				$.messager.progress();

				var base_time = $("#"+base_time_domid).datetimebox('getValue');

				$.ajax({
					type: "POST",
					url: "<?php echo base_url('background/c_crawler/book_crawler');?>",
					data: "sign=import&base_time="+base_time+"&book_id="+book_id,
					success: function(msg){
						$.messager.progress('close');
						$.messager.alert('成功','导入完成');						
					}
				}); 

				$("#"+import_win_domid).window('close');
			};

			j_module_page.settimeout_id = 0;

			/****/
			j_module_page.refreash_list = function(list_domid,second){

				$('#'+list_domid).datagrid('reload');

				window.j_module_page.settimeout_id = setTimeout("window.j_module_page.refreash_list('"+list_domid+"',"+second+")",second);
			};

		})(window.j_module_page = window.j_module_page || {});

		/**
		* 自执行
		*/
		(function(){
	
			//展示book list
			window.j_module_page.show_book_list('list','win','import_win');

		})();
		

	</script>