<!DOCTYPE html>
<html lang="zh-CN">
<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>404-页面未找到</title>
		
		<style type="text/css">@import url("/404/css/stylesheet.css");</style>
		<link href="/404/css/blue.css" rel="stylesheet" type="text/css" />
		
		<!-- Import google jquery -->
		<script type="text/javascript" src="http://www.google.com/jsapi"></script>
		
		<script type="text/javascript">
		google.load("jquery", "1.3.1");
		google.setOnLoadCallback(function() {
			 
			 //---------------- ColorChanger ----------------//
			 
			 // Change stylesheet to Blue	 
	  		 $(".colorblue").click(function(){
	  		 	$("link").attr("href", "/404/css/blue.css");
			    return false;
			});
			   
			// Change stylesheet to Red
			$(".colorred").click(function(){
				$("link").attr("href", "/404/css/red.css");
				return false;
			});
			
			// Change stylesheet to Grey
			$(".colorgrey").click(function(){
				$("link").attr("href", "/404/css/grey.css");
				return false;
			});
			
			// Change stylesheet to Brown
			$(".colorbrown").click(function(){
				$("link").attr("href", "/404/css/brown.css");
				return false;
			});
			
			// Change stylesheet to Brown
			$(".colorgreen").click(function(){
				$("link").attr("href", "/404/css/green.css");
				return false;
			});
			
			
			//---------------- Show and hide Colorchanger ----------------//
			
			$("#colors").hide();
			
			// Show colors when #showChanger clicked
			$("a#showChanger").click(function() {
				$("#colors").slideToggle("slow");
			});
		});
		</script> 
		
		<!-- PNGFix for IE6 -->
		<script type="text/javascript" src="js/jquery.pngFix.js"></script> 
		
		<!-- Active pngfix -->
		<script type="text/javascript"> 
    		$(document).ready(function(){ 
      		$(document).pngFix(); 
    	}); 
		</script> 
		
	</head>
<body>

	<!-- Colorchanger div -->
	<div id="colorchanger">
		<div id="colors">
			<!-- Blue -->
			<a class="colorbox colorblue" href="indexca00.html?theme=blue" title="Blue Theme"></a>
			
			<!-- Grey -->
			<a class="colorbox colorgrey" href="index0a59.html?theme=grey" title="Grey Theme"></a>
			
			<!-- Red -->
			<a class="colorbox colorred" href="index0e99.html?theme=red" title="Red Theme"></a>
			
			<!-- Brown -->
			<a class="colorbox colorbrown" href="index8e01.html?theme=brown" title="Brown Theme"></a>
			
			<!-- Green -->
			<a class="colorbox colorgreen" href="indexaf91.html?theme=green" title="Green Theme"></a>
		</div>
		
		<a href="#" id="showChanger"><img src="/404/images/colortab.png" alt="Change Theme" /></a>
	</div>
	<!-- End colorchanger div -->

	<!-- Warp around everything -->
	<div id="warp">
	
		
		<!-- Header top -->
		<div id="header_top"></div>
		<!-- End header top -->
		
		
		<!-- Header -->
		<div id="header">
			<h2>噢！ 页面未找到</h2>
			<!--<h5>Somebody really liked this page, or maybe your mis-typed the URL.</h5>-->
		</div>
		<!-- End Header -->
		
		
		<!-- The content div -->
		<div id="content">
		
			<!-- text -->
			<div id="text">
				<!-- The info text -->
				<p>不好意思，您查看的页面不存在或出现了故障。</p>
                <br />
				<h3>我们建议</h3>
				<!-- End info text -->
				
				<!-- Page links -->
				<ul>
					<li>&raquo; 返回<a href="HTTP://<?php echo $_SERVER['HTTP_HOST'];?>">目录</a></li>
				</ul>
				<!-- End page links -->	
			</div>
			<!-- End info text -->
		
		
			<!-- Book icon -->
			<img id="book" src="/404/images/img-01.png" alt="Book iCon" />
			<!-- End Book icon -->
			
			<div style="clear:both;"></div>
		</div>
		<!-- End Content -->
		
		
		<!-- Footer bottom -->
		<div id="footer_bottom"></div>
		<!-- End Footer bottom -->
		
		<div style="clear:both;"></div>


	</div>
	<!-- End Warp around everything -->
	
</body>
</html>