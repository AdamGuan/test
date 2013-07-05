<?php if(isset($left_menu)){ ?>
<script type="text/javascript" language="javascript">

/**
* 自执行:设置左侧manu data
*/
(function($){
	<?php foreach($left_menu as $key=>$menu){?>
	$('#tt<?php echo $key;?>').tree({
	data: <?php echo $menu['list'];?>
	});
	<?php }?>
})(jQuery);

/**
* 自执行:设置左侧manu数的选中状态,以及点击时候的动作
*/
(function($){
	$('ul[tree="tt"]').tree({
		onClick: function(node){
			$.messager.progress();
			//设置所有的树所有节点的icon为空白
			var allTree = $('ul[tree="tt"]');
			for(var i=0;i<allTree.length;++i)
			{
				var treeItem = allTree[i];

				var nodes = $(treeItem).tree('getRoots');

				for(var j=0;j<nodes.length;++j)
				{
					$(treeItem).tree('update', {
						target: nodes[j].target,
						iconCls: 'icon-blank'
					});
				}
			}
			//设置当前选中的节点的icon为选中图标
			$(this).tree('update', {
				target: node.target,
				iconCls: 'icon-ok'
			});
			//打开窗口
			var iframe = document.createElement("iframe");
			$(iframe).attr("width","100%");
			$(iframe).attr("height","100%");
			$(iframe).attr("frameborder",0);
			$(iframe).attr("src",node.attributes.url);

			if (iframe.attachEvent){
				iframe.attachEvent("onload", function(){
					$.messager.progress('close');
				});
			} else {
				iframe.onload = function(){
					$.messager.progress('close');
				};
			}
			$("#iframes").html(iframe);
		}
	});
})(jQuery);

</script>
<?php } ?>

</body>
</html>