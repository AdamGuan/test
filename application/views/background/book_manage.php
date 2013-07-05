<body>
	<div id="tabs" class="easyui-tabs" style="margin:0px;" fit="true" border="false">  
		<div title="小说管理" style="padding:10px;">
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
			* 展示book list
			*/
			j_module_page.show_book_list = function(book_list_domid,add_win_domid){
				var $book_list = $("#"+book_list_domid);
				$book_list.datagrid({
					url:"<?php echo base_url('background/c_index/book_manage');?>",
					queryParams:{assign:'get_book_list'},
					rownumbers:true,
					fitColumns:true,
					singleSelect:false,
					width:window.j_module_page.get_content_width(),
					height:(window.j_module_page.get_content_height()-31),
					columns:[[  
						{field:'select',title:'选择',width:100,checkbox:true},
						{field:'book_name',title:'书名',width:100},
						{field:'web_site_uri',title:'HOST',width:150},  
						{field:'book_author',title:'作者',width:100},
						{field:'book_description',title:'描述',width:100,},
						{field:'book_meta_keywords',title:'meta keywords',width:100},
						{field:'book_meta_description',title:'meta description',width:100},
						{field:'book_meta_title',title:'meta title',width:100},
						{field:'book_createTime',title:'创建时间',width:100},
						{field:'process',title:'缓存进度',width:100},
						{field:'actions',title:'操作',width:100,formatter: function(value,row,index){
							return '<a class="actions" href="javascript:void(0);window.j_module_page.open_modify_win(\'modify_win\','+row.book_id+',\'list\')">修改</a>';
						}},
					]],
					onLoadSuccess:function(datas){						
						window.j_module_page.datarows = datas.rows;
						window.j_module_page.refresh_cacheprocess(book_list_domid,datas.total,datas.rows);
						window.j_module_page.action_button('actions');
					},
					toolbar: [
						{
							iconCls: 'icon-add',
							text:'添加',
							handler: function(){
								window.j_module_page.open_add_win(add_win_domid,book_list_domid);
							}
						},'-',
						{
							iconCls: 'icon-remove',
							text:'删除',
							handler: function(){
								$.messager.confirm('确定','确定删除?',function(r){  
									if (r){  
										var checkeds = $book_list.datagrid('getChecked');
										var book_ids = new Array();
										for(var i=0;i<checkeds.length;++i)
										{
											book_ids[i] = checkeds[i].book_id;
										}
										$.messager.progress();
										$.ajax({
											type: "POST",
											url: "<?php echo base_url('background/c_index/book_manage');?>",
											data: "assign=do_delete&book_ids="+book_ids,
											success: function(msg){
												$.messager.progress('close'); 
												var msg = eval("("+msg+")");
												if(msg > 0)
												{
													$book_list.datagrid('reload');
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
							text:'生成缓存',
							handler: function(){
								$.messager.confirm('确定','确定生成整本小说缓存?',function(r){  
									if (r){
										var checked = $book_list.datagrid('getChecked');
										for(var i=0;i<checked.length;++i)
										{
											$.ajax({
												type: "POST",
												url: "<?php echo base_url('background/c_cache/book_cache');?>",
												data: "sign=create_book_cache&book_id="+checked[i].book_id,
												async: true,
												success: function(msg){
												}
											});
										}
									}
								});
							}
						},'-',
						{
							iconCls: 'icon-ok',
							text:'生成目录缓存',
							handler: function(){
								$.messager.confirm('确定','确定生成整本小说的目录缓存?',function(r){  
									if (r){
										var checked = $book_list.datagrid('getChecked');
										for(var i=0;i<checked.length;++i)
										{
											$.ajax({
												type: "POST",
												url: "<?php echo base_url('background/c_cache/book_cache');?>",
												data: "sign=create_book_category_cache&book_id="+checked[i].book_id,
												async: true,
												success: function(msg){
												}
											});
										}
									}
								});
							}
						}
					]
				});  
			};

			/**
			* 打开修改窗口 
			*/
			j_module_page.open_modify_win = function(modify_win_domid,book_id,book_list_domid){

				$.messager.progress(); 

				$.ajax({
					type: "POST",
					url: "<?php echo base_url('background/c_index/book_manage');?>",
					data: "assign=open_modify&book_id="+book_id,
						success: function(msg){
							var msg = eval("("+msg+")");
							var $win = $("#"+modify_win_domid);
							var content = '<table class="tab_data">';
							content += '<tr><td><label for="book_name">书名：</label></td><td><input type="text" name="book_name" id="book_name" value="'+msg.book_name+'" /></td></tr>';

							content += '<tr><td><label for="book_host">主机：</label></td><td><input type="text" name="book_host" id="book_host" value="'+msg.web_site_uri+'" /></td></tr>';

							content += '<tr><td><label for="book_author">作者：</label></td><td><input type="text" name="book_author" id="book_author" value="'+msg.book_author+'" /></td></tr>';

							content += '<tr><td><label for="book_description">描述：</label></td><td><textarea name="book_description" id="book_description">'+msg.book_description+'</textarea></td></tr>';

							content += '<tr><td><label for="book_meta_keywords">meta keywords：</label></td><td><textarea name="book_meta_keywords" id="book_meta_keywords">'+msg.book_meta_keywords+'</textarea></td></tr>';

							content += '<tr><td><label for="book_meta_description">meta description：</label></td><td><textarea name="book_meta_description" id="book_meta_description">'+msg.book_meta_description+'</textarea></td></tr>';

							content += '<tr><td><label for="book_meta_title">meta title：</label></td><td><textarea name="book_meta_title" id="book_meta_title" >'+msg.book_meta_title+'</textarea></td></tr>';

							content += '<tr><td><label for="book_create_time">创建时间：</label></td><td><input type="text" name="book_create_time" id="book_create_time"></input></td></tr>';

							content += '</table>';

							content += '<div><a id="update_save" href="#">保存</a></div>';

							$win.html(content);

							//设置时间样式
							window.j_module_page.set_datetime('book_create_time',msg.book_createTime);

							//设置button样式
							window.j_module_page.set_linkbutton('update_save');
							$('#update_save').bind('click', function(){
								window.j_module_page.update(book_id,modify_win_domid,book_list_domid,'book_name','book_host','book_author','book_description','book_meta_keywords','book_meta_description','book_meta_title','book_create_time');
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
			j_module_page.open_add_win = function(add_win_domid,book_list_domid){

				$.messager.progress(); 
				var $win = $("#"+add_win_domid);
				var content = '<table class="tab_data">';
				content += '<tr><td><label for="book_name">书名：</label></td><td><input type="text" name="book_name" id="book_name" value="" /></td></tr>';

				content += '<tr><td><label for="book_host">主机：</label></td><td><input type="text" name="book_host" id="book_host" value="" /></td></tr>';

				content += '<tr><td><label for="book_author">作者：</label></td><td><input type="text" name="book_author" id="book_author" value="" /></td></tr>';

				content += '<tr><td><label for="book_description">描述：</label></td><td><textarea name="book_description" id="book_description"></textarea></td></tr>';

				content += '<tr><td><label for="book_meta_keywords">meta keywords：</label></td><td><textarea name="book_meta_keywords" id="book_meta_keywords"></textarea></td></tr>';

				content += '<tr><td><label for="book_meta_description">meta description：</label></td><td><textarea name="book_meta_description" id="book_meta_description"></textarea></td></tr>';

				content += '<tr><td><label for="book_meta_title">meta title：</label></td><td><textarea name="book_meta_title" id="book_meta_title" ></textarea></td></tr>';

				content += '</table>';

				content += '<div><a id="add_save" href="#">保存</a></div>';

				$win.html(content);

				//设置button样式
				window.j_module_page.set_linkbutton('add_save');
				$('#add_save').bind('click', function(){
					window.j_module_page.add(add_win_domid,book_list_domid,'book_name','book_host','book_author','book_description','book_meta_keywords','book_meta_description','book_meta_title');
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
			* 设置datetime
			*/
			j_module_page.set_datetime = function(domid,value){
				$('#'+domid).datetimebox({  
					value: value,
					showSeconds: false  
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
			* 更新一本书的信息
			*/
			j_module_page.update = function(book_id,modify_win_domid,book_list_domid,book_name_domid,book_host_domid,book_author_domid,book_description_domid,book_meta_keywords_domid,book_meta_description_domid,book_meta_title_domid,book_create_time_domid){
				//获取值
				var book_name = $("#"+book_name_domid).val();
				var web_site_uri = $("#"+book_host_domid).val();
				var book_author = $("#"+book_author_domid).val();
				var book_description = $("#"+book_description_domid).val();
				var book_meta_keywords = $("#"+book_meta_keywords_domid).val();
				var book_meta_description = $("#"+book_meta_description_domid).val();
				var book_meta_title = $("#"+book_meta_title_domid).val();
				var book_createTime = $("#"+book_create_time_domid).datetimebox('getValue');
				//ajax 修改book
				$.messager.progress(); 
				$.ajax({
					type: "POST",
					url: "<?php echo base_url('background/c_index/book_manage');?>",
					data: "assign=do_modify&book_id="+book_id+"&book_name="+book_name+"&web_site_uri="+web_site_uri+"&book_author="+book_author+"&book_description="+book_description+"&book_meta_keywords="+book_meta_keywords+"&book_meta_description="+book_meta_description+"&book_meta_title="+book_meta_title+"&book_createTime="+book_createTime,
					success: function(msg){
						var msg = eval("("+msg+")");
						if(msg > 0)
						{							
							//关闭修改窗口
							$("#"+modify_win_domid).window('close');
							//刷新小说列表
							$('#'+book_list_domid).datagrid('reload');
							$.messager.progress('close');
						}
						else
						{
							$.messager.alert('错误','修改失败!','error');
						}
					}
				}); 
			};

			/**
			* 增加一本书
			*/
			j_module_page.add = function(add_win_domid,book_list_domid,book_name_domid,book_host_domid,book_author_domid,book_description_domid,book_meta_keywords_domid,book_meta_description_domid,book_meta_title_domid){
				//获取值
				var book_name = $("#"+book_name_domid).val();
				var web_site_uri = $("#"+book_host_domid).val();
				var book_author = $("#"+book_author_domid).val();
				var book_description = $("#"+book_description_domid).val();
				var book_meta_keywords = $("#"+book_meta_keywords_domid).val();
				var book_meta_description = $("#"+book_meta_description_domid).val();
				var book_meta_title = $("#"+book_meta_title_domid).val();
				//ajax 修改book
				$.messager.progress(); 
				$.ajax({
					type: "POST",
					url: "<?php echo base_url('background/c_index/book_manage');?>",
					data: "assign=do_add&book_name="+book_name+"&web_site_uri="+web_site_uri+"&book_author="+book_author+"&book_description="+book_description+"&book_meta_keywords="+book_meta_keywords+"&book_meta_description="+book_meta_description+"&book_meta_title="+book_meta_title,
					success: function(msg){
						var msg = eval("("+msg+")");
						if(msg > 0)
						{							
							//关闭修改窗口
							$("#"+add_win_domid).window('close');
							//刷新小说列表
							$('#'+book_list_domid).datagrid('reload');
							$.messager.progress('close');
						}
						else
						{
							$.messager.alert('错误','修改失败!','error');
						}
					}
				}); 
			};

			/**
			* 刷新小说生成缓存的进度
			*/
			j_module_page.refresh_cacheprocess = function(book_list_domid,book_total,book_list){
				var total = book_total;
				var book_list = window.j_module_page.datarows;
				var book_id = '';
				for(var index=0;index<total;++index)
				{
					book_id = eval('book_list['+index+'].book_id');
					$.ajax({
						type: "POST",
						url: "<?php echo base_url('background/c_cache/book_cache');?>",
						data: "sign=query_cache_process&book_id="+book_id,
						async: false,
						success: function(msg){
							var msg = eval("("+msg+")");
							if(msg == '100%')
							{
								msg = window.j_module_page.set_color_success(msg);
							}
							else if(msg != '无')
							{
								msg = window.j_module_page.set_color_failed(msg);
							}
							$('#'+book_list_domid).datagrid('updateRow',{
								index: index,
								row: {
									process: msg,
								}
							});
						}
					});
				}
				setTimeout("window.j_module_page.refresh_cacheprocess('"+book_list_domid+"',"+book_total+",'"+book_list+"')",5000);
			};

		})(window.j_module_page = window.j_module_page || {});
		
		/**
		* 自执行
		*/
		(function(){

			//展示book list
			window.j_module_page.show_book_list('list','add_win');

		})();
		

	</script>