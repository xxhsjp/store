<?php /* Smarty version 3.1.27, created on 2016-04-12 16:45:43
         compiled from "D:\workspace-php\smletter\template\tpls\user\index.html" */ ?>
<?php
/*%%SmartyHeaderCode:15665570cb5b7780f83_66231482%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'bbc63a1b58cf6feeee0716ad105ac9b01356d94f' => 
    array (
      0 => 'D:\\workspace-php\\smletter\\template\\tpls\\user\\index.html',
      1 => 1460450726,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '15665570cb5b7780f83_66231482',
  'variables' => 
  array (
    'g_project_name' => 0,
    'g_skin' => 0,
    'account' => 0,
    'profile_info_available' => 0,
    'profile_info_expired' => 0,
    'profile_info_help' => 0,
    'nav_logout' => 0,
    'profile_info_service' => 0,
    'view_compose' => 0,
    'view_sent' => 0,
    'view_replies' => 0,
    'view_drafts' => 0,
    'view_subscriber' => 0,
    'view_share' => 0,
    'profile_info_usercenter' => 0,
    'view_user_profile' => 0,
    'view_user_design' => 0,
    'view_user_setting' => 0,
    'view_confirm_yes' => 0,
    'view_confirm_no' => 0,
    'lang_json' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_570cb5b7bd2804_95430306',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_570cb5b7bd2804_95430306')) {
function content_570cb5b7bd2804_95430306 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '15665570cb5b7780f83_66231482';
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=0">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="__web_index__">
    <meta name="author" content="">
	
    <title><?php echo $_smarty_tpl->tpl_vars['g_project_name']->value;?>
</title>
	<link type="image/png" href="web/skin/<?php echo $_smarty_tpl->tpl_vars['g_skin']->value;?>
/image/favicon.ico?v=100001" rel="shortcut icon">

    <!-- Bootstrap core CSS -->
    <link href="web/skin/<?php echo $_smarty_tpl->tpl_vars['g_skin']->value;?>
/bs/css/bootstrap.css?v=100000" rel="stylesheet">
    <link href="web/skin/<?php echo $_smarty_tpl->tpl_vars['g_skin']->value;?>
/bs/css/font-awesome.css?v=100000" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="web/skin/<?php echo $_smarty_tpl->tpl_vars['g_skin']->value;?>
/main.css?v=100000" rel="stylesheet">
   
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <?php echo '<script'; ?>
 src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"><?php echo '</script'; ?>
>
      <?php echo '<script'; ?>
 src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"><?php echo '</script'; ?>
>
    <![endif]-->
    <link href="web/js/lib/datetimepicker/css/bootstrap-datetimepicker.min.css?v=100000" rel="stylesheet">

  </head>

  <body>
    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container-fluid">
        <div class="logo"><img src="web/skin/cool/image/logo-1.png"></img></div>
       
        <div class="navbar-header">
         <button type="button" class="navbar-toggle" id="logo" >
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
         </div>
       <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
            <li id="main_dropmenu"><a href="javascript:void(0);" class="dropdown-toggle"  data-toggle="dropdown"><?php echo $_smarty_tpl->tpl_vars['account']->value;?>
<i class="fa fa-angle-down"></i></a>
                <div class="user-dian dropdown-menu" >
	            <!-- h2><?php echo $_smarty_tpl->tpl_vars['profile_info_available']->value;?>
<strong id="main_free_count" class="font-red">10000</strong></h2 -->
	            <P><?php echo $_smarty_tpl->tpl_vars['profile_info_expired']->value;?>
<span id="main_expired">2017/3/15</span></P>
	           </div>
            </li>
            <li class="l-h50">|</li>
            <li><a href="#" target="_blank"><?php echo $_smarty_tpl->tpl_vars['profile_info_help']->value;?>
</a></li >
            <li class="l-h50">|</li>
            <li><a href="logout.php" class="transition"><?php echo $_smarty_tpl->tpl_vars['nav_logout']->value;?>
</a></li>
            
           </ul>
      </div>
    </nav>

    <div class="container-fluid">
      <div class="row">
      <table width="100%" border="0" cellpadding="0" celllspacing="0"><tr><td  class="td-768-no">
        <div id="sidebar" class="col-sm-3 col-md-2 hidden-xs sidebar">
          <!-- ul class="nav nav-main">
            <li class="main-li"><a href="#" lnav="nav_service"><i class="fa fa-angle-down"></i><?php echo $_smarty_tpl->tpl_vars['profile_info_service']->value;?>
</a></li>
          </ul -->
          <ul class="nav nav-sidebar" id="nav_service">
            <li class="active"><a href="#compose" vname="compose"><i class="fa fa-envelope-o"></i><?php echo $_smarty_tpl->tpl_vars['view_compose']->value;?>
</a></li>
            <li><a href="#sent" vname="sent"><i class="fa fa-clone"></i><?php echo $_smarty_tpl->tpl_vars['view_sent']->value;?>
</a></li>
            <li><a href="#replies" vname="replies"><i class="fa fa-paper-plane-o"></i><?php echo $_smarty_tpl->tpl_vars['view_replies']->value;?>
</a></li>
            <li><a href="#drafts" vname="drafts"><i class="fa fa-unlink"></i><?php echo $_smarty_tpl->tpl_vars['view_drafts']->value;?>
</a></li>
            <li><a href="#subscribers" vname="subscribers"><span class="glyphicon glyphicon-user"></span><?php echo $_smarty_tpl->tpl_vars['view_subscriber']->value;?>
</a></li>
            <li><a href="#share" vname="share"><span class="glyphicon glyphicon-user"></span><?php echo $_smarty_tpl->tpl_vars['view_share']->value;?>
</a></li>
           </ul>
           <ul class="nav nav-main">
            <li class="main-li"><a href="#" lnav="nav_setting"><i class="fa fa-angle-down"></i><?php echo $_smarty_tpl->tpl_vars['profile_info_usercenter']->value;?>
</a></li>
           </ul>
           <ul class="nav nav-sidebar" id="nav_setting">
            <li><a href="#user_profile" vname="user_profile"><i class="fa fa-gear"></i><?php echo $_smarty_tpl->tpl_vars['view_user_profile']->value;?>
</a></li>  
			<li><a href="#user_design" vname="user_design"><i class="fa fa-gear"></i><?php echo $_smarty_tpl->tpl_vars['view_user_design']->value;?>
</a></li>  
			<li><a href="#user_setting" vname="user_setting"><i class="fa fa-gear"></i><?php echo $_smarty_tpl->tpl_vars['view_user_setting']->value;?>
</a></li>  
           </ul>
        </div>
        </td><td>
        <div id="content_area" class="col-sm-9 col-md-10  col-xs-12 main">
        </div>
        </td></tr></table>
      </div>
    </div>
    
	<div id="modal_info" class="modal" tabindex="-1" role="dialog" >
	  <div class="modal-dialog modal-sm">
	    <div id="modal_info_content" class="modal-content">
	    </div>
	  </div>
	</div>
	
	<div id="modal_confirm" class="modal" tabindex="-1" role="dialog" >
	  <div class="modal-dialog modal-sm">
	    <div id="modal_confirm_content" class="modal-content">
	    	<div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        <h4 class="modal-title"></h4>
	      	</div>
		    <div class="modal-body">
		    </div>
		    <div class="modal-footer">
		        <button type="button" class="btn btn-primary"><?php echo $_smarty_tpl->tpl_vars['view_confirm_yes']->value;?>
</button>
		        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $_smarty_tpl->tpl_vars['view_confirm_no']->value;?>
</button>
		    </div>
	    </div>
	  </div>
	</div>
	
	<span id="media_test" class="hidden-xs"></span>
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
	<?php echo '<script'; ?>
 src="web/js/lib/jquery-1.11.3.min.js"><?php echo '</script'; ?>
>
	<?php echo '<script'; ?>
 src="web/js/lib/jquery.form.min.js"><?php echo '</script'; ?>
>
	<!-- Include all compiled plugins (below), or include individual files as needed -->
	<?php echo '<script'; ?>
 src="web/js/lib/bootstrap-3.3.5/dist/js/bootstrap.min.js"><?php echo '</script'; ?>
>
	 <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <?php echo '<script'; ?>
 src="web/js/lib/bootstrap-3.3.5/docs/assets/js/ie10-viewport-bug-workaround.js"><?php echo '</script'; ?>
>
  	<?php echo '<script'; ?>
 src="web/js/lib/chart.min.js"><?php echo '</script'; ?>
>
  	<?php echo '<script'; ?>
 src="web/js/common/util.js?v=100000"><?php echo '</script'; ?>
>
  	<?php echo '<script'; ?>
 src="web/js/common/Page.js?v=100000"><?php echo '</script'; ?>
>
	<?php echo '<script'; ?>
 src="web/js/main/frame.js?v=100000"><?php echo '</script'; ?>
>
	<?php echo '<script'; ?>
 src="web/js/main/main.js?v=100000"><?php echo '</script'; ?>
>
  	<?php echo '<script'; ?>
 type="text/javascript">var Lang = <?php echo $_smarty_tpl->tpl_vars['lang_json']->value;?>
,__account__ = "<?php echo $_smarty_tpl->tpl_vars['account']->value;?>
";<?php echo '</script'; ?>
>
  	<?php echo '<script'; ?>
 src="web/js/lib/datetimepicker/js/bootstrap-datetimepicker.min.js"><?php echo '</script'; ?>
>
  	<?php echo '<script'; ?>
 src="web/js/lib/datetimepicker/js/locales/bootstrap-datetimepicker.zh-CN.js"><?php echo '</script'; ?>
>
  	<?php echo '<script'; ?>
 src="web/js/common/md.js?v=100000"><?php echo '</script'; ?>
>
  	<?php echo '<script'; ?>
 src="web/js/lib/ckeditor/ckeditor.js?v=100000"  type="text/javascript"><?php echo '</script'; ?>
>
  	<?php echo '<script'; ?>
 src="web/js/lib/ckeditor/config.js?v=100000"  type="text/javascript"><?php echo '</script'; ?>
>
  </body>
</html>
<?php }
}
?>