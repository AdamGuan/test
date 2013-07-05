<body>
	<div id="tabs" class="easyui-tabs" style="margin:0px;" fit="true" border="false">  
		<div title="整本小说缓存" style="padding:10px;">
			<div id="list"></div>
		</div>
	</div>

	<script type="text/javascript">

		

		/**
		* 模块：这个页面的js函数
		*/
		(function(j_module_page){
			/**
			* 展示book list
			*/
			j_module_page.show_book_list = function(book_list_domid){
				var $book_list = $("#"+book_list_domid);
				$book_list.datagrid({
					data:<?php echo $book_list;?>,
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
						{field:'process',title:'进度',width:100},
					]],
					onLoadSuccess:function(){
							//window.j_module_page.refresh_cacheprocess(book_list_domid);
							window.j_module_page.refresh_cacheprocess(book_list_domid);
					},
					toolbar: [
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
						}
					]
				});  
			};
			
			/**
			* 刷新小说生成缓存的进度
			*/
			j_module_page.refresh_cacheprocess = function(book_list_domid){
				var total = <?php echo $book_total;?>;
				var book_list = <?php echo $book_list;?>;
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
				setTimeout("window.j_module_page.refresh_cacheprocess('"+book_list_domid+"')",5000);
			};

		})(window.j_module_page = window.j_module_page || {});

		//展示book list
		window.j_module_page.show_book_list('list');
		
		//刷新小说生成缓存的进度
		//window.j_module_page.refresh_cacheprocess('list');

	</script>