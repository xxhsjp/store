<?php
	require_once __DIR__."/conf/conf_path.php";
	require_once PATH_PROJ_ETC."conf_sys.php";
	
	if(!empty($_POST['method']) || !empty($_GET['method'])){
		$method = empty($_POST['method'])?$_GET['method']:$_POST['method'];
		if(strpos($method, '_') === 0){
			Log::warn(basename(__FILE__).'@'.__LINE__.LOG_SEP.'call unexposed method: '.$method);
		}else {
			Log::debug(basename(__FILE__).'@'.__LINE__.LOG_SEP.'call method: '.$method);
			@call_user_func($method);
		}
		exit;
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
