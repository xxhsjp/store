<?php

	require_once PATH_PROJ_LIB.'smarty-3.1.27/libs/Smarty.class.php';
	spl_autoload_register("__autoload");
	$g_smarty = new Smarty;

	$g_smarty->template_dir = PATH_PROJ_SMARTY_TPL.'user/'; 
	$g_smarty->compile_dir = PATH_PROJ_SMARTY_TPLC.'user/'; 
	$g_smarty->config_dir = PATH_PROJ_SMARTY_CACHE.'user/';
	$g_smarty->cache_dir = PATH_PROJ_SMARTY_CONF.'user/';

	$g_smarty->assign('g_skin', $g_skin);
	$g_smarty->assign('g_project_name', PROJ_NAME);

?>
