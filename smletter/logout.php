<?php
require_once __DIR__."/include/base_include.php";

$lm = new LoginManager();

$isLogout = $lm->logout();

header('Location: '.PROJ_ROOT.'login.php?msg=logout');
exit;

?>

