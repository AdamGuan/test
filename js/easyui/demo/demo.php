<?php
$d = $_REQUEST['d'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title><?php echo $d?></title>
	<link rel="stylesheet" type="text/css" href="../themes/default/easyui.css">
	<link rel="stylesheet" type="text/css" href="../themes/icon.css">
	<script type="text/javascript" src="../jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="../jquery.easyui.min.js"></script>
	<script type="text/javascript">
	$(function(){
		var url = "../txt/<?php echo $d?>.txt";
		        var content = '<iframe scrolling="auto" frameborder="0"  src="'+url+'" style="width:100%;height:100%;"></iframe>';
        $('#t').tabs().tabs('add',{
            title:'源码',
            content:content,
			
        });
	});
	</script>
</head>
<body>

	<div id="t" plain="true" style="height:450px;">
		<div title="示例" style="padding:10px;" cache="false" href="<?php echo $d?>.html">
		</div>
	</div>

</body>
</html>