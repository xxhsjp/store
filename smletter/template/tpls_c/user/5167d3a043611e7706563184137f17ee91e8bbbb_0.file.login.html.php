<?php /* Smarty version 3.1.27, created on 2016-04-11 14:22:23
         compiled from "D:\workspace-php\smletter\template\tpls\user\login.html" */ ?>
<?php
/*%%SmartyHeaderCode:342570b429f16d157_61250697%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5167d3a043611e7706563184137f17ee91e8bbbb' => 
    array (
      0 => 'D:\\workspace-php\\smletter\\template\\tpls\\user\\login.html',
      1 => 1460355739,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '342570b429f16d157_61250697',
  'variables' => 
  array (
    'g_project_name' => 0,
    'error_msg' => 0,
    'lang_json' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_570b429f1ea175_19277660',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_570b429f1ea175_19277660')) {
function content_570b429f1ea175_19277660 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '342570b429f16d157_61250697';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<meta charset="utf-8">
        <title><?php echo $_smarty_tpl->tpl_vars['g_project_name']->value;?>
</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=no">
	<meta name="description" content="">
	<meta name="author" content="">
	<!-- STYLESHEETS --><!--[if lt IE 9]><?php echo '<script'; ?>
 src="js/flot/excanvas.min.js"><?php echo '</script'; ?>
><?php echo '<script'; ?>
 src="http://html5shiv.googlecode.com/svn/trunk/html5.js"><?php echo '</script'; ?>
><?php echo '<script'; ?>
 src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"><?php echo '</script'; ?>
><![endif]-->
	<link rel="stylesheet" type="text/css" href="web/bg/css/cloud-admin.css" >
	
	<!-- DATE RANGE PICKER -->
	<link rel="stylesheet" type="text/css" href="web/bg/js/bootstrap-daterangepicker/daterangepicker-bs3.css" />
	<!-- UNIFORM -->
	<link rel="stylesheet" type="text/css" href="web/bg/js/uniform/css/uniform.default.min.css" />
	<!-- ANIMATE -->
	<link rel="stylesheet" type="text/css" href="web/bg/css/animatecss/animate.min.css" />
        <link type="image/png" href="web/skin/cool/image/favicon.ico?v=100001" rel="shortcut icon">
    <?php if (isset($_smarty_tpl->tpl_vars['error_msg']->value) === false) {?>
    <style>
    #msg_container {
    		display: none;
    }
    </style>
    <?php }?>

</head>
<body class="login">	
	<!-- PAGE -->
	<section id="page">
			<!-- HEADER -->
			<header>
				<!-- NAV-BAR -->
				<div class="container">
				</div>
				<!--/NAV-BAR -->
			</header>
			<!--/HEADER -->
			<!-- LOGIN -->
			<section id="login_bg" class="visible">
				<div class="container">
					<div class="row">
						<div class="col-md-4 col-md-offset-4">
							<div class="login-box" style="max-width:400px;">
								<h2 class="bigintro"><img src="web/skin/cool/image/logo-1.png"  alt="logo name" /></h2>
								<div class="divide-40"></div>
								<form class="form-signin" action="web/php/user/login.php" method="POST" onsubmit="return loginCtrler.validateForm(this);">
								  <div class="form-group">
									<input id="inputEmail" name="account" style="height:auto;" type="text" class="form-control" placeholder="账号" >
								  </div>
								  <div class="form-group"> 
									<input type="password" style="height:auto;"  class="form-control" id="inputPassword" placeholder="密码" required >
								  </div>
								  <div class="form-group">
									 <input style="width:auto;background:transparent;padding-left:0;" name="remember_me" type="checkbox" value="1" checked><span style="margin-left:20px">记住账号</span>
									<button type="submit" style="padding:9px 12px;margin-top:10px;" class="btn btn-danger">登录</button>
								  </div>
                                  <input type="hidden" id="submitPassword" name="password">
								</form>
								<!-- SOCIAL LOGIN -->
								<div class="divide-20"></div>
								<a href="#" onclick="swapScreen('register');return false;">马上注册</a>
								</div>
					</div>
				</div>
			</section>
			<!--/LOGIN -->
			<!-- REGISTER -->
			<section id="register">
				<div class="container">
					<div class="row">
						<div class="col-md-4 col-md-offset-4">
							<div class="login-box-plain">
								<h2 class="bigintro">注册</h2>
								<form role="form" action="web/php/user/register.php" method="POST" onsubmit="return loginCtrler.validateRegForm(this);">
								  <div class="form-group">
									<input type="text" name="account" style="height:auto;"  placeholder="用户名" class="form-control" id="exampleInputUsername" >
								  </div>
								  <div class="form-group">
									<input type="email" name="email" style="height:auto;"  placeholder="邮件地址" class="form-control" id="exampleInputEmail1" >
								  </div>
								  <div class="form-group"> 
									<input type="password"  placeholder="密码" style="height:auto;" class="form-control" id="exampleInputPassword1" >
								  	<input type="hidden" id="regPassword" name="password">
								  </div>
								  <div class="form-group"> 
									<input type="password" placeholder="确认密码" style="height:auto;"  class="form-control" id="exampleInputPassword2" >
								  </div>
								  <div class="divide-20"></div>
									<!-- label class="checkbox"> <input type="checkbox" class="uniform" value=""> I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></label -->
								  <button type="submit" class="btn btn-success">注册</button>
								</form>
								<!-- /SOCIAL REGISTER -->
								<div class="login-helpers">
									<a href="#" onclick="swapScreen('login_bg');return false;">返回登录</a> <br>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
			
			 <div id="msg_container" style="width:50%;margin:0 auto;" class="alert alert-danger form-info text-center" role="alert">
			  	<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
			  	<?php echo $_smarty_tpl->tpl_vars['error_msg']->value;?>

			 </div>
			<!--/REGISTER -->
			<!-- FORGOT PASSWORD -->
			<!-- FORGOT PASSWORD -->
	<!--/PAGE -->
	<!-- JAVASCRIPTS -->
	<!-- Placed at the end of the document so the pages load faster -->
	<!-- JQUERY -->
	<?php echo '<script'; ?>
 src="web/bg/js/jquery/jquery-2.0.3.min.js"><?php echo '</script'; ?>
>
	<?php echo '<script'; ?>
 src="web/bg/js/jQuery-Cookie/jquery.cookie.min.js"><?php echo '</script'; ?>
>
	<!-- JQUERY UI-->
	<?php echo '<script'; ?>
 src="web/bg/js/jquery-ui-1.10.3.custom/js/jquery-ui-1.10.3.custom.min.js"><?php echo '</script'; ?>
>
	<!-- BOOTSTRAP -->
	<?php echo '<script'; ?>
 src="web/bg/bootstrap-dist/js/bootstrap.min.js"><?php echo '</script'; ?>
>
	
	
	<!-- UNIFORM -->
	<?php echo '<script'; ?>
 type="text/javascript" src="web/bg/js/uniform/jquery.uniform.min.js"><?php echo '</script'; ?>
>
	<!-- BACKSTRETCH -->
	<?php echo '<script'; ?>
 type="text/javascript" src="web/bg/js/backstretch/jquery.backstretch.min.js"><?php echo '</script'; ?>
>
        <?php echo '<script'; ?>
 src="web/bg/js/backstretch.js"><?php echo '</script'; ?>
>
	<!-- CUSTOM SCRIPT -->
	<?php echo '<script'; ?>
 type="text/javascript">
		function swapScreen(id) {
			jQuery('.visible').removeClass('visible animated fadeInUp');
			jQuery('#'+id).addClass('visible animated fadeInUp');
		}
	<?php echo '</script'; ?>
>
  	<?php echo '<script'; ?>
 type="text/javascript">var Lang = <?php echo $_smarty_tpl->tpl_vars['lang_json']->value;?>
;<?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="web/js/user/login.js?v=100000"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="web/js/common/md.js"><?php echo '</script'; ?>
>
</body>
</html>
<?php }
}
?>