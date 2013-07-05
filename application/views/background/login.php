<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>后台管理登录</title>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('js/easyui/themes/gray/easyui.css');?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('js/easyui/themes/icon.css');?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('css/user_Login.css');?>">
	<script type="text/javascript" src="<?php echo base_url('js/easyui/jquery-1.8.3.min.js');?>"></script>
    <script type="text/javascript" src="<?php echo base_url('js/easyui/jquery.easyui.min.js');?>"></script>
</head>

<body id=userlogin_body>

	<DIV id=panSiteFactory>
		<DIV id=siteFactoryLogin>
			<DIV id=user_login>
				<DL>
					<DD id=user_top>
						<UL>
							<LI class=user_top_l></LI>
							<LI class=user_top_c></LI>
							<LI class=user_top_r></LI>
						</UL>
					</DD>
					<DD id=user_main>
						<UL>
							<LI class=user_main_l></LI>
							<LI class=user_main_c>
								<DIV class=user_main_box>
									<UL>
										<LI class=user_main_text>用户名： </LI>
										<LI class=user_main_input><INPUT class=TxtUserNameCssClass id=user maxLength=20 name=user> </LI>
									</UL>
									<UL>
										<LI class=user_main_text>密 码： </LI>
										<LI class=user_main_input><INPUT class=TxtPasswordCssClass id=pwd type=password name=pwd> </LI>
									</UL>
									<UL>
										<LI class=user_main_text>验证码： </LI>
										<LI class=user_main_input><INPUT class=TxtYanzheng id=captcha name=captcha> </LI>
									</UL>
									<UL>
										<LI class=user_main_text></LI>
										<LI class=user_main_input>&nbsp:&nbsp:&nbsp:&nbsp:<span id="captcha_image"><?php echo $captcha_image;?></span> </LI>
									</UL>
								</DIV>
							</LI>
							<LI class=user_main_r>
								<IMG class=IbtnEnterCssClass id="submit" style="BORDER-TOP-WIDTH: 0px; BORDER-LEFT-WIDTH: 0px; BORDER-BOTTOM-WIDTH: 0px; BORDER-RIGHT-WIDTH: 0px"  src="<?php echo base_url();?>image/admin_login//user_botton.gif" name=IbtnEnter>
							</LI>
						</UL>
					</DD>
					<DD id=user_bottom>
						<UL>
							<LI class=user_bottom_l></LI>
							<LI class=user_bottom_c></LI>
							<LI class=user_bottom_r></LI>
						</UL>
					</DD>
				</DL>
			</DIV>
		</DIV>
	</DIV>

	<script type="text/javascript">
		
		/**
		* 模块
		*/
		(function(j_module_page){
			
			/**
			* 登录
			*/
			j_module_page.login = function(button_domid,user_domid,pwd_domid,captcha_domid){

				$("#"+button_domid).click(function(){
					//获取用户名
					var user = $("#"+user_domid).val();
					//获取密码
					var pwd = $("#"+pwd_domid).val();
					//获取验证码
					var captcha = $("#"+captcha_domid).val();

					var value_valid = 1;
					if(user.length <= 0)
					{
						value_valid = 0;
						$.messager.alert('警告','用户名不能为空','warning');
					}
					if(value_valid != 1 || pwd.length <= 0)
					{
						value_valid = 0;
						$.messager.alert('警告','密码不能为空','warning');
					}
					if(value_valid != 1 || captcha.length <= 0)
					{
						value_valid = 0;
						$.messager.alert('警告','验证码不能为空','warning');
					}

					if(value_valid == 1)
					{
						$.ajax({
							type: "POST",
							url: "<?php echo base_url('background/c_login/index');?>",
							data: "sign=check_login&user="+user+"&pwd="+pwd+"&captcha="+captcha,
							success: function(msg){
								var msg = eval("("+msg+")");
								var result = msg.result;
								var success_url = msg.success_url;
								if(result == 1)
								{
									window.location.href=success_url;
								}
								else if(result == 2)
								{
									$.messager.alert('错误','验证码错误','error');
								}
								else if(result == 3)
								{
									$.messager.alert('错误','用户名或密码错误','error');
								}
								else
								{
									$.messager.alert('警告','系统故障','warning');
								}
							}
						}); 
					}
				});
			};
			
			/**
			* 验证码刷新
			*/
			j_module_page.captcha_refresh = function(captcha_refresh_domid,captcha_image_domid){

				$("#"+captcha_refresh_domid).click(function(){
					$("#"+captcha_image_domid).html('loading...');

					$.ajax({
						type: "POST",
						url: "<?php echo base_url('background/c_login/index');?>",
						data: "sign=create_captcha",
						success: function(msg){
							$("#"+captcha_image_domid).html(msg);
						}
					}); 
				});
			};

		})(window.j_module_page = window.j_module_page || {});

		
		/**
		* 自执行
		*/
		(function(){

			//登录
			window.j_module_page.login('submit','user','pwd','captcha');
			
			//验证码刷新
			window.j_module_page.captcha_refresh('captcha_image','captcha_image');

		})();

	</script>

</body>
</html>