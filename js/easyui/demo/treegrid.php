<?
function arrToTreegrid($arr){
	$tmp = array();
	foreach($arr as $key => $value){
		if(is_array($value)){
			$tmp[] = array(
				"name" => $key,
				"type"=> "Array",
				"children"=>arrToTreegrid($value)
			);
		}else{
			$tmp[] = array(
				"name" => $key,
				"value"=> $value,
				"type"=> gettype($value),
			);
		}
	}
	return $tmp;
}

$arr = array("kk"=>"asdf","bb"=>125);

$arr["cc"] = array("cc2"=>"adsa","cc3"=>"aaas","cc4"=>array(2,3));

$arr = $argc ;

$bb = json_encode(arrToTreegrid($arr));

echo "<pre>";
print_r($GLOBALS );
echo "</pre>";


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>jQuery EasyUI</title>
	<link rel="stylesheet" type="text/css" href="../themes/default/easyui.css">
	<link rel="stylesheet" type="text/css" href="../themes/icon.css">
	<script type="text/javascript" src="../jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="../jquery.easyui.min.js"></script>

	<script>
	//var data = <?php echo $bb?>;
		$(function(){
			var data = <?php echo $bb?>;
			//alert(data[0]['name']);
			$('#test').treegrid({
				title:'TreeGrid',
				iconCls:'icon-save',
				width:500,
				height:350,
				nowrap: false,
				rownumbers: true,
				animate:true,
				collapsible:true,
				idField:'name',
				treeField:'name',
				frozenColumns:[[
	                {title:'name',field:'name',width:150,
		                formatter:function(value){
		                	return '<span style="color:red">'+value+'</span>';
		                }
	                }
				]],
				columns:[[
					{field:'value',title:'Value',width:120},
					{field:'type',title:'Type',width:120,rowspan:2},
				]],

				onContextMenu: function(e,row){
					e.preventDefault();
					$(this).treegrid('unselectAll');
					$(this).treegrid('select', row.code);
					$('#mm').menu('show', {
						left: e.pageX,
						top: e.pageY
					});
				}
			});

			$('#test').treegrid('append', {
				parent: null,
				data: data
			});
		});

		function reload(){
			var node = $('#test').treegrid('getSelected');
			if (node){
				$('#test').treegrid('reload', node.code);
			} else {
				$('#test').treegrid('reload');
			}
		}
		function getChildren(){
			var node = $('#test').treegrid('getSelected');
			if (node){
				var nodes = $('#test').treegrid('getChildren', node.code);
			} else {
				var nodes = $('#test').treegrid('getChildren');
			}
			var s = '';
			for(var i=0; i<nodes.length; i++){
				s += nodes[i].code + ',';
			}
			alert(s);
		}
		function getSelected(){
			var node = $('#test').treegrid('getSelected');
			if (node){
				alert(node.code+":"+node.name);
			}
		}
		function collapse(){
			var node = $('#test').treegrid('getSelected');
			if (node){
				$('#test').treegrid('collapse', node.code);
			}
		}
		function expand(){
			var node = $('#test').treegrid('getSelected');
			if (node){
				$('#test').treegrid('expand', node.code);
			}
		}
		function collapseAll(){
			var node = $('#test').treegrid('getSelected');
			if (node){
				$('#test').treegrid('collapseAll', node.code);
			} else {
				$('#test').treegrid('collapseAll');
			}
		}
		function expandAll(){
			var node = $('#test').treegrid('getSelected');
			if (node){
				$('#test').treegrid('expandAll', node.code);
			} else {
				$('#test').treegrid('expandAll');
			}
		}
		function expandTo(){
			$('#test').treegrid('expandTo', '02013');
			$('#test').treegrid('select', '02013');
		}
		var codeIndex = 1000;
		function append(){
			codeIndex++;
			var data = [{
				code: 'code'+codeIndex,
				name: 'name'+codeIndex,
				addr: 'address'+codeIndex,
				col4: 'col4 data'
			}];
			var data = <?php echo $bb?>;
			var node = $('#test').treegrid('getSelected');
			$('#test').treegrid('append', {
				parent: (node?node.code:null),
				data: data
			});
		}
		function remove(){
			var node = $('#test').treegrid('getSelected');
			if (node){
				$('#test').treegrid('remove', node.code);
			}
		}
	</script>
</head>
<body>
	<h1>TreeGrid</h1>
	<div style="margin:10px;">
		<a href="#" onclick="reload()">reload</a>
		<a href="#" onclick="getChildren()">getChildren</a>
		<a href="#" onclick="getSelected()">getSelected</a>
		<a href="#" onclick="collapse()">collapse</a>
		<a href="#" onclick="expand()">expand</a>
		<a href="#" onclick="collapseAll()">collapseAll</a>
		<a href="#" onclick="expandAll()">expandAll</a>
		<a href="#" onclick="expandTo()">expandTo</a>
		<a href="#" onclick="append()">append</a>
	</div>
	
	<table id="test"></table>
	
	<div id="mm" class="easyui-menu" style="width:120px;">
		<div onclick="append()">Append</div>
		<div onclick="remove()">Remove</div>
	</div>
</body>
</html>