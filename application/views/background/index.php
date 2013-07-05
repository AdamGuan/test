<body class="easyui-layout">
	<!-- BOF,header-->
	<div data-options="region:'north',split:'true',border:false" style="height:50px;padding:10px;background:rgb(102, 102, 102)">
		<a id="btn" href="<?php echo base_url('background/c_login/login_out');?>" class="easyui-linkbutton">登出</a>
	</div>
	<!-- EOF,header-->

	<!-- BOF,footer-->
	<div data-options="region:'south',split:true" style="height:50px;padding:10px;background:#efefef;">
		Copyright By Adam.<br />单本小说站群管理系统
	</div>
	<!-- EOF,footer-->

	<!-- BOF,left-->
	<div data-options="region:'west',split:true" style="width:180px;padding1:1px;overflow:hidden;">
		<div class="easyui-accordion" fit="true" border="false">
			<?php foreach($left_menu as $key=>$menu){?>
			<div title="<?php echo $menu['title'];?>" style="overflow:auto;">
				<ul id="tt<?php echo $key;?>" tree="tt"></ul>
			</div>
			<?php }?>
		</div>
	</div>
	<!-- EOF,left-->

	<!-- BOF,center-->
	<div id="mainContents" data-options="region:'center',split:true" style="overflow:auto;">
		<div id="iframes">
			<iframe src="<?php echo $default_page;?>" height="100%" width="100%" frameborder="0"></iframe>
		</div>
	</div>
	<!-- EOF,center-->