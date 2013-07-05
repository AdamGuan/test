<body>
	<script type="text/javascript" src="<?php echo base_url('js/ckedit/ckeditor.js');?>"></script>

	<div id="tabs" class="easyui-tabs" style="margin:0px;" fit="true" border="false">  
		<div title="章节管理" style="padding:10px;">
			<div id="options">
				<span style="width: 151px; height: 20px;">小说选择</span>
				<input id="book_id" name="book_id" value="">
				&nbsp;&nbsp;
				<span style="width: 151px; height: 20px;">章节状态</span>
				<input id="article_status" name="article_status" value="">
				<a id="article_query" href="javascript:void(0);window.j_module_page.query_action('list','book_id','article_status');" class="easyui-linkbutton" data-options="iconCls:'icon-search'">查询</a>
			</div>
			<div id="list"></div>
		</div>
	</div>

	<div id="modify_win"></div>
	<div id="add_win"></div>

	<script type="text/javascript">
		/**
		* 模块：这个页面的js函数
		*/
		(function(j_module_page){
			/**
			* 展示article list
			*/
			j_module_page.show_article_list = function(article_list_domid,add_win_domid,book_belong_domid,article_status_domid){				
				var $article_list = $("#"+article_list_domid);
				//获取book id
				var book_belong = $("#"+book_belong_domid).combobox('getValue');
				
				//获取article status
				var article_status = $("#"+article_status_domid).combobox('getValue');
				var queryParams = {assign:'get_article_list',status:article_status,book_belong:book_belong};
				$article_list.datagrid({
					url:"<?php echo base_url('background/c_index/article_manage');?>",
					queryParams:queryParams,
					rownumbers:true,
					fitColumns:true,
					singleSelect:false,
					pagination:true,
					pageSize:10,
					pageList:[10,20,30,40,50],
					width:window.j_module_page.get_content_width(),
					height:(window.j_module_page.get_content_height()-26-31),
					columns:[[  
						{field:'article_id',title:'id',width:100,checkbox:true},
						{field:'article_title',title:'章节标题',width:100},
						{field:'article_createTime',title:'添加时间',width:100},
						{field:'is_divide',title:'分隔用',width:150,formatter: function(value,row,index){
							if(value > 0)
							{
								return window.j_module_page.set_color_failed('是');
							}
							return window.j_module_page.set_color_success('不是');
						}},  
						{field:'status',title:'状态',width:100,formatter: function(value,row,index){
							if(value > 0)
							{
								return window.j_module_page.set_color_success('有效');
							}
							return window.j_module_page.set_color_failed('无效');
						}},
						{field:'cache_exists',title:'缓存',width:100,formatter: function(value,row,index){
							if(value > 0)
							{
								return window.j_module_page.set_color_success('有效');
							}
							return window.j_module_page.set_color_failed('无效');
						}},
						{field:'actions',title:'操作',width:100,formatter: function(value,row,index){
							return '<a class="actions" href="javascript:void(0);window.j_module_page.open_modify_win(\'modify_win\','+row.article_id+',\''+article_list_domid+'\',\''+article_status_domid+'\',\''+book_belong_domid+'\')">修改</a>';
						}},
					]],
					onLoadSuccess:function(){
						window.j_module_page.action_button('actions');
					},
					toolbar: [
						{
							iconCls: 'icon-add',
							text:'添加',
							handler: function(){
								window.j_module_page.open_add_win(book_belong_domid,add_win_domid,article_list_domid,article_status_domid);
							}
						},'-',
						{
							iconCls: 'icon-remove',
							text:'删除',
							handler: function(){
								$.messager.confirm('确定','确定删除?',function(r){  
									if (r){  
										var checkeds = $article_list.datagrid('getChecked');
										var article_ids = new Array();
										for(var i=0;i<checkeds.length;++i)
										{
											article_ids[i] = checkeds[i].article_id;
										}
										$.messager.progress();
										$.ajax({
											type: "POST",
											url: "<?php echo base_url('background/c_index/article_manage');?>",
											data: "assign=del_article&article_ids="+article_ids,
											success: function(msg){
												$.messager.progress('close'); 
												var msg = eval("("+msg+")");
												if(msg > 0)
												{
													//获取book id
													var book_belong = $("#"+book_belong_domid).combobox('getValue');
													//获取article status
													var article_status = $("#"+article_status_domid).combobox('getValue');

													var queryParams = {assign:'get_article_list',status:article_status,book_belong:book_belong};
													$article_list.datagrid('reload',queryParams);
												}
												else
												{
													$.messager.alert('失败','删除失败！','error');
												}
											}
										});
									}
								});								 
							}
						},'-',
						{
							iconCls: 'icon-save',
							text:'更新缓存',
							handler: function(){
								window.j_module_page.cache_update(article_list_domid);
							}
						}
					]
				});  
			};

			/**
			* 打开修改窗口 
			*/
			j_module_page.open_modify_win = function(modify_win_domid,article_id,article_list_domid,article_list_status_domid,article_list_book_belong_domid){

				$.messager.progress(); 

				$.ajax({
					type: "POST",
					url: "<?php echo base_url('background/c_index/article_manage');?>",
					data: "assign=get_an_article_info&article_id="+article_id,
						success: function(msg){
							var msg = eval("("+msg+")");
							var $win = $("#"+modify_win_domid);
							var content = '<table class="tab_data">';
							content += '<tr><td><label for="modify_book_name">书名：</label></td><td><input id="modify_book_name" name="modify_book_name" value=""></td></tr>';

							content += '<tr><td><label for="modify_article_title">标题</label></td><td><input type="text" name="modify_article_title" id="modify_article_title" value="'+msg.article_title+'" /></td></tr>';

							content += '<tr><td><label for="modify_article_create_time">时间：</label></td><td><input type="text" name="modify_article_create_time" id="modify_article_create_time"></input></td></tr>';

							content += '<tr><td><label for="modify_article_status">状态</label></td><td><input name="modify_article_status" id="modify_article_status" value=""></td></tr>';

							content += '<tr><td><label for="modify_article_is_divide">是否为分隔作用</label></td><td><input name="modify_article_is_divide" id="modify_article_is_divide" value=""></td></tr>';

							content += '<tr><td><label for="modify_article_content">内容：</label></td><td><textarea class="ckeditor" name="modify_article_content" id="modify_article_content">'+msg.article_content+'</textarea></td></tr>';

							content += '</table>';

							content += '<div><a id="update_save" href="#">保存</a></div>';

							$win.html(content);

							//设置ckedit
							CKEDITOR.replace( 'modify_article_content',{width:400, height:30});

							//设置时间样式
							window.j_module_page.set_datetime('modify_article_create_time',msg.article_createTime);

							//设置下拉样式
							//小说
							window.j_module_page.set_select('modify_book_name',msg.book_select_data,false);
							//状态
							var json_datas = [{'id':'0','text':'无效','selected':true},{'id':'1','text':'有效'}];
							if(msg.status > 0)
							{
								json_datas = [{'id':'0','text':'无效'},{'id':'1','text':'有效','selected':true}];
							}
							window.j_module_page.set_select('modify_article_status',json_datas,false);
							//是否为分隔作用
							json_datas = [{'id':'0','text':'不是','selected':true},{'id':'1','text':'是'}];
							if(msg.is_divide > 0)
							{
								json_datas = [{'id':'0','text':'不是'},{'id':'1','text':'是','selected':true}];
							}
							window.j_module_page.set_select('modify_article_is_divide',json_datas,false);

							//设置button样式
							window.j_module_page.set_linkbutton('update_save','icon-save');
							$('#update_save').bind('click', function(){
								window.j_module_page.update(article_id,'modify_book_name','modify_article_title','modify_article_create_time','modify_article_status','modify_article_is_divide','modify_article_content',modify_win_domid,article_list_domid,article_list_status_domid,article_list_book_belong_domid);
							});

							$win.window({  
								width:600,
								modal:true
							});
							$win.window('open');
							$win.window('center');
							$.messager.progress('close'); 
						}
				});
			};

			/**
			* 打开添加窗口 
			*/
			j_module_page.open_add_win = function(book_belong_domid,add_win_domid,article_list_domid,article_list_status_domid){

				$.messager.progress(); 
				var $win = $("#"+add_win_domid);
				var content = '<table class="tab_data">';
				content += '<tr><td><label for="add_book_name">书名：</label></td><td><input id="add_book_name" name="add_book_name" value=""></td></tr>';

				content += '<tr><td><label for="add_article_title">标题</label></td><td><input type="text" name="add_article_title" id="add_article_title" value="" /></td></tr>';

				content += '<tr><td><label for="add_article_create_time">时间：</label></td><td><input type="text" name="add_article_create_time" id="add_article_create_time"></input></td></tr>';

				content += '<tr><td><label for="add_article_status">状态</label></td><td><input name="add_article_status" id="add_article_status" value=""></td></tr>';

				content += '<tr><td><label for="add_article_is_divides">是否为分隔作用</label></td><td><input name="add_article_is_divides" id="add_article_is_divides" value=""></td></tr>';

				content += '<tr><td><label for="add_article_content">内容：</label></td><td><textarea name="add_article_content" class="ckeditor" id="add_article_content"></textarea></td></tr>';

				content += '</table>';

				content += '<div><a id="add_save" href="#">保存</a></div>';

				$win.html(content);

				//设置时间样式
				window.j_module_page.set_datetime('add_article_create_time','<?php echo date("Y-m-d H:i:s",time())?>');

				//设置ckedit
				CKEDITOR.replace( 'add_article_content',{width:400, height:30} );


				//设置下拉样式
				//小说
				window.j_module_page.set_select('add_book_name',<?php echo $book_select_data;?>);
				var current_book_belong = $("#"+book_belong_domid).combobox("getValue");
				$('#add_book_name').combobox('setValue', current_book_belong);

				//状态
				var json_datas = [{'id':'0','text':'无效'},{'id':'1','text':'有效','selected':true}];
				window.j_module_page.set_select('add_article_status',json_datas,false);
				//是否为分隔作用
				json_datas = [{'id':'0','text':'不是','selected':true},{'id':'1','text':'是'}];
				window.j_module_page.set_select('add_article_is_divides',json_datas,false);

				//设置button样式
				window.j_module_page.set_linkbutton('add_save','icon-save');
				$('#add_save').bind('click', function(){
					window.j_module_page.add('add_book_name','add_article_title','add_article_create_time','add_article_status','add_article_is_divides','add_article_content',add_win_domid,article_list_domid,article_list_status_domid,book_belong_domid);
				});

				$win.window({  
					width:600,
					modal:true
				});
				$win.window('open');
				$win.window('center');
				$.messager.progress('close'); 
			};			

			/**
			* 更新一章节的信息
			*/
			j_module_page.update = function(article_id,book_domid,article_title_domid,article_createTime_domid,status_domid,is_divide_domid,article_content_domid,modify_win_domid,article_list_domid,article_list_status_domid,article_list_book_belong_domid){
				//获取值
				var book_belong = $("#"+book_domid).combobox('getValue');			
				var article_title = $("#"+article_title_domid).val();
				var article_createTime = $("#"+article_createTime_domid).datetimebox('getValue');
				var status = $("#"+status_domid).combobox('getValue');
				var is_divide = $("#"+is_divide_domid).combobox('getValue');
				var article_content = CKEDITOR.instances.modify_article_content.getData();

				//ajax 修改article
				$.messager.progress(); 
				$.ajax({
					type: "POST",
					url: "<?php echo base_url('background/c_index/article_manage');?>",
					data: "assign=modify_an_article&article_id="+article_id+"&book_belong="+book_belong+"&article_title="+article_title+"&article_createTime="+article_createTime+"&status="+status+"&is_divide="+is_divide+"&article_content="+escape(article_content),
					success: function(msg){
						$.messager.progress('close');
						//var msg = eval("("+msg+")");
						if(msg > 0)
						{							
							//关闭修改窗口
							$("#"+modify_win_domid).window('close');
							//刷新小说列表
							var article_list_status = $("#"+article_list_status_domid).combobox('getValue');
							var article_list_book_belong = $("#"+article_list_book_belong_domid).combobox('getValue');
							var queryParams = {assign:'get_article_list',status:article_list_status,book_belong:article_list_book_belong};
							$('#'+article_list_domid).datagrid('reload',queryParams);
						}
						else
						{
							$.messager.alert('错误','修改失败!','error');
						}
					}
				}); 
			};

			/**
			* 增加一章节
			*/
			j_module_page.add = function(book_domid,article_title_domid,article_createTime_domid,status_domid,is_divide_domid,article_content_domid,add_win_domid,article_list_domid,article_list_status_domid,article_list_book_belong_domid){
				//获取值
				var book_belong = $("#"+book_domid).combobox('getValue');				
				var article_title = $("#"+article_title_domid).val();
				var article_createTime = $("#"+article_createTime_domid).datetimebox('getValue');
				var status = $("#"+status_domid).combobox('getValue');
				var is_divide = $("#"+is_divide_domid).combobox('getValue');
				var article_content = CKEDITOR.instances.add_article_content.getData();
				//ajax 修改book
				$.messager.progress(); 
				$.ajax({
					type: "POST",
					url: "<?php echo base_url('background/c_index/article_manage');?>",
					data: "assign=add_an_article&book_belong="+book_belong+"&article_title="+article_title+"&article_createTime="+article_createTime+"&status="+status+"&is_divide="+is_divide+"&article_content="+article_content,
					success: function(msg){
						//var msg = eval("("+msg+")");
						$.messager.progress('close');
						if(msg > 0)
						{							
							//关闭修改窗口
							$("#"+add_win_domid).window('close');
							//刷新小说列表
							var article_list_status = $("#"+article_list_status_domid).combobox('getValue');
							var article_list_book_belong = $("#"+article_list_book_belong_domid).combobox('getValue');
							var queryParams = {assign:'get_article_list',status:article_list_status,book_belong:article_list_book_belong};
							$('#'+article_list_domid).datagrid('reload',queryParams);
						}
						else
						{
							$.messager.alert('错误','添加失败!','error');
						}
					}
				}); 
			};

			/**
			* 查询按钮的动作
			*/
			j_module_page.query_action = function(article_list_domid,book_belong_domid,article_status_domid){
				//获取选择的小说 以及章节状态
				var book_belong = $("#"+book_belong_domid).combobox('getValue');
				var article_status = $("#"+article_status_domid).combobox('getValue');
				//更新章节列表
				var queryParams = {assign:'get_article_list',status:article_status,book_belong:book_belong};
				$('#'+article_list_domid).datagrid('reload',queryParams);				
			};

			/**
			* 章节缓存更新
			*/
			j_module_page.cache_update = function(article_list_domid){

				$.messager.alert('通知','缓存生成中.');

				var $article_list = $('#'+article_list_domid);
				var checkeds = $article_list.datagrid('getChecked');
				
				for(var i=0;i < checkeds.length;++i)
				{
					var indexs = $article_list.datagrid('getRowIndex',checkeds[i]);
					var article_id = checkeds[i].article_id;					$article_list.datagrid('updateRow',{
						index: indexs,
						row: {
							cache_exists: '生成中..'
						}
					});

					$.ajax({
						type: "POST",
						url: "<?php echo base_url('background/c_cache/article_cache');?>",
						data: "sign=create_article_cache&article_id="+article_id,
						async: false,
						success: function(msg){
							$article_list.datagrid('updateRow',{
								index: indexs,								
								row: {
									cache_exists: msg
								}
							});
						}
					}); 	
				}
				window.j_module_page.action_button('actions');
			};

		})(window.j_module_page = window.j_module_page || {});
		
		
		/**
		* 自执行
		*/
		(function(){

			//设置小说选择下拉 以及 装接状态下拉
			window.j_module_page.set_select('book_id',<?php echo $book_select_data;?>,false);
			window.j_module_page.set_select('article_status',<?php echo $article_status_select_data;?>,false);

			//展示article list
			window.j_module_page.show_article_list('list','add_win','book_id','article_status');

		})();
		

	</script>