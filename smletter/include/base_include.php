<?php

	require_once __DIR__."/conf/conf_path.php";
	require_once PATH_PROJ_ETC."conf_sys.php";

	if(!isset($_SESSION)){
		session_start();
	}
	
	$g_lang = strtolower(constant('DEFAULT_LANG'));
	$g_skin = strtolower(constant('DEFAULT_SKIN'));
	
	$g_lang_key = '__'.PROJ_COOKIE_PREFIX.'_LANG__';
	$g_skin_key = '__'.PROJ_COOKIE_PREFIX.'_SKIN__';
	
	if(empty($_COOKIE[$g_lang_key]) === false){
		$g_lang = strcasecmp($_COOKIE[$g_lang_key], 'en')===0?'en':'cn';
	}
	
	if(empty($_COOKIE[$g_skin_key]) === false){
		$g_skin = strtolower($_COOKIE[$g_skin_key]);
	}
	
	if(empty($_SESSION['SESSION_LOGIN_INFO']['lang']) === false) {
		$g_lang = strtolower($_SESSION['SESSION_LOGIN_INFO']['lang']);
		setcookie($g_lang_key, $g_lang, time()+3600*24*30, '/');
	}
	
	if(empty($_SESSION['SESSION_LOGIN_INFO']['skin']) === false) {
		$g_skin = strtolower($_SESSION['SESSION_LOGIN_INFO']['skin']);
		setcookie($g_skin_key, $g_skin, time()+3600*24*30, '/');
	}
	
	function  __autoload($className) { 
		$basePath = '';
		if (strpos($className, 'Lang') === 0) {
			$basePath = constant('PATH_PROJ_LANGUAGE').$GLOBALS['g_lang'].'/'; 
		}else {
			$basePath = constant('PATH_PROJ_LIB');
		}
		$filePath = $basePath.$className.'.class.php'; 
		
		if (is_readable($filePath)) {  
			require_once($filePath);  
		}  
	}

?>
