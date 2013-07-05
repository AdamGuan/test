<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>无标题文档</title>


	<script type="text/javascript" src="jquery-1.4.2.min.js"></script>

	<script type="text/javascript">
	$(function(){
		$("#cli").click(function(){
			$('#input1').hide();
			$('#password').show();
			
		});
	});
	</script>
</head>
<body>

	<input type="text" id="input1" value="sss"/>
	<input type="password"  id="password"  style="display:none"/>
	<a href="#" id="cli">asdf</a>

</body>
</html>