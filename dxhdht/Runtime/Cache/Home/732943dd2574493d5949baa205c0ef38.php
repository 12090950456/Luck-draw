<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>登录</title>
		<link rel="stylesheet" href="/dxhd/Public/dxhdht/css/reset1.css" />
		<link rel="stylesheet" href="/dxhd/Public/dxhdht/css/common1.css" />
	</head>
	<body>
		<div class="wrap login_wrap">
			<div class="content">
				<div class="login_frame input_login" id="input_login">                        
					<h3>iTV后台管理系统登录</h3>                        
					<div class="login_err_panel" style="display:none;" id="err"> </div>                      
						<form action="/dxhd/admin.php/Home/Index/yanzheng" method="post" id="form">
						<div class="login_input_panel" id="js_mainContent">                               
							<div class="form_text_ipt">                                    
								<i class="icon_login un"> </i><input id="account"  name="username" type="text" placeholder="请输入登录名">                              
							</div> 
							<div class="form_text_ipt">     
								<i class="icon_login pwd"> </i><input id="pwd" name="password" type="password" placeholder="请输入密码">                            
							</div>
						</div>   
						  
						<div class="form_btn">                                
							<button class="btn_login" style="margin-left: 31%">登录</button>    
						</div> 
						</form>    
				</div>	
				</div>
		</div>
		<script type="text/javascript" src="/dxhd/Public/dxhdht/left/js/jquery.js" ></script>
	</body>
	</html>