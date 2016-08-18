<?php
/**
 * time zone
 */
date_default_timezone_set('Asia/Shanghai');
/**
 * project root path
 */
define('PATH_PROJ', __DIR__.'/../../');
/**
 * app path
 */
define('PATH_PROJ_APP', PATH_PROJ.'app/');
/**
 * include path
 */
define('PATH_PROJ_INCLUDE', PATH_PROJ.'include/');
/**
 * web path
 */
define('PATH_PROJ_WEB', PATH_PROJ.'web/');
/**
 * lib path
 */
define('PATH_PROJ_LIB', PATH_PROJ_APP.'lib/');
/**
 * system config 
 */
define('PATH_PROJ_ETC', PATH_PROJ_APP.'etc/');
/**
 * language
 */
define('PATH_PROJ_LANGUAGE', PATH_PROJ_INCLUDE.'language/');
/**
 * Smarty config path start--
 */
define('PATH_PROJ_SMARTY_TPL', PATH_PROJ.'template/tpls/');

define('PATH_PROJ_SMARTY_TPLC', PATH_PROJ.'template/tpls_c/');

define('PATH_PROJ_SMARTY_CACHE', PATH_PROJ.'template/cache/');

define('PATH_PROJ_SMARTY_CONF', PATH_PROJ.'template/configs/');
// Smarty config path end--

?>
